<?php
include 'database.php';

$username = ''; // Substitua por um nome de usuário de sua escolha
$password = ''; // Substitua por uma senha segura

// Esta pagina e para cadastrar apenas o primeiro adm, e so abrir ela no navegador.

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->execute(['username' => $username, 'password' => $hashedPassword]);

    echo "Administrador adicionado com sucesso.";
} catch (PDOException $e) {
    die("Erro ao adicionar administrador: " . $e->getMessage());
}
?>
