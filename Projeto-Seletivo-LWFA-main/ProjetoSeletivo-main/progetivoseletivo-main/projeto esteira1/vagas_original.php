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
  <title>Vagas Abertas | ENIAC LINK+ - Processo Seletivo Virtual</title>
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
      gap: 12px;
    }

    .logo-section h2 {
      font-size: 1.8rem;
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    .logo-icon {
      width: 40px;
      height: 40px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    nav {
      display: flex;
      gap: 2rem;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      transition: all 0.3s ease;
    }

    nav a:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: translateY(-1px);
    }

    nav a.active {
      background: rgba(255, 255, 255, 0.15);
      border-bottom: 2px solid #ffffff;
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
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 2rem;
      margin-top: 2rem;
    }

    .vaga-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid #e5e7eb;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .vaga-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(135deg, #0056b3, #004494);
    }

    .vaga-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .vaga-header {
      margin-bottom: 1rem;
    }

    .vaga-title {
      color: #0056b3;
      font-size: 1.3rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .vaga-empresa {
      color: #004494;
      font-size: 1rem;
      font-weight: 500;
    }

    .vaga-info {
      margin: 1rem 0;
    }

    .vaga-info-item {
      display: flex;
      align-items: center;
      margin-bottom: 0.5rem;
      color: #666;
      font-size: 0.9rem;
    }

    .vaga-info-item i {
      width: 16px;
      margin-right: 8px;
      color: #0056b3;
    }

    .vaga-descricao {
      color: #555;
      font-size: 0.95rem;
      line-height: 1.5;
      margin: 1rem 0;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .vaga-footer {
      margin-top: 1.5rem;
      padding-top: 1rem;
      border-top: 1px solid #e5e7eb;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .vaga-data {
      color: #999;
      font-size: 0.85rem;
    }

    .btn-candidatar {
      background: linear-gradient(135deg, #0056b3, #004494);
      color: white;
      padding: 0.7rem 1.5rem;
      border: none;
      border-radius: 6px;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-candidatar:hover {
      background: linear-gradient(135deg, #004494, #003875);
      transform: translateY(-1px);
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

    /* Responsive */
    @media (max-width: 768px) {
      .header-container {
        flex-direction: column;
        gap: 1rem;
      }

      nav {
        flex-wrap: wrap;
        justify-content: center;
      }

      .page-header h1 {
        font-size: 2rem;
      }

      .vagas-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }

      .main-container {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <div class="header-container">
      <div class="logo-section">
        <div class="logo-icon">
          <i class="fas fa-graduation-cap"></i>
        </div>
        <h2>ENIAC LINK+</h2>
      </div>
      <nav>
        <a href="index.php"><i class="fas fa-home"></i> Início</a>
        <a href="vagas.php" class="active"><i class="fas fa-briefcase"></i> Vagas</a>
        <a href="cadastro.php"><i class="fas fa-user-plus"></i> Cadastrar</a>
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
            <div class="vaga-header">
              <h3 class="vaga-title"><?php echo htmlspecialchars($vaga['titulo']); ?></h3>
              <p class="vaga-empresa"><?php echo htmlspecialchars($vaga['empresa']); ?></p>
            </div>

            <div class="vaga-info">
              <div class="vaga-info-item">
                <i class="fas fa-map-marker-alt"></i>
                <span><?php echo htmlspecialchars($vaga['localizacao']); ?></span>
              </div>
              <?php if (!empty($vaga['salario'])): ?>
              <div class="vaga-info-item">
                <i class="fas fa-money-bill-wave"></i>
                <span><?php echo htmlspecialchars($vaga['salario']); ?></span>
              </div>
              <?php endif; ?>
              <div class="vaga-info-item">
                <i class="fas fa-briefcase"></i>
                <span><?php echo htmlspecialchars($vaga['tipo']); ?></span>
              </div>
            </div>

            <div class="vaga-descricao">
              <?php echo nl2br(htmlspecialchars($vaga['descricao'])); ?>
            </div>

            <div class="vaga-footer">
              <span class="vaga-data">
                <i class="fas fa-calendar-alt"></i>
                <?php echo date('d/m/Y', strtotime($vaga['data_criacao'])); ?>
              </span>
              <a href="candidatar_vaga.php?id=<?php echo $vaga['id']; ?>" class="btn-candidatar">
                <i class="fas fa-paper-plane"></i>
                Candidatar-se
              </a>
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
</body>
</html>
