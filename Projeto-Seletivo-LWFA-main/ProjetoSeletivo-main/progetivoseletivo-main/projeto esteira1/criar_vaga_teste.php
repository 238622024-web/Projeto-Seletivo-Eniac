<?php
// Ativar exibição de erros para debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

$admin_nome = 'Administrador (Modo Teste)';
$message = '';
$message_type = '';

// Processar criação de vaga
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $pdo = $database->connect();
        
        $titulo = trim($_POST['titulo'] ?? '');
        $empresa = trim($_POST['empresa'] ?? '');
        $localizacao = trim($_POST['localizacao'] ?? '');
        $salario_min = trim($_POST['salario_min'] ?? '');
        $salario_max = trim($_POST['salario_max'] ?? '');
        $tipo = trim($_POST['tipo'] ?? '');
        $modalidade = trim($_POST['modalidade'] ?? '');
        $vagas_disponiveis = (int)($_POST['vagas_disponiveis'] ?? 1);
        $data_encerramento = trim($_POST['data_encerramento'] ?? '');
        $status = trim($_POST['status'] ?? 'ativa');
        $descricao = trim($_POST['descricao'] ?? '');
        $requisitos = trim($_POST['requisitos'] ?? '');
        $beneficios = trim($_POST['beneficios'] ?? '');
        
        // Validação básica
        if (empty($titulo) || empty($empresa) || empty($descricao)) {
            throw new Exception('Título, empresa e descrição são obrigatórios');
        }
        
        if ($vagas_disponiveis < 1) {
            $vagas_disponiveis = 1;
        }
        
        // Validar data de encerramento
        if (!empty($data_encerramento)) {
            $data_hoje = date('Y-m-d');
            if ($data_encerramento <= $data_hoje) {
                throw new Exception('Data de encerramento deve ser posterior à data atual');
            }
        } else {
            $data_encerramento = null;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO vagas (titulo, empresa, localizacao, salario_min, salario_max, tipo_contrato, modalidade, vagas_disponiveis, data_encerramento, status, descricao, requisitos, beneficios, data_publicacao, data_criacao) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), NOW())
        ");
        
        $stmt->execute([
            $titulo, $empresa, $localizacao, $salario_min, $salario_max,
            $tipo, $modalidade, $vagas_disponiveis, $data_encerramento ?: null, $status, $descricao, $requisitos, $beneficios
        ]);
        
        $message = 'Vaga criada com sucesso!';
        $message_type = 'success';
        
        // Limpar formulário após sucesso
        $_POST = [];
        
    } catch (Exception $e) {
        $message = 'Erro ao criar vaga: ' . $e->getMessage();
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Nova Vaga - LWFA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'bounce-in': 'bounceIn 0.5s ease-out',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .input-focus:focus {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.25);
        }
        
        .btn-hover {
            position: relative;
            overflow: hidden;
        }
        
        .btn-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-hover:hover::before {
            left: 100%;
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gray-50 font-inter min-h-screen">
    <!-- Cabeçalho fixo -->
    <header class="fixed top-0 left-0 right-0 bg-white shadow-sm z-40 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="gerenciar_vagas.php" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all duration-300 font-medium group">
                        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform duration-300"></i>
                        Voltar
                    </a>
                    <div class="h-8 w-px bg-gray-200"></div>
                    <h1 class="text-xl font-bold text-gray-900">Criar Nova Vaga</h1>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <i class="fas fa-briefcase"></i>
                    <span>LWFA</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Container principal -->
    <main class="pt-24 pb-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Título e descrição -->
            <div class="text-center mb-8 animate-fade-in">
                <div class="gradient-bg text-white rounded-2xl p-8 mb-8 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black opacity-10"></div>
                    <div class="relative z-10">
                        <h1 class="text-3xl md:text-4xl font-bold mb-4">
                            <i class="fas fa-plus-circle mr-3"></i>
                            Criar Nova Vaga de Emprego
                        </h1>
                        <p class="text-lg opacity-90 max-w-2xl mx-auto">
                            Preencha as informações abaixo para criar uma nova oportunidade de emprego. 
                            Certifique-se de fornecer detalhes claros e atrativos para atrair os melhores candidatos.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mensagem de feedback -->
            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 border border-green-300 text-green-800' : 'bg-red-100 border border-red-300 text-red-800'; ?>">
                    <div class="flex items-center">
                        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?> mr-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form method="POST" action="" class="space-y-8 animate-slide-up">
                <!-- Seção 1: Informações Básicas -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300 animate-bounce-in">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mr-4">
                            <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Informações Básicas</h2>
                            <p class="text-gray-500 text-sm">Dados fundamentais sobre a vaga</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="titulo" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-briefcase mr-2 text-gray-400"></i>
                                Título da Vaga <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="titulo" name="titulo" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: Desenvolvedor Full Stack Sênior"
                                   value="<?php echo htmlspecialchars($_POST['titulo'] ?? ''); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="empresa" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-building mr-2 text-gray-400"></i>
                                Empresa <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="empresa" name="empresa" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: ENIAC LINK+"
                                   value="<?php echo htmlspecialchars($_POST['empresa'] ?? ''); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="localizacao" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                Localização
                            </label>
                            <input type="text" id="localizacao" name="localizacao"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: São Paulo - SP"
                                   value="<?php echo htmlspecialchars($_POST['localizacao'] ?? ''); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="modalidade" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-laptop-house mr-2 text-gray-400"></i>
                                Modalidade
                            </label>
                            <select id="modalidade" name="modalidade"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300">
                                <option value="">Selecione a modalidade</option>
                                <option value="presencial" <?php echo ($_POST['modalidade'] ?? '') === 'presencial' ? 'selected' : ''; ?>>Presencial</option>
                                <option value="híbrido" <?php echo ($_POST['modalidade'] ?? '') === 'híbrido' ? 'selected' : ''; ?>>Híbrido</option>
                                <option value="remoto" <?php echo ($_POST['modalidade'] ?? '') === 'remoto' ? 'selected' : ''; ?>>Remoto</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Seção 2: Detalhes da Vaga -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300 animate-bounce-in" style="animation-delay: 0.2s;">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg mr-4">
                            <i class="fas fa-cogs text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Detalhes da Vaga</h2>
                            <p class="text-gray-500 text-sm">Informações sobre salário, prazo e contrato</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label for="salario_min" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-dollar-sign mr-2 text-gray-400"></i>
                                Salário Mínimo
                            </label>
                            <input type="number" id="salario_min" name="salario_min" min="0" step="100"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: 5000"
                                   value="<?php echo htmlspecialchars($_POST['salario_min'] ?? ''); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="salario_max" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-money-bill-wave mr-2 text-gray-400"></i>
                                Salário Máximo
                            </label>
                            <input type="number" id="salario_max" name="salario_max" min="0" step="100"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: 8000"
                                   value="<?php echo htmlspecialchars($_POST['salario_max'] ?? ''); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="vagas_disponiveis" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-users mr-2 text-gray-400"></i>
                                Vagas Disponíveis
                            </label>
                            <input type="number" id="vagas_disponiveis" name="vagas_disponiveis" min="1" max="50"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: 2"
                                   value="<?php echo htmlspecialchars($_POST['vagas_disponiveis'] ?? '1'); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="data_encerramento" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-calendar-times mr-2 text-gray-400"></i>
                                Data de Encerramento
                            </label>
                            <input type="date" id="data_encerramento" name="data_encerramento"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                   value="<?php echo htmlspecialchars($_POST['data_encerramento'] ?? ''); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="tipo" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-file-contract mr-2 text-gray-400"></i>
                                Tipo de Contrato
                            </label>
                            <select id="tipo" name="tipo"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300">
                                <option value="">Selecione o tipo</option>
                                <option value="clt" <?php echo ($_POST['tipo'] ?? '') === 'clt' ? 'selected' : ''; ?>>CLT</option>
                                <option value="pj" <?php echo ($_POST['tipo'] ?? '') === 'pj' ? 'selected' : ''; ?>>PJ</option>
                                <option value="estagio" <?php echo ($_POST['tipo'] ?? '') === 'estagio' ? 'selected' : ''; ?>>Estágio</option>
                                <option value="temporario" <?php echo ($_POST['tipo'] ?? '') === 'temporario' ? 'selected' : ''; ?>>Temporário</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="status" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-toggle-on mr-2 text-gray-400"></i>
                                Status
                            </label>
                            <select id="status" name="status"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300">
                                <option value="ativa" <?php echo ($_POST['status'] ?? 'ativa') === 'ativa' ? 'selected' : ''; ?>>Ativa</option>
                                <option value="pausada" <?php echo ($_POST['status'] ?? '') === 'pausada' ? 'selected' : ''; ?>>Inativa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Seção 3: Descrição e Requisitos -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300 animate-bounce-in" style="animation-delay: 0.4s;">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg mr-4">
                            <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Descrição e Requisitos</h2>
                            <p class="text-gray-500 text-sm">Detalhes sobre responsabilidades e benefícios</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label for="descricao" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-align-left mr-2 text-gray-400"></i>
                                Descrição da Vaga <span class="text-red-500">*</span>
                            </label>
                            <textarea id="descricao" name="descricao" rows="4" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300 resize-none"
                                      placeholder="Descreva as principais responsabilidades, objetivos da posição e o que a pessoa fará no dia a dia. Seja claro sobre as expectativas e o ambiente de trabalho."><?php echo htmlspecialchars($_POST['descricao'] ?? ''); ?></textarea>
                        </div>

                        <div class="space-y-2">
                            <label for="requisitos" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-list-check mr-2 text-gray-400"></i>
                                Requisitos
                            </label>
                            <textarea id="requisitos" name="requisitos" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300 resize-none"
                                      placeholder="Liste os requisitos necessários:&#10;• Formação acadêmica (ex: Superior em Tecnologia)&#10;• Experiência profissional (ex: 3+ anos em desenvolvimento)&#10;• Conhecimentos técnicos (ex: JavaScript, React, Node.js)&#10;• Habilidades comportamentais (ex: trabalho em equipe, comunicação)"><?php echo htmlspecialchars($_POST['requisitos'] ?? ''); ?></textarea>
                        </div>

                        <div class="space-y-2">
                            <label for="beneficios" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-gift mr-2 text-gray-400"></i>
                                Benefícios
                            </label>
                            <textarea id="beneficios" name="beneficios" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300 resize-none"
                                      placeholder="Descreva os benefícios oferecidos:&#10;• Vale alimentação/refeição&#10;• Plano de saúde e odontológico&#10;• Home office/flexibilidade&#10;• Desenvolvimento profissional e treinamentos&#10;• Outros benefícios (ex: gympass, day off aniversário)"><?php echo htmlspecialchars($_POST['beneficios'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-6 animate-fade-in" style="animation-delay: 0.6s;">
                    <button type="submit" class="btn-hover px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 min-w-[160px] transition-all duration-300">
                        <i class="fas fa-save"></i>
                        <span>Criar Vaga</span>
                    </button>
                    <a href="gerenciar_vagas.php" class="btn-hover px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 min-w-[160px] transition-all duration-300 no-underline">
                        <i class="fas fa-times"></i>
                        <span>Cancelar</span>
                    </a>
                </div>
            </form>
        </div>
    </main>

    <!-- Scripts -->
    <script>
        // Definir data mínima para o campo de data de encerramento
        document.addEventListener('DOMContentLoaded', function() {
            const dataInput = document.getElementById('data_encerramento');
            if (dataInput) {
                const hoje = new Date();
                const amanha = new Date(hoje);
                amanha.setDate(hoje.getDate() + 1);
                
                const dataFormatada = amanha.toISOString().split('T')[0];
                dataInput.min = dataFormatada;
            }
        });

        // Validação em tempo real
        document.getElementById('titulo').addEventListener('input', function() {
            const valor = this.value.trim();
            if (valor.length > 0 && valor.length < 5) {
                this.classList.add('border-yellow-400', 'bg-yellow-50');
                this.classList.remove('border-red-400', 'bg-red-50', 'border-green-400', 'bg-green-50');
            } else if (valor.length >= 5) {
                this.classList.add('border-green-400', 'bg-green-50');
                this.classList.remove('border-yellow-400', 'bg-yellow-50', 'border-red-400', 'bg-red-50');
            } else {
                this.classList.remove('border-yellow-400', 'bg-yellow-50', 'border-green-400', 'bg-green-50', 'border-red-400', 'bg-red-50');
            }
        });

        document.getElementById('empresa').addEventListener('input', function() {
            const valor = this.value.trim();
            if (valor.length > 0 && valor.length < 3) {
                this.classList.add('border-yellow-400', 'bg-yellow-50');
                this.classList.remove('border-red-400', 'bg-red-50', 'border-green-400', 'bg-green-50');
            } else if (valor.length >= 3) {
                this.classList.add('border-green-400', 'bg-green-50');
                this.classList.remove('border-yellow-400', 'bg-yellow-50', 'border-red-400', 'bg-red-50');
            } else {
                this.classList.remove('border-yellow-400', 'bg-yellow-50', 'border-green-400', 'bg-green-50', 'border-red-400', 'bg-red-50');
            }
        });

        // Contador de caracteres para textareas
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            const container = textarea.parentNode;
            const counter = document.createElement('div');
            counter.className = 'text-sm text-gray-500 text-right mt-1 font-medium';
            container.appendChild(counter);

            function atualizarContador() {
                const count = textarea.value.length;
                counter.textContent = `${count} caracteres`;
                
                if (count > 500) {
                    counter.className = 'text-sm text-red-500 text-right mt-1 font-medium';
                } else if (count > 300) {
                    counter.className = 'text-sm text-yellow-500 text-right mt-1 font-medium';
                } else {
                    counter.className = 'text-sm text-gray-500 text-right mt-1 font-medium';
                }
            }

            textarea.addEventListener('input', atualizarContador);
            atualizarContador();
        });

        // Feedback visual para o formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Criando Vaga...';
            submitBtn.disabled = true;
        });

        // Auto-resize para textareas
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });

        // Validação de salários
        document.getElementById('salario_min').addEventListener('input', function() {
            const salarioMax = document.getElementById('salario_max');
            if (this.value && salarioMax.value && parseFloat(this.value) > parseFloat(salarioMax.value)) {
                this.classList.add('border-red-400', 'bg-red-50');
                salarioMax.classList.add('border-red-400', 'bg-red-50');
            } else {
                this.classList.remove('border-red-400', 'bg-red-50');
                salarioMax.classList.remove('border-red-400', 'bg-red-50');
            }
        });

        document.getElementById('salario_max').addEventListener('input', function() {
            const salarioMin = document.getElementById('salario_min');
            if (this.value && salarioMin.value && parseFloat(salarioMin.value) > parseFloat(this.value)) {
                this.classList.add('border-red-400', 'bg-red-50');
                salarioMin.classList.add('border-red-400', 'bg-red-50');
            } else {
                this.classList.remove('border-red-400', 'bg-red-50');
                salarioMin.classList.remove('border-red-400', 'bg-red-50');
            }
        });
    </script>
