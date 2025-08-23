<?php
require_once 'config.php';

// Processar envio de avaliação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitizeInput($_POST['nome'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $cargo = sanitizeInput($_POST['cargo'] ?? '');
    $empresa = sanitizeInput($_POST['empresa'] ?? '');
    $avaliacao = (int)($_POST['avaliacao'] ?? 0);
    $comentario = sanitizeInput($_POST['comentario'] ?? '');
    $permite_publicacao = isset($_POST['permite_publicacao']) ? 1 : 0;

    if (!empty($nome) && !empty($email) && !empty($comentario) && $avaliacao > 0) {
        try {
            $database = new Database();
            $pdo = $database->connect();
            
            $sql = "INSERT INTO avaliacoes (nome, email, cargo, empresa, avaliacao, comentario, permite_publicacao, data_avaliacao) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $email, $cargo, $empresa, $avaliacao, $comentario, $permite_publicacao]);
            
            $success_message = 'Sua avaliação foi enviada com sucesso! Obrigado pelo feedback.';
            
        } catch (Exception $e) {
            $error_message = 'Erro ao enviar avaliação: ' . $e->getMessage();
        }
    } else {
        $error_message = 'Por favor, preencha todos os campos obrigatórios.';
    }
}

// Buscar avaliações públicas para exibir
try {
    $database = new Database();
    $pdo = $database->connect();
    
    $sql = "SELECT nome, cargo, empresa, avaliacao, comentario, data_avaliacao 
            FROM avaliacoes 
            WHERE permite_publicacao = 1 AND status = 'aprovada'
            ORDER BY data_avaliacao DESC 
            LIMIT 10";
    $stmt = $pdo->query($sql);
    $avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $avaliacoes = [];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avalie Nossa Plataforma | ENIAC LINK+</title>
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
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #34609b 100%);
            background-size: 400% 400%;
            animation: gradientShift 20s ease infinite;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Header */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
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

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #007cba, #0099ff);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .logo-section h2 {
            color: #0056b3;
            font-size: 1.8rem;
            font-weight: 700;
        }

        nav {
            display: flex;
            gap: 0;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            padding: 8px;
            backdrop-filter: blur(10px);
        }

        nav a {
            color: #333;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        nav a:hover {
            background: rgba(0, 86, 179, 0.1);
            color: #0056b3;
            transform: translateY(-2px);
        }

        nav a.active {
            background: linear-gradient(135deg, #0056b3, #007cba);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 86, 179, 0.3);
        }

        /* Main Content */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            color: #0056b3;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .page-header p {
            color: #666;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Grid Layout */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-top: 2rem;
        }

        /* Formulário de Avaliação */
        .review-form-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .review-form-section h2 {
            color: #0056b3;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .required::after {
            content: " *";
            color: #e74c3c;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0056b3;
            box-shadow: 0 0 10px rgba(0, 86, 179, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Sistema de Estrelas */
        .star-rating {
            display: flex;
            gap: 5px;
            margin: 10px 0;
        }

        .star {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .star:hover,
        .star.active {
            color: #ffd700;
            transform: scale(1.1);
        }

        .rating-text {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
        }

        /* Checkbox customizado */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 1rem 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        /* Botão */
        .btn-submit {
            background: linear-gradient(135deg, #0056b3, #007cba);
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 86, 179, 0.3);
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 86, 179, 0.4);
        }

        /* Seção de Depoimentos */
        .testimonials-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .testimonials-section h2 {
            color: #0056b3;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .testimonial-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #0056b3;
            position: relative;
        }

        .testimonial-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .testimonial-author {
            font-weight: 600;
            color: #333;
        }

        .testimonial-position {
            font-size: 0.85rem;
            color: #666;
        }

        .testimonial-stars {
            color: #ffd700;
            font-size: 1.1rem;
        }

        .testimonial-text {
            color: #555;
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }

        .testimonial-date {
            font-size: 0.8rem;
            color: #999;
        }

        /* Alertas */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 1.5rem;
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
            .header-container {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            nav {
                flex-wrap: wrap;
                justify-content: center;
            }

            .content-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .main-container {
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 2rem;
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
                    <i class="fas fa-star"></i>
                </div>
                <h2>ENIAC LINK+</h2>
            </div>
            <nav>
                <a href="index.php"><i class="fas fa-home"></i> Início</a>
                <a href="vagas.php"><i class="fas fa-briefcase"></i> Vagas</a>
                <a href="cadastro.php"><i class="fas fa-user-plus"></i> Cadastrar</a>
                <a href="fale_conosco.php"><i class="fas fa-comments"></i> Fale Conosco</a>
                <a href="avaliacoes.php" class="active"><i class="fas fa-star"></i> Avaliações</a>
                <a href="login_admin.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-star"></i> Avalie Nossa Plataforma</h1>
            <p>Sua opinião é muito importante para nós! Compartilhe sua experiência e ajude outros candidatos a conhecer melhor nossos serviços.</p>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Formulário de Avaliação -->
            <div class="review-form-section">
                <h2><i class="fas fa-edit"></i> Deixe sua Avaliação</h2>
                
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

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nome" class="required">Nome Completo</label>
                        <input type="text" id="nome" name="nome" required placeholder="Seu nome completo">
                    </div>

                    <div class="form-group">
                        <label for="email" class="required">Email</label>
                        <input type="email" id="email" name="email" required placeholder="seu@email.com">
                    </div>

                    <div class="form-group">
                        <label for="cargo">Cargo/Profissão</label>
                        <input type="text" id="cargo" name="cargo" placeholder="Ex: Desenvolvedor, Analista, Estudante">
                    </div>

                    <div class="form-group">
                        <label for="empresa">Empresa (se aplicável)</label>
                        <input type="text" id="empresa" name="empresa" placeholder="Nome da empresa onde trabalha">
                    </div>

                    <div class="form-group">
                        <label class="required">Avaliação Geral</label>
                        <div class="star-rating" id="starRating">
                            <span class="star" data-rating="1"><i class="fas fa-star"></i></span>
                            <span class="star" data-rating="2"><i class="fas fa-star"></i></span>
                            <span class="star" data-rating="3"><i class="fas fa-star"></i></span>
                            <span class="star" data-rating="4"><i class="fas fa-star"></i></span>
                            <span class="star" data-rating="5"><i class="fas fa-star"></i></span>
                        </div>
                        <div class="rating-text" id="ratingText">Clique nas estrelas para avaliar</div>
                        <input type="hidden" id="avaliacao" name="avaliacao" required>
                    </div>

                    <div class="form-group">
                        <label for="comentario" class="required">Seu Comentário</label>
                        <textarea id="comentario" name="comentario" required placeholder="Conte sobre sua experiência com nossa plataforma..."></textarea>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="permite_publicacao" name="permite_publicacao" checked>
                        <label for="permite_publicacao">Permito a publicação desta avaliação no site (sem dados pessoais sensíveis)</label>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Enviar Avaliação
                    </button>
                </form>
            </div>

            <!-- Depoimentos Existentes -->
            <div class="testimonials-section">
                <h2><i class="fas fa-comments"></i> O que nossos usuários dizem</h2>
                
                <?php if (!empty($avaliacoes)): ?>
                    <?php foreach ($avaliacoes as $avaliacao): ?>
                        <div class="testimonial-card">
                            <div class="testimonial-header">
                                <div>
                                    <div class="testimonial-author"><?php echo htmlspecialchars($avaliacao['nome']); ?></div>
                                    <?php if (!empty($avaliacao['cargo']) || !empty($avaliacao['empresa'])): ?>
                                        <div class="testimonial-position">
                                            <?php 
                                            $position = [];
                                            if (!empty($avaliacao['cargo'])) $position[] = $avaliacao['cargo'];
                                            if (!empty($avaliacao['empresa'])) $position[] = $avaliacao['empresa'];
                                            echo htmlspecialchars(implode(' - ', $position));
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="testimonial-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star" style="color: <?php echo $i <= $avaliacao['avaliacao'] ? '#ffd700' : '#ddd'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="testimonial-text">
                                "<?php echo htmlspecialchars($avaliacao['comentario']); ?>"
                            </div>
                            <div class="testimonial-date">
                                <?php echo date('d/m/Y', strtotime($avaliacao['data_avaliacao'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 2rem; color: #666;">
                        <i class="fas fa-star" style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;"></i>
                        <p>Seja o primeiro a avaliar nossa plataforma!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Sistema de estrelas
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('avaliacao');
        const ratingText = document.getElementById('ratingText');
        
        const ratingTexts = {
            1: '⭐ Muito ruim',
            2: '⭐⭐ Ruim',
            3: '⭐⭐⭐ Regular',
            4: '⭐⭐⭐⭐ Bom',
            5: '⭐⭐⭐⭐⭐ Excelente'
        };

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                ratingInput.value = rating;
                ratingText.textContent = ratingTexts[rating];
                
                // Atualizar visual das estrelas
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
            
            star.addEventListener('mouseover', function() {
                const rating = this.getAttribute('data-rating');
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.style.color = '#ffd700';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        });

        document.getElementById('starRating').addEventListener('mouseleave', function() {
            const currentRating = ratingInput.value;
            stars.forEach((s, index) => {
                if (index < currentRating) {
                    s.style.color = '#ffd700';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    </script>
</body>
</html>
