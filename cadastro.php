<?php
include 'database.php';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    try {
        $pdo = getPDO();
        $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (:email)");
        $stmt->execute(['email' => $email]);

        $successMessage = 'Inscrição realizada com sucesso!';
    } catch (PDOException $e) {
        // Se o e-mail já estiver inscrito, pode lançar um erro de duplicata
        $errorMessage = "Erro ao inscrever: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cadastro</title>
    <link rel="stylesheet" type="text/css" href="/css/login.css">
</head>
<body>
  
  <form action="cadastro.php" method="post">
      <input type="email" name="email" required placeholder="Seu email">
      <input type="submit" value="Inscrever-se">
  </form>

    <?php if ($successMessage): ?>
        <p><?= $successMessage ?></p>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <p><?= $errorMessage ?></p>
    <?php endif; ?>
</body>
</html>
