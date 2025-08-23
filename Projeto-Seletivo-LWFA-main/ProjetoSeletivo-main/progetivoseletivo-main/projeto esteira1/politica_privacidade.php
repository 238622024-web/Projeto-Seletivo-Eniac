<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade | LWFA</title>
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

        .lgpd-box {
            background: #e8f5e8;
            border: 2px solid #28a745;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
        }

        .lgpd-box h3 {
            color: #28a745;
            margin-bottom: 1rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            background: #fafafa;
            border-radius: 8px;
            overflow: hidden;
        }

        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .data-table th {
            background: #0056b3;
            color: white;
            font-weight: 600;
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

        .rights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }

        .right-card {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #0056b3;
        }

        .right-card h4 {
            color: #0056b3;
            margin-bottom: 0.5rem;
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

            .data-table {
                font-size: 0.9rem;
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
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2>LWFA</h2>
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
                <h1><i class="fas fa-shield-alt"></i> Política de Privacidade</h1>
                <div class="last-update">
                    Última atualização: 04 de agosto de 2025
                </div>
            </div>

            <div class="lgpd-box">
                <h3><i class="fas fa-gavel"></i> Conformidade com a LGPD</h3>
                <p>Esta Política de Privacidade está em conformidade com a <strong>Lei Geral de Proteção de Dados (LGPD - Lei nº 13.709/2018)</strong> e define como coletamos, usamos, armazenamos e protegemos seus dados pessoais.</p>
            </div>

            <div class="section">
                <h2>1. Introdução</h2>
                <p>O LWFA respeita sua privacidade e está comprometido em proteger seus dados pessoais. Esta política explica como tratamos as informações que você compartilha conosco ao utilizar nossa plataforma de processo seletivo.</p>
                
                <div class="highlight">
                    <strong>Responsável pelo Tratamento:</strong> LWFA - Laboratório de Desenvolvimento de Software<br>
                    <strong>Endereço:</strong> Rua Força Pública, 89 - Centro, Guarulhos - SP<br>
                    <strong>Email do DPO:</strong> dpo@lwfa.com.br
                </div>
            </div>

            <div class="section">
                <h2>2. Dados Pessoais Coletados</h2>
                <p>Coletamos apenas os dados necessários para o funcionamento do processo seletivo e prestação de nossos serviços:</p>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tipo de Dado</th>
                            <th>Exemplos</th>
                            <th>Finalidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Dados de Identificação</strong></td>
                            <td>Nome completo, CPF, RG, data de nascimento</td>
                            <td>Identificação e validação do candidato</td>
                        </tr>
                        <tr>
                            <td><strong>Dados de Contato</strong></td>
                            <td>Email, telefone, endereço</td>
                            <td>Comunicação durante o processo seletivo</td>
                        </tr>
                        <tr>
                            <td><strong>Dados Profissionais</strong></td>
                            <td>Currículo, experiências, formação</td>
                            <td>Avaliação de compatibilidade com vagas</td>
                        </tr>
                        <tr>
                            <td><strong>Dados de Navegação</strong></td>
                            <td>IP, cookies, logs de acesso</td>
                            <td>Segurança e melhoria da plataforma</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>3. Finalidades do Tratamento</h2>
                <p>Utilizamos seus dados pessoais para as seguintes finalidades legítimas:</p>
                
                <h3>3.1 Processo Seletivo</h3>
                <ul>
                    <li>Cadastro e gestão de candidatos</li>
                    <li>Análise de compatibilidade com vagas</li>
                    <li>Comunicação sobre oportunidades</li>
                    <li>Condução de entrevistas e avaliações</li>
                </ul>

                <h3>3.2 Melhoria dos Serviços</h3>
                <ul>
                    <li>Análise estatística e relatórios (dados anonimizados)</li>
                    <li>Desenvolvimento de novos recursos</li>
                    <li>Otimização da experiência do usuário</li>
                </ul>

                <h3>3.3 Cumprimento Legal</h3>
                <ul>
                    <li>Atendimento a obrigações legais</li>
                    <li>Cooperação com autoridades competentes</li>
                    <li>Defesa de direitos em processos judiciais</li>
                </ul>
            </div>

            <div class="section">
                <h2>4. Base Legal para o Tratamento</h2>
                <p>O tratamento de seus dados pessoais baseia-se nas seguintes hipóteses legais da LGPD:</p>
                
                <div class="rights-grid">
                    <div class="right-card">
                        <h4><i class="fas fa-handshake"></i> Consentimento</h4>
                        <p>Para envio de comunicações e marketing (quando aplicável)</p>
                    </div>
                    <div class="right-card">
                        <h4><i class="fas fa-file-contract"></i> Execução de Contrato</h4>
                        <p>Para processos seletivos e prestação de serviços</p>
                    </div>
                    <div class="right-card">
                        <h4><i class="fas fa-balance-scale"></i> Legítimo Interesse</h4>
                        <p>Para segurança, análises e melhorias do sistema</p>
                    </div>
                    <div class="right-card">
                        <h4><i class="fas fa-gavel"></i> Obrigação Legal</h4>
                        <p>Para cumprimento de exigências legais e regulamentares</p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>5. Compartilhamento de Dados</h2>
                <p>Seus dados pessoais podem ser compartilhados apenas nas seguintes situações:</p>
                
                <h3>5.1 Empresas Parceiras</h3>
                <p>Com empresas que oferecem vagas de emprego, limitado aos dados necessários para o processo seletivo específico e sempre com seu conhecimento.</p>

                <h3>5.2 Prestadores de Serviços</h3>
                <p>Com fornecedores que nos auxiliam na operação da plataforma, sob rígidas cláusulas de confidencialidade e proteção de dados.</p>

                <h3>5.3 Autoridades Competentes</h3>
                <p>Quando exigido por lei ou ordem judicial.</p>

                <div class="highlight">
                    <strong>Importante:</strong> Nunca vendemos, alugamos ou comercializamos seus dados pessoais com terceiros para fins publicitários.
                </div>
            </div>

            <div class="section">
                <h2>6. Seus Direitos como Titular</h2>
                <p>De acordo com a LGPD, você possui os seguintes direitos em relação aos seus dados pessoais:</p>
                
                <div class="rights-grid">
                    <div class="right-card">
                        <h4><i class="fas fa-eye"></i> Acesso</h4>
                        <p>Saber quais dados possuímos sobre você</p>
                    </div>
                    <div class="right-card">
                        <h4><i class="fas fa-edit"></i> Correção</h4>
                        <p>Corrigir dados incompletos ou incorretos</p>
                    </div>
                    <div class="right-card">
                        <h4><i class="fas fa-trash"></i> Exclusão</h4>
                        <p>Solicitar a remoção de dados desnecessários</p>
                    </div>
                    <div class="right-card">
                        <h4><i class="fas fa-ban"></i> Oposição</h4>
                        <p>Opor-se ao tratamento de seus dados</p>
                    </div>
                    <div class="right-card">
                        <h4><i class="fas fa-download"></i> Portabilidade</h4>
                        <p>Receber seus dados em formato estruturado</p>
                    </div>
                    <div class="right-card">
                        <h4><i class="fas fa-info-circle"></i> Informação</h4>
                        <p>Saber sobre o tratamento e compartilhamento</p>
                    </div>
                </div>

                <p style="margin-top: 1rem;"><strong>Como exercer seus direitos:</strong> Entre em contato através do "Fale Conosco" ou email: <strong>dpo@eniaclink.com.br</strong></p>
            </div>

            <div class="section">
                <h2>7. Segurança dos Dados</h2>
                <p>Implementamos medidas técnicas e organizacionais para proteger seus dados pessoais:</p>
                
                <h3>7.1 Medidas Técnicas</h3>
                <ul>
                    <li>Criptografia de dados sensíveis</li>
                    <li>Certificados SSL/TLS para transmissão segura</li>
                    <li>Controle de acesso baseado em função</li>
                    <li>Monitoramento contínuo de segurança</li>
                    <li>Backups regulares e seguros</li>
                </ul>

                <h3>7.2 Medidas Organizacionais</h3>
                <ul>
                    <li>Treinamento de funcionários sobre privacidade</li>
                    <li>Políticas internas de proteção de dados</li>
                    <li>Controle de acesso físico aos servidores</li>
                    <li>Auditoria regular dos processos</li>
                </ul>
            </div>

            <div class="section">
                <h2>8. Retenção de Dados</h2>
                <p>Mantemos seus dados pessoais apenas pelo tempo necessário para:</p>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tipo de Dado</th>
                            <th>Período de Retenção</th>
                            <th>Justificativa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Dados de candidatura ativa</td>
                            <td>Durante o processo seletivo</td>
                            <td>Condução do processo</td>
                        </tr>
                        <tr>
                            <td>Currículos não selecionados</td>
                            <td>12 meses após o processo</td>
                            <td>Oportunidades futuras</td>
                        </tr>
                        <tr>
                            <td>Dados de candidatos contratados</td>
                            <td>Conforme exigência legal</td>
                            <td>Obrigações trabalhistas</td>
                        </tr>
                        <tr>
                            <td>Logs de acesso</td>
                            <td>6 meses</td>
                            <td>Segurança da informação</td>
                        </tr>
                    </tbody>
                </table>

                <p>Após o período de retenção, os dados são excluídos de forma segura e irreversível.</p>
            </div>

            <div class="section">
                <h2>9. Cookies e Tecnologias Similares</h2>
                <p>Utilizamos cookies e tecnologias similares para melhorar sua experiência:</p>
                
                <h3>9.1 Tipos de Cookies</h3>
                <ul>
                    <li><strong>Cookies Essenciais:</strong> Necessários para o funcionamento básico da plataforma</li>
                    <li><strong>Cookies de Desempenho:</strong> Para analisar como a plataforma é utilizada</li>
                    <li><strong>Cookies de Funcionalidade:</strong> Para lembrar suas preferências</li>
                </ul>

                <h3>9.2 Gestão de Cookies</h3>
                <p>Você pode gerenciar os cookies através das configurações do seu navegador. Note que desabilitar cookies essenciais pode afetar o funcionamento da plataforma.</p>
            </div>

            <div class="section">
                <h2>10. Transferência Internacional</h2>
                <p>Atualmente, seus dados são processados e armazenados exclusivamente no Brasil. Caso seja necessária transferência internacional, informaremos previamente e garantiremos adequado nível de proteção.</p>
            </div>

            <div class="section">
                <h2>11. Menores de Idade</h2>
                <p>Nossa plataforma é destinada a pessoas com pelo menos 16 anos de idade. Para menores entre 16 e 18 anos, é necessário consentimento dos pais ou responsáveis legais.</p>
                
                <div class="highlight">
                    <strong>Proteção Especial:</strong> Dados de menores de idade recebem proteção adicional e tratamento específico conforme exige a LGPD.
                </div>
            </div>

            <div class="section">
                <h2>12. Incidentes de Segurança</h2>
                <p>Em caso de incidente de segurança que possa afetar seus dados pessoais:</p>
                <ul>
                    <li>Notificaremos a Autoridade Nacional de Proteção de Dados (ANPD)</li>
                    <li>Comunicaremos os titulares afetados quando necessário</li>
                    <li>Tomaremos medidas imediatas para mitigar os riscos</li>
                    <li>Investigaremos as causas e implementaremos melhorias</li>
                </ul>
            </div>

            <div class="section">
                <h2>13. Alterações nesta Política</h2>
                <p>Esta Política de Privacidade pode ser atualizada periodicamente. Principais mudanças serão comunicadas através de:</p>
                <ul>
                    <li>Notificação na plataforma</li>
                    <li>Email para usuários cadastrados</li>
                    <li>Aviso na página inicial por 30 dias</li>
                </ul>
                
                <p>Recomendamos que revise esta política regularmente para se manter informado sobre como protegemos seus dados.</p>
            </div>

            <div class="section">
                <h2>14. Canal de Privacidade</h2>
                <p>Para questões relacionadas à privacidade e proteção de dados, você pode entrar em contato através de:</p>
                
                <div class="contact-info">
                    <h3><i class="fas fa-user-shield"></i> Encarregado de Proteção de Dados (DPO)</h3>
                    <p><strong>Email:</strong> dpo@lwfa.com.br</p>
                    <p><strong>Telefone:</strong> (11) 1234-5678 - Ramal 100</p>
                    <p><strong>Formulário:</strong> <a href="fale_conosco.php" style="color: #0056b3;">Fale Conosco - Categoria "Privacidade"</a></p>
                    <p><strong>Endereço:</strong> Rua Força Pública, 89 - Centro, Guarulhos - SP - CEP: 07012-040</p>
                    <br>
                    <p style="font-size: 0.9rem; color: #666;">
                        <strong>Prazo de Resposta:</strong> Até 15 dias úteis conforme previsto na LGPD
                    </p>
                </div>
            </div>

            <div class="section">
                <h2>15. Autoridade de Controle</h2>
                <p>Você também pode apresentar reclamações à Autoridade Nacional de Proteção de Dados (ANPD):</p>
                
                <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; text-align: center;">
                    <p><strong>ANPD - Autoridade Nacional de Proteção de Dados</strong></p>
                    <p>Website: <a href="https://www.gov.br/anpd" target="_blank" style="color: #0056b3;">www.gov.br/anpd</a></p>
                    <p>Email: peticionamento.anpd@anpd.gov.br</p>
                </div>
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
