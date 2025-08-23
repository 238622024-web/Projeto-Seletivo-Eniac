<?php
// Debug do arquivo criar_vaga_teste.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug - criar_vaga_teste.php</h1>";

try {
    echo "<p>1. Verificando config.php...</p>";
    require_once 'config.php';
    echo "<p style='color: green;'>✅ config.php carregado</p>";
    
    echo "<p>2. Testando conexão com banco...</p>";
    $database = new Database();
    $pdo = $database->connect();
    echo "<p style='color: green;'>✅ Conexão com banco OK</p>";
    
    echo "<p>3. Verificando estrutura da tabela vagas...</p>";
    $stmt = $pdo->query("DESCRIBE vagas");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Colunas encontradas: " . implode(', ', $columns) . "</p>";
    
    echo "<p>4. Incluindo arquivo original...</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}

// Tentar incluir o arquivo original
ob_start();
try {
    include 'criar_vaga_teste.php';
    $content = ob_get_contents();
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao incluir arquivo: " . $e->getMessage() . "</p>";
}
ob_end_clean();

if (isset($content) && !empty($content)) {
    echo "<p style='color: green;'>✅ Arquivo carregado com sucesso</p>";
} else {
    echo "<p style='color: red;'>❌ Arquivo não gerou conteúdo</p>";
}
?>
