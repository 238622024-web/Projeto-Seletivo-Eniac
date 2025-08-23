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
  <title>Gerenciar Vagas | LWFA - Sistema de Gestão</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #34609b 100%);
      background-size: 400% 400%;
      animation: gradientShift 20s ease infinite;
      min-height: 100vh;
      color: #333;
      line-height: 1.6;
      position: relative;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Header */
    header {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #34609b 100%);
      padding: 2rem 0;
      color: white;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
      position: relative;
      overflow: hidden;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .header-container {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 2rem;
      position: relative;
      z-index: 2;
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

    .back-btn {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.15) 100%);
      color: white;
      padding: 20px 40px;
      border-radius: 25px;
      text-decoration: none;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      font-weight: 800;
      backdrop-filter: blur(25px);
      border: 3px solid rgba(255, 255, 255, 0.5);
      display: flex;
      align-items: center;
      gap: 18px;
      font-size: 1.2rem;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2), inset 0 2px 0 rgba(255, 255, 255, 0.4);
      position: relative;
      overflow: hidden;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      font-family: 'Inter', sans-serif;
      min-width: 220px;
      justify-content: center;
    }

    .back-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      transition: left 0.8s ease;
      z-index: 1;
    }

    .back-btn:hover::before {
      left: 100%;
    }

    .back-btn:hover {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.45) 0%, rgba(255, 255, 255, 0.25) 100%);
      transform: translateY(-8px) scale(1.05);
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), inset 0 2px 0 rgba(255, 255, 255, 0.5);
      border-color: rgba(255, 255, 255, 0.7);
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    .back-btn:active {
      transform: translateY(-4px) scale(1.02);
      transition: all 0.1s ease;
    }

    .back-btn i {
      font-size: 1.4rem;
      position: relative;
      z-index: 2;
      transition: transform 0.3s ease;
      text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .back-btn:hover i {
      transform: translateX(-5px) rotate(-5deg);
    }

    .back-btn span {
      position: relative;
      z-index: 2;
      text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    /* Main Content */
    .main-content {
      max-width: 1400px;
      margin: 50px auto;
      padding: 0 30px;
    }

    .page-title {
      background: rgba(255, 255, 255, 0.95);
      padding: 60px 50px;
      border-radius: 25px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
      margin-bottom: 50px;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(15px);
      position: relative;
      overflow: hidden;
    }

    .page-title h1 {
      color: #1e3c72;
      margin-bottom: 20px;
      font-size: 3.2rem;
      font-weight: 800;
      letter-spacing: -1px;
      line-height: 1.2;
    }

    .page-title p {
      color: #64748b;
      font-size: 1.3rem;
      margin-bottom: 0;
      font-weight: 400;
      max-width: 700px;
      margin: 0 auto;
      line-height: 1.6;
    }

    /* Stats Section */
    .stats-section {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
      margin-bottom: 60px;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px 35px;
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.3);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      backdrop-filter: blur(15px);
    }

    .stat-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
    }

    .stat-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 25px;
    }

    .stat-icon {
      width: 70px;
      height: 70px;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      color: white;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .stat-number {
      font-size: 3.5rem;
      font-weight: 900;
      color: #1e293b;
      margin-bottom: 12px;
      line-height: 1;
      letter-spacing: -2px;
    }

    .stat-label {
      color: #64748b;
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 20px;
      letter-spacing: 0.5px;
    }

    /* Actions Section */
    .actions-section {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
      gap: 40px;
      margin-bottom: 60px;
    }

    .action-card {
      background: rgba(255, 255, 255, 0.95);
      padding: 50px 40px;
      border-radius: 25px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
      text-decoration: none;
      color: inherit;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid rgba(255, 255, 255, 0.3);
      position: relative;
      overflow: hidden;
      cursor: pointer;
      backdrop-filter: blur(15px);
    }

    .action-card:hover {
      transform: translateY(-15px);
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.2);
    }

    .action-icon {
      width: 90px;
      height: 90px;
      border-radius: 25px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      color: white;
      margin-bottom: 30px;
      position: relative;
      z-index: 2;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .action-title {
      font-size: 1.8rem;
      font-weight: 800;
      color: #1e293b;
      margin-bottom: 20px;
      position: relative;
      z-index: 2;
      letter-spacing: -0.5px;
    }

    .action-desc {
      color: #64748b;
      font-size: 1.1rem;
      line-height: 1.7;
      position: relative;
      z-index: 2;
      font-weight: 400;
    }

    .action-features {
      list-style: none;
      margin-top: 25px;
      position: relative;
      z-index: 2;
    }

    .action-features li {
      color: #64748b;
      font-size: 1rem;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 500;
    }

    .action-features li i {
      color: #1e3c72;
      width: 18px;
      font-size: 0.9rem;
    }

    /* Color variations */
    .criar-vagas .action-icon {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }

    .editar-vagas .action-icon {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .monitorar-vagas .action-icon {
      background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .header-container {
        flex-direction: column;
        gap: 25px;
        text-align: center;
        padding: 0 1.5rem;
      }

      .page-title {
        padding: 40px 30px;
        margin-bottom: 40px;
      }

      .page-title h1 {
        font-size: 2.5rem;
      }

      .page-title p {
        font-size: 1.1rem;
      }

      .actions-section {
        grid-template-columns: 1fr;
        gap: 30px;
      }

      .action-card {
        padding: 40px 30px;
      }

      .main-content {
        padding: 0 20px;
        margin: 30px auto;
      }

      .stats-section {
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 40px;
      }

      .stat-card {
        padding: 30px 25px;
      }

      .stat-number {
        font-size: 3rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="header-container">
      <div class="logo-section">
        <img src="./imagens/Logoindex.jpg" alt="Logo da empresa" class="logo-header">
      </div>
      <a href="admin.php" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        <span>Voltar ao Painel</span>
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
      // Animação das estatísticas com contador
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
        }, index * 150);
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
        }, index * 200 + 300);
      });

      // Animação dos stats cards
      const statCards = document.querySelectorAll('.stat-card');
      statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          card.style.transition = 'all 0.5s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 100 + 200);
      });
    });
  </script>

  <!-- Footer -->
  <footer style="background: linear-gradient(135deg, #1a1a1a, #333); color: white; padding: 60px 20px 30px; margin-top: 2rem;">
      <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px;">
          <div>
              <h3 style="color: #0099ff; margin-bottom: 20px; font-weight: 600;">Processo-Seletivo</h3>
              <p style="color: #ccc; line-height: 1.6; margin-bottom: 8px;">Conectando talentos com oportunidades desde 2025. Nossa missão é simplificar o processo seletivo e aproximar candidatos das empresas ideais.</p>
              <div style="display: flex; gap: 15px; margin-top: 20px;">
                  <a href="#" style="width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; background: linear-gradient(135deg, #0077b5, #005582); box-shadow: 0 4px 15px rgba(0, 119, 181, 0.3); transition: all 0.3s ease;" title="LinkedIn">
                      <i class="fab fa-linkedin-in"></i>
                  </a>
                  <a href="#" style="width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; background: linear-gradient(135deg, #1877f2, #0d5dcc); box-shadow: 0 4px 15px rgba(24, 119, 242, 0.3); transition: all 0.3s ease;" title="Facebook">
                      <i class="fab fa-facebook-f"></i>
                  </a>
                  <a href="#" style="width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045); box-shadow: 0 4px 15px rgba(131, 58, 180, 0.3); transition: all 0.3s ease;" title="Instagram">
                      <i class="fab fa-instagram"></i>
                  </a>
                  <a href="#" style="width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; background: linear-gradient(135deg, #25d366, #1ebe57); box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3); transition: all 0.3s ease;" title="WhatsApp">
                      <i class="fab fa-whatsapp"></i>
                  </a>
                  <a href="#" style="width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; background: linear-gradient(135deg, #ff0000, #cc0000); box-shadow: 0 4px 15px rgba(255, 0, 0, 0.3); transition: all 0.3s ease;" title="YouTube">
                      <i class="fab fa-youtube"></i>
                  </a>
              </div>
          </div>
          
          <div>
              <h3 style="color: #0099ff; margin-bottom: 20px; font-weight: 600;">Links Rápidos</h3>
              <ul style="list-style: none; padding: 0;">
                  <li style="margin-bottom: 8px;"><a href="index.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Início</a></li>
                  <li style="margin-bottom: 8px;"><a href="cadastro.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Cadastro</a></li>
                  <li style="margin-bottom: 8px;"><a href="vagas.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Vagas</a></li>
                  <li style="margin-bottom: 8px;"><a href="login_admin.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">RH</a></li>
              </ul>
          </div>
          
          <div>
              <h3 style="color: #0099ff; margin-bottom: 20px; font-weight: 600;">Suporte</h3>
              <ul style="list-style: none; padding: 0;">
                  <li style="margin-bottom: 8px;"><a href="central_ajuda.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Central de Ajuda</a></li>
                  <li style="margin-bottom: 8px;"><a href="termos_uso.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Termos de Uso</a></li>
                  <li style="margin-bottom: 8px;"><a href="politica_privacidade.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Política de Privacidade</a></li>
                  <li style="margin-bottom: 8px;"><a href="fale_conosco.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Fale Conosco</a></li>
              </ul>
          </div>
          
          <div>
              <h3 style="color: #0099ff; margin-bottom: 20px; font-weight: 600;">Contato</h3>
              <p style="color: #ccc; line-height: 1.6; margin-bottom: 8px;"><i class="fas fa-envelope"></i> contato@eniaclink.com</p>
              <p style="color: #ccc; line-height: 1.6; margin-bottom: 8px;"><i class="fas fa-phone"></i> (11) 9999-9999</p>
              <p style="color: #ccc; line-height: 1.6; margin-bottom: 8px;"><i class="fas fa-map-marker-alt"></i> São Paulo, SP - Brasil</p>
          </div>
      </div>
      
      <div style="border-top: 1px solid #444; padding-top: 30px; text-align: center; color: #999;">
          <p>&copy; 2025 LWFA — Todos os direitos reservados. Desenvolvido com ❤️ para conectar pessoas.</p>
      </div>
  </footer>
</body>
</html>
