<?php
require_once 'config.php';
SessionManager::start();
SessionManager::set('admin_logged_in', true);
SessionManager::set('admin_id', 1);
SessionManager::set('admin_nome', 'Administrador');
SessionManager::set('admin_email', 'admin@eniaclink.com');
echo "Login realizado! <a href='curriculos.php'>Ir para Currículos</a>";
?>
