<?php
$host = 'localhost';
$dbname = 'lanchejudah';
$user = 'root';        
$pass = 'root';           

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Adicione esta linha temporariamente para verificar
    // echo "Conexão com o banco de dados OK!<br>";
} catch (PDOException $e) {
    // Se a conexão falhar, esta mensagem será exibida
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>