<?php
require_once 'config.php';

$message = '';
$error = '';

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    
    if (empty($nome) || empty($email)) {
        $error = 'Nome e email são obrigatórios.';
    } else {
        try {
            $database = new Database();
            $pdo = $database->connect();
            
            // Verificar senha atual se uma nova senha foi fornecida
            if (!empty($nova_senha)) {
                if (empty($senha_atual)) {
                    $error = 'Senha atual é obrigatória para alterar a senha.';
                } else {
                    // Verificar senha atual
                    $stmt = $pdo->prepare("SELECT senha_hash FROM usuarios_admin WHERE email = 'admin@eniaclink.com'");
                    $stmt->execute();
                    $admin = $stmt->fetch();
                    
                    if (!$admin || !password_verify($senha_atual, $admin['senha_hash'])) {
                        $error = 'Senha atual incorreta.';
                    }
                }
            }
            
            if (empty($error)) {
                // Atualizar perfil
                if (!empty($nova_senha)) {
                    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuarios_admin SET nome = ?, email = ?, senha_hash = ? WHERE email = 'admin@eniaclink.com' OR id = 1");
                    $stmt->execute([$nome, $email, $senha_hash]);
                } else {
                    $stmt = $pdo->prepare("UPDATE usuarios_admin SET nome = ?, email = ? WHERE email = 'admin@eniaclink.com' OR id = 1");
                    $stmt->execute([$nome, $email]);
                }
                
                $message = 'Perfil atualizado com sucesso! As alterações serão aplicadas no próximo login.';
            }
            
        } catch (Exception $e) {
            $error = 'Erro ao atualizar perfil: ' . $e->getMessage();
        }
    }
}

// Buscar dados atuais do admin
try {
    $database = new Database();
    $pdo = $database->connect();
    
    $stmt = $pdo->prepare("SELECT nome, email FROM usuarios_admin WHERE email = 'admin@eniaclink.com' OR id = 1 LIMIT 1");
    $stmt->execute();
    $admin_atual = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $admin_atual = ['nome' => 'Administrador', 'email' => 'admin@eniaclink.com'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Perfil Administrador - ENIAC LINK+</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .help-text {
            font-size: 0.875rem;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-cog"></i> Atualizar Perfil</h1>
            <p>Configure as informações do administrador</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nome">
                    <i class="fas fa-user"></i> Nome Completo
                </label>
                <input 
                    type="text" 
                    id="nome" 
                    name="nome" 
                    value="<?php echo htmlspecialchars($admin_atual['nome'] ?? ''); ?>" 
                    required
                >
                <div class="help-text">Este nome será exibido no painel administrativo</div>
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?php echo htmlspecialchars($admin_atual['email'] ?? ''); ?>" 
                    required
                >
                <div class="help-text">Email usado para fazer login no sistema</div>
            </div>

            <div class="form-group">
                <label for="senha_atual">
                    <i class="fas fa-lock"></i> Senha Atual
                </label>
                <input 
                    type="password" 
                    id="senha_atual" 
                    name="senha_atual"
                    placeholder="Deixe em branco se não quiser alterar a senha"
                >
                <div class="help-text">Necessário apenas se quiser alterar a senha</div>
            </div>

            <div class="form-group">
                <label for="nova_senha">
                    <i class="fas fa-key"></i> Nova Senha
                </label>
                <input 
                    type="password" 
                    id="nova_senha" 
                    name="nova_senha"
                    placeholder="Deixe em branco para manter a senha atual"
                >
                <div class="help-text">Mínimo de 6 caracteres (opcional)</div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
        </form>

        <div class="back-link">
            <a href="admin.php">
                <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
            </a>
        </div>
    </div>
</body>
</html>
