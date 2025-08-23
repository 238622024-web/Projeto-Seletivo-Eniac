<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos de Uso | LWFA</title>
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

        /* Main Content */
        .main-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }

        .document-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .document-header {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .document-header h1 {
            color: #0056b3;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .document-header .last-update {
            color: #666;
            font-size: 0.95rem;
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            display: inline-block;
        }

        .section {
            margin-bottom: 2.5rem;
        }

        .section h2 {
            color: #0056b3;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
            border-left: 4px solid #0056b3;
            padding-left: 1rem;
        }

        .section h3 {
            color: #333;
            font-size: 1.2rem;
            margin: 1.5rem 0 0.8rem 0;
            font-weight: 600;
        }

        .section p {
            color: #555;
            margin-bottom: 1rem;
            text-align: justify;
        }

        .section ul, .section ol {
            margin: 1rem 0;
            padding-left: 2rem;
        }

        .section li {
            color: #555;
            margin-bottom: 0.5rem;
        }

        .highlight {
            background: #e3f2fd;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #0056b3;
            margin: 1rem 0;
        }

        .highlight strong {
            color: #0056b3;
        }

        .contact-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
            text-align: center;
        }

        .contact-info h3 {
            color: #0056b3;
            margin-bottom: 1rem;
        }

        .back-button {
            background: linear-gradient(135deg, #0056b3, #007cba);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 86, 179, 0.3);
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 86, 179, 0.4);
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

            .main-container {
                padding: 1rem;
            }

            .document-container {
                padding: 2rem 1.5rem;
            }

            .document-header h1 {
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
                <img src="./imagens/Logoindex.jpg" alt="Logo da empresa" class="logo-header">
            </div>
            <nav>
                <a href="index.php"><i class="fas fa-home"></i> Início</a>
                <a href="vagas.php"><i class="fas fa-briefcase"></i> Vagas</a>
                <a href="cadastro.php"><i class="fas fa-user-plus"></i> Cadastrar</a>
                <a href="fale_conosco.php"><i class="fas fa-comments"></i> Fale Conosco</a>
                <a href="central_ajuda.php"><i class="fas fa-question-circle"></i> Ajuda</a>
                <a href="login_admin.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <a href="index.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Voltar ao Início
        </a>

        <div class="document-container">
            <div class="document-header">
                <h1><i class="fas fa-file-contract"></i> Termos de Uso</h1>
                <div class="last-update">
                    Última atualização: 04 de agosto de 2025
                </div>
            </div>

            <div class="section">
                <h2>1. Aceitação dos Termos</h2>
                <p>Ao acessar e utilizar a plataforma LWFA, você concorda em cumprir e estar vinculado a estes Termos de Uso. Se você não concordar com qualquer parte destes termos, não deverá utilizar nossos serviços.</p>

                <div class="highlight">
                    <strong>Importante:</strong> Estes termos constituem um acordo legal entre você e o ENIAC LINK+. Leia atentamente antes de prosseguir.
                </div>
            </div>

            <div class="section">
                <h2>2. Descrição do Serviço</h2>
                <p>O ENIAC LINK+ é uma plataforma online de processo seletivo que conecta candidatos a oportunidades de emprego. Nossos serviços incluem:</p>
                <ul>
                    <li>Cadastro de candidatos e currículos</li>
                    <li>Divulgação de vagas de emprego</li>
                    <li>Sistema de candidaturas online</li>
                    <li>Processo seletivo virtual</li>
                    <li>Suporte e atendimento aos usuários</li>
                </ul>
            </div>

            <div class="section">
                <h2>3. Cadastro e Conta do Usuário</h2>
                
                <h3>3.1 Elegibilidade</h3>
                <p>Para utilizar nossos serviços, você deve:</p>
                <ul>
                    <li>Ter pelo menos 16 anos de idade</li>
                    <li>Fornecer informações verdadeiras e atualizadas</li>
                    <li>Manter a confidencialidade de suas credenciais</li>
                    <li>Ser responsável por todas as atividades em sua conta</li>
                </ul>

                <h3>3.2 Responsabilidades do Usuário</h3>
                <p>Você se compromete a:</p>
                <ul>
                    <li>Fornecer informações precisas e atualizadas</li>
                    <li>Manter seus dados de contato atualizados</li>
                    <li>Não criar contas falsas ou duplicadas</li>
                    <li>Não compartilhar suas credenciais de acesso</li>
                    <li>Notificar-nos sobre qualquer uso não autorizado</li>
                </ul>
            </div>

            <div class="section">
                <h2>4. Uso Aceitável</h2>
                
                <h3>4.1 Condutas Permitidas</h3>
                <p>Você pode utilizar nossa plataforma para:</p>
                <ul>
                    <li>Cadastrar-se como candidato</li>
                    <li>Buscar e se candidatar a vagas</li>
                    <li>Atualizar seu perfil e currículo</li>
                    <li>Participar de processos seletivos</li>
                    <li>Entrar em contato com nossa equipe</li>
                </ul>

                <h3>4.2 Condutas Proibidas</h3>
                <p>É estritamente proibido:</p>
                <ul>
                    <li>Fornecer informações falsas ou enganosas</li>
                    <li>Criar múltiplas contas para a mesma pessoa</li>
                    <li>Utilizar a plataforma para fins ilegais</li>
                    <li>Tentar acessar contas de outros usuários</li>
                    <li>Interferir no funcionamento da plataforma</li>
                    <li>Enviar spam ou conteúdo malicioso</li>
                    <li>Violar direitos de propriedade intelectual</li>
                </ul>
            </div>

            <div class="section">
                <h2>5. Propriedade Intelectual</h2>
                <p>Todo o conteúdo da plataforma ENIAC LINK+, incluindo textos, gráficos, logos, ícones, imagens, clipes de áudio e software, é propriedade exclusiva do ENIAC ou de seus licenciadores e está protegido pelas leis de direitos autorais.</p>

                <div class="highlight">
                    <strong>Licença de Uso:</strong> Concedemos a você uma licença limitada, não exclusiva e não transferível para acessar e usar nossa plataforma exclusivamente para os fins aqui descritos.
                </div>
            </div>

            <div class="section">
                <h2>6. Privacidade e Proteção de Dados</h2>
                <p>Respeitamos sua privacidade e estamos comprometidos com a proteção de seus dados pessoais, em conformidade com a Lei Geral de Proteção de Dados (LGPD).</p>
                
                <h3>6.1 Coleta de Dados</h3>
                <p>Coletamos apenas os dados necessários para:</p>
                <ul>
                    <li>Processar sua candidatura</li>
                    <li>Entrar em contato quando necessário</li>
                    <li>Melhorar nossos serviços</li>
                    <li>Cumprir obrigações legais</li>
                </ul>

                <h3>6.2 Seus Direitos</h3>
                <p>Você tem o direito de:</p>
                <ul>
                    <li>Acessar seus dados pessoais</li>
                    <li>Corrigir informações incorretas</li>
                    <li>Solicitar a exclusão de seus dados</li>
                    <li>Opor-se ao tratamento de dados</li>
                    <li>Solicitar a portabilidade dos dados</li>
                </ul>
            </div>

            <div class="section">
                <h2>7. Responsabilidades e Limitações</h2>
                
                <h3>7.1 Disponibilidade do Serviço</h3>
                <p>Embora nos esforcemos para manter a plataforma disponível 24/7, não garantimos que o serviço será ininterrupto ou livre de erros. Reservamo-nos o direito de realizar manutenções programadas.</p>

                <h3>7.2 Limitação de Responsabilidade</h3>
                <p>O LWFA não se responsabiliza por:</p>
                <ul>
                    <li>Decisões de contratação das empresas parceiras</li>
                    <li>Conteúdo fornecido por terceiros</li>
                    <li>Perdas ou danos indiretos</li>
                    <li>Problemas técnicos fora de nosso controle</li>
                    <li>Uso inadequado da plataforma pelos usuários</li>
                </ul>
            </div>

            <div class="section">
                <h2>8. Modificações dos Termos</h2>
                <p>Reservamo-nos o direito de modificar estes Termos de Uso a qualquer momento. As alterações entrarão em vigor imediatamente após sua publicação na plataforma.</p>
                
                <div class="highlight">
                    <strong>Notificação:</strong> Será enviada notificação sobre alterações significativas através dos canais de comunicação disponíveis.
                </div>
            </div>

            <div class="section">
                <h2>9. Encerramento</h2>
                
                <h3>9.1 Encerramento por Parte do Usuário</h3>
                <p>Você pode encerrar sua conta a qualquer momento entrando em contato conosco através do "Fale Conosco".</p>

                <h3>9.2 Encerramento por Nossa Parte</h3>
                <p>Podemos suspender ou encerrar sua conta se:</p>
                <ul>
                    <li>Houver violação destes termos</li>
                    <li>Detectarmos atividade fraudulenta</li>
                    <li>For necessário por motivos legais</li>
                    <li>A conta permanecer inativa por período prolongado</li>
                </ul>
            </div>

            <div class="section">
                <h2>10. Lei Aplicável e Jurisdição</h2>
                <p>Estes Termos de Uso são regidos pelas leis brasileiras. Qualquer disputa será resolvida nos tribunais competentes da comarca de São Paulo, SP.</p>
            </div>

            <div class="section">
                <h2>11. Disposições Gerais</h2>
                
                <h3>11.1 Acordo Integral</h3>
                <p>Estes termos constituem o acordo integral entre você e o ENIAC LINK+ em relação ao uso da plataforma.</p>

                <h3>11.2 Independência das Cláusulas</h3>
                <p>Se qualquer disposição destes termos for considerada inválida, as demais cláusulas permanecerão em pleno vigor.</p>

                <h3>11.3 Renúncia</h3>
                <p>A falha em exercer qualquer direito não constitui renúncia a esse direito.</p>
            </div>

            <div class="contact-info">
                <h3><i class="fas fa-envelope"></i> Contato</h3>
                <p>Para dúvidas sobre estes Termos de Uso, entre em contato:</p>
                <p><strong>Email:</strong> juridico@LWFA.com.br</p>
                <p><strong>Telefone:</strong> (11) 1234-5678</p>
                <p><strong>Endereço:</strong> Rua Força Pública, 89 - Centro, Guarulhos - SP</p>
                <br>
                <a href="fale_conosco.php" style="color: #0056b3; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-comments"></i> Usar o Fale Conosco
                </a>
            </div>
        </div>
    </div>

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
