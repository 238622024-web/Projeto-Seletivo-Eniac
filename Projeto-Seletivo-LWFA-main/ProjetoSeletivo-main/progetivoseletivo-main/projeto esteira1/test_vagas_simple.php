<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testando vagas.php...<br>";

try {
    require_once 'config.php';
    
    // Verificar se há sessão ativa
    $is_admin = false;
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        $is_admin = true;
        $admin_nome = $_SESSION['admin_nome'] ?? 'Administrador';
    }

    echo "Sessão verificada. Admin: " . ($is_admin ? 'Sim' : 'Não') . "<br>";

    // Teste de conexão com banco
    $database = new Database();
    $pdo = $database->connect();
    echo "Conexão estabelecida<br>";

    // Buscar vagas
    $sql = "SELECT v.*, COUNT(c.id) as total_candidaturas 
            FROM vagas v 
            LEFT JOIN candidaturas c ON v.id = c.vaga_id 
            WHERE v.status = 'ativa'
            GROUP BY v.id 
            ORDER BY v.data_criacao DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $vagas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Vagas encontradas: " . count($vagas) . "<br>";
    
    echo "Teste completo com sucesso!<br>";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "<br>";
    echo "Linha: " . $e->getLine() . "<br>";
    echo "Arquivo: " . $e->getFile() . "<br>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teste Vagas</title>
</head>
<body>
    <h1>Teste concluído</h1>
</body>
</html>
