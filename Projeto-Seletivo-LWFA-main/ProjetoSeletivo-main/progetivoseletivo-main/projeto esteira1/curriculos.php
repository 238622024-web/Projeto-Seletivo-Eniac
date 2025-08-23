<?php
require_once 'config.php';
// --- INÍCIO IA VAGAS ---
// Buscar vagas e palavras-chave para análise automática
$vagas_ia = [];
try {
  $database = new Database();
  $pdo = $database->connect();
  $stmt = $pdo->query("SELECT id, titulo, palavras_chave FROM vagas WHERE status = 'ativa' AND palavras_chave IS NOT NULL AND palavras_chave != ''");
  $vagas_ia = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}
// --- FIM IA VAGAS ---
// Exportação para CSV deve ser processada antes de qualquer saída HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_csv'])) {
    require_once 'config.php';
    $database = new Database();
    $pdo = $database->connect();
    $where_conditions = ["curriculo_arquivo IS NOT NULL AND curriculo_arquivo != ''"];
    $params = [];
    if (!empty($_POST['palavras_chave'])) {
        $palavras = array_filter(array_map('trim', explode(',', mb_strtolower($_POST['palavras_chave'], 'UTF-8'))));
        foreach ($palavras as $kw) {
            $where_conditions[] = "(LOWER(nome) LIKE ? OR LOWER(email) LIKE ? OR LOWER(escolaridade) LIKE ? OR LOWER(experiencia) LIKE ? OR LOWER(pdf_text) LIKE ?)";
            for ($i=0; $i<5; $i++) $params[] = "%$kw%";
        }
    }
    $where_clause = "WHERE " . implode(" AND ", $where_conditions);
    $sql = "SELECT nome, email, telefone, escolaridade, experiencia FROM candidatos $where_clause ORDER BY data_cadastro DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (ob_get_level()) ob_end_clean();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=curriculos_filtrados.csv');
    header('Pragma: no-cache');
    header('Expires: 0');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Nome', 'E-mail', 'Telefone', 'Escolaridade', 'Experiência']);
    foreach ($candidatos as $candidato) {
        fputcsv($output, [
            $candidato['nome'] ?? '',
            $candidato['email'] ?? '',
            $candidato['telefone'] ?? '',
            $candidato['escolaridade'] ?? '',
            $candidato['experiencia'] ?? ''
        ]);
    }
    fclose($output);
    exit;
}
?>
<?php
  // Carregar autoload do Composer para PDF Parser
  if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
  }
require_once 'config.php';

