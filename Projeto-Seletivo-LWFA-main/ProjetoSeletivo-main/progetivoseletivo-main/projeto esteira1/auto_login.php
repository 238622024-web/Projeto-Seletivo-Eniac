<?php
require_once 'config.php';

SessionManager::start();

try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Fazer login automático para teste
    $stmt = $pdo->prepare("SELECT id, nome, email FROM usuarios_admin WHERE email = 'admin@eniaclink.com'");
    $stmt->execute();
    $usuario = $stmt->fetch();
    
    if ($usuario) {
        SessionManager::set('admin_logged_in', true);
        SessionManager::set('admin_id', $usuario['id']);
        SessionManager::set('admin_nome', $usuario['nome']);
        SessionManager::set('admin_email', $usuario['email']);
        SessionManager::set('admin_nivel', 'admin');
        
        echo "✅ Login automático realizado!\n";
        echo "👤 Usuário: " . $usuario['nome'] . "\n";
        echo "📧 Email: " . $usuario['email'] . "\n";
    } else {
        echo "❌ Usuário não encontrado\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Automático</title>
</head>
<body>
    <h2>Login Automático Realizado</h2>
    <p><a href="criar_vaga.php">🔗 Acessar Criar Vaga</a></p>
    <p><a href="admin.php">🔗 Acessar Admin Dashboard</a></p>
    <p><a href="gerenciar_vagas.php">🔗 Acessar Gerenciar Vagas</a></p>
</body>
</html>
