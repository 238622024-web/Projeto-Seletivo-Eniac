<?php
require_once 'config.php';

echo "<h1>Debug da Tabela Testimonials</h1>";

try {
    $db = (new Database())->connect();
    
    // Verificar se a tabela existe
    $stmt = $db->prepare("SHOW TABLES LIKE 'testimonials_candidatos'");
    $stmt->execute();
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "<p style='color: green;'>✅ Tabela 'testimonials_candidatos' existe!</p>";
        
        // Verificar estrutura da tabela
        echo "<h2>Estrutura da Tabela:</h2>";
        $stmt = $db->prepare("DESCRIBE testimonials_candidatos");
        $stmt->execute();
        $structure = $stmt->fetchAll();
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($structure as $column) {
            echo "<tr>";
            echo "<td>" . $column['Field'] . "</td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Key'] . "</td>";
            echo "<td>" . $column['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Verificar dados
        echo "<h2>Dados na Tabela:</h2>";
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM testimonials_candidatos");
        $stmt->execute();
        $count = $stmt->fetch();
        
        echo "<p>Total de registros: " . $count['total'] . "</p>";
        
        if ($count['total'] > 0) {
            $stmt = $db->prepare("SELECT * FROM testimonials_candidatos ORDER BY data_envio DESC LIMIT 5");
            $stmt->execute();
            $testimonials = $stmt->fetchAll();
            
            foreach ($testimonials as $testimonial) {
                echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0; background: #f9f9f9;'>";
                echo "<strong>ID:</strong> " . $testimonial['id'] . "<br>";
                echo "<strong>Nome:</strong> " . htmlspecialchars($testimonial['nome']) . "<br>";
                echo "<strong>Status:</strong> " . $testimonial['status'] . "<br>";
                echo "<strong>Mensagem:</strong> " . htmlspecialchars(substr($testimonial['mensagem'], 0, 100)) . "...<br>";
                echo "</div>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Nenhum registro encontrado na tabela!</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Tabela 'testimonials_candidatos' NÃO existe!</p>";
        
        echo "<h2>Tabelas existentes no banco:</h2>";
        $stmt = $db->prepare("SHOW TABLES");
        $stmt->execute();
        $tables = $stmt->fetchAll();
        
        foreach ($tables as $table) {
            echo "<li>" . $table[0] . "</li>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
?>
