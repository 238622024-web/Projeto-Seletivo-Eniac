<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ENIAC LINK+ | Processo Seletivo Virtual</title>
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

    /* Header Profissional */
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
      gap: 0.5rem;
      position: relative;
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

    @media (max-width: 768px) {
      .header-container {
        flex-direction: column;
        gap: 1rem;
        padding: 1rem;
      }

      .logo-header {
        width: 60px;
        height: 60px;
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
      .logo-header {
        width: 50px;
        height: 50px;
      }

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
      display: flex;
      align-items: center;
      gap: 40px;
      min-height: 70vh;
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

    .hero-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      gap: 40px;
      position: relative;
      z-index: 1;
    }

    .hero img {
      flex-shrink: 0;
    }

    .hero-text {
      flex-grow: 1;
    }

    .hero h1 {
      font-size: 3.5rem;
      margin-bottom: 24px;
      font-weight: 700;
      line-height: 1.2;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .hero p {
      font-size: 1.3rem;
      margin-bottom: 32px;
      opacity: 0.95;
      font-weight: 300;
    }

    .cta-buttons {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }

    .btn-primary {
      background: rgba(255, 255, 255, 0.95);
      color: #0056b3;
      padding: 14px 28px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-primary:hover {
      background: white;
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    .btn-secondary {
      background: transparent;
      color: white;
      border: 2px solid rgba(255, 255, 255, 0.8);
      padding: 12px 26px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: white;
    }

    .logo-hero {
      width: 200px;
      height: 200px;
      border-radius: 50%;
      object-fit: cover;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      border: 4px solid rgba(255, 255, 255, 0.2);
      transition: transform 0.3s ease;
    }

    .logo-hero:hover {
      transform: scale(1.05);
    }

    @media (max-width: 768px) {
      .hero {
        flex-direction: column;
        text-align: center;
        padding: 60px 20px;
        min-height: auto;
      }

      .hero-container {
        flex-direction: column;
        text-align: center;
      }

      .hero h1 {
        font-size: 2.5rem;
      }

      .hero p {
        font-size: 1.1rem;
      }

      .logo-hero {
        width: 150px;
        height: 150px;
      }

      .cta-buttons {
        justify-content: center;
      }
    }

    /* Seção de Estatísticas */
    .stats-section {
      background: #f8f9fa;
      padding: 60px 20px;
    }

    .stats-container {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 40px;
      text-align: center;
    }

    .stat-item {
      background: white;
      padding: 40px 20px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    .stat-item:hover {
      transform: translateY(-5px);
    }

    .stat-number {
      font-size: 3rem;
      font-weight: 700;
      color: #0056b3;
      display: block;
      margin-bottom: 12px;
    }

    .stat-label {
      font-size: 1.1rem;
      color: #666;
      font-weight: 500;
    }

    /* Seção de Serviços Melhorada */
    .services-section {
      padding: 80px 20px;
      background: white;
    }

    .services-header {
      text-align: center;
      max-width: 800px;
      margin: 0 auto 60px;
    }

    .services-header h2 {
      font-size: 2.8rem;
      color: #333;
      margin-bottom: 20px;
      font-weight: 700;
    }

    .services-header p {
      font-size: 1.2rem;
      color: #666;
      line-height: 1.6;
    }

    .carousel-wrapper {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 30px;
      padding: 0 20px;
    }

    .card {
      border-radius: 16px;
      color: white;
      position: relative;
      height: 400px;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 30px;
      background-size: cover;
      background-position: center;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
      text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.8);
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, rgba(0, 86, 179, 0.8), rgba(0, 123, 255, 0.6));
      transition: opacity 0.3s ease;
    }

    .card:hover::before {
      opacity: 0.9;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
    }

    .card-content {
      position: relative;
      z-index: 2;
    }

    .card-icon {
      font-size: 3rem;
      margin-bottom: 16px;
      display: block;
    }

    .card h3 {
      font-size: 1.8rem;
      margin-bottom: 12px;
      font-weight: 600;
    }

    .card p {
      font-size: 1rem;
      margin-bottom: 20px;
      line-height: 1.5;
      opacity: 0.95;
    }

    .card a {
      background: rgba(255, 255, 255, 0.95);
      color: #0056b3;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      align-self: flex-start;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .card a:hover {
      background: white;
      transform: translateX(5px);
    }

    /* Seção de Depoimentos */
    .testimonials-section {
      background: linear-gradient(135deg, #f8f9fa, #e9ecef);
      padding: 80px 20px;
    }

    .testimonials-container {
      max-width: 1200px;
      margin: 0 auto;
      text-align: center;
    }

    .testimonials-header h2 {
      font-size: 2.8rem;
      color: #333;
      margin-bottom: 20px;
      font-weight: 700;
    }

    .testimonials-header p {
      font-size: 1.2rem;
      color: #666;
      margin-bottom: 60px;
    }

    .testimonials-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
    }

    .testimonial-card {
      background: white;
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
      position: relative;
    }

    .testimonial-card:hover {
      transform: translateY(-5px);
    }

    .testimonial-card::before {
      content: '"';
      font-size: 4rem;
      color: #0056b3;
      position: absolute;
      top: 20px;
      left: 30px;
      font-family: Georgia, serif;
      opacity: 0.3;
    }

    .testimonial-text {
      font-size: 1.1rem;
      color: #555;
      line-height: 1.6;
      margin-bottom: 30px;
      font-style: italic;
    }

    .testimonial-author {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .author-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: linear-gradient(135deg, #0056b3, #007bff);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 1.2rem;
    }

    .author-info h4 {
      color: #333;
      font-weight: 600;
      margin-bottom: 4px;
    }

    .author-info p {
      color: #666;
      font-size: 0.9rem;
    }

    /* Testimonials Section */
    .testimonials-section {
      padding: 80px 20px;
      background: linear-gradient(135deg, #f8f9fa, #e9ecef);
      margin: 60px 0;
    }

    .testimonials-container {
      max-width: 1200px;
      margin: 0 auto;
      text-align: center;
    }

    .testimonials-section h2 {
      color: #2c3e50;
      font-size: 2.5rem;
      margin-bottom: 20px;
      font-weight: 700;
    }

    .testimonials-section .subtitle {
      color: #666;
      font-size: 1.2rem;
      margin-bottom: 50px;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    .testimonials-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 30px;
      margin-bottom: 40px;
    }

    .testimonial-card {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      text-align: left;
    }

    .testimonial-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .testimonial-rating {
      margin-bottom: 15px;
    }

    .testimonial-stars {
      color: #ffc107;
      font-size: 1.2rem;
      margin-bottom: 15px;
    }

    .testimonial-text {
      color: #555;
      font-size: 1.1rem;
      line-height: 1.6;
      margin-bottom: 20px;
      font-style: italic;
    }

    .testimonial-author {
      border-top: 1px solid #eee;
      padding-top: 15px;
    }

    .testimonial-author h4 {
      color: #2c3e50;
      font-size: 1.1rem;
      margin-bottom: 5px;
      font-weight: 600;
    }

    .testimonial-author p {
      color: #666;
      font-size: 0.9rem;
      margin: 0;
    }

    /* Loading e botão de testimonials */
    .loading-testimonials {
      text-align: center;
      padding: 40px;
      color: #666;
      font-size: 1.1rem;
    }

    .loading-testimonials .fa-spinner {
      margin-right: 10px;
      color: #007bff;
    }

    .testimonials-footer {
      text-align: center;
      margin-top: 40px;
    }

    .btn-enviar-testimonial {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
      padding: 15px 30px;
      border: none;
      border-radius: 50px;
      font-size: 1.1rem;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }

    .btn-enviar-testimonial:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
      text-decoration: none;
      color: white;
    }

    .btn-enviar-testimonial i {
      margin-right: 8px;
    }

    /* Footer Profissional */
    footer {
      background: linear-gradient(135deg, #1a1a1a, #333);
      color: white;
      padding: 60px 20px 30px;
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

    /* Responsividade Geral */
    @media (max-width: 768px) {
      .services-header h2 {
        font-size: 2.2rem;
      }

      .testimonials-header h2 {
        font-size: 2.2rem;
      }

      .carousel-wrapper {
        grid-template-columns: 1fr;
        padding: 0;
      }

      .card {
        height: 350px;
      }

      .stats-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
      }

      .testimonials-grid {
        grid-template-columns: 1fr;
      }
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
        <img src="./imagens/Logoindex.jpg" alt="Logo da Empresa" class="logo-header">
      </div>
      <nav id="nav">
        <a href="index.php" class="active"><i class="fas fa-home"></i> Início</a>
        <a href="vagas.php"><i class="fas fa-briefcase"></i> Vagas</a>
        <a href="cadastro.php"><i class="fas fa-user-plus"></i> Cadastrar</a>
        <a href="fale_conosco.php"><i class="fas fa-comments"></i> Fale Conosco</a>
        <a href="login_admin.php"><i class="fas fa-sign-in-alt"></i> Login</a>
      </nav>
    </div>
  </header>

  <section class="hero">
    <div class="hero-container">
      <img src="./imagens/Logoindex.jpg" alt="Logo ENIAC LINK+" class="logo-hero">
      <div class="hero-text">
        <h1>Processo Seletivo Virtual</h1>
        <p>Conectando você às melhores oportunidades de forma rápida, moderna e online. Sua carreira dos sonhos está a um clique de distância.</p>
        <div class="cta-buttons">
          <a href="cadastro.php" class="btn-primary">
            <i class="fas fa-user-plus"></i> Começar Agora
          </a>
          <a href="vagas.php" class="btn-secondary">
            <i class="fas fa-search"></i> Ver Vagas
          </a>
        </div>
      </div>
    </div>
  </section>

  <section class="stats-section">
    <div class="stats-container">
      <div class="stat-item fade-in-up">
        <span class="stat-number">1,250+</span>
        <span class="stat-label">Candidatos Aprovados</span>
      </div>
      <div class="stat-item fade-in-up">
        <span class="stat-number">350+</span>
        <span class="stat-label">Empresas Parceiras</span>
      </div>
      <div class="stat-item fade-in-up">
        <span class="stat-number">95%</span>
        <span class="stat-label">Taxa de Satisfação</span>
      </div>
      <div class="stat-item fade-in-up">
        <span class="stat-number">24h</span>
        <span class="stat-label">Tempo Médio de Resposta</span>
      </div>
    </div>
  </section>

  <section class="services-section">
    <div class="services-header">
      <h2>Nossos Serviços</h2>
      <p>Oferecemos uma plataforma completa para conectar talentos com as melhores oportunidades do mercado</p>
    </div>
    <div class="carousel-wrapper">
      <div class="card" style="background-image: url('https://images.unsplash.com/photo-1586281380349-632531db7ed5');">
        <div class="card-content">
          <span class="card-icon">🚀</span>
          <h3>Cadastro Inteligente</h3>
          <p>Envie seus dados e currículo em poucos minutos com nossa plataforma intuitiva e moderna.</p>
          <a href="cadastro.php">
            Cadastrar Agora <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>

      <div class="card" style="background-image: url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d');">
        <div class="card-content">
          <span class="card-icon">🔍</span>
          <h3>Vagas Personalizadas</h3>
          <p>Encontre oportunidades que combinam perfeitamente com seu perfil e objetivos profissionais.</p>
          <a href="vagas.php">
            Explorar Vagas <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>

      <div class="card" style="background-image: url('https://images.unsplash.com/photo-1560472354-b33ff0c44a43');">
        <div class="card-content">
          <span class="card-icon">🎥</span>
          <h3>Entrevistas Online</h3>
          <p>Participe de entrevistas por vídeo chamadas de forma rápida, prática e segura.</p>
          <a href="cadastro.php#entrevista">
            Agendar Entrevista <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>
  </section>

  <section class="testimonials-section">
    <div class="testimonials-container">
      <div class="testimonials-header">
        <h2>O que nossos candidatos dizem</h2>
        <p>Histórias reais de pessoas que encontraram suas oportunidades conosco</p>
      </div>
      <div class="testimonials-grid" id="candidatesTestimonials">
        <!-- Os testimonials serão carregados aqui via JavaScript -->
        <div class="loading-testimonials">
          <i class="fas fa-spinner fa-spin"></i> Carregando depoimentos...
        </div>
      </div>
      
      <!-- Botão para enviar testimonial -->
      <div class="testimonials-footer">
        <a href="enviar_testimonial.php" class="btn-enviar-testimonial">
          <i class="fas fa-plus"></i> Compartilhar Minha História
        </a>
      </div>
    </div>
  </section>

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
        <p><i class="fas fa-envelope"></i> contato@eniaclink.com</p>
        <p><i class="fas fa-phone"></i> (11) 9999-9999</p>
        <p><i class="fas fa-map-marker-alt"></i> São Paulo, SP - Brasil</p>
      </div>
    </div>
    
    <div class="footer-bottom">
      <p>&copy; 2025 LWFA — Todos os direitos reservados. Desenvolvido com ❤️ para conectar pessoas.</p>
    </div>
  </footer>

  <script>
    // Animação de entrada
    window.addEventListener('load', function() {
      const elements = document.querySelectorAll('.fade-in-up');
      elements.forEach((el, index) => {
        setTimeout(() => {
          el.style.opacity = '1';
          el.style.transform = 'translateY(0)';
        }, index * 200);
      });
      
      // Carregar testimonials dos candidatos
      loadCandidatesTestimonials();
    });

    // Função para carregar testimonials dos candidatos
    function loadCandidatesTestimonials() {
      fetch('api.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_testimonials_candidatos'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          displayCandidatesTestimonials(data.testimonials);
        } else {
          showEmptyTestimonials();
        }
      })
      .catch(error => {
        console.error('Erro ao carregar testimonials:', error);
        showEmptyTestimonials();
      });
    }

    // Função para exibir testimonials dos candidatos
    function displayCandidatesTestimonials(testimonials) {
      const grid = document.getElementById('candidatesTestimonials');
      
      if (!grid) {
        console.error('Elemento candidatesTestimonials não encontrado!');
        return;
      }
      
      grid.innerHTML = '';
      
      if (testimonials.length === 0) {
        showEmptyTestimonials();
        return;
      }
      
      testimonials.forEach(testimonial => {
        // Gerar avatar com iniciais
        const initials = testimonial.nome.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
        
        const testimonialCard = document.createElement('div');
        testimonialCard.className = 'testimonial-card';
        testimonialCard.innerHTML = `
          <p class="testimonial-text">"${testimonial.mensagem}"</p>
          <div class="testimonial-author">
            <div class="author-avatar">${initials}</div>
            <div class="author-info">
              <h4>${testimonial.nome}</h4>
              <p>${testimonial.cargo}${testimonial.empresa ? ' - ' + testimonial.empresa : ''}</p>
            </div>
          </div>
        `;
        
        grid.appendChild(testimonialCard);
      });
    }

    // Função para mostrar estado vazio
    function showEmptyTestimonials() {
      const grid = document.getElementById('candidatesTestimonials');
      grid.innerHTML = `
        <div class="empty-testimonials" style="text-align: center; padding: 40px; color: #666; grid-column: 1 / -1;">
          <i class="fas fa-comments" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.3;"></i>
          <h3 style="color: #555; margin-bottom: 10px;">Ainda não temos depoimentos</h3>
          <p style="margin-bottom: 20px;">Seja o primeiro candidato a compartilhar sua experiência conosco!</p>
        </div>
      `;
    }
  </script>

</body>
</html>
