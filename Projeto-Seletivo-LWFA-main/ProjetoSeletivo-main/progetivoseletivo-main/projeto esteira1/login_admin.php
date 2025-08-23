<?php
require_once 'config.php';
SessionManager::start();

$error_message = '';
$success_message = '';

// Verificar se há mensagem de logout
if (isset($_GET['message'])) {
    $success_message = $_GET['message'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        $error_message = 'Email e senha são obrigatórios.';
    } else {
        try {
            $db = (new Database())->connect();
            
            $stmt = $db->prepare("SELECT id, nome, email, senha_hash, nivel FROM usuarios_admin WHERE email = ? AND ativo = 1");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
                // Login bem-sucedido
                SessionManager::set('admin_logged_in', true);
                SessionManager::set('admin_id', $usuario['id']);
                SessionManager::set('admin_nome', $usuario['nome']);
                SessionManager::set('admin_email', $usuario['email']);
                SessionManager::set('admin_nivel', $usuario['nivel']);
                
                // Atualizar último login
                $stmt = $db->prepare("UPDATE usuarios_admin SET ultimo_login = NOW() WHERE id = ?");
                $stmt->execute([$usuario['id']]);
                
                // Registrar atividade
                logActivity($db, 'login_admin', "Login realizado: {$usuario['nome']}", null, null, $usuario['id']);
                
                header('Location: admin.php');
                exit;
            } else {
                $error_message = 'Email ou senha incorretos.';
            }
            
        } catch (Exception $e) {
            $error_message = 'Erro interno. Tente novamente.';
        }
    }
}

