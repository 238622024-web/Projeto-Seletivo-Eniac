<?php
require_once 'config.php';

// Verificar se é uma requisição AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    http_response_code(403);
    exit('Acesso negado');
}

header('Content-Type: application/json');

try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Buscar candidatos com suas candidaturas mais recentes
    $sql = "SELECT c.id, c.nome, c.email, c.status, c.data_cadastro,
                   v.titulo as vaga_titulo,
                   cand.status as candidatura_status,
                   cand.data_candidatura
            FROM candidatos c 
            LEFT JOIN candidaturas cand ON c.id = cand.candidato_id
            LEFT JOIN vagas v ON cand.vaga_id = v.id
            ORDER BY c.data_cadastro DESC 
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
    
    // Preparar dados para retorno
    $resultado = [
        'success' => true,
        'candidatos' => []
    ];
    
    foreach ($candidatos as $candidato) {
        $statusInfo = formatarStatus($candidato['status']);
        $vaga = $candidato['vaga_titulo'] ?? 'Cadastro geral';
        $dataFormatada = formatarData($candidato['data_cadastro']);
        
        $resultado['candidatos'][] = [
            'id' => $candidato['id'],
            'nome' => $candidato['nome'],
            'email' => $candidato['email'],
            'vaga' => $vaga,
            'status' => $statusInfo,
            'data' => $dataFormatada
        ];
    }
    
    echo json_encode($resultado);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao buscar candidatos'
    ]);
    error_log("Erro ao buscar candidatos: " . $e->getMessage());
}
?>