</body>
</html>
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Nova Vaga - ENIAC LINK+</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'bounce-in': 'bounceIn 0.5s ease-out',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .input-focus:focus {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.25);
        }
        
        .btn-hover {
            position: relative;
            overflow: hidden;
        }
        
        .btn-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-hover:hover::before {
            left: 100%;
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
        }
    </link>
</head>
<body class="bg-gray-50 font-inter min-h-screen">
    <!-- Cabeçalho fixo -->
    <header class="fixed top-0 left-0 right-0 bg-white shadow-sm z-40 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="gerenciar_vagas.php" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all duration-300 font-medium group">
                        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform duration-300"></i>
                        Voltar
                    </a>
                    <div class="h-8 w-px bg-gray-200"></div>
                    <h1 class="text-xl font-bold text-gray-900">Criar Nova Vaga</h1>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <i class="fas fa-briefcase"></i>
                    <span>ENIAC LINK+</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Container principal -->
    <main class="pt-24 pb-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Título e descrição -->
            <div class="text-center mb-8 animate-fade-in">
                <div class="gradient-bg text-white rounded-2xl p-8 mb-8 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black opacity-10"></div>
                    <div class="relative z-10">
                        <h1 class="text-3xl md:text-4xl font-bold mb-4">
                            <i class="fas fa-plus-circle mr-3"></i>
                            Criar Nova Vaga de Emprego
                        </h1>
                        <p class="text-lg opacity-90 max-w-2xl mx-auto">
                            Preencha as informações abaixo para criar uma nova oportunidade de emprego. 
                            Certifique-se de fornecer detalhes claros e atrativos para atrair os melhores candidatos.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mensagem de feedback -->
            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 border border-green-300 text-green-800' : 'bg-red-100 border border-red-300 text-red-800'; ?>">
                    <div class="flex items-center">
                        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?> mr-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form method="POST" action="" class="space-y-8 animate-slide-up">
                <!-- Seção 1: Informações Básicas -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300 animate-bounce-in">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mr-4">
                            <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Informações Básicas</h2>
                            <p class="text-gray-500 text-sm">Dados fundamentais sobre a vaga</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="titulo" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-briefcase mr-2 text-gray-400"></i>
                                Título da Vaga <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="titulo" name="titulo" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: Desenvolvedor Full Stack Sênior"
                                   value="<?php echo htmlspecialchars($_POST['titulo'] ?? ''); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="empresa" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-building mr-2 text-gray-400"></i>
                                Empresa <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="empresa" name="empresa" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: ENIAC LINK+"
                                   value="<?php echo htmlspecialchars($_POST['empresa'] ?? ''); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="localizacao" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                Localização
                            </label>
                            <input type="text" id="localizacao" name="localizacao"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: São Paulo - SP"
                                   value="<?php echo htmlspecialchars($_POST['localizacao'] ?? ''); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="modalidade" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-laptop-house mr-2 text-gray-400"></i>
                                Modalidade
                            </label>
                            <select id="modalidade" name="modalidade"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300">
                                <option value="">Selecione a modalidade</option>
                                <option value="presencial" <?php echo ($_POST['modalidade'] ?? '') === 'presencial' ? 'selected' : ''; ?>>Presencial</option>
                                <option value="híbrido" <?php echo ($_POST['modalidade'] ?? '') === 'híbrido' ? 'selected' : ''; ?>>Híbrido</option>
                                <option value="remoto" <?php echo ($_POST['modalidade'] ?? '') === 'remoto' ? 'selected' : ''; ?>>Remoto</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Seção 2: Detalhes da Vaga -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300 animate-bounce-in" style="animation-delay: 0.2s;">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg mr-4">
                            <i class="fas fa-cogs text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Detalhes da Vaga</h2>
                            <p class="text-gray-500 text-sm">Informações sobre salário, prazo e contrato</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label for="salario_min" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-dollar-sign mr-2 text-gray-400"></i>
                                Salário Mínimo
                            </label>
                            <input type="number" id="salario_min" name="salario_min" min="0" step="100"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: 5000"
                                   value="<?php echo htmlspecialchars($_POST['salario_min'] ?? ''); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="salario_max" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-money-bill-wave mr-2 text-gray-400"></i>
                                Salário Máximo
                            </label>
                            <input type="number" id="salario_max" name="salario_max" min="0" step="100"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: 8000"
                                   value="<?php echo htmlspecialchars($_POST['salario_max'] ?? ''); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="vagas_disponiveis" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-users mr-2 text-gray-400"></i>
                                Vagas Disponíveis
                            </label>
                            <input type="number" id="vagas_disponiveis" name="vagas_disponiveis" min="1" max="50"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: 2"
                                   value="<?php echo htmlspecialchars($_POST['vagas_disponiveis'] ?? '1'); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="data_encerramento" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-calendar-times mr-2 text-gray-400"></i>
                                Data de Encerramento
                            </label>
                            <input type="date" id="data_encerramento" name="data_encerramento"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                   value="<?php echo htmlspecialchars($_POST['data_encerramento'] ?? ''); ?>">
                        </div>

                        <div class="space-y-2">
                            <label for="tipo" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-file-contract mr-2 text-gray-400"></i>
                                Tipo de Contrato
                            </label>
                            <select id="tipo" name="tipo"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300">
                                <option value="">Selecione o tipo</option>
                                <option value="clt" <?php echo ($_POST['tipo'] ?? '') === 'clt' ? 'selected' : ''; ?>>CLT</option>
                                <option value="pj" <?php echo ($_POST['tipo'] ?? '') === 'pj' ? 'selected' : ''; ?>>PJ</option>
                                <option value="estagio" <?php echo ($_POST['tipo'] ?? '') === 'estagio' ? 'selected' : ''; ?>>Estágio</option>
                                <option value="temporario" <?php echo ($_POST['tipo'] ?? '') === 'temporario' ? 'selected' : ''; ?>>Temporário</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="status" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-toggle-on mr-2 text-gray-400"></i>
                                Status
                            </label>
                            <select id="status" name="status"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300">
                                <option value="ativa" <?php echo ($_POST['status'] ?? 'ativa') === 'ativa' ? 'selected' : ''; ?>>Ativa</option>
                                <option value="pausada" <?php echo ($_POST['status'] ?? '') === 'pausada' ? 'selected' : ''; ?>>Inativa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Seção 3: Descrição e Requisitos -->
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300 animate-bounce-in" style="animation-delay: 0.4s;">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg mr-4">
                            <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Descrição e Requisitos</h2>
                            <p class="text-gray-500 text-sm">Detalhes sobre responsabilidades e benefícios</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label for="descricao" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-align-left mr-2 text-gray-400"></i>
                                Descrição da Vaga <span class="text-red-500">*</span>
                            </label>
                            <textarea id="descricao" name="descricao" rows="4" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300 resize-none"
                                      placeholder="Descreva as principais responsabilidades, objetivos da posição e o que a pessoa fará no dia a dia. Seja claro sobre as expectativas e o ambiente de trabalho."><?php echo htmlspecialchars($_POST['descricao'] ?? ''); ?></textarea>
                        </div>

                        <div class="space-y-2">
                            <label for="requisitos" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-list-check mr-2 text-gray-400"></i>
                                Requisitos
                            </label>
                            <textarea id="requisitos" name="requisitos" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300 resize-none"
                                      placeholder="Liste os requisitos necessários:&#10;• Formação acadêmica (ex: Superior em Tecnologia)&#10;• Experiência profissional (ex: 3+ anos em desenvolvimento)&#10;• Conhecimentos técnicos (ex: JavaScript, React, Node.js)&#10;• Habilidades comportamentais (ex: trabalho em equipe, comunicação)"><?php echo htmlspecialchars($_POST['requisitos'] ?? ''); ?></textarea>
                        </div>

                        <div class="space-y-2">
                            <label for="beneficios" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-gift mr-2 text-gray-400"></i>
                                Benefícios
                            </label>
                            <textarea id="beneficios" name="beneficios" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300 resize-none"
                                      placeholder="Descreva os benefícios oferecidos:&#10;• Vale alimentação/refeição&#10;• Plano de saúde e odontológico&#10;• Home office/flexibilidade&#10;• Desenvolvimento profissional e treinamentos&#10;• Outros benefícios (ex: gympass, day off aniversário)"><?php echo htmlspecialchars($_POST['beneficios'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-6 animate-fade-in" style="animation-delay: 0.6s;">
                    <button type="submit" class="btn-hover px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 min-w-[160px] transition-all duration-300">
                        <i class="fas fa-save"></i>
                        <span>Criar Vaga</span>
                    </button>
                    <a href="gerenciar_vagas.php" class="btn-hover px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 min-w-[160px] transition-all duration-300 text-decoration-none">
                        <i class="fas fa-times"></i>
                        <span>Cancelar</span>
                    </a>
                </div>
            </form>
        </div>
    </main>
    <!-- Scripts -->
    <script>
        // Definir data mínima para o campo de data de encerramento
        document.addEventListener('DOMContentLoaded', function() {
            const dataInput = document.getElementById('data_encerramento');
            if (dataInput) {
                const hoje = new Date();
                const amanha = new Date(hoje);
                amanha.setDate(hoje.getDate() + 1);
                
                const dataFormatada = amanha.toISOString().split('T')[0];
                dataInput.min = dataFormatada;
            }
        });

        // Validação em tempo real
        document.getElementById('titulo').addEventListener('input', function() {
            const valor = this.value.trim();
            if (valor.length > 0 && valor.length < 5) {
                this.classList.add('border-yellow-400', 'bg-yellow-50');
                this.classList.remove('border-red-400', 'bg-red-50', 'border-green-400', 'bg-green-50');
            } else if (valor.length >= 5) {
                this.classList.add('border-green-400', 'bg-green-50');
                this.classList.remove('border-yellow-400', 'bg-yellow-50', 'border-red-400', 'bg-red-50');
            } else {
                this.classList.remove('border-yellow-400', 'bg-yellow-50', 'border-green-400', 'bg-green-50', 'border-red-400', 'bg-red-50');
            }
        });

        document.getElementById('empresa').addEventListener('input', function() {
            const valor = this.value.trim();
            if (valor.length > 0 && valor.length < 3) {
                this.classList.add('border-yellow-400', 'bg-yellow-50');
                this.classList.remove('border-red-400', 'bg-red-50', 'border-green-400', 'bg-green-50');
            } else if (valor.length >= 3) {
                this.classList.add('border-green-400', 'bg-green-50');
                this.classList.remove('border-yellow-400', 'bg-yellow-50', 'border-red-400', 'bg-red-50');
            } else {
                this.classList.remove('border-yellow-400', 'bg-yellow-50', 'border-green-400', 'bg-green-50', 'border-red-400', 'bg-red-50');
            }
        });

        // Contador de caracteres para textareas
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            const container = textarea.parentNode;
            const counter = document.createElement('div');
            counter.className = 'text-sm text-gray-500 text-right mt-1 font-medium';
            container.appendChild(counter);

            function atualizarContador() {
                const count = textarea.value.length;
                counter.textContent = `${count} caracteres`;
                
                if (count > 500) {
                    counter.className = 'text-sm text-red-500 text-right mt-1 font-medium';
                } else if (count > 300) {
                    counter.className = 'text-sm text-yellow-500 text-right mt-1 font-medium';
                } else {
                    counter.className = 'text-sm text-gray-500 text-right mt-1 font-medium';
                }
            }

            textarea.addEventListener('input', atualizarContador);
            atualizarContador();
        });

        // Feedback visual para o formulário
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Criando Vaga...';
            submitBtn.disabled = true;
        });

        // Auto-resize para textareas
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });

        // Validação de salários
        document.getElementById('salario_min').addEventListener('input', function() {
            const salarioMax = document.getElementById('salario_max');
            if (this.value && salarioMax.value && parseFloat(this.value) > parseFloat(salarioMax.value)) {
                this.classList.add('border-red-400', 'bg-red-50');
                salarioMax.classList.add('border-red-400', 'bg-red-50');
            } else {
                this.classList.remove('border-red-400', 'bg-red-50');
                salarioMax.classList.remove('border-red-400', 'bg-red-50');
            }
        });

        document.getElementById('salario_max').addEventListener('input', function() {
            const salarioMin = document.getElementById('salario_min');
            if (this.value && salarioMin.value && parseFloat(salarioMin.value) > parseFloat(this.value)) {
                this.classList.add('border-red-400', 'bg-red-50');
                salarioMin.classList.add('border-red-400', 'bg-red-50');
            } else {
                this.classList.remove('border-red-400', 'bg-red-50');
                salarioMin.classList.remove('border-red-400', 'bg-red-50');
            }
        });
    </script>
</body>
</html>
