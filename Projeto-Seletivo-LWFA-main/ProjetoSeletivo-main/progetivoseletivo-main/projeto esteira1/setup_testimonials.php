<?php
require_once 'config.php';

try {
    $db = (new Database())->connect();
    
    // Criar tabela de testimonials dos candidatos
    $sql = "
    CREATE TABLE IF NOT EXISTS testimonials_candidatos (
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
        FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE SET NULL,
        FOREIGN KEY (moderado_por) REFERENCES usuarios_admin(id),
        INDEX idx_testimonials_status (status),
        INDEX idx_testimonials_data (data_envio)
    )";
    
    $db->exec($sql);
    echo "✅ Tabela 'testimonials_candidatos' criada com sucesso!\n";
    
    // Inserir alguns testimonials de exemplo (que já existiam na página)
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
    
    echo "✅ Testimonials de exemplo inseridos com sucesso!\n";
    echo "A seção 'O que nossos candidatos dizem' agora é dinâmica e pode receber novos testimonials.\n";
    
} catch (Exception $e) {
    echo "❌ Erro ao configurar testimonials: " . $e->getMessage() . "\n";
}
?>
