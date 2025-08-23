<?php
require_once 'config.php';

// Inicializar resposta
$response = ['success' => false, 'message' => 'Método não permitido'];

try {
    $db = (new Database())->connect();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'buscar_candidato':
                $email = $_GET['email'] ?? '';
                if (empty($email)) {
                    throw new Exception('Email é obrigatório');
                }
                
                $stmt = $db->prepare("SELECT id, nome, email FROM candidatos WHERE email = ?");
                $stmt->execute([$email]);
                $candidato = $stmt->fetch();
                
                if ($candidato) {
                    $response = [
                        'success' => true,
                        'message' => 'Candidato encontrado',
                        'data' => $candidato
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Candidato não encontrado. Você precisa se cadastrar primeiro.'
                    ];
                }
                break;
                
            case 'estatisticas_dashboard':
                // Buscar estatísticas atualizadas
                $stats = [];
                
                $stmt = $db->query("SELECT COUNT(*) as total FROM candidatos WHERE status IN ('novo', 'em_analise', 'entrevista_agendada')");
                $stats['candidatos_ativos'] = $stmt->fetch()['total'];
                
                $stmt = $db->query("SELECT COUNT(*) as total FROM vagas WHERE status = 'ativa' AND data_encerramento >= CURDATE()");
                $stats['vagas_abertas'] = $stmt->fetch()['total'];
                
                $stmt = $db->query("SELECT COUNT(*) as total FROM entrevistas WHERE status = 'agendada' AND data_entrevista >= NOW()");
                $stats['entrevistas_agendadas'] = $stmt->fetch()['total'];
                
                $stmt = $db->query("SELECT COUNT(*) as total FROM candidaturas WHERE status = 'enviada' AND DATE(data_candidatura) = CURDATE()");
                $stats['candidaturas_hoje'] = $stmt->fetch()['total'];
                
                $response = [
                    'success' => true,
                    'message' => 'Estatísticas atualizadas',
                    'data' => $stats
                ];
                break;
                
            case 'candidatos_recentes':
                $stmt = $db->query("
                    SELECT id, nome, email, telefone, area_interesse, data_cadastro 
                    FROM candidatos 
                    WHERE status IN ('novo', 'em_analise', 'entrevista_agendada')
                    ORDER BY data_cadastro DESC 
                    LIMIT 10
                ");
                $candidatos = $stmt->fetchAll();
                
                foreach ($candidatos as &$candidato) {
                    $candidato['data_cadastro_formatada'] = formatDateTime($candidato['data_cadastro']);
                    $candidato['tempo_relativo'] = calcularTempoRelativo($candidato['data_cadastro']);
                }
                
                $response = [
                    'success' => true,
                    'message' => 'Candidatos encontrados',
                    'data' => $candidatos
                ];
                break;
                
            case 'vagas_recentes':
                $stmt = $db->query("
                    SELECT id, titulo, empresa, local, salario_min, salario_max, data_publicacao 
                    FROM vagas 
                    WHERE status = 'ativa' 
                    ORDER BY data_publicacao DESC 
                    LIMIT 10
                ");
                $vagas = $stmt->fetchAll();
                
                foreach ($vagas as &$vaga) {
                    $vaga['data_publicacao_formatada'] = formatDate($vaga['data_publicacao']);
                    $vaga['tempo_relativo'] = calcularTempoRelativo($vaga['data_publicacao']);
                    
                    if ($vaga['salario_min'] && $vaga['salario_max']) {
                        $vaga['salario_formatado'] = formatCurrency($vaga['salario_min']) . ' - ' . formatCurrency($vaga['salario_max']);
                    } elseif ($vaga['salario_min']) {
                        $vaga['salario_formatado'] = 'A partir de ' . formatCurrency($vaga['salario_min']);
                    } else {
                        $vaga['salario_formatado'] = 'A combinar';
                    }
                }
                
                $response = [
                    'success' => true,
                    'message' => 'Vagas encontradas',
                    'data' => $vagas
                ];
                break;
                
            case 'atividades_recentes':
                $stmt = $db->query("
                    SELECT 
                        a.*, 
                        u.nome as admin_nome,
                        u.email as admin_email
                    FROM atividades_admin a
                    LEFT JOIN usuarios_admin u ON a.admin_id = u.id
                    ORDER BY a.data_atividade DESC
                    LIMIT 15
                ");
                $atividades = $stmt->fetchAll();
                
                foreach ($atividades as &$atividade) {
                    $atividade['data_atividade_formatada'] = formatDateTime($atividade['data_atividade']);
                    $atividade['tempo_relativo'] = calcularTempoRelativo($atividade['data_atividade']);
                }
                
                $response = [
                    'success' => true,
                    'message' => 'Atividades encontradas',
                    'data' => $atividades
                ];
                break;
                
            case 'vaga_detalhes':
                $vaga_id = intval($_GET['vaga_id'] ?? 0);
                if (!$vaga_id) {
                    throw new Exception('ID da vaga é obrigatório');
                }
                
                $stmt = $db->prepare("
                    SELECT 
                        v.*,
                        COUNT(c.id) as total_candidaturas,
                        COUNT(CASE WHEN c.status = 'enviada' THEN 1 END) as candidaturas_novas,
                        COUNT(CASE WHEN c.status = 'em_analise' THEN 1 END) as candidaturas_analise
                    FROM vagas v
                    LEFT JOIN candidaturas c ON v.id = c.vaga_id
                    WHERE v.id = ?
                    GROUP BY v.id
                ");
                $stmt->execute([$vaga_id]);
                $vaga = $stmt->fetch();
                
                if (!$vaga) {
                    throw new Exception('Vaga não encontrada');
                }
                
                // Formatar dados
                $vaga['salario_formatado'] = '';
                if ($vaga['salario_min'] && $vaga['salario_max']) {
                    $vaga['salario_formatado'] = formatCurrency($vaga['salario_min']) . ' - ' . formatCurrency($vaga['salario_max']);
                } elseif ($vaga['salario_min']) {
                    $vaga['salario_formatado'] = 'A partir de ' . formatCurrency($vaga['salario_min']);
                }
                
                $vaga['data_publicacao_formatada'] = formatDate($vaga['data_publicacao']);
                $vaga['data_encerramento_formatada'] = formatDate($vaga['data_encerramento']);
                
                $response = [
                    'success' => true,
                    'message' => 'Detalhes da vaga encontrados',
                    'data' => $vaga
                ];
                break;
                
            default:
                throw new Exception('Ação não reconhecida');
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'get_testimonials':
                // Buscar avaliações publicadas
                $stmt = $db->prepare("
                    SELECT nome, comentario, rating, data_criacao 
                    FROM avaliacoes 
                    WHERE status = 'publicada' 
                    ORDER BY data_criacao DESC 
                    LIMIT 20
                ");
                $stmt->execute();
                $testimonials = $stmt->fetchAll();
                
                // Buscar estatísticas das avaliações
                $stmt = $db->prepare("
                    SELECT 
                        COUNT(*) as total,
                        AVG(rating) as average
                    FROM avaliacoes 
                    WHERE status = 'publicada'
                ");
                $stmt->execute();
                $stats = $stmt->fetch();
                
                $response = [
                    'success' => true,
                    'message' => 'Avaliações carregadas com sucesso',
                    'testimonials' => $testimonials,
                    'stats' => [
                        'total' => intval($stats['total']),
                        'average' => floatval($stats['average'])
                    ]
                ];
                break;
                
            default:
                throw new Exception('Ação não reconhecida');
        }
        
    } else {
        throw new Exception('Método não permitido');
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);

function calcularTempoRelativo($data) {
    $agora = new DateTime();
    $tempo = new DateTime($data);
    $diff = $agora->diff($tempo);
    
    if ($diff->d > 0) {
        return $diff->d . ' dia' . ($diff->d > 1 ? 's' : '') . ' atrás';
    } elseif ($diff->h > 0) {
        return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '') . ' atrás';
    } elseif ($diff->i > 0) {
        return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '') . ' atrás';
    } else {
        return 'Agora mesmo';
    }
}
?>
