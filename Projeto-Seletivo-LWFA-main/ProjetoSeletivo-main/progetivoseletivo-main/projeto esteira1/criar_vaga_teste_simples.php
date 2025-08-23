<?php
// Versão simplificada para teste
$message = '';
$message_type = '';

// Comentar a lógica do banco temporariamente para testar
/*
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = 'Formulário enviado com sucesso! (Teste)';
    $message_type = 'success';
}
*/
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
                                   placeholder="Ex: Desenvolvedor Full Stack Sênior">
                        </div>

                        <div class="space-y-2">
                            <label for="empresa" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-building mr-2 text-gray-400"></i>
                                Empresa <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="empresa" name="empresa" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: ENIAC LINK+">
                        </div>

                        <div class="space-y-2">
                            <label for="localizacao" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                Localização
                            </label>
                            <input type="text" id="localizacao" name="localizacao"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300"
                                   placeholder="Ex: São Paulo - SP">
                        </div>

                        <div class="space-y-2">
                            <label for="modalidade" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-laptop-house mr-2 text-gray-400"></i>
                                Modalidade
                            </label>
                            <select id="modalidade" name="modalidade"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-focus transition-all duration-300">
                                <option value="">Selecione a modalidade</option>
                                <option value="presencial">Presencial</option>
                                <option value="híbrido">Híbrido</option>
                                <option value="remoto">Remoto</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-6 animate-fade-in" style="animation-delay: 0.6s;">
                    <button type="submit" class="btn-hover px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 min-w-[160px] transition-all duration-300">
                        <i class="fas fa-save"></i>
                        <span>Criar Vaga (Teste)</span>
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
        console.log('Script carregado com sucesso!');
        
        // Teste simples
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM carregado!');
        });
    </script>
</body>
</html>
