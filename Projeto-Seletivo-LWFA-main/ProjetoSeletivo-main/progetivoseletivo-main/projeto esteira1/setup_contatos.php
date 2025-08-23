<?php
require_once 'config.php';

try {
    $database = new Database();
    $pdo = $database->connect();
    
    echo "<h2>🔧 Atualizando Banco de Dados - Fale Conosco</h2>";
    
    // Criar tabela de contatos
    $sql_contatos = "
    CREATE TABLE IF NOT EXISTS contatos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        telefone VARCHAR(20),
        categoria ENUM('suporte_tecnico', 'duvidas_vagas', 'problemas_cadastro', 'empresas', 'sugestoes', 'outros') NOT NULL,
        assunto VARCHAR(200) NOT NULL,
        mensagem TEXT NOT NULL,
        status ENUM('novo', 'em_andamento', 'respondido', 'resolvido') DEFAULT 'novo',
        resposta TEXT NULL,
        admin_responsavel_id INT NULL,
        data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_resposta TIMESTAMP NULL,
        FOREIGN KEY (admin_responsavel_id) REFERENCES usuarios_admin(id) ON DELETE SET NULL
    )";
    
    $pdo->exec($sql_contatos);
    echo "✅ Tabela 'contatos' criada com sucesso!<br>";
    
    // Criar índices
    $indices = [
        "CREATE INDEX IF NOT EXISTS idx_contatos_status ON contatos(status)",
        "CREATE INDEX IF NOT EXISTS idx_contatos_categoria ON contatos(categoria)",
        "CREATE INDEX IF NOT EXISTS idx_contatos_data ON contatos(data_envio)"
    ];
    
    foreach ($indices as $indice) {
        $pdo->exec($indice);
    }
    echo "✅ Índices criados com sucesso!<br>";
    
    // Inserir alguns contatos de exemplo
    $contatos_exemplo = [
        [
            'nome' => 'João Silva',
            'email' => 'joao.silva@email.com',
            'telefone' => '(11) 99999-1111',
            'categoria' => 'duvidas_vagas',
            'assunto' => 'Dúvida sobre vaga de Desenvolvedor',
            'mensagem' => 'Gostaria de saber mais informações sobre a vaga de Desenvolvedor Front-end React. Quais são os requisitos técnicos específicos?'
        ],
        [
            'nome' => 'Maria Santos',
            'email' => 'maria.santos@email.com',
            'telefone' => '(11) 99999-2222',
            'categoria' => 'problemas_cadastro',
            'assunto' => 'Erro ao finalizar cadastro',
            'mensagem' => 'Estou tentando finalizar meu cadastro, mas aparece um erro quando clico em enviar. Podem me ajudar?'
        ],
        [
            'nome' => 'TechCorp RH',
            'email' => 'rh@techcorp.com',
            'telefone' => '(11) 99999-3333',
            'categoria' => 'empresas',
            'assunto' => 'Interesse em publicar vagas',
            'mensagem' => 'Nossa empresa tem interesse em publicar vagas na plataforma. Gostaríamos de saber sobre os valores e processo.'
        ]
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO contatos (nome, email, telefone, categoria, assunto, mensagem) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($contatos_exemplo as $contato) {
        $stmt->execute([
            $contato['nome'],
            $contato['email'],
            $contato['telefone'],
            $contato['categoria'],
            $contato['assunto'],
            $contato['mensagem']
        ]);
    }
    
    echo "✅ Contatos de exemplo inseridos com sucesso!<br>";
    echo "<br><strong>🎉 Banco de dados atualizado com sucesso!</strong><br>";
    echo "<br><a href='fale_conosco.php' style='color: #0056b3; font-weight: bold;'>📧 Testar Página Fale Conosco</a><br>";
    echo "<a href='admin.php' style='color: #0056b3; font-weight: bold;'>🔧 Ir para o Painel Administrativo</a>";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
