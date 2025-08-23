<?php
require_once 'config.php';

SessionManager::start();
if (!SessionManager::has('admin_logged_in')) {
  header('Location: login_admin.php');
  exit;
}

// Conectar ao banco e buscar métricas
$database = new Database();
$pdo = $database->connect();

// Total de vagas abertas (ativas)
$total_vagas_ativas = (int)$pdo->query("SELECT COUNT(*) FROM vagas WHERE status = 'ativa'")->fetchColumn();

// Total de candidatos ativos (com currículo)
$total_candidatos = (int)$pdo->query("SELECT COUNT(*) FROM candidatos WHERE curriculo_arquivo IS NOT NULL AND curriculo_arquivo != ''")->fetchColumn();

// Total de currículos recebidos (igual candidatos ativos)
$total_curriculos = $total_candidatos;

// Alertas recentes: vagas encerradas nos últimos 7 dias
$alertas_urgentes = (int)$pdo->query("SELECT COUNT(*) FROM vagas WHERE status = 'encerrada' AND data_encerramento >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

// Candidatos novos nos últimos 7 dias
$novos_candidatos = (int)$pdo->query("SELECT COUNT(*) FROM candidatos WHERE data_cadastro >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

// Gráfico: candidaturas por semana (últimos 7 dias)
$dias = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
$candidaturas_semana = array_fill(0, 7, 0);
$stmt = $pdo->query("SELECT DAYOFWEEK(data_candidatura) as dia, COUNT(*) as total FROM candidaturas WHERE data_candidatura >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY dia");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $idx = ((int)$row['dia']-1)%7;
  $candidaturas_semana[$idx] = (int)$row['total'];
}
$tem_candidaturas = array_sum($candidaturas_semana) > 0;

// Gráfico: status das vagas
$status_labels = [];
$status_data = [];
$stmt = $pdo->query("SELECT status, COUNT(*) as total FROM vagas GROUP BY status");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $status_labels[] = ucfirst($row['status']);
  $status_data[] = (int)$row['total'];
}
$tem_status = array_sum($status_data) > 0;

