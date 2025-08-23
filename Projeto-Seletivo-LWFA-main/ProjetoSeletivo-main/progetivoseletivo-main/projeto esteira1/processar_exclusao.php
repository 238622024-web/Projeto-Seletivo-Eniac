<?php
require_once 'config.php';

// Verificar se é uma requisição AJAX e se o admin está logado
SessionManager::start();
if (!SessionManager::has('admin_logged_in')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['candidato_id']) || !isset($input['action'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$candidato_id = (int)$input['candidato_id'];
$action = $input['action'];

try {
    $database = new Database();
    $pdo = $database->connect();
    
    if ($action === 'delete_curriculum') {
        // Buscar informações do candidato
        $stmt = $pdo->prepare("SELECT nome, curriculo_arquivo FROM candidatos WHERE id = ?");
        $stmt->execute([$candidato_id]);
        $candidato = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$candidato) {
            echo json_encode(['success' => false, 'message' => 'Candidato não encontrado']);
            exit;
        }
        
        $arquivo_curriculo = $candidato['curriculo_arquivo'];
        $nome_candidato = $candidato['nome'];
        
        // Remover arquivo físico se existir
        $uploads_dir = 'uploads/';
        $arquivo_path = $uploads_dir . $arquivo_curriculo;
        $arquivo_removido = false;
        
        if ($arquivo_curriculo && file_exists($arquivo_path)) {
            if (unlink($arquivo_path)) {
                $arquivo_removido = true;
            }
        }
        
        // Atualizar banco de dados - remover referência ao currículo
        $stmt = $pdo->prepare("UPDATE candidatos SET curriculo_arquivo = NULL WHERE id = ?");
        $sucesso_bd = $stmt->execute([$candidato_id]);
        
        if ($sucesso_bd) {
            // Registrar a atividade
            $admin_id = SessionManager::get('admin_id');
            $admin_nome = SessionManager::get('admin_nome');
            $descricao = "Currículo de {$nome_candidato} foi excluído pelo admin {$admin_nome}";
            
            // Log da atividade (se a tabela existir)
            try {
                $stmt = $pdo->prepare("INSERT INTO atividades (tipo, descricao, candidato_id, admin_id) VALUES (?, ?, ?, ?)");
                $stmt->execute(['exclusao_curriculo', $descricao, $candidato_id, $admin_id]);
            } catch (Exception $e) {
                // Se não conseguir registrar atividade, continua mesmo assim
                error_log("Erro ao registrar atividade: " . $e->getMessage());
            }
            
            $message = "Currículo excluído com sucesso!";
            if ($arquivo_removido) {
                $message .= " Arquivo físico também foi removido.";
            } elseif ($arquivo_curriculo) {
                $message .= " (Arquivo físico não foi encontrado)";
            }
            
            echo json_encode([
                'success' => true, 
                'message' => $message,
                'candidato_nome' => $nome_candidato
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar banco de dados']);
        }
        
    } elseif ($action === 'delete_candidate') {
        // Excluir candidato completamente (opcional)
        $stmt = $pdo->prepare("SELECT nome, curriculo_arquivo FROM candidatos WHERE id = ?");
        $stmt->execute([$candidato_id]);
        $candidato = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$candidato) {
            echo json_encode(['success' => false, 'message' => 'Candidato não encontrado']);
            exit;
        }
        
        $arquivo_curriculo = $candidato['curriculo_arquivo'];
        $nome_candidato = $candidato['nome'];
        
        // Remover arquivo físico se existir
        if ($arquivo_curriculo && file_exists('uploads/' . $arquivo_curriculo)) {
            unlink('uploads/' . $arquivo_curriculo);
        }
        
        // Excluir candidato (CASCADE irá remover candidaturas e entrevistas relacionadas)
        $stmt = $pdo->prepare("DELETE FROM candidatos WHERE id = ?");
        $sucesso = $stmt->execute([$candidato_id]);
        
        if ($sucesso) {
            echo json_encode([
                'success' => true, 
                'message' => "Candidato {$nome_candidato} foi excluído completamente do sistema",
                'candidato_nome' => $nome_candidato
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao excluir candidato']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
    }
    
} catch (PDOException $e) {
    error_log("Erro ao processar exclusão: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>
