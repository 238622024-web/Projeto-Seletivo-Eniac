<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '256M');

require_once 'config.php';

// Verificar se há sessão ativa
$is_admin = false;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $is_admin = true;
    $admin_nome = $_SESSION['admin_nome'] ?? 'Administrador';
}

// Processar ações administrativas
$message = '';
$message_type = '';

if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $pdo = $database->connect();
        
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'create':
                    $stmt = $pdo->prepare("INSERT INTO vagas (titulo, descricao, empresa, localizacao, salario, tipo, requisitos, beneficios, status, data_criacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'ativa', NOW())");
                    $result = $stmt->execute([
                        $_POST['titulo'],
                        $_POST['descricao'],
                        $_POST['empresa'],
                        $_POST['localizacao'],
                        $_POST['salario'],
                        $_POST['tipo'],
                        $_POST['requisitos'] ?? '',
                        $_POST['beneficios'] ?? ''
                    ]);
                    
                    if ($result) {
                        $message = "Vaga criada com sucesso!";
                        $message_type = "success";
                    } else {
                        $message = "Erro ao criar vaga.";
                        $message_type = "error";
                    }
                    break;
                    
                case 'edit':
                    $stmt = $pdo->prepare("UPDATE vagas SET titulo=?, descricao=?, empresa=?, localizacao=?, salario=?, tipo=?, requisitos=?, beneficios=?, status=? WHERE id=?");
                    $result = $stmt->execute([
                        $_POST['titulo'],
                        $_POST['descricao'],
                        $_POST['empresa'],
                        $_POST['localizacao'],
                        $_POST['salario'],
                        $_POST['tipo'],
                        $_POST['requisitos'] ?? '',
                        $_POST['beneficios'] ?? '',
                        $_POST['status'],
                        $_POST['vaga_id']
                    ]);
                    
                    if ($result) {
                        $message = "Vaga atualizada com sucesso!";
                        $message_type = "success";
                    } else {
                        $message = "Erro ao atualizar vaga.";
                        $message_type = "error";
                    }
                    break;
                    
                case 'delete':
                    // Primeiro excluir candidaturas relacionadas
                    $stmt = $pdo->prepare("DELETE FROM candidaturas WHERE vaga_id = ?");
                    $stmt->execute([$_POST['vaga_id']]);
                    
                    // Depois excluir a vaga
                    $stmt = $pdo->prepare("DELETE FROM vagas WHERE id = ?");
                    $result = $stmt->execute([$_POST['vaga_id']]);
                    
                    if ($result) {
                        $message = "Vaga excluída com sucesso!";
                        $message_type = "success";
                    } else {
                        $message = "Erro ao excluir vaga.";
                        $message_type = "error";
                    }
                    break;
            }
        }
    } catch (PDOException $e) {
        $message = "Erro no banco de dados: " . $e->getMessage();
        $message_type = "error";
    }
}

// Buscar vagas
$vagas = [];
$vagas_admin = [];

