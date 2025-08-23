<?php
require_once 'config.php';
SessionManager::start();

// Verificar se usuário está logado
if (!SessionManager::has('admin_logged_in')) {
    header('Location: login_admin.php');
    exit;
}

try {
    $db = (new Database())->connect();
    
    // Estatísticas do dashboard
    $stats = [];
    
    // Total de candidatos ativos
    $stmt = $db->query("SELECT COUNT(*) as total FROM candidatos WHERE status IN ('novo', 'em_analise', 'entrevista_agendada')");
    $stats['candidatos_ativos'] = $stmt->fetch()['total'];
    
    // Total de vagas abertas
    $stmt = $db->query("SELECT COUNT(*) as total FROM vagas WHERE status = 'ativa' AND data_encerramento >= CURDATE()");
    $stats['vagas_abertas'] = $stmt->fetch()['total'];
    
    // Entrevistas agendadas
    $stmt = $db->query("SELECT COUNT(*) as total FROM entrevistas WHERE status = 'agendada' AND data_entrevista >= NOW()");
    $stats['entrevistas_agendadas'] = $stmt->fetch()['total'];
    
    // Aprovações pendentes
    $stmt = $db->query("SELECT COUNT(*) as total FROM candidaturas WHERE status = 'em_analise'");
    $stats['aprovacoes_pendentes'] = $stmt->fetch()['total'];
    
    // Candidatos recentes
    $stmt = $db->query("
        SELECT 
            c.*,
            GROUP_CONCAT(CONCAT(v.titulo, ' - ', v.empresa) SEPARATOR '; ') as vagas_candidatas
        FROM candidatos c
        LEFT JOIN candidaturas ca ON c.id = ca.candidato_id
        LEFT JOIN vagas v ON ca.vaga_id = v.id
        GROUP BY c.id
        ORDER BY c.data_cadastro DESC
        LIMIT 10
    ");
    $candidatos_recentes = $stmt->fetchAll();
    
    // Atividades recentes
    $stmt = $db->query("
        SELECT 
            a.*,
            c.nome as candidato_nome,
            v.titulo as vaga_titulo
        FROM atividades a
        LEFT JOIN candidatos c ON a.candidato_id = c.id
        LEFT JOIN vagas v ON a.vaga_id = v.id
        ORDER BY a.data_atividade DESC
        LIMIT 10
    ");
    $atividades_recentes = $stmt->fetchAll();
    
    // Candidaturas por status
    $stmt = $db->query("
        SELECT 
            status,
            COUNT(*) as total
        FROM candidaturas
        GROUP BY status
    ");
    $candidaturas_por_status = $stmt->fetchAll();
    
    // Vagas mais populares
    $stmt = $db->query("
        SELECT 
            v.titulo,
            v.empresa,
            COUNT(c.id) as total_candidaturas
        FROM vagas v
        LEFT JOIN candidaturas c ON v.id = c.vaga_id
        WHERE v.status = 'ativa'
        GROUP BY v.id
        ORDER BY total_candidaturas DESC
        LIMIT 5
    ");
    $vagas_populares = $stmt->fetchAll();
    
    // Retornar JSON se for requisição AJAX
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'candidatos_recentes' => $candidatos_recentes,
            'atividades_recentes' => $atividades_recentes,
            'candidaturas_por_status' => $candidaturas_por_status,
            'vagas_populares' => $vagas_populares
        ]);
        exit;
    }
    
} catch (Exception $e) {
    if (isset($_GET['ajax'])) {
        echo generateApiResponse(false, $e->getMessage());
        exit;
    } else {
        $stats = ['candidatos_ativos' => 0, 'vagas_abertas' => 0, 'entrevistas_agendadas' => 0, 'aprovacoes_pendentes' => 0];
        $candidatos_recentes = [];
        $atividades_recentes = [];
    }
}

// Ações do painel admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'atualizar_status_candidato':
                $candidato_id = intval($_POST['candidato_id']);
                $novo_status = $_POST['status'];
                
                $stmt = $db->prepare("UPDATE candidatos SET status = ? WHERE id = ?");
                $stmt->execute([$novo_status, $candidato_id]);
                
                // Buscar dados do candidato
                $stmt = $db->prepare("SELECT nome FROM candidatos WHERE id = ?");
                $stmt->execute([$candidato_id]);
                $candidato = $stmt->fetch();
                
                logActivity($db, 'aprovacao', 
                    "Status do candidato {$candidato['nome']} alterado para: {$novo_status}", 
                    $candidato_id, null, SessionManager::get('admin_id'));
                
                echo generateApiResponse(true, 'Status atualizado com sucesso!');
                break;
                
            case 'agendar_entrevista':
                $candidatura_id = intval($_POST['candidatura_id']);
                $data_entrevista = $_POST['data_entrevista'];
                $tipo = $_POST['tipo'] ?? 'video_call';
                $link_reuniao = $_POST['link_reuniao'] ?? '';
                $observacoes = $_POST['observacoes'] ?? '';
                
                $stmt = $db->prepare("
                    INSERT INTO entrevistas (candidatura_id, data_entrevista, tipo, link_reuniao, observacoes)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$candidatura_id, $data_entrevista, $tipo, $link_reuniao, $observacoes]);
                
                // Atualizar status da candidatura
                $stmt = $db->prepare("UPDATE candidaturas SET status = 'entrevista_agendada' WHERE id = ?");
                $stmt->execute([$candidatura_id]);
                
                logActivity($db, 'entrevista', 
                    "Entrevista agendada para: {$data_entrevista}", 
                    null, null, SessionManager::get('admin_id'));
                
                echo generateApiResponse(true, 'Entrevista agendada com sucesso!');
                break;
                
            case 'exportar_candidatos':
                // Buscar todos os candidatos
                $stmt = $db->query("
                    SELECT 
                        c.*,
                        GROUP_CONCAT(CONCAT(v.titulo, ' (', ca.status, ')') SEPARATOR '; ') as candidaturas
                    FROM candidatos c
                    LEFT JOIN candidaturas ca ON c.id = ca.candidato_id
                    LEFT JOIN vagas v ON ca.vaga_id = v.id
                    GROUP BY c.id
                    ORDER BY c.data_cadastro DESC
                ");
                $candidatos = $stmt->fetchAll();
                
                // Gerar CSV
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=candidatos_eniac_link_' . date('Y-m-d') . '.csv');
                
                $output = fopen('php://output', 'w');
                fputcsv($output, [
                    'ID', 'Nome', 'Email', 'Telefone', 'CPF', 'Data Nascimento',
                    'Cidade', 'Estado', 'Escolaridade', 'Curso', 'Experiência',
                    'Pretensão Salarial', 'Status', 'Data Cadastro', 'Candidaturas'
                ]);
                
                foreach ($candidatos as $candidato) {
                    fputcsv($output, [
                        $candidato['id'],
                        $candidato['nome'],
                        $candidato['email'],
                        $candidato['telefone'],
                        $candidato['cpf'],
                        $candidato['data_nascimento'],
                        $candidato['cidade'],
                        $candidato['estado'],
                        $candidato['escolaridade'],
                        $candidato['curso'],
                        $candidato['experiencia'],
                        $candidato['pretensao_salarial'] ? formatCurrency($candidato['pretensao_salarial']) : '',
                        $candidato['status'],
                        formatDateTime($candidato['data_cadastro']),
                        $candidato['candidaturas']
                    ]);
                }
                
                fclose($output);
                exit;
                break;
                
            default:
                throw new Exception('Ação não reconhecida.');
        }
        
    } catch (Exception $e) {
        echo generateApiResponse(false, $e->getMessage());
    }
    exit;
}
?>