// Se já estiver logado, redirecionar
if (SessionManager::has('admin_logged_in')) {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - Sistema de Recrutamento</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298, #34609b);
            background-size: 400% 400%;
            background-attachment: fixed;
            animation: gradientShift 12s ease infinite;
            height: 100vh;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #333;
            position: relative;
            overflow: hidden;
        }

        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            overflow: hidden;
        }

        /* Elementos flutuantes decorativos */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 60px 60px;
            animation: float 20s linear infinite;
            pointer-events: none;
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: floatUpDown 6s ease-in-out infinite;
            pointer-events: none;
        }

        .floating-shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: -2s;
        }

        .floating-shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 20%;
            right: 10%;
            animation-delay: -4s;
        }

        .floating-shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: -1s;
        }

        .floating-shape:nth-child(4) {
            width: 100px;
            height: 100px;
            bottom: 10%;
            right: 20%;
            animation-delay: -3s;
        }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-100px, -100px) rotate(360deg); }
        }

        @keyframes floatUpDown {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(25px);
            padding: 50px 40px;
            border-radius: 25px;
            box-shadow: 
                0 25px 80px rgba(30, 60, 114, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 10;
            transform: translateY(0);
            animation: slideInUp 0.8s ease;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #1e3c72, #2a5298, #34609b);
            border-radius: 25px 25px 0 0;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .logo-admin {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center;
            box-shadow: 
                0 20px 40px rgba(30, 60, 114, 0.3),
                0 10px 20px rgba(0, 0, 0, 0.1),
                inset 0 0 0 4px rgba(255, 255, 255, 0.9);
            border: 6px solid transparent;
            background: linear-gradient(135deg, #ffffff, #f8f9fa) padding-box,
                        linear-gradient(135deg, #1e3c72, #2a5298, #34609b) border-box;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 15px;
            position: relative;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .logo-admin:hover {
            transform: scale(1.08) translateY(-3px);
            box-shadow: 
                0 25px 50px rgba(30, 60, 114, 0.4),
                0 15px 30px rgba(0, 0, 0, 0.15),
                inset 0 0 0 4px rgba(255, 255, 255, 1);
            filter: brightness(1.1);
        }

        .logo-section p {
            color: #4a5568;
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #1e3c72;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group label i {
            width: 18px;
            text-align: center;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid rgba(30, 60, 114, 0.1);
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .form-group input:focus {
            outline: none;
            border-color: #2a5298;
            box-shadow: 
                0 0 0 4px rgba(42, 82, 152, 0.1),
                0 8px 25px rgba(30, 60, 114, 0.15);
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 1);
        }

        .btn-login {
            width: 100%;
            padding: 16px 20px;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 25px rgba(30, 60, 114, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #2a5298, #34609b);
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(30, 60, 114, 0.4);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #0056b3;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #004494;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            transition: all 0.3s ease;
            padding: 5px;
            border-radius: 50%;
        }

        .password-toggle i:hover {
            color: #2a5298;
            background: rgba(42, 82, 152, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                margin: 20px;
                padding: 40px 30px;
                max-width: 100%;
            }

            .logo-section h1 {
                font-size: 1.8rem;
            }

            .logo-admin {
                width: 80px;
                height: 80px;
            }

            .floating-shape {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }

            .form-group input {
                padding: 12px 15px;
            }

            .btn-login {
                padding: 14px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Elementos flutuantes decorativos -->
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
    
    <!-- Conteúdo principal centralizado -->
    <main class="main-content">
        <div class="login-container">
        <div class="logo-section">
            <img src="./imagens/Logoindex.jpg" alt="Logo da empresa" class="logo-admin">
            <p>Painel Administrativo</p>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    autocomplete="email"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                >
            </div>

            <div class="form-group">
                <label for="senha">
                    <i class="fas fa-lock"></i> Senha
                </label>
                <div class="password-toggle">
                    <input 
                        type="password" 
                        id="senha" 
                        name="senha" 
                        required 
                        autocomplete="current-password"
                    >
                    <i class="fas fa-eye" onclick="togglePassword()"></i>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
        </form>

        <div class="back-link">
            <a href="index.php">
                <i class="fas fa-arrow-left"></i> Voltar ao Site
            </a>
        </div>
        </div>
    </main>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('senha');
            const toggleIcon = document.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Auto-focus no primeiro campo
        document.getElementById('email').focus();
    </script>

    <!-- Footer -->
    <footer class="text-white relative z-10" style="background: linear-gradient(135deg, #1a1a1a, #2a2a2a, #1f1f1f);">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <!-- Grid principal do footer -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                
                <!-- Seção da empresa -->
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-blue-400 mb-2 flex items-center">
                        <i class="fas fa-briefcase mr-2"></i>
                        Sistema de Recrutamento
                    </h3>
                    <p class="text-gray-300 leading-relaxed text-xs">
                        Conectando talentos com oportunidades desde 2025.
                    </p>
                    <!-- Redes sociais -->
                    <div class="flex space-x-2 pt-2">
                        <a href="#" class="w-8 h-8 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg flex items-center justify-center hover:from-blue-700 hover:to-blue-800 transition-all duration-300 hover:scale-105" title="LinkedIn">
                            <i class="fab fa-linkedin-in text-white text-sm"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center hover:from-blue-600 hover:to-blue-700 transition-all duration-300 hover:scale-105" title="Facebook">
                            <i class="fab fa-facebook-f text-white text-sm"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg flex items-center justify-center hover:from-pink-600 hover:to-purple-700 transition-all duration-300 hover:scale-105" title="Instagram">
                            <i class="fab fa-instagram text-white text-sm"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center hover:from-green-600 hover:to-green-700 transition-all duration-300 hover:scale-105" title="WhatsApp">
                            <i class="fab fa-whatsapp text-white text-sm"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gradient-to-r from-red-600 to-red-700 rounded-lg flex items-center justify-center hover:from-red-700 hover:to-red-800 transition-all duration-300 hover:scale-105" title="YouTube">
                            <i class="fab fa-youtube text-white text-sm"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Links rápidos -->
                <div class="space-y-2">
                    <h3 class="text-sm font-semibold text-blue-400 mb-2 flex items-center">
                        <i class="fas fa-link mr-2"></i>
                        Links Rápidos
                    </h3>
                    <ul class="space-y-1">
                        <li><a href="index.php" class="text-gray-300 hover:text-blue-400 transition-colors duration-300 flex items-center text-xs"><i class="fas fa-home mr-2 w-3"></i>Início</a></li>
                        <li><a href="cadastro.php" class="text-gray-300 hover:text-blue-400 transition-colors duration-300 flex items-center text-xs"><i class="fas fa-user-plus mr-2 w-3"></i>Cadastro</a></li>
                        <li><a href="vagas.php" class="text-gray-300 hover:text-blue-400 transition-colors duration-300 flex items-center text-xs"><i class="fas fa-briefcase mr-2 w-3"></i>Vagas</a></li>
                        <li><a href="login_admin.php" class="text-gray-300 hover:text-blue-400 transition-colors duration-300 flex items-center text-xs"><i class="fas fa-user-tie mr-2 w-3"></i>Painel RH</a></li>
                    </ul>
                </div>
                
                <!-- Suporte -->
                <div class="space-y-2">
                    <h3 class="text-sm font-semibold text-blue-400 mb-2 flex items-center">
                        <i class="fas fa-life-ring mr-2"></i>
                        Suporte
                    </h3>
                    <ul class="space-y-1">
                        <li><a href="central_ajuda.php" class="text-gray-300 hover:text-blue-400 transition-colors duration-300 flex items-center text-xs"><i class="fas fa-question-circle mr-2 w-3"></i>Central de Ajuda</a></li>
                        <li><a href="termos_uso.php" class="text-gray-300 hover:text-blue-400 transition-colors duration-300 flex items-center text-xs"><i class="fas fa-file-contract mr-2 w-3"></i>Termos de Uso</a></li>
                        <li><a href="politica_privacidade.php" class="text-gray-300 hover:text-blue-400 transition-colors duration-300 flex items-center text-xs"><i class="fas fa-shield-alt mr-2 w-3"></i>Política de Privacidade</a></li>
                        <li><a href="fale_conosco.php" class="text-gray-300 hover:text-blue-400 transition-colors duration-300 flex items-center text-xs"><i class="fas fa-envelope mr-2 w-3"></i>Fale Conosco</a></li>
                    </ul>
                </div>
                
                <!-- Contato -->
                <div class="space-y-2">
                    <h3 class="text-sm font-semibold text-blue-400 mb-2 flex items-center">
                        <i class="fas fa-address-card mr-2"></i>
                        Contato
                    </h3>
                    <div class="space-y-1">
                        <div class="flex items-center text-gray-300 text-xs">
                            <i class="fas fa-envelope mr-2 text-blue-400 w-3"></i>
                            <span>contato@sistemarecrutamento.com</span>
                        </div>
                        <div class="flex items-center text-gray-300 text-xs">
                            <i class="fas fa-phone mr-2 text-blue-400 w-3"></i>
                            <span>(11) 9999-9999</span>
                        </div>
                        <div class="flex items-center text-gray-300 text-xs">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-400 w-3"></i>
                            <span>São Paulo, SP</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Linha divisória -->
            <div class="border-t border-gray-700 pt-3">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-gray-400 text-xs mb-2 md:mb-0">
                        © 2025 <span class="text-blue-400 font-semibold">Sistema de Recrutamento</span> — Todos os direitos reservados.
                    </div>
                    <div class="text-gray-400 text-xs flex items-center">
                        Desenvolvido com <i class="fas fa-heart text-red-500 mx-1 animate-pulse"></i> para conectar pessoas
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
