<?php
require_once 'config.php';

try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Verificar se a coluna 'tipo' existe
    $stmt = $pdo->prepare("SHOW COLUMNS FROM vagas LIKE 'tipo'");
    $stmt->execute();
    $column_exists = $stmt->fetch();
    
    if (!$column_exists) {
        echo "Coluna 'tipo' não encontrada. Adicionando...<br>";
        
        // Adicionar coluna tipo
        $stmt = $pdo->prepare("ALTER TABLE vagas ADD COLUMN tipo VARCHAR(50) DEFAULT 'CLT' AFTER salario");
        $stmt->execute();
        
        echo "Coluna 'tipo' adicionada com sucesso!<br>";
        
        // Atualizar registros existentes
        $stmt = $pdo->prepare("UPDATE vagas SET tipo = 'CLT' WHERE tipo IS NULL OR tipo = ''");
        $stmt->execute();
        
        echo "Registros existentes atualizados com tipo padrão 'CLT'<br>";
        
    } else {
        echo "Coluna 'tipo' já existe na tabela.<br>";
    }
    
    // Verificar estrutura atualizada
    $stmt = $pdo->prepare("DESCRIBE vagas");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Estrutura atual da tabela:</h3>";
    foreach ($columns as $column) {
        echo $column['Field'] . " - " . $column['Type'] . "<br>";
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
