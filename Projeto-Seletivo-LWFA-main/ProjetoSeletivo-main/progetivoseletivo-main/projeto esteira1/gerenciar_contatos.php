<?php
require_once 'config.php';
SessionManager::start();

// Verificar se o usuário está logado
if (!SessionManager::has('admin_logged_in')) {
    header('Location: login_admin.php');
    exit;
}

$admin_nome = SessionManager::get('admin_nome', 'Administrador');
$admin_id = SessionManager::get('admin_id');

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $contato_id = (int)($_POST['contato_id'] ?? 0);
    
    if ($acao === 'responder' && $contato_id > 0) {
        $resposta = sanitizeInput($_POST['resposta'] ?? '');
        $novo_status = sanitizeInput($_POST['novo_status'] ?? 'respondido');
        
        if (!empty($resposta)) {
            try {
                $database = new Database();
                $pdo = $database->connect();
                
                $sql = "UPDATE contatos SET resposta = ?, status = ?, admin_responsavel_id = ?, data_resposta = NOW() WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$resposta, $novo_status, $admin_id, $contato_id]);
                
                $success_message = 'Resposta enviada com sucesso!';
            } catch (Exception $e) {
                $error_message = 'Erro ao enviar resposta: ' . $e->getMessage();
            }
        }
    } elseif ($acao === 'alterar_status' && $contato_id > 0) {
        $novo_status = sanitizeInput($_POST['novo_status'] ?? '');
        
        try {
            $database = new Database();
            $pdo = $database->connect();
            
            $sql = "UPDATE contatos SET status = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$novo_status, $contato_id]);
            
            $success_message = 'Status atualizado com sucesso!';
        } catch (Exception $e) {
            $error_message = 'Erro ao atualizar status: ' . $e->getMessage();
        }
    }
}

// Buscar contatos
try {
    $database = new Database();
    $pdo = $database->connect();
    
    $filtro_status = $_GET['status'] ?? '';
    $filtro_categoria = $_GET['categoria'] ?? '';
    
    $where_clauses = [];
    $params = [];
    
    if (!empty($filtro_status)) {
        $where_clauses[] = "status = ?";
        $params[] = $filtro_status;
    }
    
    if (!empty($filtro_categoria)) {
        $where_clauses[] = "categoria = ?";
        $params[] = $filtro_categoria;
    }
    
    $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';
    
    $sql = "SELECT c.*, u.nome as admin_nome 
            FROM contatos c 
            LEFT JOIN usuarios_admin u ON c.admin_responsavel_id = u.id 
            $where_sql 
            ORDER BY c.data_envio DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $contatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Estatísticas
    $stats_sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'novo' THEN 1 ELSE 0 END) as novos,
                    SUM(CASE WHEN status = 'em_andamento' THEN 1 ELSE 0 END) as em_andamento,
                    SUM(CASE WHEN status = 'respondido' THEN 1 ELSE 0 END) as respondidos,
                    SUM(CASE WHEN status = 'resolvido' THEN 1 ELSE 0 END) as resolvidos
                  FROM contatos";
    $stats = $pdo->query($stats_sql)->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $contatos = [];
    $stats = ['total' => 0, 'novos' => 0, 'em_andamento' => 0, 'respondidos' => 0, 'resolvidos' => 0];
    error_log("Erro ao buscar contatos: " . $e->getMessage());
}

// Função para formatar status
function formatarStatusContato($status) {
    $status_map = [
        'novo' => ['Novo', 'danger'],
        'em_andamento' => ['Em Andamento', 'warning'],
        'respondido' => ['Respondido', 'info'],
        'resolvido' => ['Resolvido', 'success']
    ];
    return $status_map[$status] ?? ['Desconhecido', 'secondary'];
}

