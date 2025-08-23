<?php
require_once 'config.php';
SessionManager::start();

// Verificar se o usuário está logado
if (!SessionManager::has('admin_logged_in')) {
    header('Location: login_admin.php');
    exit;
}

$admin_nome = SessionManager::get('admin_nome', 'Administrador');
$admin_email = SessionManager::get('admin_email', '');

// Buscar estatísticas do dashboard
try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Total de candidatos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM candidatos");
    $stmt->execute();
    $total_candidatos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de vagas ativas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM vagas WHERE status = 'ativa'");
    $stmt->execute();
    $total_vagas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Entrevistas agendadas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM entrevistas WHERE status = 'agendada'");
    $stmt->execute();
    $total_entrevistas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Candidatos em análise
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM candidatos WHERE status = 'em_analise'");
    $stmt->execute();
    $total_analise = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Buscar candidatos recentes
    $sql = "SELECT nome, email, status, data_cadastro FROM candidatos ORDER BY data_cadastro DESC LIMIT 5";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $candidatos_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $total_candidatos = 0;
    $total_vagas = 0;
    $total_entrevistas = 0;
    $total_analise = 0;
    $candidatos_recentes = [];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - ENIAC LINK+</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #667eea;
      --secondary: #764ba2;
      --accent: #f093fb;
      --success: #4facfe;
      --warning: #43e97b;
      --danger: #f5576c;
      --glass-bg: rgba(255, 255, 255, 0.1);
      --glass-border: rgba(255, 255, 255, 0.2);
      --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.1);
      --shadow-xl: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 50%, var(--accent) 100%);
      background-size: 400% 400%;
      animation: gradientShift 15s ease infinite;
      min-height: 100vh;
      overflow-x: hidden;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Header Moderno */
    header {
      background: var(--glass-bg);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--glass-border);
      padding: 1rem 0;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: var(--shadow-lg);
    }

    .header-container {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 2rem;
    }

    .logo-section {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .logo-header {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      border: 3px solid rgba(255, 255, 255, 0.3);
      transition: all 0.3s ease;
    }

    .logo-header:hover {
      transform: scale(1.1) rotate(5deg);
    }

    .logo-text {
      color: white;
      font-weight: 700;
      font-size: 1.5rem;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Navegação Horizontal */
    nav {
      display: flex;
      flex-direction: row;
      gap: 0.5rem;
      align-items: center;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      padding: 0.75rem 1.5rem;
      border-radius: 50px;
      background: var(--glass-bg);
      backdrop-filter: blur(10px);
      border: 1px solid var(--glass-border);
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.9rem;
      position: relative;
      overflow: hidden;
    }

    nav a::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.6s ease;
    }

    nav a:hover::before {
      left: 100%;
    }

    nav a:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    nav a.active {
      background: linear-gradient(135deg, var(--success), #00f2fe);
      box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
    }

    /* Container Principal */
    .main-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 2rem;
    }

    /* Hero Section */
    .hero-section {
      background: var(--glass-bg);
      backdrop-filter: blur(20px);
      border: 1px solid var(--glass-border);
      border-radius: 24px;
      padding: 2.5rem;
      margin-bottom: 2rem;
      box-shadow: var(--shadow-xl);
      position: relative;
      overflow: hidden;
    }

    .hero-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border-radius: 24px 24px 0 0;
    }

    .hero-content {
      text-align: center;
      color: white;
    }

    .hero-title {
      font-size: 2.5rem;
      font-weight: 800;
      margin-bottom: 1rem;
      background: linear-gradient(135deg, #fff 0%, #f0f8ff 100%);
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .hero-subtitle {
      font-size: 1.1rem;
      opacity: 0.9;
      margin-bottom: 2rem;
      color: rgba(255, 255, 255, 0.8);
    }

    .admin-badge {
      display: inline-flex;
      align-items: center;
      gap: 1rem;
      background: var(--glass-bg);
      backdrop-filter: blur(10px);
      padding: 1rem 2rem;
      border-radius: 50px;
      border: 1px solid var(--glass-border);
      box-shadow: var(--shadow-lg);
    }

    .admin-info .admin-name {
      font-weight: 700;
      font-size: 1.1rem;
      color: white;
    }

    .admin-info .admin-email {
      font-size: 0.9rem;
      opacity: 0.8;
      color: rgba(255, 255, 255, 0.7);
    }

    /* Grid de Estatísticas */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: var(--glass-bg);
      backdrop-filter: blur(20px);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      padding: 2rem;
      text-align: center;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      box-shadow: var(--shadow-lg);
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      border-radius: 20px 20px 0 0;
    }

    .stat-card:nth-child(1)::before { background: linear-gradient(135deg, var(--primary), var(--secondary)); }
    .stat-card:nth-child(2)::before { background: linear-gradient(135deg, var(--success), #00f2fe); }
    .stat-card:nth-child(3)::before { background: linear-gradient(135deg, var(--warning), #38f9d7); }
    .stat-card:nth-child(4)::before { background: linear-gradient(135deg, var(--accent), var(--danger)); }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-xl);
    }

    .stat-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
      font-size: 2rem;
      color: white;
    }

    .stat-card:nth-child(1) .stat-icon { background: linear-gradient(135deg, var(--primary), var(--secondary)); }
    .stat-card:nth-child(2) .stat-icon { background: linear-gradient(135deg, var(--success), #00f2fe); }
    .stat-card:nth-child(3) .stat-icon { background: linear-gradient(135deg, var(--warning), #38f9d7); }
    .stat-card:nth-child(4) .stat-icon { background: linear-gradient(135deg, var(--accent), var(--danger)); }

    .stat-number {
      font-size: 2.5rem;
      font-weight: 800;
      color: white;
      margin-bottom: 0.5rem;
    }

    .stat-label {
      font-size: 1rem;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.8);
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    /* Grid de Ações */
    .actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .action-card {
      background: var(--glass-bg);
      backdrop-filter: blur(20px);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      padding: 2rem;
      text-decoration: none;
      color: white;
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      box-shadow: var(--shadow-lg);
    }

    .action-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-xl);
      border-color: rgba(255, 255, 255, 0.3);
    }

    .action-icon {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1rem;
      font-size: 1.5rem;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .action-title {
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: white;
    }

    .action-description {
      font-size: 0.9rem;
      opacity: 0.8;
      color: rgba(255, 255, 255, 0.7);
    }

    /* Seção de Atividades Recentes */
    .recent-section {
      background: var(--glass-bg);
      backdrop-filter: blur(20px);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      padding: 2rem;
      box-shadow: var(--shadow-lg);
      position: relative;
      overflow: hidden;
    }

    .recent-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border-radius: 20px 20px 0 0;
    }

    .section-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: white;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .activity-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 12px;
      margin-bottom: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
    }

    .activity-item:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: translateX(5px);
    }

    .activity-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--success), #00f2fe);
      color: white;
      font-size: 0.9rem;
    }

    .activity-content {
      flex: 1;
    }

    .activity-text {
      color: white;
      font-weight: 500;
      margin-bottom: 0.25rem;
    }

    .activity-time {
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.8rem;
    }

    /* Ações do Perfil */
    .profile-actions {
      display: flex;
      gap: 1rem;
      margin-top: 1rem;
      justify-content: center;
    }

    .btn-profile, .btn-logout {
      padding: 0.75rem 1.5rem;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      border: 1px solid var(--glass-border);
      backdrop-filter: blur(10px);
    }

    .btn-profile {
      background: linear-gradient(135deg, var(--success), #00f2fe);
      color: white;
      box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
    }

    .btn-logout {
      background: linear-gradient(135deg, var(--accent), var(--danger));
      color: white;
      box-shadow: 0 8px 25px rgba(245, 87, 108, 0.3);
    }

    .btn-profile:hover, .btn-logout:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-xl);
    }

    /* Responsivo */
    @media (max-width: 768px) {
      .header-container {
        flex-direction: column;
        gap: 1rem;
        padding: 1rem;
      }

      nav {
        flex-wrap: wrap;
        justify-content: center;
      }

      .main-container {
        padding: 1rem;
      }

      .hero-title {
        font-size: 2rem;
      }

      .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
      }

      .actions-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }

      .profile-actions {
        flex-direction: column;
      }
    }

    /* Partículas flutuantes */
    .floating-particles {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: -1;
    }

    .particle {
      position: absolute;
      width: 4px;
      height: 4px;
      background: rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      animation: float 20s infinite linear;
    }

    @keyframes float {
      0% {
        transform: translateY(100vh) rotate(0deg);
        opacity: 0;
      }
      10% {
        opacity: 1;
      }
      90% {
        opacity: 1;
      }
      100% {
        transform: translateY(-100px) rotate(360deg);
        opacity: 0;
      }
    }
  </style>
