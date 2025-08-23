<?php
require_once 'config.php';

echo "<h2>🌟 Criando Sistema de Avaliações</h2>";

try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Criar tabela de avaliações
    $sql_avaliacoes = "
    CREATE TABLE IF NOT EXISTS avaliacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        cargo VARCHAR(100),
        empresa VARCHAR(100),
        avaliacao INT NOT NULL CHECK (avaliacao >= 1 AND avaliacao <= 5),
        comentario TEXT NOT NULL,
        permite_publicacao TINYINT(1) DEFAULT 0,
        status ENUM('pendente', 'aprovada', 'rejeitada') DEFAULT 'pendente',
        ip_address VARCHAR(45),
        data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_moderacao TIMESTAMP NULL,
        moderado_por INT,
        INDEX idx_avaliacoes_status (status),
        INDEX idx_avaliacoes_avaliacao (avaliacao),
        INDEX idx_avaliacoes_data (data_avaliacao)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql_avaliacoes);
    echo "✅ Tabela 'avaliacoes' criada com sucesso!<br>";
    
    // Inserir avaliações de exemplo
    $avaliacoes_exemplo = [
        [
            'nome' => 'Maria Silva',
            'email' => 'maria.silva@email.com',
            'cargo' => 'Desenvolvedora Full Stack',
            'empresa' => 'TechCorp',
            'avaliacao' => 5,
            'comentario' => 'Plataforma incrível! Consegui uma vaga excelente através do ENIAC LINK+. O processo foi transparente e a equipe muito profissional.',
            'permite_publicacao' => 1,
            'status' => 'aprovada'
        ],
        [
            'nome' => 'João Santos',
            'email' => 'joao.santos@email.com',
            'cargo' => 'Analista de Marketing',
            'empresa' => 'Creative Agency',
            'avaliacao' => 4,
            'comentario' => 'Ótima experiência! O cadastro é simples e recebi retorno rápido das empresas. Recomendo para quem busca oportunidades.',
            'permite_publicacao' => 1,
            'status' => 'aprovada'
        ],
        [
            'nome' => 'Ana Costa',
            'email' => 'ana.costa@email.com',
            'cargo' => 'Designer UX/UI',
            'empresa' => '',
            'avaliacao' => 5,
            'comentario' => 'Como recém-formada, o ENIAC LINK+ foi fundamental para conseguir minha primeira oportunidade. Interface intuitiva e suporte excelente!',
            'permite_publicacao' => 1,
            'status' => 'aprovada'
        ],
        [
            'nome' => 'Carlos Oliveira',
            'email' => 'carlos.oliveira@email.com',
            'cargo' => 'Gerente de Projetos',
            'empresa' => 'InnovaTech',
            'avaliacao' => 4,
            'comentario' => 'Processo bem organizado e empresas de qualidade. A plataforma facilita muito a busca por talentos e oportunidades.',
            'permite_publicacao' => 1,
            'status' => 'aprovada'
        ],
        [
            'nome' => 'Fernanda Lima',
            'email' => 'fernanda.lima@email.com',
            'cargo' => 'Analista de Dados',
            'empresa' => 'DataScience Inc',
            'avaliacao' => 5,
            'comentario' => 'Fantástico! A conexão entre candidatos e empresas é muito eficiente. Consegui 3 entrevistas em uma semana!',
            'permite_publicacao' => 1,
            'status' => 'aprovada'
        ]
    ];
    
    $sql_insert = "
    INSERT INTO avaliacoes (nome, email, cargo, empresa, avaliacao, comentario, permite_publicacao, status, data_avaliacao) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ";
    
    $stmt = $pdo->prepare($sql_insert);
    
    foreach ($avaliacoes_exemplo as $avaliacao) {
        $stmt->execute([
            $avaliacao['nome'],
            $avaliacao['email'],
            $avaliacao['cargo'],
            $avaliacao['empresa'],
            $avaliacao['avaliacao'],
            $avaliacao['comentario'],
            $avaliacao['permite_publicacao'],
            $avaliacao['status']
        ]);
    }
    
    echo "✅ Avaliações de exemplo inseridas com sucesso!<br>";
    
    // Verificar dados inseridos
    $count_sql = "SELECT COUNT(*) as total FROM avaliacoes";
    $count = $pdo->query($count_sql)->fetch()['total'];
    echo "📊 Total de avaliações no banco: <strong>$count</strong><br>";
    
    // Calcular média de avaliações
    $media_sql = "SELECT AVG(avaliacao) as media FROM avaliacoes WHERE status = 'aprovada'";
    $media = $pdo->query($media_sql)->fetch()['media'];
    echo "⭐ Média de avaliações: <strong>" . number_format($media, 1) . "/5.0</strong><br>";
    
    echo "<br><strong>🎉 Sistema de Avaliações configurado com sucesso!</strong><br><br>";
    
    echo "<a href='avaliacoes.php' style='color: #0056b3; font-weight: bold;'>🌟 Testar Página de Avaliações</a><br>";
    echo "<a href='admin.php' style='color: #0056b3; font-weight: bold;'>🔧 Ir para o Painel Administrativo</a>";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
