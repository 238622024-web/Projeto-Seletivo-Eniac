<?php
require_once 'config.php';
SessionManager::start();

// Destruir a sessão
SessionManager::destroy();

// Redirecionar para a página de login
header('Location: login_admin.php?message=Logout realizado com sucesso');
exit;
?>
