<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartilhar Minha História - ENIAC LINK+</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .testimonial-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 600px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header p {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
            border: 2px solid #6c757d;
        }

        .btn-secondary:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-2px);
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #155724;
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #721c24;
        }

        .info-box {
            background: rgba(23, 162, 184, 0.1);
            border: 1px solid rgba(23, 162, 184, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .info-box h3 {
            color: #17a2b8;
            margin-bottom: 10px;
            font-size: 1.3rem;
        }

        .info-box p {
            color: #666;
            line-height: 1.6;
            margin: 0;
        }

        @media (max-width: 768px) {
            .testimonial-container {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .btn-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="testimonial-container">
        <div class="header">
            <h1><i class="fas fa-heart"></i> Compartilhar Minha História</h1>
            <p>Conte-nos sobre sua experiência e ajude outros candidatos a conhecer nossa plataforma</p>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $db = (new Database())->connect();
                
                $nome = trim($_POST['nome'] ?? '');
                $cargo = trim($_POST['cargo'] ?? '');
                $empresa = trim($_POST['empresa'] ?? '');
                $mensagem = trim($_POST['mensagem'] ?? '');
                $linkedin = trim($_POST['linkedin'] ?? '');
                
                // Validações
                if (empty($nome) || empty($mensagem)) {
                    throw new Exception('Nome e mensagem são obrigatórios.');
                }
                
                if (strlen($mensagem) < 50) {
                    throw new Exception('A mensagem deve ter pelo menos 50 caracteres.');
                }
                
                if (strlen($mensagem) > 500) {
                    throw new Exception('A mensagem deve ter no máximo 500 caracteres.');
                }
                
                // Inserir testimonial
                $stmt = $db->prepare("
                    INSERT INTO testimonials_candidatos (nome, cargo, empresa, mensagem, linkedin, status) 
                    VALUES (?, ?, ?, ?, ?, 'pendente')
                ");
                
                $stmt->execute([$nome, $cargo, $empresa, $mensagem, $linkedin]);
                
                echo '<div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> 
                    <strong>Obrigado!</strong> Seu depoimento foi enviado com sucesso e está aguardando moderação. 
                    Assim que aprovado, aparecerá na nossa página principal.
                </div>';
                
            } catch (Exception $e) {
                echo '<div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Erro:</strong> ' . htmlspecialchars($e->getMessage()) . '
                </div>';
            }
        }
        ?>

        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> Como funciona?</h3>
            <p>Seu depoimento passará por uma moderação rápida antes de aparecer na página principal. Isso garante a qualidade e autenticidade dos testimonials exibidos para outros candidatos.</p>
        </div>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="nome"><i class="fas fa-user"></i> Seu Nome *</label>
                    <input type="text" id="nome" name="nome" required maxlength="100" 
                           value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                           placeholder="Ex: Maria Silva">
                </div>
                
                <div class="form-group">
                    <label for="cargo"><i class="fas fa-briefcase"></i> Cargo/Função</label>
                    <input type="text" id="cargo" name="cargo" maxlength="100"
                           value="<?= htmlspecialchars($_POST['cargo'] ?? '') ?>"
                           placeholder="Ex: Desenvolvedora">
                </div>
            </div>

            <div class="form-group">
                <label for="empresa"><i class="fas fa-building"></i> Empresa (opcional)</label>
                <input type="text" id="empresa" name="empresa" maxlength="100"
                       value="<?= htmlspecialchars($_POST['empresa'] ?? '') ?>"
                       placeholder="Ex: Tech Solutions Ltda">
            </div>

            <div class="form-group">
                <label for="linkedin"><i class="fab fa-linkedin"></i> LinkedIn (opcional)</label>
                <input type="url" id="linkedin" name="linkedin" maxlength="200"
                       value="<?= htmlspecialchars($_POST['linkedin'] ?? '') ?>"
                       placeholder="https://linkedin.com/in/seu-perfil">
            </div>

            <div class="form-group">
                <label for="mensagem"><i class="fas fa-comment"></i> Sua História *</label>
                <textarea id="mensagem" name="mensagem" required minlength="50" maxlength="500"
                          placeholder="Conte sua experiência conosco: Como foi o processo? Conseguiu a vaga? O que achou da plataforma? (mínimo 50 caracteres)"><?= htmlspecialchars($_POST['mensagem'] ?? '') ?></textarea>
                <small style="color: #666; font-size: 0.9rem;">
                    <span id="charCount">0</span>/500 caracteres
                </small>
            </div>

            <div class="btn-container">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Enviar Depoimento
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </form>
    </div>

    <script>
        // Contador de caracteres
        const textarea = document.getElementById('mensagem');
        const charCount = document.getElementById('charCount');
        
        function updateCharCount() {
            charCount.textContent = textarea.value.length;
            
            if (textarea.value.length < 50) {
                charCount.style.color = '#dc3545';
            } else if (textarea.value.length > 450) {
                charCount.style.color = '#fd7e14';
            } else {
                charCount.style.color = '#28a745';
            }
        }
        
        textarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Inicializar contador
    </script>
</body>
</html>
