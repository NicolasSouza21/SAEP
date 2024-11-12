<?php
// db.php

$host = 'localhost';       // Endereço do servidor PostgreSQL
$dbname = 'todolist';      // Nome do banco de dados
$user = 'postgres';        // Usuário do banco de dados
$password = 'postgres';    // Senha do usuário
$port = '5432';            // Porta do PostgreSQL (padrão 5432)

try {
    // Conexão com o PostgreSQL usando PDO
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname;port=$port", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>
