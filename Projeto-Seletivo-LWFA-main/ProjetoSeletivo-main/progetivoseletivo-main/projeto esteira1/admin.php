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

// Buscar candidatos recentes do banco de dados
try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Buscar candidatos simples primeiro
    $sql = "SELECT id, nome, email, status, data_cadastro FROM candidatos ORDER BY data_cadastro DESC LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $candidatos_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Processar candidatos para incluir vaga
    $candidatos = [];
    foreach ($candidatos_raw as $candidato) {
        // Buscar vaga do candidato (se houver)
        $sql_vaga = "SELECT v.titulo FROM candidaturas c 
                     JOIN vagas v ON c.vaga_id = v.id 
                     WHERE c.candidato_id = ? 
                     ORDER BY c.data_candidatura DESC LIMIT 1";
        $stmt_vaga = $pdo->prepare($sql_vaga);
        $stmt_vaga->execute([$candidato['id']]);
        $vaga = $stmt_vaga->fetch(PDO::FETCH_ASSOC);
        
        $candidato['vaga_titulo'] = $vaga ? $vaga['titulo'] : null;
        $candidato['candidatura_status'] = null;
        $candidato['data_candidatura'] = null;
        
        $candidatos[] = $candidato;
    }
    
    // Buscar estatísticas para o dashboard
    $stats = [];
    
    // Total de candidatos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM candidatos");
    $stmt->execute();
    $stats['candidatos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de vagas ativas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM vagas WHERE status = 'ativa'");
    $stmt->execute();
    $stats['vagas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Entrevistas agendadas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM entrevistas WHERE status = 'agendada'");
    $stmt->execute();
    $stats['entrevistas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Candidatos em análise
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM candidatos WHERE status = 'em_analise'");
    $stmt->execute();
    $stats['analise'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Buscar atividades recentes do banco de dados
    $atividades_recentes = [];
    
    // Buscar candidaturas recentes (últimas 24 horas)
    $sql_candidaturas = "
        SELECT c.data_candidatura, cand.nome, v.titulo as vaga_titulo, 'candidatura' as tipo
        FROM candidaturas c 
        JOIN candidatos cand ON c.candidato_id = cand.id 
        JOIN vagas v ON c.vaga_id = v.id 
        WHERE c.data_candidatura >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ORDER BY c.data_candidatura DESC 
        LIMIT 5
    ";
    
    $stmt = $pdo->prepare($sql_candidaturas);
    $stmt->execute();
    $candidaturas_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar entrevistas agendadas recentes
    $sql_entrevistas = "
        SELECT e.data_criacao, cand.nome, v.titulo as vaga_titulo, 'entrevista' as tipo
        FROM entrevistas e 
        JOIN candidatos cand ON e.candidato_id = cand.id 
        JOIN vagas v ON e.vaga_id = v.id 
        WHERE e.data_criacao >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ORDER BY e.data_criacao DESC 
        LIMIT 3
    ";
    
    try {
        $stmt = $pdo->prepare($sql_entrevistas);
        $stmt->execute();
        $entrevistas_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Tabela de entrevistas pode não existir ainda
        $entrevistas_recentes = [];
    }
    
    // Buscar candidatos aprovados recentes
    $sql_aprovados = "
        SELECT cand.data_cadastro, cand.nome, 'aprovacao' as tipo
        FROM candidatos cand 
        WHERE cand.status = 'aprovado' 
        AND cand.data_cadastro >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ORDER BY cand.data_cadastro DESC 
        LIMIT 3
    ";
    
    $stmt = $pdo->prepare($sql_aprovados);
    $stmt->execute();
    $aprovados_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combinar todas as atividades
    foreach ($candidaturas_recentes as $ativ) {
        $atividades_recentes[] = [
            'tipo' => 'candidatura',
            'titulo' => 'Nova candidatura',
            'descricao' => $ativ['nome'] . ' se candidatou para ' . $ativ['vaga_titulo'],
            'data' => $ativ['data_candidatura'],
            'icon' => 'fa-user-plus',
            'class' => 'new'
        ];
    }
    
    foreach ($entrevistas_recentes as $ativ) {
        $atividades_recentes[] = [
            'tipo' => 'entrevista',
            'titulo' => 'Entrevista agendada',
            'descricao' => $ativ['nome'] . ' - ' . $ativ['vaga_titulo'],
            'data' => $ativ['data_criacao'],
            'icon' => 'fa-calendar-check',
            'class' => 'interview'
        ];
    }
    
    foreach ($aprovados_recentes as $ativ) {
        $atividades_recentes[] = [
            'tipo' => 'aprovacao',
            'titulo' => 'Candidato aprovado',
            'descricao' => $ativ['nome'] . ' foi aprovado',
            'data' => $ativ['data_cadastro'],
            'icon' => 'fa-check-circle',
            'class' => 'approved'
        ];
    }
    
    // Ordenar atividades por data (mais recente primeiro)
    usort($atividades_recentes, function($a, $b) {
        return strtotime($b['data']) - strtotime($a['data']);
    });
    
    // Limitar a 5 atividades mais recentes
    $atividades_recentes = array_slice($atividades_recentes, 0, 5);
    
} catch (PDOException $e) {
    $candidatos = [];
    $stats = ['candidatos' => 0, 'vagas' => 0, 'entrevistas' => 0, 'analise' => 0];
    $atividades_recentes = [];
    error_log("Erro ao buscar candidatos: " . $e->getMessage());
}

// Função para formatar status
function formatarStatus($status) {
    $statusMap = [
        'novo' => ['class' => 'status-new', 'text' => 'Novo'],
        'em_analise' => ['class' => 'status-review', 'text' => 'Em análise'],
        'entrevista_agendada' => ['class' => 'status-interview', 'text' => 'Entrevista agendada'],
        'aprovado' => ['class' => 'status-approved', 'text' => 'Aprovado'],
        'rejeitado' => ['class' => 'status-rejected', 'text' => 'Rejeitado']
    ];
    
    return $statusMap[$status] ?? ['class' => 'status-new', 'text' => 'Novo'];
}

// Função para formatar data
function formatarData($data) {
    $agora = new DateTime();
    $dataObj = new DateTime($data);
    $diff = $agora->diff($dataObj);
    
    if ($diff->days == 0) {
        return 'Hoje';
    } elseif ($diff->days == 1) {
        return 'Ontem';
    } elseif ($diff->days < 7) {
        return $diff->days . ' dias atrás';
    } else {
        return $dataObj->format('d/m/Y');
    }
}

// Função para formatar tempo de atividade
function formatarTempoAtividade($data) {
    $agora = new DateTime();
    $dataObj = new DateTime($data);
    $diff = $agora->diff($dataObj);
    
    if ($diff->i < 1 && $diff->h == 0 && $diff->days == 0) {
        return 'Agora mesmo';
    } elseif ($diff->i < 60 && $diff->h == 0 && $diff->days == 0) {
        return $diff->i . ' min atrás';
    } elseif ($diff->h < 24 && $diff->days == 0) {
        return $diff->h . 'h atrás';
    } elseif ($diff->days == 1) {
        return '1 dia atrás';
    } elseif ($diff->days < 7) {
        return $diff->days . ' dias atrás';
    } else {
        return $dataObj->format('d/m/Y');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Painel RH | ENIAC LINK+ - Gestão de Candidatos</title>
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
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #34609b 100%);
      background-size: 400% 400%;
      animation: gradientShift 20s ease infinite;
      color: #333;
      line-height: 1.6;
      min-height: 100vh;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Header Profissional */
    header {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #34609b 100%);
      padding: 0;
      color: white;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
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
      display: flex !important;
      flex-direction: row !important;
      gap: 0.5rem;
      position: relative;
      align-items: center;
      flex-wrap: nowrap !important;
    }

    /* Garantir navegação horizontal */
    #nav {
      display: flex !important;
      flex-direction: row !important;
      flex-wrap: nowrap !important;
      align-items: center !important;
      justify-content: center !important;
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
        gap: 1.5rem;
        padding: 1rem;
      }

      nav {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
      }

      #nav {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        justify-content: center !important;
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

      .mobile-menu-btn {
        display: none;
      }
    }

    @media (max-width: 480px) {
      nav {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        gap: 0.3rem;
        justify-content: center;
        width: 100%;
      }

      #nav {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        justify-content: center !important;
      }

      nav a {
        padding: 0.5rem 0.8rem;
        font-size: 0.75rem;
        border-radius: 18px;
        flex: 0 0 auto;
        white-space: nowrap;
      }

      nav a i {
        font-size: 0.9rem;
      }

      .header-container {
        padding: 0.8rem;
      }
    }

      .header-container {
        padding: 1rem;
      }
    }

    /* Hero Section */
    .hero {
      background: linear-gradient(135deg, #0056b3, #007bff, #0099ff);
      background-size: 400% 400%;
      animation: gradientShift 8s ease infinite;
      color: white;
      padding: 60px 20px 80px;
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
      font-size: 2.8rem;
      margin-bottom: 20px;
      font-weight: 700;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .hero p {
      font-size: 1.2rem;
      opacity: 0.95;
      font-weight: 300;
      margin-bottom: 20px;
    }

    .admin-badge {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      padding: 15px 25px;
      border-radius: 15px;
      display: inline-flex;
      align-items: center;
      gap: 15px;
      margin-top: 25px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .admin-badge i {
      font-size: 1.2rem;
      color: #00d4ff;
    }

    .admin-info {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .admin-name {
      font-weight: 700;
      font-size: 1rem;
      color: white;
    }

    .admin-email {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.8);
      font-weight: 400;
    }

    .admin-actions {
      margin-top: 20px;
      margin-bottom: 25px;
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .btn-profile {
      background: rgba(0, 212, 255, 0.15);
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 25px;
      font-size: 0.9rem;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s ease;
      border: 1px solid rgba(0, 212, 255, 0.3);
    }

    .btn-profile:hover {
      background: rgba(0, 212, 255, 0.25);
      border-color: rgba(0, 212, 255, 0.5);
      transform: translateY(-2px);
      text-decoration: none;
      color: white;
    }

    .btn-logout {
      background: rgba(255, 255, 255, 0.1);
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 25px;
      font-size: 0.9rem;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s ease;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .btn-logout:hover {
      background: rgba(255, 77, 77, 0.2);
      border-color: rgba(255, 77, 77, 0.4);
      transform: translateY(-2px);
      text-decoration: none;
      color: white;
    }

    @media (max-width: 768px) {
      .hero {
        padding: 50px 20px 70px;
      }
      
      .hero h1 {
        font-size: 2.2rem;
      }
      
      .hero p {
        font-size: 1.1rem;
      }
      
      .admin-actions {
        justify-content: center;
        margin-bottom: 30px;
      }
      
      .btn-profile,
      .btn-logout {
        padding: 12px 18px;
        font-size: 0.85rem;
      }
    }

    /* Dashboard Stats */
    .dashboard-stats {
      max-width: 1200px;
      margin: 40px auto 60px;
      padding: 0 20px;
      position: relative;
      z-index: 10;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(15px);
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, #1e3c72, #2a5298, #34609b);
      background-size: 200% 200%;
      animation: gradientShift 8s ease infinite;
    }

    .stat-card:nth-child(1) .stat-icon { 
      background: linear-gradient(135deg, #10b981, #059669); 
    }
    .stat-card:nth-child(2) .stat-icon { 
      background: linear-gradient(135deg, #3b82f6, #2563eb); 
    }
    .stat-card:nth-child(3) .stat-icon { 
      background: linear-gradient(135deg, #f59e0b, #d97706); 
    }
    .stat-card:nth-child(4) .stat-icon { 
      background: linear-gradient(135deg, #8b5cf6, #7c3aed); 
    }

    .stat-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
    }

    .stat-icon {
      width: 70px;
      height: 70px;
      margin: 0 auto 25px;
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
      margin-bottom: 0;
      letter-spacing: 0.5px;
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      color: #333;
      display: block;
      margin-bottom: 8px;
    }

    .stat-label {
      color: #666;
      font-weight: 500;
      font-size: 1rem;
    }

    .stat-change {
      font-size: 0.85rem;
      margin-top: 8px;
      padding: 4px 8px;
      border-radius: 12px;
      font-weight: 600;
    }

    .stat-change.positive {
      background: #d4edda;
      color: #155724;
    }

    .stat-change.negative {
      background: #f8d7da;
      color: #721c24;
    }

    /* Main Content */
    .main-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px 80px;
    }

    .content-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 40px;
      margin-bottom: 40px;
    }

    @media (max-width: 968px) {
      .content-grid {
        grid-template-columns: 1fr;
        gap: 30px;
      }
    }

    /* Quick Actions */
    .quick-actions {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
      margin-bottom: 40px;
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .section-title {
      font-size: 1.8rem;
      color: #1e293b;
      margin-bottom: 30px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 15px;
      letter-spacing: -0.5px;
    }

    .actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }

    .action-card {
      padding: 25px 20px;
      border: 2px solid #f0f0f0;
      border-radius: 12px;
      text-align: center;
      transition: all 0.3s ease;
      cursor: pointer;
      text-decoration: none;
      color: inherit;
      display: block;
    }

    .action-card:hover {
      border-color: #0056b3;
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0, 86, 179, 0.15);
    }

    .action-icon {
      width: 50px;
      height: 50px;
      margin: 0 auto 15px;
      background: linear-gradient(135deg, #0056b3, #007bff);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.2rem;
    }

    .action-title {
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
    }

    .action-desc {
      color: #666;
      font-size: 0.9rem;
      line-height: 1.4;
    }

    /* Candidates Table */
    .candidates-section {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .table-controls {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
      flex-wrap: wrap;
      gap: 15px;
    }

    .search-box {
      display: flex;
      align-items: center;
      background: #f8f9fa;
      border-radius: 8px;
      padding: 10px 15px;
      border: 1px solid #e9ecef;
      min-width: 250px;
    }

    .search-box input {
      border: none;
      background: none;
      outline: none;
      margin-left: 10px;
      font-size: 1rem;
      flex-grow: 1;
    }

    .filter-btn {
      background: linear-gradient(135deg, #0056b3, #007bff);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .filter-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(0, 86, 179, 0.3);
    }

    .candidates-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .candidates-table th,
    .candidates-table td {
      padding: 15px 12px;
      text-align: left;
      border-bottom: 1px solid #f0f0f0;
    }

    .candidates-table th {
      background: #f8f9fa;
      font-weight: 600;
      color: #333;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .candidates-table tbody tr:hover {
      background: #f8f9fa;
    }

    .candidate-name {
      font-weight: 600;
      color: #333;
    }

    .candidate-email {
      color: #666;
      font-size: 0.9rem;
    }

    .status-badge {
      padding: 4px 12px;
      border-radius: 15px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .status-new { background: #e3f2fd; color: #0056b3; }
    .status-review { background: #fff3cd; color: #856404; }
    .status-interview { background: #d4edda; color: #155724; }
    .status-approved { background: #d1ecf1; color: #0c5460; }
    .status-rejected { background: #f8d7da; color: #721c24; }

    .action-buttons {
      display: flex;
      gap: 8px;
    }

    .btn-sm {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 0.8rem;
      transition: all 0.3s ease;
    }

    .btn-view {
      background: #e3f2fd;
      color: #0056b3;
    }

    .btn-contact {
      background: #e8f5e8;
      color: #2e7d32;
    }

    .btn-sm:hover {
      transform: translateY(-1px);
    }

    /* Recent Activity */
    .recent-activity {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .empty-state {
      text-align: center;
      padding: 60px 40px;
      color: #64748b;
    }

    .empty-state i {
      display: block;
      margin-bottom: 20px;
    }

    .empty-state h3 {
      margin-bottom: 10px;
      font-weight: 600;
    }

    .empty-state p {
      margin-bottom: 0;
      opacity: 0.8;
    }

    .activity-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 15px 0;
      border-bottom: 1px solid #f0f0f0;
      transition: all 0.3s ease;
    }

    .activity-item:hover {
      background: #f8fafc;
      border-radius: 8px;
      padding: 15px 12px;
      margin: 0 -12px;
    }

    .activity-item:last-child {
      border-bottom: none;
    }

    .activity-icon {
      width: 45px;
      height: 45px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
      color: white;
      flex-shrink: 0;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .activity-icon.new { 
      background: linear-gradient(135deg, #10b981, #059669); 
    }
    .activity-icon.interview { 
      background: linear-gradient(135deg, #3b82f6, #2563eb); 
    }
    .activity-icon.approved { 
      background: linear-gradient(135deg, #8b5cf6, #7c3aed); 
    }

    .activity-content {
      flex-grow: 1;
    }

    .activity-title {
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 4px;
      font-size: 1rem;
    }

    .activity-desc {
      color: #64748b;
      font-size: 0.9rem;
      line-height: 1.4;
    }

    .activity-time {
      color: #94a3b8;
      font-size: 0.85rem;
      font-weight: 500;
      min-width: 80px;
      text-align: right;
    }
    /* Footer */
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

    /* Responsive Tables */
    @media (max-width: 768px) {
      .candidates-table {
        font-size: 0.9rem;
      }
      
      .candidates-table th,
      .candidates-table td {
        padding: 10px 8px;
      }
      
      .action-buttons {
        flex-direction: column;
        gap: 4px;
      }
      
      .table-controls {
        flex-direction: column;
        align-items: stretch;
      }
      
      .search-box {
        min-width: auto;
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
    /* Floating shapes background effect */
    .floating-shapes {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: -1;
    }

    .shape {
      position: absolute;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 50%;
      animation: float 25s infinite linear;
    }

    .shape:nth-child(1) {
      width: 120px;
      height: 120px;
      top: 20%;
      left: 10%;
      animation-delay: 0s;
    }

    .shape:nth-child(2) {
      width: 160px;
      height: 160px;
      top: 60%;
      left: 80%;
      animation-delay: 8s;
    }

    .shape:nth-child(3) {
      width: 100px;
      height: 100px;
      top: 80%;
      left: 20%;
      animation-delay: 16s;
    }

    @keyframes float {
      0% {
        transform: translateY(0px) rotate(0deg);
        opacity: 0.6;
      }
      50% {
        transform: translateY(-120px) rotate(180deg);
        opacity: 0.3;
      }
      100% {
        transform: translateY(0px) rotate(360deg);
        opacity: 0.6;
      }
    }

    /* Smooth scrolling */
    html {
      scroll-behavior: smooth;
    }
  </style>
</head>
<body>
  <div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
  </div>
  
  <header>
    <div class="header-container">
      <div class="logo-section">
        <img src="./imagens/Logoindex.jpg" alt="Logo da empresa" class="logo-header">
      </div>
      <nav id="nav" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; align-items: center !important; justify-content: center !important; gap: 0.5rem;">
        <a href="index.php"><i class="fas fa-home"></i> Início</a>
        <a href="vagas.php"><i class="fas fa-briefcase"></i> Vagas</a>
        <a href="cadastro.php"><i class="fas fa-user-plus"></i> Cadastrar</a>
        <a href="admin.php" class="active"><i class="fas fa-user-shield"></i> Dashboard</a>
      </nav>
    </div>
  </header>

  <section class="hero">
    <div class="hero-content">
      <h1><i class="fas fa-chart-line"></i> Painel do RH</h1>
      <p>Central de controle completa para gestão de candidatos e processos seletivos</p>
      <div class="admin-badge">
        <i class="fas fa-user-shield"></i>
        <div class="admin-info">
          <span class="admin-name"><?php echo htmlspecialchars($admin_nome); ?></span>
          <?php if (!empty($admin_email)): ?>
          <span class="admin-email"><?php echo htmlspecialchars($admin_email); ?></span>
          <?php endif; ?>
        </div>
      </div>
      <div class="admin-actions">
        <a href="update_admin_profile.php" class="btn-profile">
          <i class="fas fa-user-edit"></i> Editar Perfil
        </a>
        <a href="logout_admin.php" class="btn-logout">
          <i class="fas fa-sign-out-alt"></i> Sair do Sistema
        </a>
      </div>
    </div>
  </section>

  <!-- Dashboard Stats -->
  <section class="dashboard-stats">
    <div class="stats-grid">
      <div class="stat-card fade-in-up">
        <div class="stat-icon">
          <i class="fas fa-users"></i>
        </div>
        <span class="stat-number"><?php echo $stats['candidatos']; ?></span>
        <span class="stat-label">Candidatos Ativos</span>
        <div class="stat-change positive">Total cadastrados</div>
      </div>
      
      <div class="stat-card fade-in-up">
        <div class="stat-icon">
          <i class="fas fa-briefcase"></i>
        </div>
        <span class="stat-number"><?php echo $stats['vagas']; ?></span>
        <span class="stat-label">Vagas Abertas</span>
        <div class="stat-change positive">Vagas ativas</div>
      </div>
      
      <div class="stat-card fade-in-up">
        <div class="stat-icon">
          <i class="fas fa-calendar-check"></i>
        </div>
        <span class="stat-number"><?php echo $stats['entrevistas']; ?></span>
        <span class="stat-label">Entrevistas Agendadas</span>
        <div class="stat-change positive">Aguardando realização</div>
      </div>
      
      <div class="stat-card fade-in-up">
        <div class="stat-icon">
          <i class="fas fa-user-check"></i>
        </div>
        <span class="stat-number"><?php echo $stats['analise']; ?></span>
        <span class="stat-label">Em Análise</span>
        <div class="stat-change <?php echo $stats['analise'] > 0 ? 'negative' : 'positive'; ?>">
          <?php echo $stats['analise'] > 0 ? 'Aguardando análise' : 'Nenhum pendente'; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Quick Actions -->
    <section class="quick-actions fade-in-up">
      <h2 class="section-title">
        <i class="fas fa-bolt"></i>
        Ações Rápidas
      </h2>
      
      <div class="actions-grid">
        <a href="https://docs.google.com/spreadsheets/d/1s0k7SrKQQtJFZb1OowF1DWbTBynAhbHhVLrcUNM8194/edit?usp=sharing" target="_blank" class="action-card">
          <div class="action-icon">
            <i class="fas fa-table"></i>
          </div>
          <div class="action-title">Planilha de Candidatos</div>
          <div class="action-desc">Visualizar e gerenciar todos os dados dos candidatos</div>
        </a>
        
        <a href="curriculos.php" class="action-card">
          <div class="action-icon">
            <i class="fas fa-file-pdf"></i>
          </div>
          <div class="action-title">Pasta de Currículos</div>
          <div class="action-desc">Acessar todos os currículos enviados em PDF</div>
        </a>
        
        <a href="https://meet.google.com" target="_blank" class="action-card">
          <div class="action-icon">
            <i class="fas fa-video"></i>
          </div>
          <div class="action-title">Google Meet</div>
          <div class="action-desc">Iniciar reunião para entrevistas online</div>
        </a>
        
        <a href="https://calendar.google.com" target="_blank" class="action-card">
          <div class="action-icon">
            <i class="fas fa-calendar-alt"></i>
          </div>
          <div class="action-title">Agenda de Entrevistas</div>
          <div class="action-desc">Gerenciar horários e compromissos</div>
        </a>
        
        <a href="mailto:" class="action-card">
          <div class="action-icon">
            <i class="fas fa-envelope"></i>
          </div>
          <div class="action-title">Enviar E-mails</div>
          <div class="action-desc">Comunicação direta com candidatos</div>
        </a>
        
        <a href="gerenciar_vagas.php" class="action-card">
          <div class="action-icon">
            <i class="fas fa-briefcase"></i>
          </div>
          <div class="action-title">Gerenciar Vagas</div>
          <div class="action-desc">Criar, editar e monitorar vagas ativas</div>
        </a>
        
        <a href="gerenciar_contatos.php" class="action-card">
          <div class="action-icon">
            <i class="fas fa-comments"></i>
          </div>
          <div class="action-title">Gerenciar Contatos</div>
          <div class="action-desc">Visualizar e responder mensagens do "Fale Conosco"</div>
        </a>
      </div>
    </section>

    <div class="content-grid">
      <!-- Candidates Section -->
      <section class="candidates-section fade-in-up">
        <h2 class="section-title">
          <i class="fas fa-users"></i>
          Candidatos Recentes
        </h2>
        
        <div class="table-controls">
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Buscar candidato..." id="searchCandidates">
          </div>
          <div style="display: flex; gap: 10px;">
            <button class="filter-btn" onclick="atualizarCandidatos()" id="btnAtualizar">
              <i class="fas fa-sync-alt"></i> Atualizar
            </button>
            <button class="filter-btn" onclick="exportData()">
              <i class="fas fa-download"></i> Exportar Dados
            </button>
          </div>
        </div>
        
        <table class="candidates-table">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Vaga</th>
              <th>Status</th>
              <th>Data</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody id="candidatesTableBody">
            <?php if (empty($candidatos)): ?>
              <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #666;">
                  <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.3;"></i>
                  <p style="font-size: 1.1rem; margin-bottom: 10px;">Nenhum candidato encontrado</p>
                  <p style="font-size: 0.9rem; opacity: 0.7;">Os candidatos aparecerão aqui quando se cadastrarem no sistema</p>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($candidatos as $candidato): ?>
                <?php 
                  $statusInfo = formatarStatus($candidato['status']);
                  $vaga = $candidato['vaga_titulo'] ?? 'Cadastro geral';
                  $dataFormatada = formatarData($candidato['data_cadastro']);
                ?>
                <tr>
                  <td>
                    <div class="candidate-name"><?php echo htmlspecialchars($candidato['nome']); ?></div>
                    <div class="candidate-email"><?php echo htmlspecialchars($candidato['email']); ?></div>
                  </td>
                  <td><?php echo htmlspecialchars($vaga); ?></td>
                  <td>
                    <span class="status-badge <?php echo $statusInfo['class']; ?>">
                      <?php echo $statusInfo['text']; ?>
                    </span>
                  </td>
                  <td><?php echo $dataFormatada; ?></td>
                  <td>
                    <div class="action-buttons">
                      <button class="btn-sm btn-view" onclick="viewCandidate(<?php echo $candidato['id']; ?>)">
                        <i class="fas fa-eye"></i> Ver
                      </button>
                      <button class="btn-sm btn-contact" onclick="contactCandidate(<?php echo $candidato['id']; ?>, '<?php echo addslashes($candidato['nome']); ?>', '<?php echo addslashes($candidato['email']); ?>')">
                        <i class="fas fa-phone"></i> Contato
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <!-- Recent Activity -->
      <section class="recent-activity fade-in-up">
        <h2 class="section-title">
          <i class="fas fa-clock"></i>
          Atividades Recentes
        </h2>
        
        <?php if (empty($atividades_recentes)): ?>
          <div class="empty-state">
            <i class="fas fa-clock" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.3; color: #64748b;"></i>
            <h3 style="color: #64748b; margin-bottom: 10px; font-size: 1.2rem;">Nenhuma atividade recente</h3>
            <p style="color: #94a3b8; font-size: 1rem; line-height: 1.5;">
              As atividades aparecerão aqui quando candidatos se cadastrarem, <br>
              entrevistas forem agendadas ou aprovações forem realizadas.
            </p>
          </div>
        <?php else: ?>
          <?php foreach ($atividades_recentes as $atividade): ?>
            <div class="activity-item">
              <div class="activity-icon <?php echo $atividade['class']; ?>">
                <i class="fas <?php echo $atividade['icon']; ?>"></i>
              </div>
              <div class="activity-content">
                <div class="activity-title"><?php echo htmlspecialchars($atividade['titulo']); ?></div>
                <div class="activity-desc"><?php echo htmlspecialchars($atividade['descricao']); ?></div>
              </div>
              <div class="activity-time"><?php echo formatarTempoAtividade($atividade['data']); ?></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>
    </div>
  </div>
  <footer>
    <div class="footer-container">
      <div class="footer-section">
        <h3>ENIAC LINK+</h3>
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
          <li><a href="#">Central de Ajuda</a></li>
          <li><a href="#">Termos de Uso</a></li>
          <li><a href="#">Política de Privacidade</a></li>
          <li><a href="#">Fale Conosco</a></li>
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
      <p>&copy; 2025 ENIAC LINK+ — Todos os direitos reservados. Desenvolvido com ❤️ para conectar pessoas.</p>
    </div>
  </footer>

  <script>
    // Função para atualizar lista de candidatos
    function atualizarCandidatos() {
      const btnAtualizar = document.getElementById('btnAtualizar');
      const icon = btnAtualizar.querySelector('i');
      
      // Animação de carregamento
      icon.classList.remove('fa-sync-alt');
      icon.classList.add('fa-spinner', 'fa-spin');
      btnAtualizar.disabled = true;
      
      fetch('get_candidatos.php', {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const tbody = document.getElementById('candidatesTableBody');
          
          if (data.candidatos.length === 0) {
            tbody.innerHTML = `
              <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #666;">
                  <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.3;"></i>
                  <p style="font-size: 1.1rem; margin-bottom: 10px;">Nenhum candidato encontrado</p>
                  <p style="font-size: 0.9rem; opacity: 0.7;">Os candidatos aparecerão aqui quando se cadastrarem no sistema</p>
                </td>
              </tr>
            `;
          } else {
            tbody.innerHTML = data.candidatos.map(candidato => `
              <tr>
                <td>
                  <div class="candidate-name">${candidato.nome}</div>
                  <div class="candidate-email">${candidato.email}</div>
                </td>
                <td>${candidato.vaga}</td>
                <td>
                  <span class="status-badge ${candidato.status.class}">
                    ${candidato.status.text}
                  </span>
                </td>
                <td>${candidato.data}</td>
                <td>
                  <div class="action-buttons">
                    <button class="btn-sm btn-view" onclick="viewCandidate(${candidato.id})">
                      <i class="fas fa-eye"></i> Ver
                    </button>
                    <button class="btn-sm btn-contact" onclick="contactCandidate(${candidato.id}, '${candidato.nome.replace(/'/g, "\\'")}', '${candidato.email}')">
                      <i class="fas fa-phone"></i> Contato
                    </button>
                  </div>
                </td>
              </tr>
            `).join('');
          }
          
          // Mostrar notificação de sucesso
          showNotification('Lista de candidatos atualizada!', 'success');
        } else {
          showNotification('Erro ao atualizar candidatos', 'error');
        }
      })
      .catch(error => {
        console.error('Erro:', error);
        showNotification('Erro ao conectar com o servidor', 'error');
      })
      .finally(() => {
        // Restaurar botão
        icon.classList.remove('fa-spinner', 'fa-spin');
        icon.classList.add('fa-sync-alt');
        btnAtualizar.disabled = false;
      });
    }

    // Função para mostrar notificações
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 10000;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      `;
      
      if (type === 'success') {
        notification.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
      } else if (type === 'error') {
        notification.style.background = 'linear-gradient(135deg, #dc3545, #e74c3c)';
      } else {
        notification.style.background = 'linear-gradient(135deg, #007bff, #0056b3)';
      }
      
      notification.textContent = message;
      document.body.appendChild(notification);
      
      // Remover após 3 segundos
      setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
          document.body.removeChild(notification);
        }, 300);
      }, 3000);
    }

    // Busca de candidatos
    document.getElementById('searchCandidates').addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const rows = document.querySelectorAll('#candidatesTableBody tr');
      
      rows.forEach(row => {
        const name = row.querySelector('.candidate-name').textContent.toLowerCase();
        const email = row.querySelector('.candidate-email').textContent.toLowerCase();
        const position = row.cells[1].textContent.toLowerCase();
        
        if (name.includes(searchTerm) || email.includes(searchTerm) || position.includes(searchTerm)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });

    // Funções dos botões
    function viewCandidate(id) {
      alert(`Visualizando detalhes do candidato ${id}\n\nEsta funcionalidade direcionaria para uma página detalhada com:\n• Dados pessoais completos\n• Currículo anexado\n• Histórico de comunicações\n• Avaliações e notas\n• Status do processo seletivo`);
    }

    function contactCandidate(id, name, email) {
      const phone = prompt(`Contatar ${name}\n\nOpções de contato:\n1. WhatsApp: (11) 99999-9999\n2. E-mail: ${email}\n3. Telefone: (11) 99999-9999\n\nDigite o número da opção ou 'cancelar':`);
      
      if (phone && phone !== 'cancelar') {
        switch(phone) {
          case '1':
            window.open(`https://wa.me/5511999999999?text=Olá ${name}, tudo bem? Sou do RH da ENIAC LINK+ e gostaria de conversar sobre sua candidatura.`, '_blank');
            break;
          case '2':
            window.open(`mailto:${email}?subject=ENIAC LINK+ - Processo Seletivo&body=Olá ${name},%0D%0A%0D%0ATudo bem? Sou do RH da ENIAC LINK+ e gostaria de conversar sobre sua candidatura.%0D%0A%0D%0AAtenciosamente,%0D%0AEquipe RH ENIAC LINK+`, '_blank');
            break;
          case '3':
            alert(`Ligando para ${name}...\nTelefone: (11) 99999-9999`);
            break;
          default:
            alert('Opção inválida!');
        }
      }
    }

    function exportData() {
      alert('Exportação de Dados\n\nFormatos disponíveis:\n• Excel (.xlsx)\n• CSV (.csv)\n• PDF (relatório)\n\nEsta funcionalidade geraria um arquivo com todos os dados dos candidatos para análise offline.');
      
      // Simular download - em produção, isso faria uma requisição para um endpoint PHP
      const rows = document.querySelectorAll('#candidatesTableBody tr');
      let csvContent = 'Nome,Email,Vaga,Status,Data\n';
      
      rows.forEach(row => {
        if (row.cells.length > 1) { // Ignora a linha de "nenhum candidato"
          const nome = row.querySelector('.candidate-name')?.textContent || '';
          const email = row.querySelector('.candidate-email')?.textContent || '';
          const vaga = row.cells[1]?.textContent || '';
          const status = row.cells[2]?.textContent || '';
          const data = row.cells[3]?.textContent || '';
          csvContent += `"${nome}","${email}","${vaga}","${status}","${data}"\n`;
        }
      });
      
      const link = document.createElement('a');
      link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvContent);
      link.download = 'candidatos_eniac_link.csv';
      link.click();
    }

    // Atualizar estatísticas em tempo real (simulação)
    function updateStats() {
      const stats = document.querySelectorAll('.stat-number');
      stats.forEach((stat, index) => {
        const currentValue = parseInt(stat.textContent);
        // Simular pequenas variações
        if (Math.random() > 0.8) {
          const change = Math.random() > 0.5 ? 1 : -1;
          stat.textContent = Math.max(0, currentValue + change);
        }
      });
    }

    // Adicionar nova atividade (simulação)
    function addNewActivity() {
      const activities = [
        { icon: 'user-plus', title: 'Nova candidatura', desc: 'Novo candidato se inscreveu', type: 'new' },
        { icon: 'calendar-check', title: 'Entrevista agendada', desc: 'Nova entrevista marcada', type: 'interview' },
        { icon: 'check-circle', title: 'Candidato aprovado', desc: 'Processo concluído com sucesso', type: 'approved' },
        { icon: 'file-upload', title: 'Currículo recebido', desc: 'Novo arquivo enviado', type: 'new' }
      ];
      
      if (Math.random() > 0.7) {
        const activity = activities[Math.floor(Math.random() * activities.length)];
        const activityList = document.querySelector('.recent-activity');
        const newActivity = document.createElement('div');
        newActivity.className = 'activity-item';
        newActivity.innerHTML = `
          <div class="activity-icon ${activity.type}">
            <i class="fas fa-${activity.icon}"></i>
          </div>
          <div class="activity-content">
            <div class="activity-title">${activity.title}</div>
            <div class="activity-desc">${activity.desc}</div>
          </div>
          <div class="activity-time">Agora</div>
        `;
        
        // Inserir no topo da lista
        const firstActivity = activityList.querySelector('.activity-item');
        if (firstActivity) {
          activityList.insertBefore(newActivity, firstActivity);
        }
        
        // Remover atividades antigas (manter apenas 5)
        const allActivities = activityList.querySelectorAll('.activity-item');
        if (allActivities.length > 5) {
          allActivities[allActivities.length - 1].remove();
        }
      }
    }

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

    // Atualizar dados periodicamente (apenas para demonstração)
    setInterval(updateStats, 30000); // A cada 30 segundos
    setInterval(addNewActivity, 45000); // A cada 45 segundos

    // Notificações desktop (se permitido)
    if ('Notification' in window && Notification.permission === 'granted') {
      setInterval(() => {
        if (Math.random() > 0.9) {
          new Notification('ENIAC LINK+ RH', {
            body: 'Nova candidatura recebida!',
            icon: '/favicon.ico'
          });
        }
      }, 60000);
    }

    // Solicitar permissão para notificações
    if ('Notification' in window && Notification.permission === 'default') {
      Notification.requestPermission();
    }

    // Funções para gerenciamento de vagas
    function criarVaga() {
      showNotification('Redirecionando para criação de vaga...', 'info');
      
      // Simular modal ou redirecionamento
      setTimeout(() => {
        // Aqui você pode abrir um modal ou redirecionar para página específica
        if (confirm('🆕 CRIAR NOVA VAGA\n\nDeseja ser redirecionado para o formulário de criação de vagas?\n\n• Definir cargo e requisitos\n• Configurar benefícios\n• Publicar vaga ativa')) {
          window.location.href = 'criar_vaga.php';
        }
      }, 500);
    }

    function editarVagas() {
      showNotification('Carregando vagas para edição...', 'info');
      
      setTimeout(() => {
        if (confirm('✏️ EDITAR VAGAS EXISTENTES\n\nDeseja ver a lista de vagas para edição?\n\n• Modificar descrições\n• Atualizar requisitos\n• Ajustar salários\n• Pausar/Reativar vagas')) {
          window.location.href = 'vagas.php?action=edit';
        }
      }, 500);
    }

    function monitorarVagas() {
      showNotification('Abrindo painel de monitoramento...', 'info');
      
      setTimeout(() => {
        if (confirm('📊 MONITORAR VAGAS ATIVAS\n\nDeseja acessar o dashboard de monitoramento?\n\n• Visualizar estatísticas\n• Acompanhar candidaturas\n• Métricas de performance\n• Relatórios detalhados')) {
          window.location.href = 'vagas.php?action=monitor';
        }
      }, 500);
    }

    // Função para mostrar notificações elegantes
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        z-index: 10000;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        background: ${type === 'success' ? 'linear-gradient(135deg, #28a745, #20c997)' : 
                     type === 'error' ? 'linear-gradient(135deg, #dc3545, #e74c3c)' : 
                     'linear-gradient(135deg, #007bff, #0056b3)'};
      `;
      
      notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 10px;">
          <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
          <span>${message}</span>
        </div>
      `;
      
      document.body.appendChild(notification);
      
      // Animação de entrada
      setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
      }, 100);
      
      // Remoção automática
      setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
          if (document.body.contains(notification)) {
            document.body.removeChild(notification);
          }
        }, 300);
      }, 4000);
    }

    // Animações de entrada melhoradas para todos os elementos
    document.addEventListener('DOMContentLoaded', function() {
      // Animar estatísticas com contador
      const statNumbers = document.querySelectorAll('.stat-number');
      statNumbers.forEach((stat, index) => {
        const finalValue = parseInt(stat.textContent) || 0;
        stat.textContent = '0';
        
        setTimeout(() => {
          let currentValue = 0;
          const increment = finalValue / 40;
          const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
              stat.textContent = finalValue;
              clearInterval(timer);
            } else {
              stat.textContent = Math.floor(currentValue);
            }
          }, 30);
        }, index * 150);
      });

      // Animar cards das estatísticas
      const statCards = document.querySelectorAll('.stat-card');
      statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px) scale(0.9)';
        
        setTimeout(() => {
          card.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0) scale(1)';
        }, index * 100 + 300);
      });

      // Animar seções principais
      const sections = document.querySelectorAll('.candidates-section, .recent-activity, .quick-actions');
      sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(40px)';
        
        setTimeout(() => {
          section.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
          section.style.opacity = '1';
          section.style.transform = 'translateY(0)';
        }, index * 200 + 800);
      });

      // Animar atividades
      const activities = document.querySelectorAll('.activity-item');
      activities.forEach((activity, index) => {
        activity.style.opacity = '0';
        activity.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
          activity.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
          activity.style.opacity = '1';
          activity.style.transform = 'translateX(0)';
        }, index * 100 + 1200);
      });
    });

    // Auto-refresh das atividades a cada 5 minutos
    setInterval(function() {
      // Apenas recarrega a página se houver mudanças reais
      fetch(window.location.href, { 
        method: 'HEAD',
        cache: 'no-cache'
      }).then(() => {
        console.log('✅ Dados atualizados - ' + new Date().toLocaleTimeString());
      });
    }, 300000); // 5 minutos
  </script>

</body>
</html>
