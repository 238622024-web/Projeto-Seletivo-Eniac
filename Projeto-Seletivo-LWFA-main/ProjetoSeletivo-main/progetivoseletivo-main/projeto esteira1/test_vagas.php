<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Iniciando teste do vagas.php...<br>";

try {
    require_once 'config.php';
    echo "Config.php carregado com sucesso<br>";
    
    $database = new Database();
    echo "Classe Database instanciada<br>";
    
    $pdo = $database->connect();
    echo "Conexão com banco estabelecida<br>";
    
    // Teste simples de query
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM vagas");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "Total de vagas no banco: " . $result['total'] . "<br>";
    
    echo "Teste concluído com sucesso!<br>";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "<br>";
    echo "Linha: " . $e->getLine() . "<br>";
    echo "Arquivo: " . $e->getFile() . "<br>";
}
?>
