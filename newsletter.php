<?php
if (isset($_GET['id'])) {
    include 'database.php';

    try {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM newsletters WHERE id = :id');
        $stmt->execute(['id' => $_GET['id']]);
        $newsletter = $stmt->fetch();

        if (!$newsletter) {
            die("Newsletter não encontrada.");
        }
    } catch (PDOException $e) {
        die("Erro ao buscar newsletter: " . $e->getMessage());
    }
} else {
    die("ID da newsletter não especificado.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($newsletter['title']) ?></title>
    <link rel="stylesheet" type="text/css" href="/css/newsletter.css">
</head>
<body>
  <!DOCTYPE html>
  <html>
  <head>
      <title><?= htmlspecialchars($newsletter['title']) ?></title>
      <link rel="stylesheet" type="text/css" href="style.css">
  </head>
  <body>
      <div class="header">
          <h1>Krust Newsletter</h1>
      </div>

      <div class="container">
          <h1><?= htmlspecialchars($newsletter['title']) ?></h1>
          <p><?= nl2br(htmlspecialchars($newsletter['content'])) ?></p>
          <p><strong>Escrito por:</strong> <?= htmlspecialchars($newsletter['author']) ?></p>
          <a href="posts.php" class="button">Voltar para a lista</a>
      </div>
      <div class="footer">
          &copy; <?= date("Y") ?> Krust Newsletter. Todos os direitos reservados.
      </div>
  </body>
  </html>

</body>
</html>
