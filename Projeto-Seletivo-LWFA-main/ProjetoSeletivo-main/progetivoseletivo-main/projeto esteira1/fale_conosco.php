<?php
require_once 'config.php';

$success_message = '';
$error_message = '';

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitizeInput($_POST['nome'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $telefone = sanitizeInput($_POST['telefone'] ?? '');
    $categoria = sanitizeInput($_POST['categoria'] ?? '');
    $assunto = sanitizeInput($_POST['assunto'] ?? '');
    $mensagem = sanitizeInput($_POST['mensagem'] ?? '');
    
    // Validações
    if (empty($nome) || empty($email) || empty($categoria) || empty($assunto) || empty($mensagem)) {
        $error_message = 'Todos os campos obrigatórios devem ser preenchidos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Por favor, insira um email válido.';
    } else {
        try {
            $database = new Database();
            $pdo = $database->connect();
            
            // Inserir mensagem no banco
            $sql = "INSERT INTO contatos (nome, email, telefone, categoria, assunto, mensagem, data_envio) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $email, $telefone, $categoria, $assunto, $mensagem]);
            
            // Enviar email de notificação (simulado)
            $success_message = 'Mensagem enviada com sucesso! Entraremos em contato em breve.';
            
            // Limpar campos após sucesso
            $nome = $email = $telefone = $categoria = $assunto = $mensagem = '';
            
        } catch (Exception $e) {
            $error_message = 'Erro interno. Tente novamente mais tarde.';
            error_log("Erro ao salvar contato: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fale Conosco | LWFA - Processo Seletivo Virtual</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #333;
            line-height: 1.6;
        }

        /* Header - Mesmo padrão */
        header {
            background: linear-gradient(135deg, #0056b3, #004494);
            padding: 0;
            color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
        }

        .logo-section {
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .logo-header {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border: 3px solid rgba(255, 255, 255, 0.3);
            transition: transform 0.3s ease;
        }

        .logo-header:hover {
            transform: scale(1.05);
        }
            align-items: center;
            justify-content: center;
        }

        nav {
            display: flex;
            gap: 0.5rem;
            position: relative;
            flex-wrap: nowrap;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        nav a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        nav a:hover::before {
            left: 100%;
        }

        nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #00d4ff, #0099ff);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(-50%);
            border-radius: 2px;
        }

        nav a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 153, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.4);
            color: #ffffff;
        }

        nav a:hover::after {
            width: 80%;
        }

        nav a:hover i {
            transform: rotate(360deg) scale(1.2);
            color: #00d4ff;
        }

        nav a.active {
            background: linear-gradient(135deg, #0099ff, #00d4ff);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 6px 20px rgba(0, 153, 255, 0.4);
            transform: translateY(-2px);
        }

        nav a.active::after {
            width: 90%;
            background: rgba(255, 255, 255, 0.8);
        }

        nav a i {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 1rem;
        }

        /* Main Content */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header h1 {
            color: #0056b3;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #666;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Layout do Fale Conosco */
        .contact-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 4rem;
            margin-top: 3rem;
        }

        /* Formulário */
        .contact-form {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 86, 179, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .required {
            color: #e74c3c;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            background: #fff;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #0056b3;
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
        }

        select {
            cursor: pointer;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #0056b3, #007bff);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 86, 179, 0.3);
        }

        /* Informações de Contato */
        .contact-info {
            background: linear-gradient(135deg, #0056b3, #007bff);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            height: fit-content;
        }

        .contact-info h3 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .info-item i {
            font-size: 1.2rem;
            margin-top: 2px;
            color: #00d4ff;
        }

        .info-content h4 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
            font-weight: 600;
        }

        .info-content p {
            font-size: 0.9rem;
            opacity: 0.9;
            line-height: 1.4;
        }

        /* FAQ Rápido */
        .faq-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .faq-item {
            margin-bottom: 1rem;
        }

        .faq-question {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .faq-answer {
            font-size: 0.85rem;
            opacity: 0.9;
            line-height: 1.4;
        }

        /* Alertas */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .contact-layout {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .contact-info {
                order: -1;
            }
        }

        /* Desktop - garantir layout horizontal */
        @media (min-width: 769px) {
            .header-container {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
            }
            
            nav {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                gap: 0.5rem !important;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1.5rem;
                padding: 1rem;
            }

            nav {
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.5rem;
            }

            nav a {
                padding: 0.6rem 1.2rem;
                font-size: 0.85rem;
                border-radius: 20px;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .contact-form,
            .contact-info {
                padding: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 1rem;
            }

            .contact-form,
            .contact-info {
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 1.8rem;
            }
        }

        /* Footer - Mesmo padrão das outras páginas */
        footer {
            background: linear-gradient(135deg, #1a1a1a, #333);
            color: white;
            padding: 60px 20px 30px;
            margin-top: 80px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h3 {
            color: #0099ff;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .footer-section p, .footer-section li {
            color: #ccc;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #0099ff;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-link {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.2rem;
            position: relative;
            overflow: hidden;
        }

        .social-link.linkedin {
            background: linear-gradient(135deg, #0077b5, #005582);
            box-shadow: 0 4px 15px rgba(0, 119, 181, 0.3);
        }

        .social-link.linkedin:hover {
            background: linear-gradient(135deg, #005582, #003d5c);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 119, 181, 0.4);
        }

        .social-link.facebook {
            background: linear-gradient(135deg, #1877f2, #0d5dcc);
            box-shadow: 0 4px 15px rgba(24, 119, 242, 0.3);
        }

        .social-link.facebook:hover {
            background: linear-gradient(135deg, #0d5dcc, #0a4da3);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(24, 119, 242, 0.4);
        }

        .social-link.instagram {
            background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045);
            box-shadow: 0 4px 15px rgba(131, 58, 180, 0.3);
        }

        .social-link.instagram:hover {
            background: linear-gradient(135deg, #6a2c93, #e11d48, #f59e0b);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(131, 58, 180, 0.4);
        }

        .social-link.whatsapp {
            background: linear-gradient(135deg, #25d366, #1ebe57);
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
        }

        .social-link.whatsapp:hover {
            background: linear-gradient(135deg, #1ebe57, #189c47);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4);
        }

        .social-link.youtube {
            background: linear-gradient(135deg, #ff0000, #cc0000);
            box-shadow: 0 4px 15px rgba(255, 0, 0, 0.3);
        }

        .social-link.youtube:hover {
            background: linear-gradient(135deg, #cc0000, #990000);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 0, 0, 0.4);
        }

        .footer-bottom {
            border-top: 1px solid #444;
            padding-top: 30px;
            text-align: center;
            color: #999;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo-section">
                <img src="./imagens/Logoindex.jpg" alt="Logo da empresa" class="logo-header">
            </div>
            <nav>
                <a href="index.php"><i class="fas fa-home"></i> Início</a>
                <a href="vagas.php"><i class="fas fa-briefcase"></i> Vagas</a>
                <a href="cadastro.php"><i class="fas fa-user-plus"></i> Cadastrar</a>
                <a href="fale_conosco.php" class="active"><i class="fas fa-envelope"></i> Contato</a>
                <a href="login_admin.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <div class="page-header">
            <h1><i class="fas fa-envelope"></i> Fale Conosco</h1>
            <p>Estamos aqui para ajudar! Entre em contato conosco para tirar suas dúvidas, reportar problemas ou dar sugestões.</p>
        </div>

        <div class="contact-layout">
            <!-- Formulário de Contato -->
            <div class="contact-form">
                <h2><i class="fas fa-paper-plane"></i> Envie sua Mensagem</h2>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">Nome Completo <span class="required">*</span></label>
                            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefone">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($telefone ?? ''); ?>" placeholder="(11) 99999-9999">
                        </div>
                        <div class="form-group">
                            <label for="categoria">Categoria <span class="required">*</span></label>
                            <select id="categoria" name="categoria" required>
                                <option value="">Selecione uma categoria</option>
                                <option value="suporte_tecnico" <?php echo ($categoria ?? '') === 'suporte_tecnico' ? 'selected' : ''; ?>>Suporte Técnico</option>
                                <option value="duvidas_vagas" <?php echo ($categoria ?? '') === 'duvidas_vagas' ? 'selected' : ''; ?>>Dúvidas sobre Vagas</option>
                                <option value="problemas_cadastro" <?php echo ($categoria ?? '') === 'problemas_cadastro' ? 'selected' : ''; ?>>Problemas no Cadastro</option>
                                <option value="empresas" <?php echo ($categoria ?? '') === 'empresas' ? 'selected' : ''; ?>>Para Empresas</option>
                                <option value="sugestoes" <?php echo ($categoria ?? '') === 'sugestoes' ? 'selected' : ''; ?>>Sugestões</option>
                                <option value="outros" <?php echo ($categoria ?? '') === 'outros' ? 'selected' : ''; ?>>Outros</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="assunto">Assunto <span class="required">*</span></label>
                        <input type="text" id="assunto" name="assunto" value="<?php echo htmlspecialchars($assunto ?? ''); ?>" required placeholder="Resuma o motivo do seu contato">
                    </div>

                    <div class="form-group">
                        <label for="mensagem">Mensagem <span class="required">*</span></label>
                        <textarea id="mensagem" name="mensagem" required placeholder="Descreva detalhadamente sua dúvida, problema ou sugestão..."><?php echo htmlspecialchars($mensagem ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i>
                        Enviar Mensagem
                    </button>
                </form>
            </div>

            <!-- Informações de Contato -->
            <div class="contact-info">
                <h3><i class="fas fa-info-circle"></i> Informações de Contato</h3>

                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div class="info-content">
                        <h4>Email Principal</h4>
                        <p>contato@LWFA.com<br>
                        Respondemos em até 24 horas</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div class="info-content">
                        <h4>Telefone/WhatsApp</h4>
                        <p>(11) 9999-9999<br>
                        Segunda a Sexta: 8h às 18h</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="info-content">
                        <h4>Endereço</h4>
                        <p>São Paulo, SP - Brasil<br>
                        Atendimento presencial com agendamento</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div class="info-content">
                        <h4>Horário de Atendimento</h4>
                        <p>Segunda a Sexta: 8h às 18h<br>
                        Sábado: 9h às 13h</p>
                    </div>
                </div>

                <!-- FAQ Rápido -->
                <div class="faq-section">
                    <h4><i class="fas fa-question-circle"></i> Perguntas Frequentes</h4>
                    
                    <div class="faq-item">
                        <div class="faq-question">Como me candidatar a uma vaga?</div>
                        <div class="faq-answer">Acesse a página "Vagas", encontre a oportunidade ideal e clique em "Candidatar-se".</div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Como acompanhar minha candidatura?</div>
                        <div class="faq-answer">Após se candidatar, você receberá atualizações por email sobre o status do processo.</div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Esqueci minha senha, e agora?</div>
                        <div class="faq-answer">Entre em contato conosco informando seu email cadastrado para redefinir sua senha.</div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Como empresas podem anunciar vagas?</div>
                        <div class="faq-answer">Entre em contato pelo formulário selecionando "Para Empresas" e nossa equipe entrará em contato.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h3>Processo-Seletivo</h3>
                <p>Conectando talentos com oportunidades desde 2025. Nossa missão é simplificar o processo seletivo e aproximar candidatos das empresas ideais.</p>
                <div class="social-links">
                    <a href="#" class="social-link linkedin" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-link facebook" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link instagram" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link whatsapp" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="#" class="social-link youtube" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Links Rápidos</h3>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="cadastro.php">Cadastro</a></li>
                    <li><a href="vagas.php">Vagas</a></li>
                    <li><a href="fale_conosco.php">Fale Conosco</a></li>
                    <li><a href="login_admin.php">RH</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Suporte</h3>
                <ul>
                    <li><a href="fale_conosco.php">Central de Ajuda</a></li>
                    <li><a href="#">Termos de Uso</a></li>
                    <li><a href="#">Política de Privacidade</a></li>
                    <li><a href="fale_conosco.php">Fale Conosco</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Contato</h3>
                <p><i class="fas fa-envelope"></i> contato@lwfa.com</p>
                <p><i class="fas fa-phone"></i> (11) 9999-9999</p>
                <p><i class="fas fa-map-marker-alt"></i> São Paulo, SP - Brasil</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 LWFA — Todos os direitos reservados. Desenvolvido com ❤️ para conectar pessoas.</p>
        </div>
    </footer>
</body>
</html>
