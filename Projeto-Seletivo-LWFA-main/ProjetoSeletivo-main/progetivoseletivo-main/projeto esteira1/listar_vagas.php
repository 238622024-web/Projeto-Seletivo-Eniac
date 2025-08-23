<?php
require_once 'config.php';

try {
    $db = (new Database())->connect();
    
    // Parâmetros de busca e filtros
    $busca = $_GET['busca'] ?? '';
    $area = $_GET['area'] ?? '';
    $modalidade = $_GET['modalidade'] ?? '';
    $nivel = $_GET['nivel'] ?? '';
    $cidade = $_GET['cidade'] ?? '';
    $salario_min = $_GET['salario_min'] ?? 0;
    $limit = intval($_GET['limit'] ?? 10);
    $offset = intval($_GET['offset'] ?? 0);
    
    // Construir query de busca
    $where_conditions = ["v.status = 'ativa'", "v.data_encerramento >= CURDATE()"];
    $params = [];
    
    if (!empty($busca)) {
        $where_conditions[] = "(v.titulo LIKE ? OR v.empresa LIKE ? OR v.descricao LIKE ?)";
        $params[] = "%{$busca}%";
        $params[] = "%{$busca}%";
        $params[] = "%{$busca}%";
    }
    
    if (!empty($area)) {
        $where_conditions[] = "v.area = ?";
        $params[] = $area;
    }
    
    if (!empty($modalidade)) {
        $where_conditions[] = "v.modalidade = ?";
        $params[] = $modalidade;
    }
    
    if (!empty($nivel)) {
        $where_conditions[] = "v.nivel = ?";
        $params[] = $nivel;
    }
    
    if (!empty($cidade)) {
        $where_conditions[] = "v.localizacao LIKE ?";
        $params[] = "%{$cidade}%";
    }
    
    if ($salario_min > 0) {
        $where_conditions[] = "v.salario_min >= ?";
        $params[] = $salario_min;
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Query principal
    $sql = "
        SELECT 
            v.*,
            COUNT(c.id) as total_candidaturas
        FROM vagas v
        LEFT JOIN candidaturas c ON v.id = c.vaga_id
        WHERE {$where_clause}
        GROUP BY v.id
        ORDER BY v.data_publicacao DESC
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $vagas = $stmt->fetchAll();
    
    // Contar total de resultados
    $count_sql = "
        SELECT COUNT(DISTINCT v.id) as total
        FROM vagas v
        WHERE {$where_clause}
    ";
    
    $count_params = array_slice($params, 0, -2); // Remove limit e offset
    $stmt = $db->prepare($count_sql);
    $stmt->execute($count_params);
    $total_vagas = $stmt->fetch()['total'];
    
    // Estatísticas
    $stats_sql = "
        SELECT 
            COUNT(*) as total_vagas_ativas,
            COUNT(DISTINCT empresa) as total_empresas,
            AVG(salario_max) as salario_medio
        FROM vagas 
        WHERE status = 'ativa' AND data_encerramento >= CURDATE()
    ";
    $stmt = $db->query($stats_sql);
    $stats = $stmt->fetch();
    
    // Formatar dados das vagas
    foreach ($vagas as &$vaga) {
        $vaga['salario_formatado'] = '';
        if ($vaga['salario_min'] && $vaga['salario_max']) {
            $vaga['salario_formatado'] = formatCurrency($vaga['salario_min']) . ' - ' . formatCurrency($vaga['salario_max']);
        } elseif ($vaga['salario_min']) {
            $vaga['salario_formatado'] = 'A partir de ' . formatCurrency($vaga['salario_min']);
        } elseif ($vaga['salario_max']) {
            $vaga['salario_formatado'] = 'Até ' . formatCurrency($vaga['salario_max']);
        } else {
            $vaga['salario_formatado'] = 'A combinar';
        }
        
        $vaga['data_publicacao_formatada'] = formatDate($vaga['data_publicacao']);
        $vaga['data_encerramento_formatada'] = formatDate($vaga['data_encerramento']);
        
        // Calcular dias restantes
        $hoje = new DateTime();
        $encerramento = new DateTime($vaga['data_encerramento']);
        $dias_restantes = $hoje->diff($encerramento)->days;
        $vaga['dias_restantes'] = $encerramento > $hoje ? $dias_restantes : 0;
    }
    
    // Retornar JSON se for requisição AJAX
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'vagas' => $vagas,
            'total' => $total_vagas,
            'stats' => $stats,
            'has_more' => ($offset + $limit) < $total_vagas
        ]);
        exit;
    }
    
} catch (Exception $e) {
    if (isset($_GET['ajax'])) {
        echo generateApiResponse(false, $e->getMessage());
        exit;
    } else {
        $vagas = [];
        $total_vagas = 0;
        $stats = ['total_vagas_ativas' => 0, 'total_empresas' => 0, 'salario_medio' => 0];
    }
}
?>
