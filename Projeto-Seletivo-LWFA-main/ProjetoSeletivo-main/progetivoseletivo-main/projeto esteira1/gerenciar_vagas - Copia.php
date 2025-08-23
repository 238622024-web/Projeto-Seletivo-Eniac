<?php
require_once 'config.php';

SessionManager::start();

// Verificar se o usuário está logado
if (!SessionManager::has('admin_logged_in')) {
    header('Location: login_admin.php');
    exit;
}

$admin_nome = SessionManager::get('admin_nome', 'Administrador');

// Buscar estatísticas de vagas
try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Total de vagas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM vagas");
    $stmt->execute();
    $total_vagas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Vagas ativas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM vagas WHERE status = 'ativa'");
    $stmt->execute();
    $vagas_ativas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Vagas pausadas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM vagas WHERE status = 'pausada'");
    $stmt->execute();
    $vagas_pausadas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Total de candidaturas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM candidaturas");
    $stmt->execute();
    $total_candidaturas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
} catch (PDOException $e) {
    $total_vagas = 0;
    $vagas_ativas = 0;
    $vagas_pausadas = 0;
    $total_candidaturas = 0;
    error_log("Erro ao buscar estatísticas: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gerenciar Vagas | ENIAC LINK+ - Sistema de Gestão</title>
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
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      color: #333;
      line-height: 1.6;
    }

    /* Header */
    header {
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      padding: 1rem 0;
      color: white;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
      position: relative;
      overflow: hidden;
    }

    header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a" cx="50%" cy="0%" r="50%"><stop offset="0%" stop-color="rgba(255,255,255,0.1)"/><stop offset="100%" stop-color="rgba(255,255,255,0)"/></radialGradient></defs><rect width="100" height="20" fill="url(%23a)"/></svg>');
      pointer-events: none;
    }

    .header-container {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 2rem;
      position: relative;
      z-index: 1;
    }

    .logo-section {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .logo-section h2 {
      font-size: 1.5rem;
      font-weight: 600;
      letter-spacing: 1px;
    }

    .logo-icon {
      width: 45px;
      height: 45px;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(10px);
      font-size: 1.2rem;
    }

    .back-btn {
      background: rgba(255, 255, 255, 0.1);
      color: white;
      padding: 12px 24px;
      border-radius: 10px;
      text-decoration: none;
      transition: all 0.3s ease;
      font-weight: 500;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .back-btn:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    /* Main Content */
    .main-content {
      max-width: 1400px;
      margin: 40px auto;
      padding: 0 20px;
    }

    .page-title {
      background: linear-gradient(135deg, #ffffff, #f8fafc);
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(10px);
    }

    .page-title h1 {
      color: #1e3c72;
      margin-bottom: 15px;
      font-size: 2.5rem;
      font-weight: 700;
    }

    .page-title p {
      color: #64748b;
      font-size: 1.1rem;
      margin-bottom: 0;
    }

    /* Stats Section */
    .stats-section {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
      margin-bottom: 40px;
    }

    .stat-card {
      background: linear-gradient(135deg, #ffffff, #f8fafc);
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.8);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #3b82f6, #8b5cf6, #06d6a0);
      background-size: 200% 200%;
      animation: gradientShift 3s ease infinite;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .stat-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .stat-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      color: white;
    }

    .stat-number {
      font-size: 2.8rem;
      font-weight: 800;
      color: #1e293b;
      margin-bottom: 8px;
      line-height: 1;
    }

    .stat-label {
      color: #64748b;
      font-weight: 600;
      font-size: 1rem;
      margin-bottom: 15px;
    }

    /* Action Cards */
    .actions-section {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 30px;
      margin-bottom: 40px;
    }

    .action-card {
      background: linear-gradient(135deg, #ffffff, #f8fafc);
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
      text-decoration: none;
      color: inherit;
      transition: all 0.3s ease;
      border: 1px solid rgba(255, 255, 255, 0.8);
      position: relative;
      overflow: hidden;
      cursor: pointer;
    }

    .action-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(139, 92, 246, 0.05));
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .action-card:hover::before {
      opacity: 1;
    }

    .action-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .action-icon {
      width: 80px;
      height: 80px;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: white;
      margin-bottom: 25px;
      position: relative;
      z-index: 1;
    }

    .action-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 15px;
      position: relative;
      z-index: 1;
    }

    .action-desc {
      color: #64748b;
      font-size: 1rem;
      line-height: 1.6;
      position: relative;
      z-index: 1;
    }

    .action-features {
      list-style: none;
      margin-top: 20px;
      position: relative;
      z-index: 1;
    }

    .action-features li {
      color: #64748b;
      font-size: 0.9rem;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .action-features li i {
      color: #3b82f6;
      width: 16px;
    }

    /* Color variations */
    .criar-vagas .action-icon {
      background: linear-gradient(135deg, #10b981, #059669);
    }

    .editar-vagas .action-icon {
      background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .monitorar-vagas .action-icon {
      background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .header-container {
        flex-direction: column;
        gap: 20px;
        text-align: center;
      }

      .page-title h1 {
        font-size: 2rem;
      }

      .actions-section {
        grid-template-columns: 1fr;
      }

      .action-card {
        padding: 30px;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="header-container">
      <div class="logo-section">
        <div class="logo-icon">
          <i class="fas fa-briefcase"></i>
        </div>
        <h2>Gerenciar Vagas</h2>
      </div>
      <a href="admin.php" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        Voltar ao Painel
      </a>
    </div>
  </header>

  <div class="main-content">
    <div class="page-title">
      <h1><i class="fas fa-cogs"></i> Central de Gestão de Vagas</h1>
      <p>Sistema completo para criação, edição e monitoramento de vagas de emprego</p>
    </div>

    <!-- Estatísticas -->
    <div class="stats-section">
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
            <i class="fas fa-briefcase"></i>
          </div>
        </div>
        <div class="stat-number"><?php echo $total_vagas; ?></div>
        <div class="stat-label">Total de Vagas</div>
      </div>
      
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
            <i class="fas fa-play-circle"></i>
          </div>
        </div>
        <div class="stat-number"><?php echo $vagas_ativas; ?></div>
        <div class="stat-label">Vagas Ativas</div>
      </div>
      
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <i class="fas fa-pause-circle"></i>
          </div>
        </div>
        <div class="stat-number"><?php echo $vagas_pausadas; ?></div>
        <div class="stat-label">Vagas Pausadas</div>
      </div>
      
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
            <i class="fas fa-users"></i>
          </div>
        </div>
        <div class="stat-number"><?php echo $total_candidaturas; ?></div>
        <div class="stat-label">Total de Candidaturas</div>
      </div>
    </div>

    <!-- Ações Principais -->
    <div class="actions-section">
      <a href="criar_vaga_teste.php" class="action-card criar-vagas" style="text-decoration: none; color: inherit; display: block;">
        <div class="action-icon">
          <i class="fas fa-plus-circle"></i>
        </div>
        <div class="action-title">Criar Vagas</div>
        <div class="action-desc">
          Crie novas oportunidades de emprego com formulário completo e intuitivo
        </div>
        <ul class="action-features">
          <li><i class="fas fa-check"></i> Formulário completo</li>
          <li><i class="fas fa-check"></i> Validação automática</li>
          <li><i class="fas fa-check"></i> Interface profissional</li>
          <li><i class="fas fa-check"></i> Publicação imediata</li>
        </ul>
        <div style="margin-top: 20px; text-align: center;">
          <span class="btn-action" style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 12px 24px; border-radius: 10px; display: inline-flex; align-items: center; gap: 8px; font-weight: 600; font-size: 1rem;">
            <i class="fas fa-plus"></i>
            Criar Nova Vaga
          </span>
        </div>
      </a>

      <div class="action-card editar-vagas" onclick="window.location.href='editar_vagas.php'">
        <div class="action-icon">
          <i class="fas fa-edit"></i>
        </div>
        <div class="action-title">Editar Vagas</div>
        <div class="action-desc">
          Modifique vagas existentes, atualize informações e remova vagas completas
        </div>
        <ul class="action-features">
          <li><i class="fas fa-check"></i> Atualizar descrições</li>
          <li><i class="fas fa-check"></i> Modificar requisitos</li>
          <li><i class="fas fa-check"></i> Ajustar salários</li>
          <li><i class="fas fa-check"></i> Excluir vagas finalizadas</li>
        </ul>
      </div>

      <div class="action-card monitorar-vagas" onclick="window.location.href='monitorar_vagas.php'">
        <div class="action-icon">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="action-title">Monitorar Vagas</div>
        <div class="action-desc">
          Acompanhe métricas, estatísticas e performance das vagas ativas
        </div>
        <ul class="action-features">
          <li><i class="fas fa-check"></i> Relatórios detalhados</li>
          <li><i class="fas fa-check"></i> Métricas de candidaturas</li>
          <li><i class="fas fa-check"></i> Taxa de conversão</li>
          <li><i class="fas fa-check"></i> Dashboard executivo</li>
        </ul>
      </div>
    </div>
  </div>

  <script>
    // Animações de entrada
    document.addEventListener('DOMContentLoaded', function() {
      // Animação das estatísticas
      const stats = document.querySelectorAll('.stat-number');
      stats.forEach((stat, index) => {
        const finalValue = parseInt(stat.textContent);
        stat.textContent = '0';
        
        setTimeout(() => {
          let currentValue = 0;
          const increment = finalValue / 30;
          const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
              stat.textContent = finalValue;
              clearInterval(timer);
            } else {
              stat.textContent = Math.floor(currentValue);
            }
          }, 50);
        }, index * 200);
      });

      // Animação dos cards
      const cards = document.querySelectorAll('.action-card');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
          card.style.transition = 'all 0.6s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 200 + 500);
      });
    });

    // Feedback visual nos cliques
    document.querySelectorAll('.action-card').forEach(card => {
      card.addEventListener('click', function() {
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
          this.style.transform = '';
        }, 150);
      });
    });

    // Função específica para ir para criar vaga
    function irParaCriarVaga() {
      console.log('Redirecionando para criar_vaga.php...');
      window.location.href = 'criar_vaga.php';
    }

    // Debug - verificar se os eventos estão funcionando
    document.addEventListener('click', function(e) {
      if (e.target.closest('.criar-vagas')) {
        console.log('Clique no card criar-vagas detectado');
      }
    });
  </script>
</body>
</html>
