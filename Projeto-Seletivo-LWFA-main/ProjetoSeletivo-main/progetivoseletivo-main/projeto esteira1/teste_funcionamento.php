<?php
echo "<h1>Teste de Funcionamento</h1>";
echo "<p>Data/Hora: " . date('d/m/Y H:i:s') . "</p>";
echo "<p>PHP versão: " . phpversion() . "</p>";

// Teste de conexão com banco
try {
    require_once 'config.php';
    $database = new Database();
    $pdo = $database->connect();
    echo "<p style='color: green;'>✅ Conexão com banco OK!</p>";
    
    // Verificar tabela vagas
    $stmt = $pdo->query("SHOW TABLES LIKE 'vagas'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Tabela vagas encontrada!</p>";
        
        // Verificar coluna status
        $stmt = $pdo->query("SHOW COLUMNS FROM vagas LIKE 'status'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Coluna status encontrada!</p>";
        } else {
            echo "<p style='color: red;'>❌ Coluna status não encontrada!</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Tabela vagas não encontrada!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro no banco: " . $e->getMessage() . "</p>";
}

echo "<h2>Links de Teste:</h2>";
echo "<ul>";
echo "<li><a href='index.php'>Página Principal</a></li>";
echo "<li><a href='criar_vaga_novo.php'>Criar Vaga (Nova)</a></li>";
echo "<li><a href='criar_vaga_teste.php'>Criar Vaga (Teste)</a></li>";
echo "<li><a href='gerenciar_vagas.php'>Gerenciar Vagas</a></li>";
echo "<li><a href='vagas.php'>Listar Vagas</a></li>";
echo "</ul>";
?>
