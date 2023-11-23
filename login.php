<?php
session_start();
include 'database.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: admin.php");
            exit;
        } else {
            $errorMessage = 'Usuário ou senha incorretos.';
        }
    } catch (PDOException $e) {
        $errorMessage = "Erro de login: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="/css/login.css">
</head>
<body>
    <form action="login.php" method="post">
        <input type="text" name="username" required placeholder="Nome de usuário">
        <input type="password" name="password" required placeholder="Senha">
        <input type="submit" value="Entrar">
    </form>
    <?php if ($errorMessage): ?>
        <p><?= $errorMessage ?></p>
    <?php endif; ?>
</body>
</html>
