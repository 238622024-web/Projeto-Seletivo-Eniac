<?php
require_once 'config.php';

// Script para alterar senha do administrador
echo "<h2>Alterar Senha do Administrador</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    if (empty($nova_senha) || empty($confirmar_senha)) {
        echo "<p style='color: red;'>Todos os campos são obrigatórios!</p>";
    } elseif ($nova_senha !== $confirmar_senha) {
        echo "<p style='color: red;'>As senhas não coincidem!</p>";
    } elseif (strlen($nova_senha) < 6) {
        echo "<p style='color: red;'>A senha deve ter pelo menos 6 caracteres!</p>";
    } else {
        try {
            $db = (new Database())->connect();
            
            // Gerar hash da nova senha
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            
            // Atualizar a senha no banco
            $stmt = $db->prepare("UPDATE usuarios_admin SET senha_hash = ? WHERE email = 'admin@eniaclink.com'");
            
            if ($stmt->execute([$senha_hash])) {
                echo "<p style='color: green; font-weight: bold;'>✅ Senha alterada com sucesso!</p>";
                echo "<p><a href='login_admin.php'>Fazer login com a nova senha</a></p>";
            } else {
                echo "<p style='color: red;'>Erro ao alterar a senha!</p>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha Admin - ENIAC LINK+</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #1e3c72;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="password"]:focus {
            border-color: #1e3c72;
            outline: none;
        }
        button {
            width: 100%;
            background: #1e3c72;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #2a5298;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #1e3c72;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #1e3c72;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="info">
            <strong>ℹ️ Informações:</strong><br>
            • Email do admin: <strong>admin@eniaclink.com</strong><br>
            • Senha atual: <strong>password</strong><br>
            • A nova senha deve ter pelo menos 6 caracteres
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="nova_senha">Nova Senha:</label>
                <input type="password" id="nova_senha" name="nova_senha" required minlength="6">
            </div>

            <div class="form-group">
                <label for="confirmar_senha">Confirmar Nova Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required minlength="6">
            </div>

            <button type="submit">Alterar Senha</button>
        </form>

        <div class="back-link">
            <a href="login_admin.php">← Voltar para o Login</a>
        </div>
    </div>
</body>
</html>
