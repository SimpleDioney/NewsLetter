<?php

include 'database.php';
include 'email_validator.php';

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$successMessage = '';
$errorMessage = '';
$showVerificationPopup = false;
$maxAttempts = 3;
$attemptInterval = 30;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        $verificationCode = rand(100000, 999999);

        try {
            $pdo = getPDO();
            $stmt = $pdo->prepare("SELECT * FROM subscribers WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $subscriber = $stmt->fetch();

            if ($subscriber) {
                $stmt = $pdo->prepare("UPDATE subscribers SET verification_code = :code, code_sent_time = NOW(), verification_attempts = 0 WHERE email = :email");
                $stmt->execute(['email' => $email, 'code' => $verificationCode]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO subscribers (email, verification_code, code_sent_time, verification_attempts, is_active) VALUES (:email, :code, NOW(), 0, FALSE)");
                $stmt->execute(['email' => $email, 'code' => $verificationCode]);
            }

            $_SESSION['verification_email'] = $email;
            $_SESSION['attempts'] = 0;
            $_SESSION['last_attempt_time'] = time();

            sendEmail($email, "Seu Código de Verificação", "$verificationCode", $emailConfig);

            $showVerificationPopup = true;
        } catch (PDOException $e) {
            $errorMessage = "Erro: " . $e->getMessage();
        }
    } elseif (isset($_POST['verification_code'])) {
        $code = $_POST['verification_code'];
        $email = $_SESSION['verification_email'] ?? '';
        $currentAttempts = $_SESSION['attempts'] ?? 0;
        $lastAttemptTime = $_SESSION['last_attempt_time'] ?? time();

        if (time() - $lastAttemptTime < $attemptInterval) {
            $errorMessage = 'Aguarde 30 segundos antes de tentar novamente.';
            $showVerificationPopup = true;
        } elseif ($currentAttempts >= $maxAttempts) {
            $errorMessage = 'Limite de tentativas excedido.';
        } else {
            if ($email) {
                try {
                    $pdo = getPDO();
                    $stmt = $pdo->prepare("SELECT * FROM subscribers WHERE email = :email AND verification_code = :code");
                    $stmt->execute(['email' => $email, 'code' => $code]);
                    $subscriber = $stmt->fetch();

                    if ($subscriber) {
                        $updateStmt = $pdo->prepare("UPDATE subscribers SET is_active = TRUE WHERE email = :email");
                        $updateStmt->execute(['email' => $email]);

                        $successMessage = 'Inscrição confirmada!';
                    } else {
                        $_SESSION['attempts']++;
                        $_SESSION['last_attempt_time'] = time();
                        $errorMessage = 'Código de verificação incorreto. Tentativas restantes: ' . ($maxAttempts - $currentAttempts - 1);
                        $showVerificationPopup = true;
                    }
                } catch (PDOException $e) {
                    $errorMessage = "Erro: " . $e->getMessage();
                }
            } else {
                $errorMessage = 'Erro: E-mail não encontrado.';
                $showVerificationPopup = true;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cadastro</title>
    <link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>
    <?php if ($showVerificationPopup): ?>
        <div id="verificationPopup">
            <form action="cadastro.php" method="post">
                <input type="text" name="verification_code" placeholder="Código de 6 dígitos">
                <input type="submit" value="Verificar Código">
            </form>
            <?php if ($errorMessage): ?>
                <p><?= $errorMessage ?></p>
            <?php endif; ?>
        </div>
    <?php elseif (!$successMessage): ?>
        <form action="cadastro.php" method="post">
            <input type="email" name="email" required placeholder="Seu email">
            <input type="submit" value="Inscrever-se">
        </form>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <p><?= $successMessage ?></p>
    <?php endif; ?>
</body>
</html>
