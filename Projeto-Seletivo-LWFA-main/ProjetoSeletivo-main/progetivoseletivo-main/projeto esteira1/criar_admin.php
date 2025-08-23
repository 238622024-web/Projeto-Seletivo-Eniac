<?php
require_once 'config.php';

try {
    $db = (new Database())->connect();
    
    // Definir nova senha
    $senha = 'admin123';
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Atualizar senha do admin
    $stmt = $db->prepare("UPDATE usuarios_admin SET senha_hash = ? WHERE email = 'admin@eniaclink.com'");
    $stmt->execute([$senha_hash]);
    
    echo "✅ Senha do admin atualizada com sucesso!<br>";
    echo "📧 Email: admin@eniaclink.com<br>";
    echo "🔑 Senha: admin123<br>";
    echo "<br>";
    echo "Agora você pode fazer login no painel administrativo.";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