// Gráfico: crescimento de vagas por mês (últimos 8 meses)
$meses = [];
$vagas_mes = [];
for ($i = 7; $i >= 0; $i--) {
  $mes = date('M', strtotime("-{$i} month"));
  $ano = date('Y', strtotime("-{$i} month"));
  $meses[] = ucfirst($mes);
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM vagas WHERE MONTH(data_criacao) = ? AND YEAR(data_criacao) = ?");
  $stmt->execute([date('n', strtotime("-{$i} month")), $ano]);
  $vagas_mes[] = (int)$stmt->fetchColumn();
}
$tem_vagas_mes = array_sum($vagas_mes) > 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Monitorar Vagas | ENIAC LINK+</title>
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
      background: #f7f9fb;
      color: #333;
      min-height: 100vh;
    }
    .container {
      max-width: 1200px;
      margin: 40px auto;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.10);
      padding: 0 0 40px 0;
    }
    .header {
      background: linear-gradient(135deg, #0056b3, #004494);
      color: #fff;
      padding: 36px 0 24px 0;
      border-radius: 18px 18px 0 0;
      text-align: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    }
    .header h1 {
      font-size: 2.3rem;
      font-weight: 700;
      margin-bottom: 8px;
      letter-spacing: 1px;
    }
    .header p {
      font-size: 1.1rem;
      color: #e0e7ef;
    }
    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 1.5rem;
      margin: 40px 0 32px 0;
      padding: 0 32px;
    }
    .metric-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.07);
      padding: 28px 24px 20px 24px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 10px;
      border: 1.5px solid #e9ecef;
      transition: box-shadow 0.2s;
      position: relative;
    }
    .metric-card:hover {
      box-shadow: 0 6px 24px rgba(0,86,179,0.10);
      border-color: #b3d4fc;
    }
    .metric-icon {
      font-size: 2.2rem;
      color: #0056b3;
      margin-bottom: 8px;
    }
    .metric-title {
      font-size: 1.1rem;
      color: #555;
      font-weight: 500;
    }
    .metric-value {
      font-size: 2.1rem;
      font-weight: 700;
      color: #004494;
    }
    .metric-trend {
      font-size: 0.95rem;
      color: #16a34a;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .charts-section {
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      justify-content: space-between;
      padding: 0 32px;
    }
    .chart-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.07);
      padding: 24px 18px 18px 18px;
      flex: 1 1 350px;
      min-width: 320px;
      border: 1.5px solid #e9ecef;
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 20px;
    }
    .chart-title {
      font-size: 1.1rem;
      color: #0056b3;
      font-weight: 600;
      margin-bottom: 18px;
      text-align: center;
    }
    .back-btn {
      background: linear-gradient(135deg, #0056b3, #004494);
      color: white;
      padding: 13px 28px;
      border: none;
      border-radius: 10px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
      margin: 32px 0 0 32px;
      font-size: 1.05rem;
    }
    .back-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0,86,179,0.13);
    }
    @media (max-width: 900px) {
      .container { padding: 0 0 24px 0; }
      .metrics-grid, .charts-section { padding: 0 10px; }
      .chart-card { min-width: 220px; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1><i class="fas fa-chart-line"></i> Monitorar Vagas</h1>
      <p>Dashboard de métricas e estatísticas em tempo real</p>
    </div>
    <div class="metrics-grid">
      <div class="metric-card">
        <span class="metric-icon"><i class="fas fa-briefcase"></i></span>
        <span class="metric-title">Vagas Abertas</span>
        <span class="metric-value"><?php echo $total_vagas_ativas; ?></span>
        <span class="metric-trend"><i class="fas fa-arrow-up"></i> <?php echo $total_vagas_ativas > 0 ? '+'.($total_vagas_ativas) : 'Nenhuma vaga ativa'; ?></span>
      </div>
      <div class="metric-card">
        <span class="metric-icon"><i class="fas fa-user-check"></i></span>
        <span class="metric-title">Candidatos Ativos</span>
        <span class="metric-value"><?php echo $total_candidatos; ?></span>
        <span class="metric-trend"><i class="fas fa-arrow-up"></i> <?php echo $novos_candidatos > 0 ? '+'.$novos_candidatos.' novos/7d' : 'Nenhum novo'; ?></span>
      </div>
      <div class="metric-card">
        <span class="metric-icon"><i class="fas fa-file-alt"></i></span>
        <span class="metric-title">Currículos Recebidos</span>
        <span class="metric-value"><?php echo $total_curriculos; ?></span>
        <span class="metric-trend"><i class="fas fa-arrow-up"></i> <?php echo $novos_candidatos > 0 ? '+'.$novos_candidatos.' semana' : 'Nenhum novo'; ?></span>
      </div>
      <div class="metric-card">
        <span class="metric-icon"><i class="fas fa-bell"></i></span>
        <span class="metric-title">Alertas Recentes</span>
        <span class="metric-value"><?php echo $alertas_urgentes; ?></span>
        <span class="metric-trend" style="color:#eab308;"><i class="fas fa-exclamation-triangle"></i> <?php echo $alertas_urgentes > 0 ? $alertas_urgentes.' urgentes' : 'Nenhum'; ?></span>
      </div>
    </div>
    <div class="charts-section">
      <div class="chart-card">
        <div class="chart-title">Candidaturas por Semana</div>
        <?php 
          $total_cand = array_sum($candidaturas_semana);
          $dias_com_cand = array_filter($candidaturas_semana, function($v){return $v>0;});
        ?>
        <?php if ($tem_candidaturas && count($dias_com_cand) > 1) { ?>
          <?php $url_cand = "https://quickchart.io/chart?c={type:'bar',data:{labels:['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],datasets:[{label:'Candidaturas',data:[".implode(',', $candidaturas_semana)."],backgroundColor:'#0056b3'}]}}"; ?>
          <img src='<?php echo $url_cand; ?>' alt='Gráfico de Candidaturas' style='width:100%;max-width:320px;'>
          <div style='font-size:0.8rem;word-break:break-all;color:#888;margin-top:8px;'><b>URL:</b> <?php echo $url_cand; ?></div>
        <?php } elseif ($tem_candidaturas && count($dias_com_cand) == 1) { ?>
          <div style='color:#999;text-align:center;padding:40px 0;'>Só há candidaturas em um dia da semana.<br>Cadastre mais para ver o gráfico.</div>
        <?php } else { ?>
          <div style='color:#999;text-align:center;padding:40px 0;'>Sem dados de candidaturas na semana.</div>
        <?php } ?>
      </div>
      <div class="chart-card">
        <div class="chart-title">Distribuição de Status das Vagas</div>
        <?php 
          $status_naozero = array_filter($status_data, function($v){return $v>0;});
        ?>
        <?php if ($tem_status && count($status_naozero) > 1) { ?>
          <?php $url_status = "https://quickchart.io/chart?c={type:'doughnut',data:{labels:[".'\''.implode("','",$status_labels).'\''."],datasets:[{data:[".implode(',', $status_data)."],backgroundColor:['#0056b3','#e5e7eb','#fbbf24','#f87171','#34d399','#fbbf24']}]} }"; ?>
          <img src='<?php echo $url_status; ?>' alt='Gráfico de Status' style='width:100%;max-width:320px;'>
          <div style='font-size:0.8rem;word-break:break-all;color:#888;margin-top:8px;'><b>URL:</b> <?php echo $url_status; ?></div>
        <?php } elseif ($tem_status && count($status_naozero) == 1) { ?>
          <div style='color:#999;text-align:center;padding:40px 0;'>Só há vagas em um status.<br>Cadastre vagas com outros status para ver o gráfico.</div>
        <?php } else { ?>
          <div style='color:#999;text-align:center;padding:40px 0;'>Sem dados de status de vagas.</div>
        <?php } ?>
      </div>
      <div class="chart-card">
        <div class="chart-title">Crescimento de Vagas</div>
        <?php 
          $meses_naozero = array_filter($vagas_mes, function($v){return $v>0;});
        ?>
        <?php if ($tem_vagas_mes && count($meses_naozero) > 1) { ?>
          <?php $url_vagas = "https://quickchart.io/chart?c={type:'line',data:{labels:[".'\''.implode("','",$meses).'\''."],datasets:[{label:'Vagas',data:[".implode(',', $vagas_mes)."],borderColor:'#0056b3',backgroundColor:'rgba(0,86,179,0.1)',fill:true}]}}"; ?>
          <img src='<?php echo $url_vagas; ?>' alt='Gráfico de Vagas' style='width:100%;max-width:320px;'>
          <div style='font-size:0.8rem;word-break:break-all;color:#888;margin-top:8px;'><b>URL:</b> <?php echo $url_vagas; ?></div>
        <?php } elseif ($tem_vagas_mes && count($meses_naozero) == 1) { ?>
          <div style='color:#999;text-align:center;padding:40px 0;'>Só há vagas cadastradas em um mês.<br>Cadastre vagas em outros meses para ver o gráfico.</div>
        <?php } else { ?>
          <div style='color:#999;text-align:center;padding:40px 0;'>Sem dados de vagas cadastradas.</div>
        <?php } ?>
      </div>
    </div>
    <a href="gerenciar_vagas.php" class="back-btn">
      <i class="fas fa-arrow-left"></i>
      Voltar ao Gerenciamento
    </a>
  </div>
</body>
</html>
