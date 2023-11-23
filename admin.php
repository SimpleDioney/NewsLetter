<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php';
include 'email_config.php';

$successMessage = '';
$errorMessage = '';

// Adicionar um novo administrador
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['newAdmin'])) {
    $adminUsername = $_POST['adminUsername'];
    $adminPassword = password_hash($_POST['adminPassword'], PASSWORD_DEFAULT);

    try {
        $pdo = getPDO();
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->execute(['username' => $adminUsername, 'password' => $adminPassword]);

        $successMessage = "Administrador adicionado com sucesso.";
    } catch (PDOException $e) {
        $errorMessage = "Erro ao adicionar administrador: " . $e->getMessage();
    }
}



// Publicar uma nova newsletter
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['publishNewsletter'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_POST['author'];

    try {
        $pdo = getPDO();
        $stmt = $pdo->prepare("INSERT INTO newsletters (title, content, author) VALUES (:title, :content, :author)");
        $stmt->execute(['title' => $title, 'content' => $content, 'author' => $author]);

        // Enviar e-mail para os assinantes
        $stmt = $pdo->query('SELECT email FROM subscribers');
        $subscribers = $stmt->fetchAll();

        foreach ($subscribers as $subscriber) {
            sendEmail($subscriber['email'], "" . $title, $content, $emailConfig);
        }

        $successMessage = "Newsletter publicada e enviada com sucesso!";
    } catch (PDOException $e) {
        $errorMessage = "Erro ao salvar a newsletter: " . $e->getMessage();
    }
}

// Deletar uma newsletter
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteNewsletter'])) {
    $newsletterId = $_POST['newsletterId'];

    try {
        $pdo = getPDO();
        $stmt = $pdo->prepare("DELETE FROM newsletters WHERE id = :id");
        $stmt->execute(['id' => $newsletterId]);

        $successMessage = "Newsletter deletada com sucesso.";
    } catch (PDOException $e) {
        $errorMessage = "Erro ao deletar a newsletter: " . $e->getMessage();
    }
}

// Editar uma newsletter
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editNewsletter'])) {
    $newsletterId = $_POST['newsletterId'];
    $newTitle = $_POST['newTitle'];
    $newContent = $_POST['newContent'];
    $newAuthor = $_POST['newAuthor'];

    try {
        $pdo = getPDO();
        $stmt = $pdo->prepare("UPDATE newsletters SET title = :title, content = :content, author = :author WHERE id = :id");
        $stmt->execute(['title' => $newTitle, 'content' => $newContent, 'author' => $newAuthor, 'id' => $newsletterId]);

        $successMessage = "Newsletter editada com sucesso.";
    } catch (PDOException $e) {
        $errorMessage = "Erro ao editar a newsletter: " . $e->getMessage();
    }
}