// Função para formatar categoria
function formatarCategoria($categoria) {
    $categoria_map = [
        'suporte_tecnico' => 'Suporte Técnico',
        'duvidas_vagas' => 'Dúvidas sobre Vagas',
        'problemas_cadastro' => 'Problemas no Cadastro',
        'empresas' => 'Para Empresas',
        'sugestoes' => 'Sugestões',
        'outros' => 'Outros'
    ];
    return $categoria_map[$categoria] ?? 'Desconhecido';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Contatos | Admin - ENIAC LINK+</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        /* Header - Mesmo padrão */
        header {
            background: linear-gradient(135deg, #0056b3, #004494);
            padding: 0;
            color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        /* Main Content */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .page-title h1 {
            color: #0056b3;
            font-size: 1.8rem;
            margin-bottom: 0.25rem;
        }

        .page-title p {
            color: #666;
            font-size: 0.95rem;
        }

        /* Estatísticas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #0056b3;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #0056b3;
            display: block;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        /* Filtros */
        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .filters-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .filter-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .filter-group select {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.3s ease;
        }

        .filter-group select:focus {
            outline: none;
            border-color: #0056b3;
        }

        .btn-filter {
            background: #0056b3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .btn-filter:hover {
            background: #004494;
        }

        /* Tabela de Contatos */
        .contacts-table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table-header {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e0e0e0;
        }

        .table-header h3 {
            color: #333;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .contacts-table {
            width: 100%;
            border-collapse: collapse;
        }

        .contacts-table th,
        .contacts-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .contacts-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .contact-name {
            font-weight: 600;
            color: #333;
        }

        .contact-email {
            font-size: 0.85rem;
            color: #666;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-danger { background: #fee; color: #c53030; }
        .status-warning { background: #fff3cd; color: #856404; }
        .status-info { background: #cce7ff; color: #0056b3; }
        .status-success { background: #d1f2eb; color: #00875a; }

        .category-badge {
            background: #f0f0f0;
            color: #666;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        /* Ações */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-view {
            background: #0056b3;
            color: white;
        }

        .btn-view:hover {
            background: #004494;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background: white;
            margin: 2% auto;
            padding: 0;
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
            border-radius: 10px 10px 0 0;
        }

        .modal-header h2 {
            color: #333;
            font-size: 1.3rem;
        }

        .close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .contact-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .detail-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
        }

        .detail-label {
            font-weight: 600;
            color: #666;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            color: #333;
            font-size: 0.95rem;
        }

        .message-content {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #0056b3;
            margin: 1rem 0;
        }

        .response-form {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e0e0e0;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            background: #0056b3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-submit:hover {
            background: #004494;
        }

        /* Alertas */
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-success {
            background: #d1f2eb;
            color: #00875a;
            border: 1px solid #9ae6c8;
        }

        .alert-error {
            background: #fee;
            color: #c53030;
            border: 1px solid #feb2b2;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .contacts-table {
                font-size: 0.9rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .modal-content {
                margin: 5% auto;
                width: 95%;
            }

            .contact-details {
                grid-template-columns: 1fr;
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
                    <i class="fas fa-envelope"></i>
                </div>
                <h2>ENIAC LINK+ | Contatos</h2>
            </div>
            <div class="admin-info">
                <i class="fas fa-user-shield"></i>
                <span><?php echo htmlspecialchars($admin_nome); ?></span>
                <a href="admin.php" style="color: white; margin-left: 15px;">
                    <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fas fa-envelope"></i> Gerenciar Contatos</h1>
                <p>Visualize e responda às mensagens enviadas através do formulário "Fale Conosco"</p>
            </div>
        </div>

        <!-- Alertas -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['total']; ?></span>
                <span class="stat-label">Total de Contatos</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['novos']; ?></span>
                <span class="stat-label">Novos</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['em_andamento']; ?></span>
                <span class="stat-label">Em Andamento</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['respondidos']; ?></span>
                <span class="stat-label">Respondidos</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['resolvidos']; ?></span>
                <span class="stat-label">Resolvidos</span>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters">
            <form method="GET" action="">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="">Todos os Status</option>
                            <option value="novo" <?php echo ($filtro_status === 'novo') ? 'selected' : ''; ?>>Novo</option>
                            <option value="em_andamento" <?php echo ($filtro_status === 'em_andamento') ? 'selected' : ''; ?>>Em Andamento</option>
                            <option value="respondido" <?php echo ($filtro_status === 'respondido') ? 'selected' : ''; ?>>Respondido</option>
                            <option value="resolvido" <?php echo ($filtro_status === 'resolvido') ? 'selected' : ''; ?>>Resolvido</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Categoria</label>
                        <select name="categoria">
                            <option value="">Todas as Categorias</option>
                            <option value="suporte_tecnico" <?php echo ($filtro_categoria === 'suporte_tecnico') ? 'selected' : ''; ?>>Suporte Técnico</option>
                            <option value="duvidas_vagas" <?php echo ($filtro_categoria === 'duvidas_vagas') ? 'selected' : ''; ?>>Dúvidas sobre Vagas</option>
                            <option value="problemas_cadastro" <?php echo ($filtro_categoria === 'problemas_cadastro') ? 'selected' : ''; ?>>Problemas no Cadastro</option>
                            <option value="empresas" <?php echo ($filtro_categoria === 'empresas') ? 'selected' : ''; ?>>Para Empresas</option>
                            <option value="sugestoes" <?php echo ($filtro_categoria === 'sugestoes') ? 'selected' : ''; ?>>Sugestões</option>
                            <option value="outros" <?php echo ($filtro_categoria === 'outros') ? 'selected' : ''; ?>>Outros</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabela de Contatos -->
        <div class="contacts-table-container">
            <div class="table-header">
                <h3>Mensagens de Contato (<?php echo count($contatos); ?>)</h3>
            </div>
            
            <?php if (!empty($contatos)): ?>
                <table class="contacts-table">
                    <thead>
                        <tr>
                            <th>Contato</th>
                            <th>Categoria</th>
                            <th>Assunto</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contatos as $contato): ?>
                            <?php 
                            $status_info = formatarStatusContato($contato['status']);
                            $categoria_formatada = formatarCategoria($contato['categoria']);
                            ?>
                            <tr>
                                <td>
                                    <div class="contact-info">
                                        <div class="contact-name"><?php echo htmlspecialchars($contato['nome']); ?></div>
                                        <div class="contact-email"><?php echo htmlspecialchars($contato['email']); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge"><?php echo $categoria_formatada; ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($contato['assunto']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $status_info[1]; ?>">
                                        <?php echo $status_info[0]; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($contato['data_envio'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-view" onclick="viewContact(<?php echo $contato['id']; ?>)">
                                            <i class="fas fa-eye"></i> Ver
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding: 2rem; text-align: center; color: #666;">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                    <p>Nenhuma mensagem encontrada.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para Visualizar/Responder Contato -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detalhes do Contato</h2>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Conteúdo será carregado via JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // Dados dos contatos para JavaScript
        const contatos = <?php echo json_encode($contatos); ?>;

        function viewContact(id) {
            const contato = contatos.find(c => c.id == id);
            if (!contato) return;

            const statusInfo = {
                'novo': ['Novo', 'danger'],
                'em_andamento': ['Em Andamento', 'warning'],
                'respondido': ['Respondido', 'info'],
                'resolvido': ['Resolvido', 'success']
            };

            const categoriaInfo = {
                'suporte_tecnico': 'Suporte Técnico',
                'duvidas_vagas': 'Dúvidas sobre Vagas',
                'problemas_cadastro': 'Problemas no Cadastro',
                'empresas': 'Para Empresas',
                'sugestoes': 'Sugestões',
                'outros': 'Outros'
            };

            const status = statusInfo[contato.status] || ['Desconhecido', 'secondary'];
            const categoria = categoriaInfo[contato.categoria] || 'Desconhecido';

            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = `
                <div class="contact-details">
                    <div class="detail-item">
                        <div class="detail-label">Nome</div>
                        <div class="detail-value">${contato.nome}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Email</div>
                        <div class="detail-value">${contato.email}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Telefone</div>
                        <div class="detail-value">${contato.telefone || 'Não informado'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Categoria</div>
                        <div class="detail-value">${categoria}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <span class="status-badge status-${status[1]}">${status[0]}</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Data de Envio</div>
                        <div class="detail-value">${new Date(contato.data_envio).toLocaleString('pt-BR')}</div>
                    </div>
                </div>

                <div class="detail-item" style="grid-column: 1 / -1;">
                    <div class="detail-label">Assunto</div>
                    <div class="detail-value">${contato.assunto}</div>
                </div>

                <div class="message-content">
                    <h4 style="margin-bottom: 1rem; color: #0056b3;">Mensagem:</h4>
                    <p style="white-space: pre-wrap; line-height: 1.6;">${contato.mensagem}</p>
                </div>

                ${contato.resposta ? `
                    <div class="message-content" style="border-left-color: #28a745;">
                        <h4 style="margin-bottom: 1rem; color: #28a745;">Resposta (${contato.admin_nome || 'Admin'}):</h4>
                        <p style="white-space: pre-wrap; line-height: 1.6;">${contato.resposta}</p>
                        <small style="color: #666; margin-top: 1rem; display: block;">
                            Respondido em: ${new Date(contato.data_resposta).toLocaleString('pt-BR')}
                        </small>
                    </div>
                ` : ''}

                <form method="POST" class="response-form">
                    <input type="hidden" name="acao" value="responder">
                    <input type="hidden" name="contato_id" value="${contato.id}">
                    
                    <div class="form-group">
                        <label>Novo Status</label>
                        <select name="novo_status" required>
                            <option value="em_andamento" ${contato.status === 'em_andamento' ? 'selected' : ''}>Em Andamento</option>
                            <option value="respondido" ${contato.status === 'respondido' ? 'selected' : ''}>Respondido</option>
                            <option value="resolvido" ${contato.status === 'resolvido' ? 'selected' : ''}>Resolvido</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Resposta ${contato.resposta ? '(Atualizar)' : ''}</label>
                        <textarea name="resposta" placeholder="Digite sua resposta aqui..." required>${contato.resposta || ''}</textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> 
                        ${contato.resposta ? 'Atualizar Resposta' : 'Enviar Resposta'}
                    </button>
                </form>
            `;

            document.getElementById('contactModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('contactModal').style.display = 'none';
        }

        // Fechar modal clicando fora
        window.onclick = function(event) {
            const modal = document.getElementById('contactModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
