<?php
// Verifica se o driver está disponível
if (!extension_loaded('pdo_pgsql')) {
    die('A extensão PDO PostgreSQL não está instalada. Por favor, instale php-pgsql.');
}

$host = 'localhost';
$port = '5432';
$dbname = 'projeto_arvores';
$user = 'postgres';
$pass = 'postgres';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}?>