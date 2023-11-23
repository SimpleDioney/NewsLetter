<?php
include 'database.php';

try {
    $pdo = getPDO();
    $stmt = $pdo->query('SELECT id, title, content FROM newsletters');
    $newsletters = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar newsletters: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Posts</title>
    <link rel="stylesheet" type="text/css" href="/css/posts.css">
</head>
<body>
    <div id="posts">
<?php foreach ($newsletters as $newsletter): ?>
    <div class='newsletter'>
        <h3><?= htmlspecialchars($newsletter['title'] ?? 'Título não disponível') ?></h3>
        <p><?= substr(htmlspecialchars($newsletter['content'] ?? 'Conteúdo não disponível'), 0, 150) ?>...</p>
        <a href="newsletter.php?id=<?= $newsletter['id'] ?>">Ler mais</a>
    </div>
<?php endforeach; ?>

    </div>
</body>
</html>
