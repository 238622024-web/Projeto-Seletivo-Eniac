<?php
require_once 'config.php';

try {
    $db = (new Database())->connect();
    
    // Limpar todas as avaliações de teste
    $stmt = $db->prepare("DELETE FROM avaliacoes");
    $stmt->execute();
    
    echo "✅ Tabela de avaliações limpa com sucesso!\n";
    echo "Agora a seção 'O que nossos usuários dizem' ficará vazia até que usuários reais façam avaliações.\n";
    
} catch (Exception $e) {
    echo "❌ Erro ao limpar avaliações: " . $e->getMessage() . "\n";
}
?>
