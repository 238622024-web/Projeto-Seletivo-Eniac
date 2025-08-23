<?php
// Teste da API para testimonials

echo "<h1>Teste da API - Testimonials</h1>";

// Simular requisição POST
$_POST['action'] = 'get_testimonials_candidatos';
$_SERVER['REQUEST_METHOD'] = 'POST';

echo "<h2>Testando endpoint get_testimonials_candidatos:</h2>";

// Incluir o arquivo da API
ob_start();
include 'api.php';
$resultado = ob_get_clean();

echo "<pre>" . htmlspecialchars($resultado) . "</pre>";

echo "<h2>Verificando dados diretamente no banco:</h2>";

require_once 'config.php';

try {
    $db = (new Database())->connect();
    
    $stmt = $db->prepare("SELECT * FROM testimonials_candidatos ORDER BY data_envio DESC");
    $stmt->execute();
    $testimonials = $stmt->fetchAll();
    
    echo "<p>Total de testimonials no banco: " . count($testimonials) . "</p>";
    
    foreach ($testimonials as $testimonial) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<strong>Nome:</strong> " . htmlspecialchars($testimonial['nome']) . "<br>";
        echo "<strong>Cargo:</strong> " . htmlspecialchars($testimonial['cargo']) . "<br>";
        echo "<strong>Status:</strong> " . htmlspecialchars($testimonial['status']) . "<br>";
        echo "<strong>Mensagem:</strong> " . htmlspecialchars($testimonial['mensagem']) . "<br>";
        echo "<strong>Data:</strong> " . $testimonial['data_envio'] . "<br>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro ao acessar banco: " . $e->getMessage() . "</p>";
}
?>
