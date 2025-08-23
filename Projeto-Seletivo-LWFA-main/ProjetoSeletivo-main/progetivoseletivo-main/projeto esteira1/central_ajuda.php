<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central de Ajuda | ENIAC LINK+</title>
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
            justify-content: flex-start;
        }

        .logo-header {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border: 3px solid rgba(255, 255, 255, 0.3);
            transition: transform 0.3s ease;
        }

        .logo-header:hover {
            transform: scale(1.05);
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

        /* Search Box */
        .search-container {
            margin-bottom: 2rem;
            text-align: center;
        }

        .search-box {
            position: relative;
            max-width: 500px;
            margin: 0 auto;
        }

        .search-box input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #0056b3;
            box-shadow: 0 0 20px rgba(0, 86, 179, 0.2);
        }

        .search-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.1rem;
        }

        /* FAQ Categories */
        .faq-categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .category-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .category-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #0056b3, #007cba);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .category-card h3 {
            color: #0056b3;
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .category-card p {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .category-card .faq-count {
            color: #0056b3;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* FAQ Items */
        .faq-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .faq-section h2 {
            color: #0056b3;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .faq-item {
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
        }

        .faq-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .faq-question {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            padding: 1rem 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: color 0.3s ease;
        }

        .faq-question:hover {
            color: #0056b3;
        }

        .faq-question i {
            transition: transform 0.3s ease;
        }

        .faq-question.active i {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 0 0 1rem 0;
            color: #666;
            line-height: 1.6;
            display: none;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .faq-answer.show {
            display: block;
        }

        /* Contact Section */
        .contact-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .contact-section h2 {
            color: #0056b3;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .contact-section p {
            color: #666;
            margin-bottom: 1.5rem;
        }

        .contact-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0056b3, #007cba);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 86, 179, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 86, 179, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.9);
            color: #0056b3;
            border: 2px solid #0056b3;
        }

        .btn-secondary:hover {
            background: #0056b3;
            color: white;
            transform: translateY(-2px);
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

            .page-header h1 {
                font-size: 2rem;
            }

            .main-container {
                padding: 1rem;
            }

            .contact-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo-section">
                <img src="./imagens/Logoindex.jpg" alt="Logo da empresa" class="logo-header">
            </div>
            <nav>
                <a href="index.php"><i class="fas fa-home"></i> Início</a>
                <a href="vagas.php"><i class="fas fa-briefcase"></i> Vagas</a>
                <a href="cadastro.php"><i class="fas fa-user-plus"></i> Cadastrar</a>
                <a href="fale_conosco.php"><i class="fas fa-comments"></i> Fale Conosco</a>
                <a href="central_ajuda.php" class="active"><i class="fas fa-question-circle"></i> Ajuda</a>
                <a href="login_admin.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-question-circle"></i> Central de Ajuda</h1>
            <p>Encontre respostas para suas dúvidas sobre o processo seletivo, cadastro de candidatos e muito mais.</p>
        </div>

        <!-- Search Box -->
        <div class="search-container">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Digite sua dúvida ou palavra-chave...">
                <i class="fas fa-search"></i>
            </div>
        </div>

        <!-- FAQ Categories -->
        <div class="faq-categories">
            <div class="category-card" onclick="scrollToSection('cadastro')">
                <div class="category-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3>Cadastro e Login</h3>
                <p>Dúvidas sobre como se cadastrar, fazer login e gerenciar sua conta.</p>
                <div class="faq-count">5 perguntas</div>
            </div>

            <div class="category-card" onclick="scrollToSection('vagas')">
                <div class="category-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h3>Vagas e Candidaturas</h3>
                <p>Como encontrar vagas, se candidatar e acompanhar o processo seletivo.</p>
                <div class="faq-count">7 perguntas</div>
            </div>

            <div class="category-card" onclick="scrollToSection('tecnico')">
                <div class="category-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <h3>Suporte Técnico</h3>
                <p>Problemas técnicos, compatibilidade e dificuldades no sistema.</p>
                <div class="faq-count">4 perguntas</div>
            </div>

            <div class="category-card" onclick="scrollToSection('empresas')">
                <div class="category-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3>Para Empresas</h3>
                <p>Informações para empresas interessadas em nossos candidatos.</p>
                <div class="faq-count">3 perguntas</div>
            </div>
        </div>

        <!-- FAQ Sections -->
        <div id="cadastro" class="faq-section">
            <h2><i class="fas fa-user-plus"></i> Cadastro e Login</h2>
            
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Como faço para me cadastrar no sistema?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Para se cadastrar no ENIAC LINK+:</p>
                    <ol>
                        <li>Clique em "Cadastrar" no menu principal</li>
                        <li>Preencha todos os campos obrigatórios (nome, email, telefone, etc.)</li>
                        <li>Faça o upload do seu currículo em PDF</li>
                        <li>Clique em "Cadastrar" para finalizar</li>
                    </ol>
                    <p>Após o cadastro, você receberá uma confirmação e poderá se candidatar às vagas disponíveis.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Esqueci minha senha, como recuperar?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Atualmente, para problemas com senha, entre em contato através do "Fale Conosco" informando:</p>
                    <ul>
                        <li>Seu nome completo</li>
                        <li>Email usado no cadastro</li>
                        <li>Telefone de contato</li>
                    </ul>
                    <p>Nossa equipe irá ajudá-lo a recuperar o acesso à sua conta.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Posso alterar meus dados após o cadastro?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Para alterar seus dados cadastrais, entre em contato através do "Fale Conosco" informando:</p>
                    <ul>
                        <li>Quais dados deseja alterar</li>
                        <li>Os novos dados corretos</li>
                        <li>Justificativa para a alteração</li>
                    </ul>
                    <p>Nossa equipe fará as alterações necessárias em seu perfil.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Que formato de currículo devo enviar?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>O currículo deve estar em formato <strong>PDF</strong> com as seguintes características:</p>
                    <ul>
                        <li>Tamanho máximo: 5MB</li>
                        <li>Nome sugerido: "Curriculo_SeuNome.pdf"</li>
                        <li>Conteúdo atualizado e organizado</li>
                        <li>Informações de contato visíveis</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    O cadastro é gratuito?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Sim! O cadastro no ENIAC LINK+ é <strong>100% gratuito</strong>. Não cobramos nenhuma taxa para:</p>
                    <ul>
                        <li>Cadastro no sistema</li>
                        <li>Candidatura às vagas</li>
                        <li>Participação no processo seletivo</li>
                        <li>Suporte e atendimento</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="vagas" class="faq-section">
            <h2><i class="fas fa-briefcase"></i> Vagas e Candidaturas</h2>
            
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Como encontrar vagas disponíveis?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Para encontrar vagas:</p>
                    <ol>
                        <li>Acesse a seção "Vagas" no menu principal</li>
                        <li>Visualize todas as oportunidades disponíveis</li>
                        <li>Use os filtros para encontrar vagas específicas</li>
                        <li>Clique em "Ver Detalhes" para mais informações</li>
                    </ol>
                    <p>As vagas são atualizadas regularmente com novas oportunidades.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Como me candidatar a uma vaga?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Para se candidatar:</p>
                    <ol>
                        <li>Certifique-se de estar cadastrado no sistema</li>
                        <li>Acesse a vaga de interesse</li>
                        <li>Clique em "Candidatar-se"</li>
                        <li>Preencha as informações adicionais, se solicitadas</li>
                        <li>Confirme sua candidatura</li>
                    </ol>
                    <p><strong>Dica:</strong> Mantenha seu currículo sempre atualizado!</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Posso me candidatar a mais de uma vaga?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Sim! Você pode se candidatar a <strong>quantas vagas desejar</strong>, desde que:</p>
                    <ul>
                        <li>Atenda aos requisitos de cada vaga</li>
                        <li>Tenha disponibilidade para as oportunidades</li>
                        <li>Mantenha seu perfil atualizado</li>
                    </ul>
                    <p>Recomendamos focar em vagas que realmente combinam com seu perfil.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Como acompanhar o status da minha candidatura?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Para acompanhar suas candidaturas:</p>
                    <ul>
                        <li>Nossa equipe entrará em contato por telefone ou email</li>
                        <li>Fique atento às ligações e emails oficiais</li>
                        <li>Em caso de dúvidas, use o "Fale Conosco"</li>
                    </ul>
                    <p>Mantenha seus dados de contato sempre atualizados!</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Qual o prazo para retorno após candidatura?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>O prazo varia conforme a vaga, mas em geral:</p>
                    <ul>
                        <li><strong>Análise inicial:</strong> até 7 dias úteis</li>
                        <li><strong>Contato para entrevista:</strong> até 15 dias úteis</li>
                        <li><strong>Resultado final:</strong> até 30 dias úteis</li>
                    </ul>
                    <p>Vagas urgentes podem ter prazos menores.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    As vagas têm data de expiração?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Sim! Cada vaga possui uma <strong>data de encerramento</strong> que é exibida nos detalhes da oportunidade. Após essa data:</p>
                    <ul>
                        <li>A vaga deixa de aceitar candidaturas</li>
                        <li>O processo seletivo é finalizado</li>
                        <li>Novas oportunidades similares podem ser criadas</li>
                    </ul>
                    <p><strong>Dica:</strong> Candidate-se o quanto antes!</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Há vagas para pessoas com deficiência?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Sim! O ENIAC LINK+ promove a <strong>inclusão e diversidade</strong>. Oferecemos:</p>
                    <ul>
                        <li>Vagas específicas para PcD</li>
                        <li>Oportunidades em empresas inclusivas</li>
                        <li>Suporte especializado no processo seletivo</li>
                        <li>Adequação de ambientes e processos</li>
                    </ul>
                    <p>Entre em contato para mais informações sobre acessibilidade.</p>
                </div>
            </div>
        </div>

        <div id="tecnico" class="faq-section">
            <h2><i class="fas fa-cog"></i> Suporte Técnico</h2>
            
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    O sistema não está carregando, o que fazer?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Tente as seguintes soluções:</p>
                    <ol>
                        <li>Atualize a página (F5 ou Ctrl+F5)</li>
                        <li>Limpe o cache do navegador</li>
                        <li>Teste em outro navegador (Chrome, Firefox, Edge)</li>
                        <li>Verifique sua conexão com a internet</li>
                        <li>Desative temporariamente bloqueadores de anúncio</li>
                    </ol>
                    <p>Se o problema persistir, entre em contato através do "Fale Conosco".</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Não consigo fazer upload do meu currículo
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Verifique se seu arquivo atende aos requisitos:</p>
                    <ul>
                        <li><strong>Formato:</strong> PDF apenas</li>
                        <li><strong>Tamanho:</strong> máximo 5MB</li>
                        <li><strong>Nome:</strong> sem caracteres especiais</li>
                    </ul>
                    <p>Se ainda assim não funcionar:</p>
                    <ul>
                        <li>Tente comprimir o PDF</li>
                        <li>Renomeie o arquivo</li>
                        <li>Use outro navegador</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Qual navegador é recomendado?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>O ENIAC LINK+ funciona melhor nos seguintes navegadores <strong>atualizados</strong>:</p>
                    <ul>
                        <li><strong>Google Chrome</strong> (recomendado)</li>
                        <li><strong>Mozilla Firefox</strong></li>
                        <li><strong>Microsoft Edge</strong></li>
                        <li><strong>Safari</strong> (Mac)</li>
                    </ul>
                    <p>Evite versões muito antigas ou navegadores não suportados.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    O sistema funciona no celular?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Sim! O ENIAC LINK+ é <strong>totalmente responsivo</strong> e funciona perfeitamente em:</p>
                    <ul>
                        <li>Smartphones Android e iOS</li>
                        <li>Tablets</li>
                        <li>Desktops e notebooks</li>
                    </ul>
                    <p>Para melhor experiência no celular, use o navegador Chrome ou Safari.</p>
                </div>
            </div>
        </div>

        <div id="empresas" class="faq-section">
            <h2><i class="fas fa-building"></i> Para Empresas</h2>
            
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Como minha empresa pode publicar vagas?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Para empresas interessadas em nossos candidatos:</p>
                    <ol>
                        <li>Entre em contato através do "Fale Conosco"</li>
                        <li>Selecione a categoria "Para Empresas"</li>
                        <li>Informe detalhes sobre a vaga e empresa</li>
                        <li>Nossa equipe entrará em contato</li>
                    </ol>
                    <p>Oferecemos parcerias personalizadas para cada necessidade.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Qual o perfil dos candidatos?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Nossos candidatos são estudantes e egressos da <strong>ENIAC</strong> com perfil de:</p>
                    <ul>
                        <li>Ensino técnico e superior</li>
                        <li>Áreas de tecnologia, administração, saúde</li>
                        <li>Jovens talentos em início de carreira</li>
                        <li>Profissionais em transição de carreira</li>
                    </ul>
                    <p>Todos passam por processo de capacitação da instituição.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Há custos para publicar vagas?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Entre em contato para conhecer nossos <strong>planos de parceria</strong>:</p>
                    <ul>
                        <li>Opções gratuitas para empresas parceiras</li>
                        <li>Planos específicos por volume de vagas</li>
                        <li>Serviços de pré-seleção personalizada</li>
                        <li>Suporte dedicado para recrutamento</li>
                    </ul>
                    <p>Cada caso é analisado individualmente.</p>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="contact-section">
            <h2><i class="fas fa-headset"></i> Não encontrou sua resposta?</h2>
            <p>Nossa equipe está pronta para ajudá-lo! Entre em contato através dos canais disponíveis.</p>
            <div class="contact-buttons">
                <a href="fale_conosco.php" class="btn btn-primary">
                    <i class="fas fa-comments"></i> Fale Conosco
                </a>
                <a href="mailto:contato@eniaclink.com.br" class="btn btn-secondary">
                    <i class="fas fa-envelope"></i> Email Direto
                </a>
            </div>
        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                    if (searchTerm.length > 2) {
                        item.querySelector('.faq-answer').classList.add('show');
                        item.querySelector('.faq-question').classList.add('active');
                    }
                } else {
                    item.style.display = searchTerm ? 'none' : 'block';
                }
            });
        });

        // FAQ toggle
        function toggleFaq(button) {
            const answer = button.nextElementSibling;
            const isOpen = answer.classList.contains('show');
            
            // Close all other FAQs
            document.querySelectorAll('.faq-answer').forEach(a => a.classList.remove('show'));
            document.querySelectorAll('.faq-question').forEach(q => q.classList.remove('active'));
            
            // Toggle current FAQ
            if (!isOpen) {
                answer.classList.add('show');
                button.classList.add('active');
            }
        }

        // Scroll to section
        function scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    </script>

    <!-- Footer -->
    <footer style="background: linear-gradient(135deg, #1a1a1a, #333); color: white; padding: 60px 20px 30px; margin-top: 2rem;">
        <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px;">
            <div>
                <h3 style="color: #0099ff; margin-bottom: 20px; font-weight: 600;">Processo-Seletivo</h3>
                <p style="color: #ccc; line-height: 1.6; margin-bottom: 8px;">Conectando talentos com oportunidades desde 2025. Nossa missão é simplificar o processo seletivo e aproximar candidatos das empresas ideais.</p>
                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <a href="#" style="width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; background: linear-gradient(135deg, #0077b5, #005582); box-shadow: 0 4px 15px rgba(0, 119, 181, 0.3); transition: all 0.3s ease;" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" style="width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; background: linear-gradient(135deg, #1877f2, #0d5dcc); box-shadow: 0 4px 15px rgba(24, 119, 242, 0.3); transition: all 0.3s ease;" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" style="width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045); box-shadow: 0 4px 15px rgba(131, 58, 180, 0.3); transition: all 0.3s ease;" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" style="width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; background: linear-gradient(135deg, #25d366, #1ebe57); box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3); transition: all 0.3s ease;" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="#" style="width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; background: linear-gradient(135deg, #ff0000, #cc0000); box-shadow: 0 4px 15px rgba(255, 0, 0, 0.3); transition: all 0.3s ease;" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            
            <div>
                <h3 style="color: #0099ff; margin-bottom: 20px; font-weight: 600;">Links Rápidos</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 8px;"><a href="index.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Início</a></li>
                    <li style="margin-bottom: 8px;"><a href="cadastro.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Cadastro</a></li>
                    <li style="margin-bottom: 8px;"><a href="vagas.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Vagas</a></li>
                    <li style="margin-bottom: 8px;"><a href="login_admin.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">RH</a></li>
                </ul>
            </div>
            
            <div>
                <h3 style="color: #0099ff; margin-bottom: 20px; font-weight: 600;">Suporte</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 8px;"><a href="central_ajuda.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Central de Ajuda</a></li>
                    <li style="margin-bottom: 8px;"><a href="termos_uso.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Termos de Uso</a></li>
                    <li style="margin-bottom: 8px;"><a href="politica_privacidade.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Política de Privacidade</a></li>
                    <li style="margin-bottom: 8px;"><a href="fale_conosco.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Fale Conosco</a></li>
                </ul>
            </div>
            
            <div>
                <h3 style="color: #0099ff; margin-bottom: 20px; font-weight: 600;">Contato</h3>
                <p style="color: #ccc; line-height: 1.6; margin-bottom: 8px;"><i class="fas fa-envelope"></i> contato@eniaclink.com</p>
                <p style="color: #ccc; line-height: 1.6; margin-bottom: 8px;"><i class="fas fa-phone"></i> (11) 9999-9999</p>
                <p style="color: #ccc; line-height: 1.6; margin-bottom: 8px;"><i class="fas fa-map-marker-alt"></i> São Paulo, SP - Brasil</p>
            </div>
        </div>
        
        <div style="border-top: 1px solid #444; padding-top: 30px; text-align: center; color: #999;">
            <p>&copy; 2025 LWFA — Todos os direitos reservados. Desenvolvido com ❤️ para conectar pessoas.</p>
        </div>
    </footer>
</body>
</html>