</head>
<body>
  <!-- Partículas flutuantes -->
  <div class="floating-particles"></div>

  <!-- Header -->
  <header>
    <div class="header-container">
      <div class="logo-section">
        <img src="./imagens/Logoindex.jpg" alt="Logo ENIAC LINK+" class="logo-header">
        <div class="logo-text">ENIAC LINK+</div>
      </div>
      <nav>
        <a href="index.php"><i class="fas fa-home"></i> Início</a>
        <a href="vagas.php"><i class="fas fa-briefcase"></i> Vagas</a>
        <a href="cadastro.php"><i class="fas fa-user-plus"></i> Cadastrar</a>
        <a href="admin.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
      </nav>
    </div>
  </header>

  <!-- Container Principal -->
  <div class="main-container">
    <!-- Hero Section -->
    <div class="hero-section">
      <div class="hero-content">
        <h1 class="hero-title">
          <i class="fas fa-chart-line"></i> Painel de Controle RH
        </h1>
        <p class="hero-subtitle">
          Central completa para gestão de candidatos e processos seletivos
        </p>
        <div class="admin-badge">
          <i class="fas fa-user-shield" style="font-size: 1.5rem; color: white;"></i>
          <div class="admin-info">
            <div class="admin-name"><?php echo htmlspecialchars($admin_nome); ?></div>
            <?php if (!empty($admin_email)): ?>
              <div class="admin-email"><?php echo htmlspecialchars($admin_email); ?></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="profile-actions">
          <a href="update_admin_profile.php" class="btn-profile">
            <i class="fas fa-user-cog"></i> Editar Perfil
          </a>
          <a href="logout_admin.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Sair
          </a>
        </div>
      </div>
    </div>

    <!-- Grid de Estatísticas -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-number"><?php echo $total_candidatos; ?></div>
        <div class="stat-label">Candidatos</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-briefcase"></i>
        </div>
        <div class="stat-number"><?php echo $total_vagas; ?></div>
        <div class="stat-label">Vagas Ativas</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-number"><?php echo $total_entrevistas; ?></div>
        <div class="stat-label">Entrevistas</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-clock"></i>
        </div>
        <div class="stat-number"><?php echo $total_analise; ?></div>
        <div class="stat-label">Em Análise</div>
      </div>
    </div>

    <!-- Grid de Ações */
    <div class="actions-grid">
      <a href="gerenciar_vagas.php" class="action-card">
        <div class="action-icon">
          <i class="fas fa-briefcase"></i>
        </div>
        <div class="action-title">Gerenciar Vagas</div>
        <div class="action-description">Criar, editar e gerenciar vagas abertas</div>
      </a>
      
      <a href="curriculos.php" class="action-card">
        <div class="action-icon">
          <i class="fas fa-file-alt"></i>
        </div>
        <div class="action-title">Visualizar Currículos</div>
        <div class="action-description">Analisar currículos dos candidatos</div>
      </a>
      
      <a href="gerenciar_contatos.php" class="action-card">
        <div class="action-icon">
          <i class="fas fa-envelope"></i>
        </div>
        <div class="action-title">Mensagens de Contato</div>
        <div class="action-description">Gerenciar mensagens do "Fale Conosco"</div>
      </a>
      
      <a href="https://meet.google.com" target="_blank" class="action-card">
        <div class="action-icon">
          <i class="fas fa-video"></i>
        </div>
        <div class="action-title">Google Meet</div>
        <div class="action-description">Iniciar videoconferências</div>
      </a>
      
      <a href="https://calendar.google.com" target="_blank" class="action-card">
        <div class="action-icon">
          <i class="fas fa-calendar"></i>
        </div>
        <div class="action-title">Agenda</div>
        <div class="action-description">Gerenciar entrevistas e compromissos</div>
      </a>
      
      <a href="https://docs.google.com/spreadsheets" target="_blank" class="action-card">
        <div class="action-icon">
          <i class="fas fa-chart-bar"></i>
        </div>
        <div class="action-title">Relatórios</div>
        <div class="action-description">Gerar planilhas e relatórios</div>
      </a>
    </div>

    <!-- Atividades Recentes -->
    <div class="recent-section">
      <h2 class="section-title">
        <i class="fas fa-history"></i> Candidatos Recentes
      </h2>
      
      <?php if (!empty($candidatos_recentes)): ?>
        <?php foreach ($candidatos_recentes as $candidato): ?>
          <div class="activity-item">
            <div class="activity-icon">
              <i class="fas fa-user"></i>
            </div>
            <div class="activity-content">
              <div class="activity-text">
                <strong><?php echo htmlspecialchars($candidato['nome']); ?></strong> se cadastrou
              </div>
              <div class="activity-time">
                <?php 
                $data = new DateTime($candidato['data_cadastro']);
                echo $data->format('d/m/Y H:i'); 
                ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="activity-item">
          <div class="activity-icon">
            <i class="fas fa-info"></i>
          </div>
          <div class="activity-content">
            <div class="activity-text">Nenhum candidato encontrado</div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // Criar partículas flutuantes
    function createParticles() {
      const container = document.querySelector('.floating-particles');
      
      for (let i = 0; i < 20; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 20 + 's';
        particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
        container.appendChild(particle);
      }
    }

    // Inicializar partículas
    createParticles();

    // Atualizar estatísticas periodicamente
    setInterval(() => {
      fetch('admin.php')
        .then(response => response.text())
        .then(data => {
          // Atualizar apenas se necessário
          console.log('Dados atualizados');
        })
        .catch(error => console.log('Erro ao atualizar:', error));
    }, 30000); // Atualizar a cada 30 segundos
  </script>
</body>
</html>