try {
    $database = new Database();
    $pdo = $database->connect();
    
    $sql = "SELECT v.*, COUNT(c.id) as total_candidaturas 
            FROM vagas v 
            LEFT JOIN candidaturas c ON v.id = c.vaga_id 
            WHERE v.status = 'ativa'
            GROUP BY v.id 
            ORDER BY v.data_criacao DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $vagas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Se for admin, buscar todas as vagas (ativas e pausadas)
    if ($is_admin) {
        $sql_admin = "SELECT v.*, COUNT(c.id) as total_candidaturas 
                      FROM vagas v 
                      LEFT JOIN candidaturas c ON v.id = c.vaga_id 
                      GROUP BY v.id 
                      ORDER BY v.data_criacao DESC";
        
        $stmt_admin = $pdo->prepare($sql_admin);
        $stmt_admin->execute();
        $vagas_admin = $stmt_admin->fetchAll(PDO::FETCH_ASSOC);
    }
    
} catch (PDOException $e) {
    $vagas = [];
    $vagas_admin = [];
    $message = "Erro ao conectar com o banco de dados: " . $e->getMessage();
    $message_type = "error";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vagas - ENIAC LINK+</title>
    <link rel="icon" href="imagens/Logoindex.jpg" type="image/jpeg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

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

        nav {
            display: flex;
            gap: 2rem;
            align-items: center;
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
        }

        .admin-panel {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            margin: 20px auto;
            max-width: 1200px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #667eea;
        }

        .admin-welcome {
            color: #667eea;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .message.success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .message.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .form-container {
            background: rgba(248, 250, 252, 0.8);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .vagas-publicas {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .vagas-header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .vagas-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .vagas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }

        .vaga-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .vaga-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .vaga-title {
            color: #667eea;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .vaga-empresa {
            color: #764ba2;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .vaga-info {
            margin-bottom: 20px;
        }

        .vaga-info-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #555;
        }

        .vaga-info-item i {
            width: 20px;
            color: #667eea;
            margin-right: 10px;
        }

        .vaga-descricao {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .btn-candidatar {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            width: 100%;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-candidatar:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
            }

            nav {
                flex-wrap: wrap;
                justify-content: center;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .vagas-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
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
                <a href="vagas.php"><i class="fas fa-briefcase"></i> Vagas</a>
                <a href="cadastro.php"><i class="fas fa-user-plus"></i> Cadastrar</a>
                <?php if ($is_admin): ?>
                    <a href="admin.php"><i class="fas fa-cog"></i> Painel Admin</a>
                    <a href="login_admin.php?logout=1"><i class="fas fa-sign-out-alt"></i> Sair</a>
                <?php else: ?>
                    <a href="login_admin.php"><i class="fas fa-sign-in-alt"></i> Admin</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <?php if ($is_admin): ?>
        <!-- Painel Administrativo -->
        <div class="admin-panel">
            <div class="admin-header">
                <h1 class="admin-welcome">Gerenciamento de Vagas</h1>
                <span style="color: #667eea;">Bem-vindo, <?php echo htmlspecialchars($admin_nome); ?>!</span>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Formulário de Criação -->
            <div class="form-container">
                <h3 style="margin-bottom: 20px; color: #667eea;">
                    <i class="fas fa-plus-circle"></i> Nova Vaga
                </h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="titulo">Título da Vaga *</label>
                            <input type="text" id="titulo" name="titulo" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="empresa">Empresa *</label>
                            <input type="text" id="empresa" name="empresa" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="localizacao">Localização *</label>
                            <input type="text" id="localizacao" name="localizacao" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="salario">Salário</label>
                            <input type="text" id="salario" name="salario" placeholder="Ex: R$ 3.000,00 - R$ 5.000,00">
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo">Tipo de Contrato *</label>
                            <select id="tipo" name="tipo" required>
                                <option value="">Selecione...</option>
                                <option value="CLT">CLT</option>
                                <option value="PJ">PJ</option>
                                <option value="Estágio">Estágio</option>
                                <option value="Freelancer">Freelancer</option>
                                <option value="Temporário">Temporário</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao">Descrição da Vaga *</label>
                        <textarea id="descricao" name="descricao" rows="4" required placeholder="Descreva as principais responsabilidades e atividades da vaga..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="requisitos">Requisitos</label>
                        <textarea id="requisitos" name="requisitos" rows="3" placeholder="Liste os requisitos necessários para a vaga..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="beneficios">Benefícios</label>
                        <textarea id="beneficios" name="beneficios" rows="3" placeholder="Liste os benefícios oferecidos..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Criar Vaga
                    </button>
                </form>
            </div>

            <!-- Lista de Vagas Admin -->
            <h3 style="color: #667eea; margin-bottom: 20px;">
                <i class="fas fa-list"></i> Vagas Cadastradas (<?php echo count($vagas_admin); ?>)
            </h3>
            
            <div class="vagas-grid">
                <?php if (!empty($vagas_admin)): ?>
                    <?php foreach ($vagas_admin as $vaga): ?>
                        <div class="vaga-card">
                            <h3 class="vaga-title"><?php echo htmlspecialchars($vaga['titulo']); ?></h3>
                            <p class="vaga-empresa"><?php echo htmlspecialchars($vaga['empresa']); ?></p>
                            
                            <div class="vaga-info">
                                <div class="vaga-info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($vaga['localizacao']); ?></span>
                                </div>
                                <div class="vaga-info-item">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span><?php echo htmlspecialchars($vaga['salario'] ?: 'A combinar'); ?></span>
                                </div>
                                <div class="vaga-info-item">
                                    <i class="fas fa-briefcase"></i>
                                    <span><?php echo htmlspecialchars($vaga['tipo']); ?></span>
                                </div>
                                <div class="vaga-info-item">
                                    <i class="fas fa-users"></i>
                                    <span><?php echo $vaga['total_candidaturas']; ?> candidatura(s)</span>
                                </div>
                                <div class="vaga-info-item">
                                    <i class="fas fa-toggle-<?php echo $vaga['status'] === 'ativa' ? 'on' : 'off'; ?>"></i>
                                    <span>Status: <?php echo ucfirst($vaga['status']); ?></span>
                                </div>
                            </div>
                            
                            <div class="vaga-descricao">
                                <?php echo nl2br(htmlspecialchars(substr($vaga['descricao'], 0, 200) . (strlen($vaga['descricao']) > 200 ? '...' : ''))); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; color: #666; padding: 40px; grid-column: 1 / -1;">
                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 20px; display: block;"></i>
                        Nenhuma vaga encontrada
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <!-- Visualização Pública -->
        <div class="vagas-publicas">
            <div class="vagas-header">
                <h1><i class="fas fa-briefcase"></i> Vagas Disponíveis</h1>
                <p>Encontre a oportunidade perfeita para sua carreira</p>
            </div>
            
            <div class="vagas-grid">
                <?php if (!empty($vagas)): ?>
                    <?php foreach ($vagas as $vaga): ?>
                        <div class="vaga-card">
                            <h3 class="vaga-title"><?php echo htmlspecialchars($vaga['titulo']); ?></h3>
                            <p class="vaga-empresa"><?php echo htmlspecialchars($vaga['empresa']); ?></p>
                            
                            <div class="vaga-info">
                                <div class="vaga-info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($vaga['localizacao']); ?></span>
                                </div>
                                <div class="vaga-info-item">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span><?php echo htmlspecialchars($vaga['salario'] ?: 'A combinar'); ?></span>
                                </div>
                                <div class="vaga-info-item">
                                    <i class="fas fa-briefcase"></i>
                                    <span><?php echo htmlspecialchars($vaga['tipo']); ?></span>
                                </div>
                                <div class="vaga-info-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Publicada em <?php echo date('d/m/Y', strtotime($vaga['data_criacao'])); ?></span>
                                </div>
                            </div>
                            
                            <div class="vaga-descricao">
                                <?php echo nl2br(htmlspecialchars(substr($vaga['descricao'], 0, 200) . (strlen($vaga['descricao']) > 200 ? '...' : ''))); ?>
                            </div>
                            
                            <?php if (!empty($vaga['requisitos'])): ?>
                                <div style="margin: 15px 0;">
                                    <strong style="color: #667eea;">Requisitos:</strong>
                                    <p style="font-size: 0.9rem; color: #666;">
                                        <?php echo nl2br(htmlspecialchars(substr($vaga['requisitos'], 0, 100) . (strlen($vaga['requisitos']) > 100 ? '...' : ''))); ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                            
                            <a href="candidatar_vaga.php?id=<?php echo $vaga['id']; ?>" class="btn-candidatar">
                                <i class="fas fa-paper-plane"></i> Candidatar-se
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; color: white; padding: 40px; grid-column: 1 / -1;">
                        <i class="fas fa-inbox" style="font-size: 4rem; margin-bottom: 20px; display: block;"></i>
                        <h2>Nenhuma vaga disponível no momento</h2>
                        <p style="font-size: 1.1rem; margin-top: 10px;">Volte em breve para conferir novas oportunidades!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // Auto-hide messages
        setTimeout(function() {
            const messages = document.querySelectorAll('.message');
            messages.forEach(function(message) {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s ease';
                setTimeout(function() {
                    message.style.display = 'none';
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>
