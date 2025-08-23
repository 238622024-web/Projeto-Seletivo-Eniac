<?php
require_once 'config.php';

try {
    $db = (new Database())->connect();
    
    echo "<h1>Recriando Tabela de Testimonials</h1>";
    
    // Apagar tabela se existir
    $db->exec("DROP TABLE IF EXISTS testimonials_candidatos");
    echo "<p>✅ Tabela antiga removida (se existia)</p>";
    
    // Criar tabela nova
    $sql = "
    CREATE TABLE testimonials_candidatos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        cargo VARCHAR(100),
        empresa VARCHAR(100),
        mensagem TEXT NOT NULL,
        foto VARCHAR(255),
        linkedin VARCHAR(200),
        candidato_id INT,
        status ENUM('pendente', 'publicado', 'rejeitado') DEFAULT 'pendente',
        data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_moderacao TIMESTAMP NULL,
        moderado_por INT,
        INDEX idx_testimonials_status (status),
        INDEX idx_testimonials_data (data_envio)
    )";
    
    $db->exec($sql);
    echo "<p>✅ Tabela 'testimonials_candidatos' criada com sucesso!</p>";
    
    // Inserir dados de exemplo
    $testimonials = [
        [
            'nome' => 'Marina Ferreira',
            'cargo' => 'Desenvolvedora Front-end',
            'empresa' => 'Tech Solutions',
            'mensagem' => 'Consegui minha vaga dos sonhos em apenas 2 semanas! O processo foi super rápido e eficiente. Recomendo para todos.',
            'status' => 'publicado'
        ],
        [
            'nome' => 'Roberto Silva',
            'cargo' => 'Analista de Dados',
            'empresa' => 'DataCorp',
            'mensagem' => 'Plataforma incrível! A interface é muito intuitiva e o suporte é excepcional. Encontrei várias oportunidades alinhadas com meu perfil.',
            'status' => 'publicado'
        ],
        [
            'nome' => 'Ana Costa',
            'cargo' => 'Gerente de Projetos',
            'empresa' => 'Inovação Ltda',
            'mensagem' => 'O processo seletivo online foi uma experiência fantástica. Muito mais prático que os métodos tradicionais!',
            'status' => 'publicado'
        ]
    ];
    
    $stmt = $db->prepare("
        INSERT INTO testimonials_candidatos (nome, cargo, empresa, mensagem, status) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    foreach ($testimonials as $testimonial) {
        $stmt->execute([
            $testimonial['nome'],
            $testimonial['cargo'],
            $testimonial['empresa'],
            $testimonial['mensagem'],
            $testimonial['status']
        ]);
    }
    
    echo "<p>✅ " . count($testimonials) . " testimonials inseridos com sucesso!</p>";
    
    // Verificar se os dados foram inseridos
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM testimonials_candidatos WHERE status = 'publicado'");
    $stmt->execute();
    $count = $stmt->fetch();
    
    echo "<p>📊 Total de testimonials publicados: " . $count['total'] . "</p>";
    
    echo "<h2>Teste da API:</h2>";
    
    // Testar a função da API diretamente
    $stmt = $db->prepare("
        SELECT nome, cargo, empresa, mensagem, data_envio, linkedin
        FROM testimonials_candidatos 
        WHERE status = 'publicado' 
        ORDER BY data_envio DESC 
        LIMIT 6
    ");
    $stmt->execute();
    $testimonials_api = $stmt->fetchAll();
    
    echo "<p>🔌 API retorna " . count($testimonials_api) . " testimonials</p>";
    
    foreach ($testimonials_api as $t) {
        echo "<div style='border: 1px solid #007bff; padding: 10px; margin: 5px 0;'>";
        echo "<strong>" . htmlspecialchars($t['nome']) . "</strong> - " . htmlspecialchars($t['cargo']) . "<br>";
        echo htmlspecialchars($t['mensagem']) . "<br>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
?>
