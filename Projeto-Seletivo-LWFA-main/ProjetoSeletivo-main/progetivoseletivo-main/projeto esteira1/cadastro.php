<?php
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
$vaga_id = isset($_GET['vaga_id']) ? $_GET['vaga_id'] : null;

// Se foi passado vaga_id, buscar informações da vaga
$vaga_info = null;
if ($vaga_id) {
    require_once 'config.php';
    try {
        $database = new Database();
        $pdo = $database->connect();
        
        $stmt = $pdo->prepare("SELECT titulo, empresa FROM vagas WHERE id = ? AND status = 'ativa'");
        $stmt->execute([$vaga_id]);
        $vaga_info = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Em caso de erro, ignorar
        $vaga_info = null;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cadastro | LWFA - Processo Seletivo Virtual</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
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

    /* Header Profissional - Mesmo do index.html */
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

    /* Responsividade do logo */
    @media (max-width: 768px) {
      .logo-header {
        width: 60px;
        height: 60px;
      }
    }

    @media (max-width: 480px) {
      .logo-header {
        width: 50px;
        height: 50px;
      }
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

    /* Efeito de pulsação para o botão ativo */
    nav a.active {
      animation: pulseGlow 2s ease-in-out infinite alternate;
    }

    @keyframes pulseGlow {
      0% {
        box-shadow: 0 6px 20px rgba(0, 153, 255, 0.4);
      }
      100% {
        box-shadow: 0 6px 25px rgba(0, 153, 255, 0.6), 0 0 30px rgba(0, 212, 255, 0.3);
      }
    }

    /* Efeito de partículas no hover */
    nav a:hover {
      animation: sparkle 0.6s ease-in-out;
    }

    @keyframes sparkle {
      0%, 100% { filter: brightness(1); }
      50% { filter: brightness(1.2) saturate(1.3); }
    }

    /* Menu Mobile */
    .mobile-menu-btn {
      display: none;
      background: none;
      border: none;
      color: white;
      font-size: 1.5rem;
      cursor: pointer;
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

      nav a:hover {
        transform: translateY(-2px) scale(1.03);
      }

      .mobile-menu-btn {
        display: none;
      }
    }

    @media (max-width: 480px) {
      nav {
        gap: 0.3rem;
      }

      nav a {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
        border-radius: 18px;
      }

      nav a i {
        font-size: 0.9rem;
      }

      .header-container {
        padding: 0.8rem;
      }
    }

    /* Hero Section Melhorada */
    .hero {
      background: linear-gradient(135deg, #0056b3, #007bff, #0099ff);
      background-size: 400% 400%;
      animation: gradientShift 8s ease infinite;
      color: white;
      padding: 80px 20px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
      opacity: 0.3;
    }

    .hero-content {
      position: relative;
      z-index: 1;
      max-width: 800px;
      margin: 0 auto;
    }

    .hero h1 {
      font-size: 3rem;
      margin-bottom: 20px;
      font-weight: 700;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .hero p {
      font-size: 1.3rem;
      opacity: 0.95;
      font-weight: 300;
      margin-bottom: 30px;
    }

    .hero-features {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
      margin-top: 30px;
    }

    .hero-feature {
      display: flex;
      align-items: center;
      gap: 10px;
      background: rgba(255, 255, 255, 0.1);
      padding: 12px 20px;
      border-radius: 25px;
      font-weight: 500;
    }

    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.2rem;
      }

      .hero p {
        font-size: 1.1rem;
      }

      .hero-features {
        flex-direction: column;
        align-items: center;
        gap: 15px;
      }
    }

    /* Main Content */
    .main-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 80px 20px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: start;
    }

    @media (max-width: 968px) {
      .main-content {
        grid-template-columns: 1fr;
        gap: 40px;
        padding: 60px 20px;
      }
    }

    /* Seção de Benefícios */
    .benefits-section {
      background: #f8f9fa;
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    .benefits-section h2 {
      font-size: 2rem;
      color: #333;
      margin-bottom: 30px;
      font-weight: 700;
      text-align: center;
    }

    .benefit-item {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 25px;
      padding: 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease;
    }

    .benefit-item:hover {
      transform: translateY(-3px);
    }

    .benefit-icon {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, #0056b3, #007bff);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.5rem;
      flex-shrink: 0;
    }

    .benefit-content h3 {
      color: #333;
      font-weight: 600;
      margin-bottom: 8px;
    }

    .benefit-content p {
      color: #666;
      font-size: 0.95rem;
      line-height: 1.5;
    }

    /* Formulário Profissional */
    .form-section {
      background: white;
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    .form-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .form-header h2 {
      font-size: 2rem;
      color: #333;
      margin-bottom: 15px;
      font-weight: 700;
    }

    .form-header p {
      color: #666;
      font-size: 1.1rem;
    }

    .form-group {
      margin-bottom: 25px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
      font-size: 0.95rem;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
      width: 100%;
      padding: 15px 20px;
      border: 2px solid #e9ecef;
      border-radius: 10px;
      font-size: 1rem;
      font-family: 'Inter', sans-serif;
      transition: all 0.3s ease;
      background: #fff;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
      outline: none;
      border-color: #0056b3;
      box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
    }

    .form-group textarea {
      resize: vertical;
      min-height: 100px;
    }

    .form-group input[type="file"] {
      padding: 12px 15px;
      background: #f8f9fa;
      border: 2px dashed #dee2e6;
      cursor: pointer;
    }

    .form-group input[type="file"]:hover {
      border-color: #0056b3;
      background: #f0f8ff;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    @media (max-width: 768px) {
      .form-row {
        grid-template-columns: 1fr;
      }
    }

    .submit-btn {
      width: 100%;
      background: linear-gradient(135deg, #0056b3, #007bff);
      color: white;
      padding: 18px 30px;
      border: none;
      border-radius: 10px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 20px;
      box-shadow: 0 4px 15px rgba(0, 86, 179, 0.3);
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 86, 179, 0.4);
    }

    .submit-btn:active {
      transform: translateY(0);
    }

    .submit-btn i {
      margin-right: 10px;
    }

    /* Progress Steps */
    .progress-steps {
      display: flex;
      justify-content: center;
      margin-bottom: 40px;
      gap: 20px;
    }

    .step {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 20px;
      background: #f8f9fa;
      border-radius: 25px;
      font-weight: 500;
      color: #666;
    }

    .step.active {
      background: linear-gradient(135deg, #0056b3, #007bff);
      color: white;
    }

    .step-number {
      width: 25px;
      height: 25px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.9rem;
      font-weight: 600;
    }

    /* Footer Profissional */
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

    /* LinkedIn - Azul oficial */
    .social-link.linkedin {
      background: linear-gradient(135deg, #0077b5, #005582);
      box-shadow: 0 4px 15px rgba(0, 119, 181, 0.3);
    }

    .social-link.linkedin:hover {
      background: linear-gradient(135deg, #005582, #003d5c);
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0, 119, 181, 0.4);
    }

    /* Facebook - Azul oficial */
    .social-link.facebook {
      background: linear-gradient(135deg, #1877f2, #0d5dcc);
      box-shadow: 0 4px 15px rgba(24, 119, 242, 0.3);
    }

    .social-link.facebook:hover {
      background: linear-gradient(135deg, #0d5dcc, #0a4da3);
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(24, 119, 242, 0.4);
    }

    /* Instagram - Gradiente oficial */
    .social-link.instagram {
      background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045);
      box-shadow: 0 4px 15px rgba(131, 58, 180, 0.3);
    }

    .social-link.instagram:hover {
      background: linear-gradient(135deg, #6a2c93, #e11d48, #f59e0b);
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(131, 58, 180, 0.4);
    }

    /* WhatsApp - Verde oficial */
    .social-link.whatsapp {
      background: linear-gradient(135deg, #25d366, #1ebe57);
      box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
    }

    .social-link.whatsapp:hover {
      background: linear-gradient(135deg, #1ebe57, #189c47);
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4);
    }

    /* YouTube - Vermelho oficial */
    .social-link.youtube {
      background: linear-gradient(135deg, #ff0000, #cc0000);
      box-shadow: 0 4px 15px rgba(255, 0, 0, 0.3);
    }

    .social-link.youtube:hover {
      background: linear-gradient(135deg, #cc0000, #990000);
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(255, 0, 0, 0.4);
    }

    /* Efeito de brilho no hover */
    .social-link::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      transition: left 0.5s ease;
    }

    .social-link:hover::before {
      left: 100%;
    }

    .footer-bottom {
      border-top: 1px solid #444;
      padding-top: 30px;
      text-align: center;
      color: #999;
    }

    /* Animações */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .fade-in-up {
      animation: fadeInUp 0.6s ease-out;
    }

    /* Success Message */
    .success-message {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      margin-bottom: 30px;
      display: none;
    }

    .success-message.show {
      display: block;
      animation: fadeInUp 0.5s ease-out;
    }

    /* Loading Spinner */
    .loading-spinner {
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 1s ease-in-out infinite;
      display: none;
      margin-right: 10px;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Scroll suave */
    html {
      scroll-behavior: smooth;
    }
  </style>
</head>
<body>
  <header>
    <div class="header-container">
      <div class="logo-section">
        <img src="./imagens/Logoindex.jpg" alt="Logo da empresa" class="logo-header">
      </div>
      <nav id="nav">
        <a href="index.php"><i class="fas fa-home"></i> Início</a>
        <a href="vagas.php"><i class="fas fa-briefcase"></i> Vagas</a>
        <a href="cadastro.php" class="active"><i class="fas fa-user-plus"></i> Cadastrar</a>
        <a href="fale_conosco.php"><i class="fas fa-comments"></i> Fale Conosco</a>
        <a href="login_admin.php"><i class="fas fa-sign-in-alt"></i> Login</a>
      </nav>
    </div>
  </header>

  <section class="hero">
    <div class="hero-content">
      <h1><i class="fas fa-user-plus"></i> Cadastro de Candidato</h1>
      <p>Dê o primeiro passo para sua nova carreira. Nosso processo é rápido, seguro e totalmente online.</p>
      
      <div class="hero-features">
        <div class="hero-feature">
          <i class="fas fa-clock"></i>
          <span>Processo Rápido</span>
        </div>
        <div class="hero-feature">
          <i class="fas fa-shield-alt"></i>
          <span>100% Seguro</span>
        </div>
        <div class="hero-feature">
          <i class="fas fa-mobile-alt"></i>
          <span>Totalmente Online</span>
        </div>
      </div>
    </div>
  </section>

  <div class="progress-steps">
    <div class="step active">
      <div class="step-number">1</div>
      <span>Dados Pessoais</span>
    </div>
    <div class="step">
      <div class="step-number">2</div>
      <span>Confirmação</span>
    </div>
    <div class="step">
      <div class="step-number">3</div>
      <span>Finalizado</span>
    </div>
  </div>

  <div class="main-content">
    <!-- Seção de Benefícios -->
    <div class="benefits-section fade-in-up">
      <h2><i class="fas fa-star"></i> Por que escolher LWFA?</h2>
      
      <div class="benefit-item">
        <div class="benefit-icon">
          <i class="fas fa-rocket"></i>
        </div>
        <div class="benefit-content">
          <h3>Processo Acelerado</h3>
          <p>Respostas em até 24 horas e contato direto com empresas parceiras.</p>
        </div>
      </div>

      <div class="benefit-item">
        <div class="benefit-icon">
          <i class="fas fa-building"></i>
        </div>
        <div class="benefit-content">
          <h3>350+ Empresas Parceiras</h3>
          <p>Acesso exclusivo a vagas em grandes empresas do mercado.</p>
        </div>
      </div>

      <div class="benefit-item">
        <div class="benefit-icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="benefit-content">
          <h3>Suporte Personalizado</h3>
          <p>Acompanhamento completo durante todo o processo seletivo.</p>
        </div>
      </div>

      <div class="benefit-item">
        <div class="benefit-icon">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="benefit-content">
          <h3>95% Taxa de Sucesso</h3>
          <p>Histórico comprovado de aprovações em processos seletivos.</p>
        </div>
      </div>

      <div class="benefit-item">
        <div class="benefit-icon">
          <i class="fas fa-video"></i>
        </div>
        <div class="benefit-content">
          <h3>Entrevistas Online</h3>
          <p>Flexibilidade total com entrevistas por vídeo conferência.</p>
        </div>
      </div>
    </div>

    <!-- Formulário -->
    <div class="form-section fade-in-up">
      <div class="success-message" id="successMessage"<?php echo $success ? ' style="display: block;"' : ''; ?>>
        <i class="fas fa-check-circle"></i>
        <h3>Cadastro realizado com sucesso!</h3>
        <p>Em breve entraremos em contato com você.</p>
      </div>

      <?php if ($error): ?>
      <div class="error-message" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 20px; border-radius: 10px; text-align: center; margin-bottom: 30px;">
        <i class="fas fa-exclamation-circle"></i>
        <h3>Erro no cadastro</h3>
        <p><?php echo htmlspecialchars($error); ?></p>
      </div>
      <?php endif; ?>

      <div class="form-header">
        <h2><i class="fas fa-edit"></i> Complete seu Cadastro</h2>
        <p>Preencha todos os campos para participar do processo seletivo</p>
      </div>

      <form id="formCadastro" action="processar_cadastro.php" method="POST" enctype="multipart/form-data">
        <?php if ($vaga_id): ?>
        <input type="hidden" name="vaga_id" value="<?php echo htmlspecialchars($vaga_id); ?>">
        <?php endif; ?>
        
        <?php if ($vaga_info): ?>
        <div style="background: linear-gradient(135deg, #e3f2fd, #bbdefb); padding: 20px; border-radius: 10px; margin-bottom: 30px; border-left: 4px solid #0056b3;">
          <h3 style="color: #0056b3; margin-bottom: 10px;">
            <i class="fas fa-briefcase"></i> Candidatura para:
          </h3>
          <p style="font-size: 1.1rem; font-weight: 500; color: #333; margin: 0;">
            <?php echo htmlspecialchars($vaga_info['titulo']); ?> - <?php echo htmlspecialchars($vaga_info['empresa']); ?>
          </p>
        </div>
        <?php endif; ?>
        <div class="form-row">
          <div class="form-group">
            <label for="nome">
              <i class="fas fa-user"></i> Nome Completo *
            </label>
            <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" required />
          </div>
          
          <div class="form-group">
            <label for="email">
              <i class="fas fa-envelope"></i> E-mail *
            </label>
            <input type="email" id="email" name="email" placeholder="seu@email.com" required />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="telefone">
              <i class="fas fa-phone"></i> Telefone (WhatsApp) *
            </label>
            <input type="tel" id="telefone" name="telefone" placeholder="(11) 99999-9999" required />
          </div>
          
          <div class="form-group">
            <label for="idade">
              <i class="fas fa-calendar"></i> Idade *
            </label>
            <input type="number" id="idade" name="idade" placeholder="Sua idade" min="16" max="80" required />
          </div>
        </div>

        <div class="form-group">
          <label for="endereco">
            <i class="fas fa-map-marker-alt"></i> Endereço Completo *
          </label>
          <input type="text" id="endereco" name="endereco" placeholder="Rua, número, bairro, cidade - UF" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="escolaridade">
              <i class="fas fa-graduation-cap"></i> Escolaridade *
            </label>
            <select id="escolaridade" name="escolaridade" required>
              <option value="">Selecione sua escolaridade</option>
              <option value="ensino-fundamental">Ensino Fundamental</option>
              <option value="ensino-medio">Ensino Médio</option>
              <option value="ensino-tecnico">Ensino Técnico</option>
              <option value="ensino-superior">Ensino Superior</option>
              <option value="pos-graduacao">Pós-graduação</option>
              <option value="mestrado">Mestrado</option>
              <option value="doutorado">Doutorado</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="experiencia">
              <i class="fas fa-briefcase"></i> Experiência Profissional *
            </label>
            <select id="experiencia" name="experiencia" required>
              <option value="">Selecione sua experiência</option>
              <option value="sem-experiencia">Primeiro Emprego</option>
              <option value="1-2-anos">1 a 2 anos</option>
              <option value="3-5-anos">3 a 5 anos</option>
              <option value="6-10-anos">6 a 10 anos</option>
              <option value="mais-10-anos">Mais de 10 anos</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="vaga">
            <i class="fas fa-search"></i> Vaga de Interesse *
          </label>
          <input type="text" id="vaga" name="vaga" 
                 placeholder="Ex: Desenvolvedor, Analista, Vendedor..." 
                 value="<?php echo $vaga_info ? htmlspecialchars($vaga_info['titulo']) : ''; ?>"
                 <?php echo $vaga_info ? 'readonly style="background-color: #f8f9fa;"' : ''; ?> 
                 required />
        </div>

        <div class="form-group">
          <label for="salario">
            <i class="fas fa-dollar-sign"></i> Pretensão Salarial
          </label>
          <select id="salario" name="salario">
            <option value="">Selecione a faixa salarial</option>
            <option value="ate-1500">Até R$ 1.500</option>
            <option value="1500-3000">R$ 1.500 - R$ 3.000</option>
            <option value="3000-5000">R$ 3.000 - R$ 5.000</option>
            <option value="5000-8000">R$ 5.000 - R$ 8.000</option>
            <option value="8000-12000">R$ 8.000 - R$ 12.000</option>
            <option value="acima-12000">Acima de R$ 12.000</option>
            <option value="a-combinar">A combinar</option>
          </select>
        </div>

        <div class="form-group">
          <label for="disponibilidade">
            <i class="fas fa-clock"></i> Disponibilidade para Entrevista *
          </label>
          <textarea id="disponibilidade" name="disponibilidade" placeholder="Informe os melhores dias e horários para contato e entrevistas..." required></textarea>
        </div>

        <div class="form-group">
          <label for="observacoes">
            <i class="fas fa-comment"></i> Observações Adicionais
          </label>
          <textarea id="observacoes" name="observacoes" placeholder="Conte um pouco sobre você, suas habilidades e objetivos profissionais..."></textarea>
        </div>

        <div class="form-group">
          <label for="curriculo">
            <i class="fas fa-file-pdf"></i> Currículo (PDF) *
          </label>
          <input type="file" id="curriculo" name="curriculo" accept="application/pdf" required />
          <small style="color: #666; font-size: 0.9rem; margin-top: 5px; display: block;">
            Arquivo deve ter no máximo 5MB e estar em formato PDF
          </small>
        </div>

        <button type="submit" class="submit-btn">
          <div class="loading-spinner" id="loadingSpinner"></div>
          <i class="fas fa-paper-plane" id="submitIcon"></i>
          <span id="submitText">Enviar Cadastro</span>
        </button>
      </form>
    </div>
  </div>

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
          <li><a href="login_admin.php">RH</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h3>Suporte</h3>
        <ul>
          <li><a href="central_ajuda.php">Central de Ajuda</a></li>
          <li><a href="termos_uso.php">Termos de Uso</a></li>
          <li><a href="politica_privacidade.php">Política de Privacidade</a></li>
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

  <script>
    // Máscara para telefone
    document.getElementById('telefone').addEventListener('input', function (e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length >= 11) {
        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
      } else if (value.length >= 7) {
        value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
      } else if (value.length >= 3) {
        value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
      }
      e.target.value = value;
    });

    // Validação e envio do formulário
    document.getElementById('formCadastro').addEventListener('submit', function (e) {
      // Validação básica
      const nome = document.getElementById('nome').value.trim();
      const email = document.getElementById('email').value.trim();
      const telefone = document.getElementById('telefone').value.trim();
      const curriculo = document.getElementById('curriculo').files[0];
      
      if (!nome || !email || !telefone) {
        e.preventDefault();
        alert('Por favor, preencha todos os campos obrigatórios.');
        return;
      }
      
      // Validar email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('Por favor, insira um email válido.');
        return;
      }
      
      // Validar arquivo PDF (se enviado)
      if (curriculo && curriculo.type !== 'application/pdf') {
        e.preventDefault();
        alert('Por favor, envie apenas arquivos PDF para o currículo.');
        return;
      }
      
      // Validar tamanho do arquivo (5MB)
      if (curriculo && curriculo.size > 5 * 1024 * 1024) {
        e.preventDefault();
        alert('O arquivo do currículo deve ter no máximo 5MB.');
        return;
      }
      
      // Mostrar loading
      const submitBtn = document.querySelector('.submit-btn');
      const spinner = document.getElementById('loadingSpinner');
      const icon = document.getElementById('submitIcon');
      const text = document.getElementById('submitText');
      
      submitBtn.disabled = true;
      spinner.style.display = 'inline-block';
      icon.style.display = 'none';
      text.textContent = 'Enviando...';
      
      // O formulário será enviado normalmente para processar_cadastro.php
    });

    // Animação de entrada
    window.addEventListener('load', function() {
      const elements = document.querySelectorAll('.fade-in-up');
      elements.forEach((el, index) => {
        setTimeout(() => {
          el.style.opacity = '1';
          el.style.transform = 'translateY(0)';
        }, index * 200);
      });
    });

    // Preview do arquivo selecionado
    document.getElementById('curriculo').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const fileInfo = document.createElement('small');
        fileInfo.style.color = '#28a745';
        fileInfo.style.display = 'block';
        fileInfo.style.marginTop = '5px';
        fileInfo.innerHTML = `<i class="fas fa-check"></i> Arquivo selecionado: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
        
        // Remover info anterior se existir
        const existingInfo = e.target.parentNode.querySelector('.file-info');
        if (existingInfo) {
          existingInfo.remove();
        }
        
        fileInfo.classList.add('file-info');
        e.target.parentNode.appendChild(fileInfo);
      }
    });
  </script>

</body>
</html>