// Buscar candidatos com currículos
try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Parâmetros de filtro e busca
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    $per_page = 10;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * $per_page;
    
    // Base da query
    $where_conditions = ["curriculo_arquivo IS NOT NULL AND curriculo_arquivo != ''"];
    $params = [];
    
    // Aplicar filtros
    if (!empty($search)) {
        $where_conditions[] = "(nome LIKE ? OR email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if (!empty($status_filter)) {
        $where_conditions[] = "status = ?";
        $params[] = $status_filter;
    }
    
    if (!empty($date_from)) {
        $where_conditions[] = "DATE(data_cadastro) >= ?";
        $params[] = $date_from;
    }
    
    if (!empty($date_to)) {
        $where_conditions[] = "DATE(data_cadastro) <= ?";
        $params[] = $date_to;
    }
    
    $where_clause = "WHERE " . implode(" AND ", $where_conditions);
    
    // Contar total de registros para paginação
    $count_sql = "SELECT COUNT(*) as total FROM candidatos $where_clause";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_records / $per_page);
    
    // Buscar candidatos com paginação
    $sql = "SELECT c.id, c.nome, c.email, c.telefone, c.curriculo_arquivo, c.data_cadastro, c.status,
                   c.escolaridade, c.experiencia, c.pretensao_salarial,
                   COUNT(cv.id) as total_candidaturas,
                   MAX(cv.data_candidatura) as ultima_candidatura
            FROM candidatos c 
            LEFT JOIN candidaturas cv ON c.id = cv.candidato_id
            $where_clause
            GROUP BY c.id
            ORDER BY c.data_cadastro DESC 
            LIMIT $per_page OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $candidatos_com_curriculo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar estatísticas avançadas
    $stats = [];
    
    // Total de candidatos com currículo
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM candidatos WHERE curriculo_arquivo IS NOT NULL");
    $stmt->execute();
    $stats['total_curriculos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Candidatos por status
    $stmt = $pdo->prepare("
        SELECT status, COUNT(*) as total 
        FROM candidatos 
        WHERE curriculo_arquivo IS NOT NULL 
        GROUP BY status
    ");
    $stmt->execute();
    $stats_por_status = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Candidatos novos (últimos 7 dias)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM candidatos 
        WHERE curriculo_arquivo IS NOT NULL 
        AND data_cadastro >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    $stmt->execute();
    $stats['novos_7_dias'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Média de candidaturas por candidato
    $stmt = $pdo->prepare("
        SELECT AVG(candidaturas) as media
        FROM (
            SELECT COUNT(cv.id) as candidaturas
            FROM candidatos c
            LEFT JOIN candidaturas cv ON c.id = cv.candidato_id
            WHERE c.curriculo_arquivo IS NOT NULL
            GROUP BY c.id
        ) as sub
    ");
    $stmt->execute();
    $stats['media_candidaturas'] = round($stmt->fetch(PDO::FETCH_ASSOC)['media'] ?? 0, 1);
    
} catch (PDOException $e) {
    $candidatos_com_curriculo = [];
    $total_records = 0;
    $total_pages = 0;
    $stats = ['total_curriculos' => 0, 'novos_7_dias' => 0, 'media_candidaturas' => 0];
    $stats_por_status = [];
    error_log("Erro ao buscar currículos: " . $e->getMessage());
}

SessionManager::start();

// Verificar se o usuário está logado
if (!SessionManager::has('admin_logged_in')) {
    header('Location: login_admin.php');
    exit;
}

$admin_nome = SessionManager::get('admin_nome', 'Administrador');

// Buscar currículos e dados dos candidatos
try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Buscar candidatos com currículos
    $sql = "SELECT id, nome, email, curriculo_arquivo, data_cadastro 
            FROM candidatos 
            WHERE curriculo_arquivo IS NOT NULL AND curriculo_arquivo != ''
            ORDER BY data_cadastro DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $candidatos_com_curriculo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $candidatos_com_curriculo = [];
    error_log("Erro ao buscar currículos: " . $e->getMessage());
}

// Verificar arquivos físicos na pasta uploads
$uploads_dir = 'uploads/';
$arquivos_fisicos = [];
if (is_dir($uploads_dir)) {
    $files = scandir($uploads_dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
            $arquivos_fisicos[] = [
                'nome' => $file,
                'tamanho' => filesize($uploads_dir . $file),
                'data_modificacao' => filemtime($uploads_dir . $file)
            ];
        }
    }
}

function formatBytes($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

function formatarStatus($status) {
    $statusMap = [
        'novo' => ['class' => 'status-new', 'text' => 'Novo', 'icon' => 'fas fa-star'],
        'em_analise' => ['class' => 'status-review', 'text' => 'Em Análise', 'icon' => 'fas fa-search'],
        'entrevista_agendada' => ['class' => 'status-interview', 'text' => 'Entrevista', 'icon' => 'fas fa-calendar'],
        'aprovado' => ['class' => 'status-approved', 'text' => 'Aprovado', 'icon' => 'fas fa-check-circle'],
        'rejeitado' => ['class' => 'status-rejected', 'text' => 'Rejeitado', 'icon' => 'fas fa-times-circle']
    ];
    
    return $statusMap[$status] ?? ['class' => 'status-new', 'text' => 'Novo', 'icon' => 'fas fa-star'];
}

function formatarEscolaridade($escolaridade) {
    $escolaridadeMap = [
        'fundamental' => 'Ensino Fundamental',
        'medio' => 'Ensino Médio',
        'superior' => 'Superior',
        'pos_graduacao' => 'Pós-Graduação',
        'mestrado' => 'Mestrado',
        'doutorado' => 'Doutorado'
    ];
    
    return $escolaridadeMap[$escolaridade] ?? 'Não informado';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pasta de Currículos | ENIAC LINK+ - Gestão de Candidatos</title>
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
      background-color: #f8f9fa;
      color: #333;
      line-height: 1.6;
    }

    /* Header */
    header {
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      padding: 1rem 0;
      color: white;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
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
    }

    .page-title h1 {
      color: #1e3c72;
      margin-bottom: 15px;
      font-size: 2.2rem;
      font-weight: 700;
    }

    .page-title p {
      color: #64748b;
      font-size: 1.1rem;
      margin-bottom: 0;
    }

    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
      flex-wrap: wrap;
      gap: 20px;
    }

    .dashboard-title {
      color: #1e293b;
      font-size: 1.8rem;
      font-weight: 700;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .dashboard-title i {
      color: #3b82f6;
    }

    .dashboard-actions {
      display: flex;
      gap: 15px;
      align-items: center;
    }

    .export-btn {
      background: linear-gradient(135deg, #059669, #10b981);
      color: white;
      padding: 12px 24px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .export-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(5, 150, 105, 0.3);
    }

    .refresh-btn {
      background: linear-gradient(135deg, #3b82f6, #2563eb);
      color: white;
      padding: 12px 16px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .refresh-btn:hover {
      transform: translateY(-2px) rotate(180deg);
      box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    .stats-section {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
      background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .stat-trend {
      font-size: 0.85rem;
      font-weight: 600;
      padding: 4px 12px;
      border-radius: 20px;
      background: #ecfdf5;
      color: #059669;
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

    .stat-details {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
      color: #64748b;
    }

    /* Filtros Avançados */
    .filters-section {
      background: linear-gradient(135deg, #ffffff, #f8fafc);
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
      border: 1px solid rgba(255, 255, 255, 0.8);
    }

    .filters-title {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 25px;
      color: #1e293b;
      font-size: 1.2rem;
      font-weight: 600;
    }

    .filters-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 25px;
    }

    .filter-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .filter-label {
      font-weight: 600;
      color: #374151;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .filter-input {
      padding: 12px 16px;
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: white;
    }

    .filter-input:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filters-actions {
      display: flex;
      gap: 15px;
      justify-content: flex-end;
      align-items: center;
    }

    .filter-btn {
      padding: 12px 24px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .filter-btn.primary {
      background: linear-gradient(135deg, #3b82f6, #2563eb);
      color: white;
    }

    .filter-btn.secondary {
      background: #f1f5f9;
      color: #64748b;
      border: 2px solid #e2e8f0;
    }

    .filter-btn:hover {
      transform: translateY(-2px);
    }

    .filter-btn.primary:hover {
      box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    .filter-btn.secondary:hover {
      background: #e2e8f0;
    }

    /* Estilos Profissionais para Tabela */
    .curriculos-table-section {
      background: linear-gradient(135deg, #ffffff, #f8fafc);
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      margin-bottom: 30px;
    }

    .table-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 25px 30px;
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      color: white;
    }

    .table-title {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 1.3rem;
      font-weight: 700;
    }

    .total-count {
      background: rgba(255, 255, 255, 0.2);
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.9rem;
    }

    .table-actions {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .view-toggle {
      display: flex;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      overflow: hidden;
    }

    .view-btn {
      padding: 10px 15px;
      border: none;
      background: transparent;
      color: white;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .view-btn.active {
      background: rgba(255, 255, 255, 0.2);
    }

    .view-btn:hover {
      background: rgba(255, 255, 255, 0.15);
    }

    .items-per-page {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      padding: 8px 15px;
      border-radius: 8px;
      font-size: 0.9rem;
    }

    /* Cards View */
    .candidates-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 25px;
      padding: 30px;
    }

    .candidate-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      transition: all 0.3s ease;
      border: 1px solid #e5e7eb;
    }

    .candidate-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    .card-header {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 20px;
      background: linear-gradient(135deg, #f8fafc, #ffffff);
      border-bottom: 1px solid #e5e7eb;
    }

    .candidate-avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #3b82f6, #2563eb);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 1.2rem;
    }

    .candidate-info {
      flex: 1;
    }

    .candidate-name {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
      color: #1e293b;
    }

    .candidate-email {
      margin: 5px 0 0 0;
      color: #64748b;
      font-size: 0.9rem;
    }

    .card-actions {
      display: flex;
      gap: 8px;
    }

    .action-btn {
      width: 40px;
      height: 40px;
      border: none;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .action-btn.view {
      background: #3b82f6;
      color: white;
    }

    .action-btn.download {
      background: #10b981;
      color: white;
    }

    .action-btn.delete {
      background: #ef4444;
      color: white;
    }

    .action-btn:hover {
      transform: scale(1.1);
    }

    .card-details {
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .detail-item {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #64748b;
      font-size: 0.9rem;
    }

    .detail-item i {
      color: #3b82f6;
      width: 16px;
    }

    .file-badge {
      background: #3b82f6;
      color: white;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.8rem;
      margin-left: auto;
    }

    /* Professional Table */
    .professional-table {
      width: 100%;
      border-collapse: collapse;
      background: white;
    }

    .professional-table th {
      background: linear-gradient(135deg, #f8fafc, #e2e8f0);
      color: #374151;
      font-weight: 600;
      padding: 18px 20px;
      text-align: left;
      border-bottom: 2px solid #e5e7eb;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .professional-table td {
      padding: 16px 20px;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: middle;
    }

    .professional-table tr:hover {
      background: #f8fafc;
    }

    .candidate-cell {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .candidate-avatar-small {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      background: linear-gradient(135deg, #3b82f6, #2563eb);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 1rem;
    }

    .candidate-name-table {
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 4px;
    }

    .candidate-email-table {
      color: #64748b;
      font-size: 0.9rem;
    }

    .contact-info {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #64748b;
    }

    .date-badge {
      background: #e0f2fe;
      color: #0277bd;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 500;
    }

    .status-badge {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .status-badge.available {
      background: #dcfce7;
      color: #166534;
    }

    .status-badge.unavailable {
      background: #fef2f2;
      color: #991b1b;
    }

    .table-actions-cell {
      display: flex;
      gap: 8px;
    }

    .table-action-btn {
      width: 35px;
      height: 35px;
      border: none;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .table-action-btn.view {
      background: #e0f2fe;
      color: #0277bd;
    }

    .table-action-btn.download {
      background: #f0fdf4;
      color: #166534;
    }

    .table-action-btn.delete {
      background: #fef2f2;
      color: #991b1b;
    }

    .table-action-btn:hover {
      transform: scale(1.1);
    }

    /* Pagination */
    .pagination-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 25px 30px;
      background: #f8fafc;
      border-top: 1px solid #e5e7eb;
    }

    .pagination-info-text {
      color: #64748b;
      font-size: 0.9rem;
    }

    .pagination-controls {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .pagination-btn {
      padding: 10px 16px;
      border: 1px solid #e5e7eb;
      background: white;
      color: #64748b;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .pagination-btn:hover:not(:disabled) {
      background: #f1f5f9;
      border-color: #3b82f6;
    }

    .pagination-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .pagination-numbers {
      display: flex;
      gap: 5px;
    }

    .pagination-number {
      width: 40px;
      height: 40px;
      border: 1px solid #e5e7eb;
      background: white;
      color: #64748b;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .pagination-number.active {
      background: #3b82f6;
      color: white;
      border-color: #3b82f6;
    }

    .pagination-number:hover:not(.active) {
      background: #f1f5f9;
      border-color: #3b82f6;
    }

    /* Empty States */
    .empty-state {
      text-align: center;
      padding: 60px 30px;
      color: #64748b;
    }

    .empty-state i {
      font-size: 4rem;
      margin-bottom: 20px;
      color: #cbd5e1;
    }

    .empty-state h3 {
      margin: 0 0 10px 0;
      color: #374151;
    }

    .empty-state-table {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
      padding: 40px;
      color: #64748b;
    }

    .empty-state-table i {
      font-size: 3rem;
      color: #cbd5e1;
    }

    .empty-row {
      border: none !important;
    }
    }

    .stat-label {
      color: #666;
      font-size: 0.9rem;
    }

    /* Curriculos Section */
    .curriculos-section {
      background: white;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    .section-title {
      font-size: 1.5rem;
      color: #333;
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .curriculos-table {
      width: 100%;
      border-collapse: collapse;
    }

    .curriculos-table th,
    .curriculos-table td {
      padding: 15px 12px;
      text-align: left;
      border-bottom: 1px solid #f0f0f0;
    }

    .curriculos-table th {
      background: #f8f9fa;
      font-weight: 600;
      color: #333;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .curriculos-table tbody tr:hover {
      background: #f8f9fa;
    }

    .candidato-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .candidato-avatar {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #0056b3, #007bff);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
    }

    .candidato-details h4 {
      font-weight: 600;
      color: #333;
      margin-bottom: 4px;
    }

    .candidato-details span {
      color: #666;
      font-size: 0.9rem;
    }

    .arquivo-info {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .arquivo-icon {
      color: #dc3545;
      font-size: 1.2rem;
    }

    .action-buttons {
      display: flex;
      gap: 8px;
    }

    .btn-sm {
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.85rem;
      font-weight: 500;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.3s ease;
    }

    .btn-view {
      background: #e3f2fd;
      color: #0056b3;
    }

    .btn-download {
      background: #e8f5e8;
      color: #2e7d32;
    }

    .btn-delete {
      background: #ffebee;
      color: #c62828;
    }

    .btn-delete-candidate {
      background: #f3e5f5;
      color: #7b1fa2;
    }

    .btn-sm:hover {
      transform: translateY(-2px);
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #666;
    }

    .empty-state i {
      font-size: 4rem;
      margin-bottom: 20px;
      opacity: 0.3;
    }

    /* Responsivo */
    @media (max-width: 768px) {
      .curriculos-table {
        font-size: 0.9rem;
      }
      
      .action-buttons {
        flex-direction: column;
        gap: 4px;
      }
      
      .btn-sm {
        font-size: 0.8rem;
        padding: 6px 12px;
      }
      
      .action-buttons .btn-sm {
        justify-content: center;
      }
    }
    
    @media (max-width: 1024px) {
      .action-buttons {
        flex-wrap: wrap;
        gap: 6px;
      }
      
      .btn-sm {
        font-size: 0.8rem;
        padding: 6px 10px;
        flex: 1;
        min-width: 90px;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="header-container">
      <div class="logo-section">
        <div class="logo-icon">
          <i class="fas fa-file-pdf"></i>
        </div>
        <h2>Pasta de Currículos</h2>
      </div>
      <a href="admin.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> Voltar ao Painel
      </a>
    </div>
  </header>

  <div class="main-content">
    <div class="page-title">
      <h1><i class="fas fa-folder-open"></i> Central de Gestão de Currículos</h1>
      <p>Plataforma completa para análise, triagem e gestão de talentos com ferramentas avançadas de RH</p>
    </div>

    <div class="dashboard-header">
      <h2 class="dashboard-title">
        <i class="fas fa-chart-bar"></i>
        Dashboard Executivo
      </h2>
      <div class="dashboard-actions">
        <button onclick="exportarRelatorio()" class="export-btn">
          <i class="fas fa-file-excel"></i>
          Exportar Relatório
        </button>
        <button onclick="atualizarDados()" class="refresh-btn" title="Atualizar dados">
          <i class="fas fa-sync-alt"></i>
        </button>
      </div>
    </div>

    <div class="stats-section">
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon">
            <i class="fas fa-file-pdf"></i>
          </div>
          <div class="stat-trend">+<?php echo $stats['novos_7_dias']; ?> esta semana</div>
        </div>
        <div class="stat-number"><?php echo $stats['total_curriculos']; ?></div>
        <div class="stat-label">Total de Currículos</div>
        <div class="stat-details">
          <i class="fas fa-info-circle"></i>
          <span>Base de talentos ativa</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon" style="background: linear-gradient(135deg, #059669, #10b981);">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-trend"><?php echo count($arquivos_fisicos); ?> arquivos</div>
        </div>
        <div class="stat-number"><?php echo $total_records; ?></div>
        <div class="stat-label">Candidatos Filtrados</div>
        <div class="stat-details">
          <i class="fas fa-filter"></i>
          <span>Resultados da busca atual</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon" style="background: linear-gradient(135deg, #7c3aed, #8b5cf6);">
            <i class="fas fa-chart-line"></i>
          </div>
          <div class="stat-trend">Média por candidato</div>
        </div>
        <div class="stat-number"><?php echo $stats['media_candidaturas']; ?></div>
        <div class="stat-label">Candidaturas/Perfil</div>
        <div class="stat-details">
          <i class="fas fa-trending-up"></i>
          <span>Engajamento médio</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon" style="background: linear-gradient(135deg, #dc2626, #ef4444);">
            <i class="fas fa-database"></i>
          </div>
          <div class="stat-trend">
            <?php 
            $total_size = 0;
            foreach ($arquivos_fisicos as $arquivo) {
                $total_size += $arquivo['tamanho'];
            }
            echo formatBytes($total_size);
            ?>
          </div>
        </div>
        <div class="stat-number"><?php echo count($arquivos_fisicos); ?></div>
        <div class="stat-label">Arquivos em Disco</div>
        <div class="stat-details">
          <i class="fas fa-hdd"></i>
          <span>Armazenamento local</span>
      </div>
    </div>
  </div>

  <!-- Seção de Filtros Avançados -->
  <div class="filters-section">
    <div class="filters-title">
      <i class="fas fa-filter"></i>
      <span>Filtros Avançados</span>
    </div>
    
    <div class="filters-grid">
      <div class="filter-group">
        <label class="filter-label">Nome do Candidato</label>
        <input type="text" class="filter-input" id="filter-nome" placeholder="Buscar por nome...">
      </div>
      
      <div class="filter-group">
        <label class="filter-label">Área de Interesse</label>
        <select class="filter-input" id="filter-area">
          <option value="">Todas as áreas</option>
          <option value="tecnologia">Tecnologia</option>
          <option value="vendas">Vendas</option>
          <option value="marketing">Marketing</option>
          <option value="rh">Recursos Humanos</option>
          <option value="financeiro">Financeiro</option>
          <option value="operacoes">Operações</option>
        </select>
      </div>
      
      <div class="filter-group">
        <label class="filter-label">Experiência Mínima</label>
        <select class="filter-input" id="filter-experiencia">
          <option value="">Qualquer experiência</option>
          <option value="0-1">0-1 anos</option>
          <option value="1-3">1-3 anos</option>
          <option value="3-5">3-5 anos</option>
          <option value="5+">5+ anos</option>
        </select>
      </div>
      
      <div class="filter-group">
        <label class="filter-label">Data de Cadastro</label>
        <select class="filter-input" id="filter-data">
          <option value="">Qualquer período</option>
          <option value="hoje">Hoje</option>
          <option value="semana">Esta semana</option>
          <option value="mes">Este mês</option>
          <option value="trimestre">Último trimestre</option>
        </select>
      </div>
    </div>
    
    <div class="filters-actions">
      <button class="filter-btn secondary" onclick="limparFiltros()">
        <i class="fas fa-eraser"></i>
        Limpar Filtros
      </button>
      <button class="filter-btn primary" onclick="aplicarFiltros()">
        <i class="fas fa-search"></i>
        Aplicar Filtros
      </button>
    </div>
  </div>    <div class="curriculos-section">
      <h2 class="section-title">
        <i class="fas fa-list"></i>
        Currículos dos Candidatos
      </h2>
      
      <style>
        .match-high { background: #e6ffe6 !important; border-left: 5px solid #28a745; }
        .match-medium { background: #fffbe6 !important; border-left: 5px solid #ffc107; }
        .match-low { background: #ffeaea !important; border-left: 5px solid #dc3545; }
        .keyword-box { margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; box-shadow: 0 1px 4px #0001; }
        .keyword-box label { font-weight: bold; margin-right: 10px; }
        .keyword-box input[type=text] { width: 350px; padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; }
        .keyword-box button { padding: 6px 16px; border-radius: 4px; border: none; background: #28a745; color: #fff; font-weight: bold; cursor: pointer; }
        .keyword-box button:hover { background: #218838; }
      </style>

      <form method="get" class="keyword-box" style="display:inline-block;" id="form-palavras-chave">
  <script>
    // Se houver currículos filtrados, rola até a tabela automaticamente
    document.addEventListener('DOMContentLoaded', function() {
      const url = new URL(window.location.href);
      if (url.searchParams.get('palavras_chave')) {
        const tabela = document.querySelector('.curriculos-table');
        if (tabela) {
          tabela.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    });
  </script>
  <?php if (!empty($candidatos_com_curriculo) && !empty($palavras_chave)): ?>
    <form id="form-export-csv" method="post" style="display:inline-block; margin-left:20px;">
      <?php foreach ($_GET as $k => $v): if ($k !== 'export_csv'): ?>
        <input type="hidden" name="<?php echo htmlspecialchars($k); ?>" value="<?php echo htmlspecialchars($v); ?>">
      <?php endif; endforeach; ?>
      <input type="hidden" name="export_csv" value="1">
  <button id="export-btn" type="button" onclick="document.getElementById('form-export-csv').submit();" style="padding:6px 16px; border-radius:4px; border:none; background:#007bff; color:#fff; font-weight:bold; cursor:pointer;">Exportar para CSV</button>
    </form>
  <?php endif; ?>
        <label for="palavras_chave">Palavras-chave desejadas:</label>
        <input type="text" name="palavras_chave" id="palavras_chave" placeholder="Ex: PHP, liderança, inglês" value="<?php echo isset($_GET['palavras_chave']) ? htmlspecialchars($_GET['palavras_chave']) : ''; ?>">
        <button type="submit">Filtrar por IA</button>
        <span style="color:#888; font-size:0.9em; margin-left:10px;">Separe por vírgula</span>
      </form>

      <?php
        // IA simples: match de palavras-chave
      // --- INÍCIO IA ---
      // Função para remover acentos
        function removerAcentos($str) {
          return preg_replace([
            '/[áàãâä]/u','/[éèêë]/u','/[íìîï]/u','/[óòõôö]/u','/[úùûü]/u','/[ç]/u',
            '/[ÁÀÃÂÄ]/u','/[ÉÈÊË]/u','/[ÍÌÎÏ]/u','/[ÓÒÕÔÖ]/u','/[ÚÙÛÜ]/u','/[Ç]/u'
          ],
          ['a','e','i','o','u','c','A','E','I','O','U','C'], $str);
        }
        // Lista simples de sinônimos (pode ser expandida)
        $sinonimos = [
          'marketing' => ['publicidade', 'propaganda', 'comunicação'],
          'digital' => ['online', 'internet'],
          'social media' => ['mídias sociais', 'redes sociais'],
          'tráfego' => ['ads', 'anúncios', 'campanhas'],
          'conteúdo' => ['redação', 'copywriting', 'texto'],
          'anúncios' => ['ads', 'campanhas', 'publicidade'],
          'google ads' => ['adwords'],
          'facebook' => ['meta'],
          'instagram' => ['meta'],
          'copywriting' => ['redação', 'texto'],
          'analytics' => ['analise', 'dados'],
        ];
        $palavras_chave = isset($_GET['palavras_chave']) ? array_filter(array_map(function($p) {
          return removerAcentos(mb_strtolower(trim($p), 'UTF-8'));
        }, explode(',', $_GET['palavras_chave']))) : [];
        $matches = [];
      if (!empty($palavras_chave)) {
      $pdfParserDisponivel = class_exists('Smalot\\PdfParser\\Parser');
      foreach ($candidatos_com_curriculo as &$cand) {
            $conteudo = $cand['nome'] . ' ' . $cand['email'];
            if (isset($cand['telefone'])) $conteudo .= ' ' . $cand['telefone'];
            if (isset($cand['escolaridade'])) $conteudo .= ' ' . $cand['escolaridade'];
            if (isset($cand['experiencia'])) $conteudo .= ' ' . $cand['experiencia'];
            if (isset($cand['pretensao_salarial'])) $conteudo .= ' ' . $cand['pretensao_salarial'];
            if (isset($cand['total_candidaturas'])) $conteudo .= ' ' . $cand['total_candidaturas'];
            if (isset($cand['ultima_candidatura'])) $conteudo .= ' ' . $cand['ultima_candidatura'];
            // Usar texto do PDF salvo no banco, ou extrair e salvar se não existir
            if ($pdfParserDisponivel && !empty($cand['curriculo_arquivo'])) {
              if (!empty($cand['pdf_text'])) {
                $conteudo .= ' ' . $cand['pdf_text'];
              } else {
                $pdfPath = __DIR__ . '/uploads/' . $cand['curriculo_arquivo'];
                if (file_exists($pdfPath)) {
                  try {
                    $parser = new Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($pdfPath);
                    $pdfText = $pdf->getText();
                    $conteudo .= ' ' . $pdfText;
                    // Salvar no banco para uso futuro
                    $db = (new Database())->connect();
                    $update = $db->prepare("UPDATE candidatos SET pdf_text = ? WHERE id = ?");
                    $update->execute([$pdfText, $cand['id']]);
                  } catch (Exception $e) {
                    // Se der erro, ignora o PDF
                  }
                }
              }
            }
            $conteudo_normalizado = removerAcentos(mb_strtolower($conteudo, 'UTF-8'));
            // --- INÍCIO PONTUAÇÃO PONDERADA IA ---
            $match_count = 0;
            $palavras_encontradas = [];
            $match_experiencia = 0;
            $match_escolaridade = 0;
            foreach ($palavras_chave as $kw) {
              $encontrado = false;
              // Experiência e escolaridade recebem peso maior
              if (isset($cand['experiencia']) && strpos(removerAcentos(mb_strtolower($cand['experiencia'], 'UTF-8')), $kw) !== false) {
                $match_experiencia++;
                $encontrado = true;
              }
              if (isset($cand['escolaridade']) && strpos(removerAcentos(mb_strtolower($cand['escolaridade'], 'UTF-8')), $kw) !== false) {
                $match_escolaridade++;
                $encontrado = true;
              }
              if (!$encontrado) {
                if ($kw && strpos($conteudo_normalizado, $kw) !== false) {
                  $encontrado = true;
                } elseif (isset($sinonimos[$kw])) {
                  foreach ($sinonimos[$kw] as $sin) {
                    if (strpos($conteudo_normalizado, removerAcentos(mb_strtolower($sin, 'UTF-8'))) !== false) {
                      $encontrado = true;
                      break;
                    }
                  }
                }
              }
              if ($encontrado) {
                $match_count++;
                $palavras_encontradas[] = $kw;
              }
            }
            // Peso: experiência = 2, escolaridade = 1.5, demais = 1
            $score = $match_count + ($match_experiencia * 2) + ($match_escolaridade * 1.5);
            // --- FIM PONTUAÇÃO PONDERADA IA ---
            // Pontuação extra para quem tem mais candidaturas recentes
            $bonus = 0;
            if (isset($cand['total_candidaturas']) && $cand['total_candidaturas'] > 0) $bonus += min(2, (int)$cand['total_candidaturas']);
            if (isset($cand['ultima_candidatura']) && strtotime($cand['ultima_candidatura']) > strtotime('-30 days')) $bonus += 1;
            $cand['match_score'] = $score + $bonus;
            $cand['palavras_encontradas'] = $palavras_encontradas;
          }
          unset($cand);
          // Ordenar por match_score desc
          usort($candidatos_com_curriculo, function($a, $b) { return ($b['match_score'] ?? 0) <=> ($a['match_score'] ?? 0); });
        }
        // --- FIM IA ---
      ?>

      <?php if (empty($candidatos_com_curriculo)): ?>
        <div class="empty-state">
          <i class="fas fa-file-pdf"></i>
          <h3>Nenhum currículo encontrado</h3>
          <p>Os currículos enviados pelos candidatos aparecerão aqui</p>
        </div>
      <?php else: ?>
        <table class="curriculos-table">
          <thead>
            <tr>
              <th>Candidato</th>
              <th>Arquivo</th>
              <th>Data de Envio</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($candidatos_com_curriculo as $candidato): ?>
              <?php
                $match = $candidato['match_score'] ?? null;
                $match_class = '';
                if ($match !== null && !empty($palavras_chave)) {
                  $max_score = count($palavras_chave) + 3; // 3 pontos extras possíveis
                  if ($match >= $max_score - 1) $match_class = 'match-high';
                  elseif ($match >= ceil($max_score/2)) $match_class = 'match-medium';
                  elseif ($match > 0) $match_class = 'match-low';
                }
              ?>
              <tr data-candidato-id="<?php echo $candidato['id']; ?>" class="<?php echo $match_class; ?>">
                <td>
                  <div class="candidato-info">
                    <div class="candidato-avatar">
                      <?php echo strtoupper(substr($candidato['nome'], 0, 1)); ?>
                    </div>
                    <div class="candidato-details">
                      <h4><?php echo htmlspecialchars($candidato['nome']); ?></h4>
                      <span><?php echo htmlspecialchars($candidato['email']); ?></span>
                      <?php if ($match_class): ?>
                        <span style="display:inline-block; margin-left:8px; font-size:0.95em; color:#28a745; font-weight:bold;">
                          IA: Compatibilidade <?php echo $match; ?> pontos
                        </span>
                        <?php if (!empty($candidato['palavras_encontradas'])): ?>
                          <div style="font-size:0.92em; color:#555; margin-top:2px;">
                            <b>Palavras-chave encontradas:</b> <?php echo implode(', ', $candidato['palavras_encontradas']); ?>
                          </div>
                        <?php endif; ?>
                      <?php endif; ?>

                      <?php
                        // Buscar as vagas para as quais o candidato se candidatou
                        $vagas_candidato = [];
                        try {
                          $db = new Database();
                          $pdo = $db->connect();
                          $stmtVagasCand = $pdo->prepare("SELECT v.id, v.titulo, v.palavras_chave FROM candidaturas c INNER JOIN vagas v ON c.vaga_id = v.id WHERE c.candidato_id = ? AND v.status = 'ativa' AND v.palavras_chave IS NOT NULL AND v.palavras_chave != ''");
                          $stmtVagasCand->execute([$candidato['id']]);
                          $vagas_candidato = $stmtVagasCand->fetchAll(PDO::FETCH_ASSOC);
                        } catch (Exception $e) {}
                      ?>
                      <?php if (!empty($vagas_candidato)): ?>
                        <div style="margin-top:6px; font-size:0.93em;">
                          <b>Compatibilidade automática com vagas que se candidatou:</b>
                          <ul style="margin:0; padding-left:18px;">
                            <?php foreach ($vagas_candidato as $vaga): ?>
                              <?php
                                $palavras_vaga = array_filter(array_map('trim', explode(',', mb_strtolower($vaga['palavras_chave'], 'UTF-8'))));
                                $conteudo_cand = isset($candidato['pdf_text']) ? mb_strtolower($candidato['pdf_text'], 'UTF-8') : '';
                                $conteudo_cand .= ' ' . mb_strtolower($candidato['nome'] . ' ' . $candidato['email'] . ' ' . ($candidato['experiencia'] ?? '') . ' ' . ($candidato['escolaridade'] ?? ''), 'UTF-8');
                                $encontradas = [];
                                foreach ($palavras_vaga as $kwv) {
                                  if ($kwv && strpos($conteudo_cand, $kwv) !== false) $encontradas[] = $kwv;
                                }
                                $percent = count($palavras_vaga) ? round((count($encontradas)/count($palavras_vaga))*100) : 0;
                              ?>
                              <li style="margin-bottom:2px;">
                                <b><?php echo htmlspecialchars($vaga['titulo']); ?>:</b>
                                <span style="color:<?php echo $percent>=70?'#28a745':($percent>=40?'#ffc107':'#dc3545'); ?>; font-weight:bold;">
                                  <?php echo $percent; ?>%
                                </span>
                                <?php if ($percent>0): ?>
                                  <span style="color:#555; font-size:0.92em;">(<?php echo implode(', ', $encontradas); ?>)</span>
                                <?php endif; ?>
                              </li>
                            <?php endforeach; ?>
                          </ul>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="arquivo-info">
                    <i class="fas fa-file-pdf arquivo-icon"></i>
                    <div>
                      <div><?php echo htmlspecialchars($candidato['curriculo_arquivo']); ?></div>
                      <?php if (file_exists($uploads_dir . $candidato['curriculo_arquivo'])): ?>
                        <small style="color: #28a745;">✓ Arquivo disponível</small>
                      <?php else: ?>
                        <small style="color: #dc3545;">⚠ Arquivo não encontrado</small>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td><?php echo date('d/m/Y H:i', strtotime($candidato['data_cadastro'])); ?></td>
                <td>
                  <div class="action-buttons">
                    <?php if (file_exists($uploads_dir . $candidato['curriculo_arquivo'])): ?>
                      <a href="<?php echo $uploads_dir . $candidato['curriculo_arquivo']; ?>" 
                         target="_blank" class="btn-sm btn-view">
                        <i class="fas fa-eye"></i> Visualizar
                      </a>
                      <a href="<?php echo $uploads_dir . $candidato['curriculo_arquivo']; ?>" 
                         download class="btn-sm btn-download">
                        <i class="fas fa-download"></i> Baixar
                      </a>
                    <?php else: ?>
                      <span style="color: #999; font-size: 0.8rem;">Arquivo indisponível</span>
                    <?php endif; ?>
                    
                    <button onclick="confirmarExclusaoCurriculo(<?php echo $candidato['id']; ?>, '<?php echo addslashes($candidato['nome']); ?>')" 
                            class="btn-sm btn-delete" title="Excluir apenas o currículo">
                      <i class="fas fa-trash"></i> Excluir Currículo
                    </button>
                    
                    <button onclick="confirmarExclusaoCandidato(<?php echo $candidato['id']; ?>, '<?php echo addslashes($candidato['nome']); ?>')" 
                            class="btn-sm btn-delete-candidate" title="Excluir candidato completamente">
                      <i class="fas fa-user-times"></i> Excluir Candidato
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <script>
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
        background: ${type === 'success' ? 'linear-gradient(135deg, #28a745, #20c997)' : 
                     type === 'error' ? 'linear-gradient(135deg, #dc3545, #e74c3c)' : 
                     'linear-gradient(135deg, #007bff, #0056b3)'};
      `;
      
      notification.textContent = message;
      document.body.appendChild(notification);
      
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

    // Alternância entre visualizações Cards e Table
    document.querySelectorAll('.view-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const viewType = btn.dataset.view;
        
        // Atualizar botões
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        // Alternar visualizações
        if (viewType === 'cards') {
          document.getElementById('cards-view').style.display = 'grid';
          document.getElementById('table-view').style.display = 'none';
        } else {
          document.getElementById('cards-view').style.display = 'none';
          document.getElementById('table-view').style.display = 'block';
        }
        
        showNotification(`Visualização alterada para ${viewType === 'cards' ? 'cards' : 'tabela'}`, 'info');
      });
    });

    // Funções dos filtros
    function aplicarFiltros() {
      const nome = document.getElementById('filter-nome').value.toLowerCase();
      const area = document.getElementById('filter-area').value;
      const experiencia = document.getElementById('filter-experiencia').value;
      const data = document.getElementById('filter-data').value;
      
      let filtrosAplicados = 0;
      
      if (nome) filtrosAplicados++;
      if (area) filtrosAplicados++;
      if (experiencia) filtrosAplicados++;
      if (data) filtrosAplicados++;
      
      if (filtrosAplicados === 0) {
        showNotification('Nenhum filtro selecionado', 'error');
        return;
      }
      
      showNotification(`${filtrosAplicados} filtro(s) aplicado(s)`, 'success');
      
      // Aqui você implementaria a lógica real de filtro
      console.log('Filtros:', { nome, area, experiencia, data });
    }

    function limparFiltros() {
      document.getElementById('filter-nome').value = '';
      document.getElementById('filter-area').value = '';
      document.getElementById('filter-experiencia').value = '';
      document.getElementById('filter-data').value = '';
      
      showNotification('Filtros limpos', 'info');
    }

    // Checkbox "Selecionar todos"
    document.getElementById('select-all')?.addEventListener('change', function() {
      const checkboxes = document.querySelectorAll('.candidate-checkbox');
      checkboxes.forEach(cb => cb.checked = this.checked);
      
      const count = this.checked ? checkboxes.length : 0;
      if (count > 0) {
        showNotification(`${count} candidato(s) selecionado(s)`, 'info');
      }
    });

    // Função para confirmar exclusão de candidato
    function confirmarExclusaoCandidato(candidatoId, nomeCandidate) {
      if (confirm(`🚨 EXCLUIR CANDIDATO COMPLETAMENTE\n\nDeseja realmente excluir "${nomeCandidate}" do sistema?\n\n• Todos os dados serão removidos\n• Currículo será excluído\n• Candidaturas serão removidas\n• Esta ação NÃO pode ser desfeita\n\nTem certeza absoluta?`)) {
        if (confirm(`⚠️ CONFIRMAÇÃO FINAL\n\nEsta é sua última chance!\n\nExcluir "${nomeCandidate}" PERMANENTEMENTE?`)) {
          excluirCandidato(candidatoId);
        }
      }
    }

    // Função para processar exclusão
    function excluirCandidato(candidatoId) {
      showNotification('Excluindo candidato...', 'info');
      
      fetch('processar_exclusao.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          candidato_id: candidatoId,
          action: 'delete_candidate'
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification(data.message, 'success');
          
          // Recarregar página após exclusão
          setTimeout(() => {
            location.reload();
          }, 2000);
        } else {
          showNotification(data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Erro:', error);
        showNotification('Erro ao processar solicitação', 'error');
      });
    }

    // Função de exportar dados (placeholder)
    function exportarDados() {
      showNotification('Preparando exportação...', 'info');
      
      setTimeout(() => {
        showNotification('Dados exportados com sucesso!', 'success');
      }, 2000);
    }

    // Função de atualizar dados (placeholder)
    function atualizarDados() {
      showNotification('Atualizando dados...', 'info');
      
      setTimeout(() => {
        location.reload();
      }, 1500);
    }

    // Mostrar notificação ao clicar em download
    document.querySelectorAll('.btn-download, .action-btn.download, .table-action-btn.download').forEach(btn => {
      btn.addEventListener('click', () => {
        showNotification('Download iniciado!', 'success');
      });
    });

    // Mostrar notificação ao visualizar
    document.querySelectorAll('.btn-view, .action-btn.view, .table-action-btn.view').forEach(btn => {
      btn.addEventListener('click', () => {
        showNotification('Abrindo currículo...', 'info');
      });
    });

    // Animações de entrada
    document.addEventListener('DOMContentLoaded', function() {
      // Animação das estatísticas
      const stats = document.querySelectorAll('.stat-number');
      stats.forEach((stat, index) => {
        setTimeout(() => {
          stat.style.opacity = '1';
          stat.style.transform = 'translateY(0)';
        }, index * 200);
      });

      // Animação dos cards
      const cards = document.querySelectorAll('.candidate-card');
      cards.forEach((card, index) => {
        setTimeout(() => {
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 100);
      });
    });
  </script>
</body>
</html>
