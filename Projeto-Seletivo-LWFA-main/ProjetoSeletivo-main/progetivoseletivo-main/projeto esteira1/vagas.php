<?php
require_once 'config.php';

// Buscar vagas ativas
try {
    $database = new Database();
    $pdo = $database->connect();
    
    $sql = "SELECT * FROM vagas WHERE status = 'ativa' ORDER BY data_criacao DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $vagas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $vagas = [];
    error_log("Erro ao buscar vagas: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Vagas Abertas  - Processo Seletivo Virtual</title>
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

    /* Header Profissional - Mesmo padrão */
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

    nav {
      display: flex;
      flex-direction: row;
      gap: 0.5rem;
      position: relative;
      flex-wrap: nowrap;
      align-items: center;
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

    /* Vagas Grid */
    .vagas-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1rem;
      margin-top: 2rem;
      max-width: 1200px;
      margin-left: auto;
      margin-right: auto;
      padding: 0 1rem;
    }

    .vaga-card {
      background: white;
      border-radius: 6px;
      padding: 1rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      border: 1px solid #e9ecef;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .vaga-card:hover {
      border-color: #007cba;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .vaga-card.expanded {
      grid-column: 1 / -1;
      max-width: none;
      transform: none;
      box-shadow: 0 10px 30px rgba(0, 124, 186, 0.2);
    }

    .vaga-card-content {
      padding: 0;
    }

    .vaga-header {
      margin-bottom: 0.75rem;
      padding-bottom: 0.75rem;
      border-bottom: 1px solid #f0f0f0;
    }

    .vaga-header-left {
      width: 100%;
    }

    .vaga-header-right {
      display: none;
    }

    .vaga-title {
      color: #1a1a1a;
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 0.25rem;
      line-height: 1.3;
    }

    .vaga-empresa {
      color: #666;
      font-size: 0.9rem;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .vaga-empresa::before {
      content: '\f1ad';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      color: #007cba;
      font-size: 0.8rem;
    }

    .vaga-quick-info {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      margin: 0.75rem 0;
    }

    .vaga-info-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: #555;
      font-size: 0.85rem;
      padding: 0;
    }

    .vaga-info-item i {
      width: 16px;
      color: #007cba;
      font-size: 0.85rem;
      text-align: center;
    }

    .vaga-preview-description {
      color: #666;
      font-size: 0.85rem;
      line-height: 1.4;
      margin: 0.75rem 0;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .vaga-expanded-content {
      display: none;
      margin-top: 2rem;
      padding-top: 2rem;
      border-top: 2px solid rgba(30, 60, 114, 0.1);
    }

    .vaga-card.expanded .vaga-preview-description {
      display: none;
    }

    .vaga-card.expanded .vaga-expanded-content {
      display: block;
    }

    .vaga-full-description {
      color: #555;
      font-size: 1rem;
      line-height: 1.7;
      margin-bottom: 2rem;
    }

    .vaga-requirements, .vaga-benefits {
      margin: 2rem 0;
    }

    .vaga-requirements h4, .vaga-benefits h4 {
      color: #1e3c72;
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .vaga-details-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin: 2rem 0;
      padding: 1.5rem;
      background: rgba(30, 60, 114, 0.03);
      border-radius: 15px;
      border: 1px solid rgba(30, 60, 114, 0.1);
    }

    .vaga-detail-section {
      text-align: center;
    }

    .vaga-detail-section h4 {
      color: #1e3c72;
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .vaga-detail-section p {
      color: #555;
      font-weight: 500;
      font-size: 0.95rem;
    }

    .vaga-footer {
      margin-top: 0.75rem;
      padding-top: 0.75rem;
      border-top: 1px solid #f0f0f0;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .vaga-meta {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      gap: 0.75rem;
    }

    .vaga-data {
      color: #999;
      font-size: 0.8rem;
      display: flex;
      align-items: center;
      gap: 0.25rem;
    }

    .vaga-actions {
      display: flex;
      gap: 0.5rem;
      width: 100%;
      justify-content: space-between;
    }

    .btn-expand {
      background: #f8f9fa;
      color: #007cba;
      border: 1px solid #007cba;
      padding: 0.5rem 0.75rem;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.25rem;
      flex: 1;
      text-align: center;
      min-height: 36px;
    }

    .btn-expand:hover {
      background: #007cba;
      color: white;
    }

    .btn-candidatar {
      background: #007cba;
      color: white;
      padding: 0.5rem 0.75rem;
      border: none;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.25rem;
      flex: 1;
      text-align: center;
      min-height: 36px;
    }

    .btn-candidatar:hover {
      background: #005a87;
      color: white;
      text-decoration: none;
    }

    .no-vagas {
      text-align: center;
      padding: 4rem 2rem;
      color: #666;
    }

    .no-vagas i {
      font-size: 4rem;
      color: #ddd;
      margin-bottom: 1rem;
    }

    .no-vagas h3 {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
      color: #555;
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

    /* Responsive */
    @media (max-width: 768px) {
      .header-container {
        flex-direction: column;
        gap: 1.5rem;
        padding: 1rem;
      }

      nav {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
      }

      nav a {
        padding: 0.6rem 1.2rem;
        font-size: 0.85rem;
        border-radius: 20px;
        flex: 0 0 auto;
        white-space: nowrap;
      }

      nav a:hover {
        transform: translateY(-2px) scale(1.03);
      }

      .page-header h1 {
        font-size: 2rem;
      }

      .vagas-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
      }

      .vaga-actions {
        flex-direction: row;
        gap: 0.5rem;
      }

      .btn-expand, .btn-candidatar {
        flex: 1;
        padding: 0.6rem;
        font-size: 0.8rem;
        min-height: 40px;
      }
    }

    @media (max-width: 480px) {
      .main-container {
        padding: 1rem;
      }

      nav {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0.3rem;
        justify-content: center;
      }

      nav a {
        padding: 0.5rem 0.8rem;
        font-size: 0.75rem;
        border-radius: 18px;
        flex: 0 0 auto;
        white-space: nowrap;
      }

      nav a i {
        font-size: 0.8rem;
      }

      .header-container {
        padding: 0.8rem;
      }

      .vagas-grid {
        grid-template-columns: 1fr;
        gap: 0.8rem;
        padding: 0 0.5rem;
      }

      .vaga-card {
        padding: 0.75rem;
      }

      .vaga-title {
        font-size: 0.95rem;
      }

      .vaga-empresa {
        font-size: 0.8rem;
      }

      .page-header h1 {
        font-size: 1.8rem;
      }

      .vaga-quick-info {
        gap: 0.5rem;
      }

      .vaga-info-item {
        font-size: 0.75rem;
      }

      .vaga-actions {
        flex-direction: column;
        gap: 0.5rem;
      }

      .btn-expand, .btn-candidatar {
        width: 100%;
        padding: 0.6rem;
        font-size: 0.8rem;
        min-height: 44px;
      }

      .vaga-footer {
        gap: 0.5rem;
      }
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

    /* Twitter/X - Preto moderno */
    .social-link.twitter {
      background: linear-gradient(135deg, #000000, #1a1a1a);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    .social-link.twitter:hover {
      background: linear-gradient(135deg, #1a1a1a, #333333);
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
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
  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <div class="header-container">
      <div class="logo-section">
        <img src="./imagens/Logoindex.jpg" alt="Logo da Empresa" class="logo-header">
      </div>
      <nav>
        <a href="index.php"><i class="fas fa-home"></i> Início</a>
        <a href="vagas.php" class="active"><i class="fas fa-briefcase"></i> Vagas</a>
        <a href="cadastro.php"><i class="fas fa-user-plus"></i> Cadastrar</a>
        <a href="fale_conosco.php"><i class="fas fa-comments"></i> Fale Conosco</a>
        <a href="login_admin.php"><i class="fas fa-sign-in-alt"></i> Login</a>
      </nav>
    </div>
  </header>

  <!-- Main Content -->
  <div class="main-container">
    <div class="page-header">
      <h1><i class="fas fa-briefcase"></i> Vagas Disponíveis</h1>
      <p>Encontre a oportunidade perfeita para impulsionar sua carreira profissional</p>
    </div>

    <?php if (!empty($vagas)): ?>
      <div class="vagas-grid">
        <?php foreach ($vagas as $vaga): ?>
          <div class="vaga-card">
            <div class="vaga-card-content">
              <div class="vaga-header">
                <div class="vaga-header-left">
                  <h3 class="vaga-title"><?php echo htmlspecialchars($vaga['titulo']); ?></h3>
                  <p class="vaga-empresa"><?php echo htmlspecialchars($vaga['empresa']); ?></p>
                </div>
                <div class="vaga-header-right">
                  <div class="vaga-status-badge">Ativa</div>
                </div>
              </div>

              <div class="vaga-quick-info">
                <div class="vaga-info-item">
                  <i class="fas fa-map-marker-alt"></i>
                  <span><?php echo htmlspecialchars($vaga['localizacao']); ?></span>
                </div>
                <?php if (!empty($vaga['salario_min']) || !empty($vaga['salario_max'])): ?>
                <div class="vaga-info-item">
                  <i class="fas fa-money-bill-wave"></i>
                  <span>
                    <?php 
                    if (!empty($vaga['salario_min']) && !empty($vaga['salario_max'])) {
                        echo 'R$ ' . number_format($vaga['salario_min'], 0, ',', '.') . ' - R$ ' . number_format($vaga['salario_max'], 0, ',', '.');
                    } elseif (!empty($vaga['salario_min'])) {
                        echo 'A partir de R$ ' . number_format($vaga['salario_min'], 0, ',', '.');
                    } elseif (!empty($vaga['salario_max'])) {
                        echo 'Até R$ ' . number_format($vaga['salario_max'], 0, ',', '.');
                    }
                    ?>
                  </span>
                </div>
                <?php endif; ?>
                <?php if (!empty($vaga['tipo_contrato'])): ?>
                <div class="vaga-info-item">
                  <i class="fas fa-briefcase"></i>
                  <span><?php echo ucfirst(htmlspecialchars($vaga['tipo_contrato'])); ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($vaga['modalidade'])): ?>
                <div class="vaga-info-item">
                  <i class="fas fa-laptop-house"></i>
                  <span><?php echo ucfirst(htmlspecialchars($vaga['modalidade'])); ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($vaga['nivel'])): ?>
                <div class="vaga-info-item">
                  <i class="fas fa-user-graduate"></i>
                  <span><?php echo ucfirst(htmlspecialchars($vaga['nivel'])); ?></span>
                </div>
                <?php endif; ?>
              </div>

              <div class="vaga-preview-description">
                <?php echo nl2br(htmlspecialchars($vaga['descricao'])); ?>
              </div>

              <div class="vaga-expanded-content">
                <div class="vaga-full-description">
                  <?php echo nl2br(htmlspecialchars($vaga['descricao'])); ?>
                </div>
                
                <div class="vaga-details-grid">
                  <?php if (!empty($vaga['salario_min']) || !empty($vaga['salario_max'])): ?>
                  <div class="vaga-detail-section">
                    <h4><i class="fas fa-money-bill-wave"></i> Faixa Salarial</h4>
                    <p>
                      <?php 
                      if (!empty($vaga['salario_min']) && !empty($vaga['salario_max'])) {
                          echo 'R$ ' . number_format($vaga['salario_min'], 0, ',', '.') . ' - R$ ' . number_format($vaga['salario_max'], 0, ',', '.');
                      } elseif (!empty($vaga['salario_min'])) {
                          echo 'A partir de R$ ' . number_format($vaga['salario_min'], 0, ',', '.');
                      } elseif (!empty($vaga['salario_max'])) {
                          echo 'Até R$ ' . number_format($vaga['salario_max'], 0, ',', '.');
                      }
                      ?>
                    </p>
                  </div>
                  <?php endif; ?>
                  
                  <?php if (!empty($vaga['area'])): ?>
                  <div class="vaga-detail-section">
                    <h4><i class="fas fa-tags"></i> Área</h4>
                    <p><?php echo htmlspecialchars($vaga['area']); ?></p>
                  </div>
                  <?php endif; ?>
                  
                  <?php if (!empty($vaga['vagas_disponiveis'])): ?>
                  <div class="vaga-detail-section">
                    <h4><i class="fas fa-users"></i> Vagas Disponíveis</h4>
                    <p><?php echo $vaga['vagas_disponiveis']; ?> vaga(s)</p>
                  </div>
                  <?php endif; ?>
                  
                  <?php if (!empty($vaga['data_encerramento'])): ?>
                  <div class="vaga-detail-section">
                    <h4><i class="fas fa-clock"></i> Prazo</h4>
                    <p>Até <?php echo date('d/m/Y', strtotime($vaga['data_encerramento'])); ?></p>
                  </div>
                  <?php endif; ?>
                </div>
                
                <?php if (!empty($vaga['requisitos'])): ?>
                <div class="vaga-requirements">
                  <h4><i class="fas fa-check-circle"></i> Requisitos</h4>
                  <div><?php echo nl2br(htmlspecialchars($vaga['requisitos'])); ?></div>
                </div>
                <?php endif; ?>

                <?php if (!empty($vaga['beneficios'])): ?>
                <div class="vaga-benefits">
                  <h4><i class="fas fa-star"></i> Benefícios</h4>
                  <div><?php echo nl2br(htmlspecialchars($vaga['beneficios'])); ?></div>
                </div>
                <?php endif; ?>
              </div>

              <div class="vaga-footer">
                <div class="vaga-meta">
                  <span class="vaga-data">
                    <i class="fas fa-calendar-alt"></i>
                    <?php echo date('d/m/Y', strtotime($vaga['data_publicacao'])); ?>
                  </span>
                </div>
                <div class="vaga-actions">
                  <button class="btn-expand" onclick="event.stopPropagation(); toggleVagaExpansion(this.closest('.vaga-card'))">
                    <i class="fas fa-expand-alt"></i>
                    <span class="expand-text">Ver mais</span>
                  </button>
                  <a href="candidatar_vaga.php?id=<?php echo $vaga['id']; ?>" class="btn-candidatar" onclick="event.stopPropagation()">
                    <i class="fas fa-paper-plane"></i>
                    Candidatar-se
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="no-vagas">
        <i class="fas fa-inbox"></i>
        <h3>Nenhuma vaga disponível</h3>
        <p>No momento não há vagas abertas. Volte em breve para conferir novas oportunidades!</p>
      </div>
    <?php endif; ?>
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
    function toggleVagaExpansion(card) {
      const isExpanded = card.classList.contains('expanded');
      
      // Fechar outros cards expandidos
      document.querySelectorAll('.vaga-card.expanded').forEach(expandedCard => {
        if (expandedCard !== card) {
          expandedCard.classList.remove('expanded');
          const expandBtn = expandedCard.querySelector('.btn-expand');
          const expandIcon = expandBtn.querySelector('i');
          const expandText = expandBtn.querySelector('.expand-text');
          expandIcon.className = 'fas fa-expand-alt';
          expandText.textContent = 'Ver mais';
        }
      });
      
      // Toggle do card atual
      if (isExpanded) {
        card.classList.remove('expanded');
        const expandBtn = card.querySelector('.btn-expand');
        const expandIcon = expandBtn.querySelector('i');
        const expandText = expandBtn.querySelector('.expand-text');
        expandIcon.className = 'fas fa-expand-alt';
        expandText.textContent = 'Ver mais';
      } else {
        card.classList.add('expanded');
        const expandBtn = card.querySelector('.btn-expand');
        const expandIcon = expandBtn.querySelector('i');
        const expandText = expandBtn.querySelector('.expand-text');
        expandIcon.className = 'fas fa-compress-alt';
        expandText.textContent = 'Ver menos';
        
        // Smooth scroll para o card expandido
        setTimeout(() => {
          card.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
          });
        }, 200);
      }
    }

    // Adicionar animação suave nos hovers
    document.addEventListener('DOMContentLoaded', function() {
      const cards = document.querySelectorAll('.vaga-card');

      // Animação de entrada dos cards
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
          if (entry.isIntersecting) {
            setTimeout(() => {
              entry.target.style.opacity = '1';
              entry.target.style.transform = 'translateY(0)';
            }, index * 100);
          }
        });
      });

      cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
      });
    });
  </script>
</body>
</html>
