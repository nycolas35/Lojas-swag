<?php
// Ajuste os dados abaixo conforme o seu MySQL (XAMPP: usuário root, senha vazia)
$host = 'localhost';
$banco = 'lojas_swag';
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro ao conectar ao banco: ' . htmlspecialchars($e->getMessage()));
}

function preco($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}