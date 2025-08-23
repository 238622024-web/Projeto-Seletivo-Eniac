<?php
// Script para verificar e corrigir estrutura do banco de dados
require_once 'config.php';

try {
    $database = new Database();
    $pdo = $database->connect();
    
    echo "<h2>Verificando estrutura do banco de dados...</h2>";
    
    // Verificar se a tabela vagas existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'vagas'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>❌ Tabela 'vagas' não encontrada!</p>";
        echo "<p>Criando tabela vagas...</p>";
        
        $createTable = "
        CREATE TABLE vagas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(150) NOT NULL,
            empresa VARCHAR(100) NOT NULL,
            descricao TEXT NOT NULL,
            requisitos TEXT,
            beneficios TEXT,
            salario_min DECIMAL(10,2),
            salario_max DECIMAL(10,2),
            tipo_contrato ENUM('clt', 'pj', 'estagio', 'freelancer', 'temporario'),
            modalidade ENUM('presencial', 'remoto', 'hibridinhas', 'híbrido') DEFAULT 'presencial',
            localizacao VARCHAR(100),
            nivel ENUM('junior', 'pleno', 'senior', 'gerencia', 'diretoria'),
            area VARCHAR(50),
            status ENUM('ativa', 'pausada', 'encerrada') DEFAULT 'ativa',
            data_publicacao DATE,
            data_encerramento DATE,
            vagas_disponiveis INT DEFAULT 1,
            visualizacoes INT DEFAULT 0,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($createTable);
        echo "<p style='color: green;'>✅ Tabela 'vagas' criada com sucesso!</p>";
    } else {
        echo "<p style='color: green;'>✅ Tabela 'vagas' encontrada!</p>";
    }
    
    // Verificar colunas da tabela vagas
    echo "<h3>Estrutura da tabela vagas:</h3>";
    $stmt = $pdo->query("DESCRIBE vagas");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th></tr>";
    
    $hasStatus = false;
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
        
        if ($column['Field'] === 'status') {
            $hasStatus = true;
        }
    }
    echo "</table>";
    
    if (!$hasStatus) {
        echo "<p style='color: red;'>❌ Coluna 'status' não encontrada!</p>";
        echo "<p>Adicionando coluna status...</p>";
        
        $addColumn = "ALTER TABLE vagas ADD COLUMN status ENUM('ativa', 'pausada', 'encerrada') DEFAULT 'ativa'";
        $pdo->exec($addColumn);
        echo "<p style='color: green;'>✅ Coluna 'status' adicionada com sucesso!</p>";
    } else {
        echo "<p style='color: green;'>✅ Coluna 'status' encontrada!</p>";
    }
    
    // Teste de inserção simples
    echo "<h3>Teste de inserção:</h3>";
    $testInsert = "INSERT INTO vagas (titulo, empresa, descricao, status) VALUES ('Teste', 'Empresa Teste', 'Descrição teste', 'ativa')";
    
    try {
        $pdo->exec($testInsert);
        echo "<p style='color: green;'>✅ Teste de inserção funcionou!</p>";
        
        // Remover o registro de teste
        $pdo->exec("DELETE FROM vagas WHERE titulo = 'Teste' AND empresa = 'Empresa Teste'");
        echo "<p style='color: blue;'>ℹ️ Registro de teste removido.</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro no teste de inserção: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
?>