// Buscar todas as newsletters
try {
    $pdo = getPDO();
    $stmt = $pdo->query('SELECT * FROM newsletters');
    $newsletters = $stmt->fetchAll();
} catch (PDOException $e) {
    $errorMessage = "Erro ao buscar newsletters: " . $e->getMessage();
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin</title>
    <link rel="stylesheet" type="text/css" href="/css/admin.css">
    <script>
  function openPopup(popupId) {
      document.getElementById(popupId).classList.add('open');
      document.getElementById('overlay').style.display = 'block';
  }

  function closePopup() {
      document.querySelectorAll('.popup').forEach(popup => popup.classList.remove('open'));
      document.getElementById('overlay').style.display = 'none';
  }

      function openPopupEdit(newsletterId, title, content, author) {
          document.getElementById('editNewsletterId').value = newsletterId;
          document.getElementById('editNewsletterTitle').value = title;
          document.getElementById('editNewsletterContent').textContent = content;
          document.getElementById('editNewsletterAuthor').value = author;
          closePopup(); // Fechando o menu de seleção
          openPopup('popupEditNewsletter');
      }

      function openPopupDelete(newsletterId, newsletterTitle) {
          document.getElementById('deleteNewsletterId').value = newsletterId;
          document.getElementById('deleteNewsletterTitle').innerText = newsletterTitle;
          closePopup(); // Fechando o menu de seleção
          openPopup('popupConfirmDeleteNewsletter');
      }
    </script>
</head>
<body>
    <div class="overlay" id="overlay" onclick="closePopup()"></div>

  <div class="navbar">
      <a href="#home">Home</a>
      <div class="dropdown">
          <button class="dropbtn">Newsletter 
              <i class="fa fa-caret-down"></i>
          </button>
          <div class="dropdown-content">
              <a href="#" onclick="openPopup('popupAddAdmin')">Adicionar Administrador</a>
              <a href="#" onclick="openPopup('popupListEditNewsletter')">Editar Newsletter</a>
              <a href="#" onclick="openPopup('popupListDeleteNewsletter')">Deletar Newsletter</a>
          </div>
      </div> 
  </div>


    <div class="popup" id="popupAddAdmin">
        <!-- Formulário para adicionar administrador -->
        <form action="admin.php" method="post">
            <input type="text" name="adminUsername" placeholder="Nome de usuário do admin">
            <input type="password" name="adminPassword" placeholder="Senha do admin">
            <input type="submit" name="newAdmin" value="Adicionar Administrador">
        </form>
        <button onclick="closePopup()">Fechar</button>
    </div>

    <div class="popup" id="popupEditNewsletter">
        <form id="editNewsletterForm" action="admin.php" method="post">
            <input type="hidden" name="newsletterId" id="editNewsletterId">
            <input type="text" name="newTitle" placeholder="Novo Título" id="editNewsletterTitle">
            <textarea name="newContent" placeholder="Novo Conteúdo" id="editNewsletterContent"></textarea>
            <input type="text" name="newAuthor" placeholder="Novo Autor" id="editNewsletterAuthor">
            <input type="submit" name="editNewsletter" value="Salvar Alterações">
        </form>
        <button onclick="closePopup()">Fechar</button>
    </div>
  <!-- Lista de newsletters para exclusão -->
  <div class="popup" id="popupListDeleteNewsletter">
      <?php foreach ($newsletters as $newsletter): ?>
          <button onclick="openPopupDelete(<?= $newsletter['id'] ?>, '<?= addslashes($newsletter['title']) ?>')">Deletar: <?= $newsletter['title'] ?></button>
      <?php endforeach; ?>
  </div>

  <!-- Pop-up de confirmação de exclusão -->
  <div class="popup" id="popupConfirmDeleteNewsletter">
      <p>Tem certeza de que deseja deletar a newsletter: <span id="deleteNewsletterTitle"></span>?</p>
      <form action="admin.php" method="post">
          <input type="hidden" name="newsletterId" id="deleteNewsletterId">
          <input type="submit" name="deleteNewsletter" value="Deletar">
      </form>
      <button onclick="closePopup()">Cancelar</button>
  </div>
    <div class="popup" id="popupDeleteNewsletter">
        <form id="deleteNewsletterForm" action="admin.php" method="post">
            <input type="hidden" name="newsletterId" id="deleteNewsletterId">
            <p>Tem certeza de que deseja deletar esta newsletter?</p>
            <button type="button" onclick="openPopup('popupConfirmDeleteNewsletter')">Deletar</button>
            <button type="button" onclick="openPopup('popupConfirmCancelDeleteNewsletter')">Cancelar</button>
        </form>
        <button onclick="closePopup()">Fechar</button>
    </div>
  
  
  <!-- Pop-ups para listagem de newsletters para edição e exclusão -->
  <div class="popup" id="popupListEditNewsletter">
      <?php foreach ($newsletters as $newsletter): ?>
          <button onclick="openPopupEdit(<?= $newsletter['id'] ?>, '<?= addslashes($newsletter['title']) ?>', '<?= addslashes($newsletter['content']) ?>', '<?= addslashes($newsletter['author']) ?>')">Editar: <?= $newsletter['title'] ?></button>
      <?php endforeach; ?>
  </div>

  <div class="popup" id="popupListDeleteNewsletter">
      <?php foreach ($newsletters as $newsletter): ?>
          <button onclick="openPopupDelete(<?= $newsletter['id'] ?>)">Deletar: <?= $newsletter['title'] ?></button>
      <?php endforeach; ?>
  </div>
  
    <!-- Formulário para publicar uma newsletter -->
    <form action="admin.php" method="post">
        <input type="text" name="title" required placeholder="Título da Newsletter">
        <textarea name="content" required placeholder="Conteúdo da Newsletter"></textarea>
        <input type="text" name="author" required placeholder="Nome do Escritor">
        <input type="submit" name="publishNewsletter" value="Publicar Newsletter">
    </form>

    <?php if ($successMessage): ?>
        <p><?= $successMessage ?></p>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <p><?= $errorMessage ?></p>
    <?php endif; ?>
</body>
</html>