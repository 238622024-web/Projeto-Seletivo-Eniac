<?php
// Verificar se foi passado o ID da vaga
$vaga_id = $_GET['id'] ?? null;

if (!$vaga_id) {
    // Se não foi passado ID da vaga, redirecionar para página de vagas
    header('Location: vagas.php');
    exit;
}

// Verificar se a vaga existe
require_once 'config.php';

try {
    $database = new Database();
    $pdo = $database->connect();
    
    $stmt = $pdo->prepare("SELECT * FROM vagas WHERE id = ? AND status = 'ativa'");
    $stmt->execute([$vaga_id]);
    $vaga = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$vaga) {
        // Vaga não encontrada, redirecionar para página de vagas
        header('Location: vagas.php?erro=vaga_nao_encontrada');
        exit;
    }
    
} catch (PDOException $e) {
    // Erro no banco, redirecionar para página de vagas
    header('Location: vagas.php?erro=banco_dados');
    exit;
}

// Redirecionar para o cadastro com o ID da vaga
header('Location: cadastro.php?vaga_id=' . $vaga_id);
exit;
?>

