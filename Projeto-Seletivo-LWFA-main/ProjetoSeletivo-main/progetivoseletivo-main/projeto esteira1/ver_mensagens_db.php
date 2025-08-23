<?php
require_once 'config.php';

try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Buscar todas as mensagens
    $sql = "SELECT * FROM contatos ORDER BY data_envio DESC";
    $stmt = $pdo->query($sql);
    $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $mensagens = [];
    $error = "Erro ao conectar: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens no Banco de Dados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0056b3;
            text-align: center;
            margin-bottom: 30px;
        }
        .total {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
            color: #0056b3;
        }
        .mensagem {
            border: 1px solid #ddd;
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            background: #fafafa;
        }
        .mensagem-header {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
        }
        .campo {
            margin: 5px 0;
        }
        .campo strong {
            color: #333;
        }
        .status {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-novo { background: #fee; color: #c53030; }
        .status-em_andamento { background: #fff3cd; color: #856404; }
        .status-respondido { background: #cce7ff; color: #0056b3; }
        .status-resolvido { background: #d1f2eb; color: #00875a; }
        .mensagem-texto {
            background: white;
            padding: 15px;
            border-left: 4px solid #0056b3;
            margin: 10px 0;
            white-space: pre-wrap;
            line-height: 1.6;
        }
        .resposta {
            background: #e8f5e8;
            padding: 15px;
            border-left: 4px solid #28a745;
            margin: 10px 0;
            white-space: pre-wrap;
            line-height: 1.6;
        }
        .error {
            background: #fee;
            color: #c53030;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .btn-voltar {
            background: #0056b3;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .categoria {
            background: #f0f0f0;
            color: #666;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Mensagens Salvas no Banco de Dados</h1>
        
        <a href="admin.php" class="btn-voltar">← Voltar ao Painel Admin</a>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php else: ?>
            <div class="total">
                Total de mensagens no banco: <strong><?php echo count($mensagens); ?></strong>
            </div>
            
            <?php if (empty($mensagens)): ?>
                <div style="text-align: center; padding: 40px; color: #666;">
                    <h3>📭 Nenhuma mensagem encontrada</h3>
                    <p>Ainda não há mensagens enviadas através do formulário "Fale Conosco".</p>
                    <a href="fale_conosco.php" style="color: #0056b3;">Enviar uma mensagem de teste</a>
                </div>
            <?php else: ?>
                <?php foreach ($mensagens as $index => $msg): ?>
                    <div class="mensagem">
                        <h3 style="color: #0056b3; margin-bottom: 15px;">
                            Mensagem #<?php echo $msg['id']; ?> - <?php echo htmlspecialchars($msg['nome']); ?>
                        </h3>
                        
                        <div class="mensagem-header">
                            <div class="campo">
                                <strong>ID:</strong> <?php echo $msg['id']; ?>
                            </div>
                            <div class="campo">
                                <strong>Email:</strong> <?php echo htmlspecialchars($msg['email']); ?>
                            </div>
                            <div class="campo">
                                <strong>Telefone:</strong> <?php echo htmlspecialchars($msg['telefone'] ?: 'Não informado'); ?>
                            </div>
                            <div class="campo">
                                <strong>Data de Envio:</strong> <?php echo date('d/m/Y H:i:s', strtotime($msg['data_envio'])); ?>
                            </div>
                            <div class="campo">
                                <strong>Categoria:</strong> 
                                <span class="categoria">
                                    <?php 
                                    $categorias = [
                                        'suporte_tecnico' => 'Suporte Técnico',
                                        'duvidas_vagas' => 'Dúvidas sobre Vagas',
                                        'problemas_cadastro' => 'Problemas no Cadastro',
                                        'empresas' => 'Para Empresas',
                                        'sugestoes' => 'Sugestões',
                                        'outros' => 'Outros'
                                    ];
                                    echo $categorias[$msg['categoria']] ?? $msg['categoria'];
                                    ?>
                                </span>
                            </div>
                            <div class="campo">
                                <strong>Status:</strong> 
                                <span class="status status-<?php echo $msg['status']; ?>">
                                    <?php 
                                    $status = [
                                        'novo' => 'Novo',
                                        'em_andamento' => 'Em Andamento',
                                        'respondido' => 'Respondido',
                                        'resolvido' => 'Resolvido'
                                    ];
                                    echo $status[$msg['status']] ?? $msg['status'];
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="campo">
                            <strong>Assunto:</strong> <?php echo htmlspecialchars($msg['assunto']); ?>
                        </div>
                        
                        <div class="mensagem-texto">
                            <strong>Mensagem:</strong><br>
                            <?php echo htmlspecialchars($msg['mensagem']); ?>
                        </div>
                        
                        <?php if (!empty($msg['resposta'])): ?>
                            <div class="resposta">
                                <strong>Resposta do Admin:</strong><br>
                                <?php echo htmlspecialchars($msg['resposta']); ?>
                                <br><br>
                                <small><strong>Respondido em:</strong> <?php echo date('d/m/Y H:i:s', strtotime($msg['data_resposta'])); ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
        
        <div style="margin-top: 40px; padding: 20px; background: #e3f2fd; border-radius: 5px;">
            <h3 style="color: #0056b3;">🎯 Como funciona:</h3>
            <ol style="line-height: 1.8;">
                <li><strong>Usuário envia mensagem</strong> através do formulário "Fale Conosco"</li>
                <li><strong>Mensagem é salva</strong> automaticamente nesta tabela do banco de dados</li>
                <li><strong>Administrador vê</strong> todas as mensagens através do painel de gerenciamento</li>
                <li><strong>Admin pode responder</strong> e alterar o status da mensagem</li>
                <li><strong>Histórico completo</strong> fica salvo no banco para consulta futura</li>
            </ol>
            
            <p style="margin-top: 15px;">
                <strong>📍 Localização:</strong> As mensagens ficam na tabela <code>contatos</code> do banco de dados <code>processo_seletivo</code>
            </p>
        </div>
    </div>
</body>
</html>
