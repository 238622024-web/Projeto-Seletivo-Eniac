<?php
require_once 'config.php';

try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Criar/atualizar usuário admin com senha conhecida
    $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("
        INSERT INTO usuarios_admin (nome, email, senha_hash, nivel, ativo) 
        VALUES ('Administrador', 'admin@eniaclink.com', ?, 'admin', 1)
        ON DUPLICATE KEY UPDATE senha_hash = ?, ativo = 1
    ");
    
    $stmt->execute([$senha_hash, $senha_hash]);
    
    echo "✅ Usuário admin criado/atualizado com sucesso!\n";
    echo "📧 Email: admin@eniaclink.com\n";
    echo "🔑 Senha: admin123\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
