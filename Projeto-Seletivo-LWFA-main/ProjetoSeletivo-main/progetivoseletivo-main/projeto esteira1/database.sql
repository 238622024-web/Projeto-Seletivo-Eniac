-- Banco de Dados ENIAC LINK+
-- Criação das tabelas do sistema de recrutamento

CREATE DATABASE IF NOT EXISTS eniac_link CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eniac_link;

-- Tabela de usuários/candidatos
CREATE TABLE candidatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    cpf VARCHAR(14) UNIQUE,
    data_nascimento DATE,
    endereco TEXT,
    cep VARCHAR(10),
    cidade VARCHAR(50),
    estado VARCHAR(2),
    escolaridade ENUM('fundamental', 'medio', 'superior', 'pos_graduacao', 'mestrado', 'doutorado'),
    curso VARCHAR(100),
    instituicao VARCHAR(100),
    experiencia TEXT,
    habilidades TEXT,
    linkedin VARCHAR(200),
    github VARCHAR(200),
    curriculo_arquivo VARCHAR(255),
    pdf_text LONGTEXT NULL,
    carta_apresentacao TEXT,
    pretensao_salarial DECIMAL(10,2),
    disponibilidade ENUM('imediata', '15_dias', '30_dias', '60_dias'),
    status ENUM('novo', 'em_analise', 'entrevista_agendada', 'aprovado', 'rejeitado') DEFAULT 'novo',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de vagas
CREATE TABLE vagas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    empresa VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    palavras_chave TEXT,
    requisitos TEXT,
    beneficios TEXT,
    salario_min DECIMAL(10,2),
    salario_max DECIMAL(10,2),
    tipo_contrato ENUM('clt', 'pj', 'estagio', 'freelancer', 'temporario'),
    modalidade ENUM('presencial', 'remoto', 'hibridinhas', 'híbrido') DEFAULT 'presencial',
    localizacao VARCHAR(100),
    nivel ENUM('junior', 'pleno', 'senior', 'gerencia', 'diretoria'),
    area VARCHAR(50),
    status ENUM('ativa', 'pausada', 'encerrada') DEFAULT 'ativa',
    data_publicacao DATE,
    data_encerramento DATE,
    vagas_disponiveis INT DEFAULT 1,
    visualizacoes INT DEFAULT 0,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de candidaturas (relaciona candidatos com vagas)
CREATE TABLE candidaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidato_id INT NOT NULL,
    vaga_id INT NOT NULL,
    status ENUM('enviada', 'visualizada', 'em_analise', 'entrevista_agendada', 'aprovada', 'rejeitada') DEFAULT 'enviada',
    data_candidatura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    observacoes TEXT,
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE CASCADE,
    FOREIGN KEY (vaga_id) REFERENCES vagas(id) ON DELETE CASCADE,
    UNIQUE KEY unique_candidatura (candidato_id, vaga_id)
);

-- Tabela de entrevistas
CREATE TABLE entrevistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidatura_id INT NOT NULL,
    data_entrevista DATETIME NOT NULL,
    tipo ENUM('presencial', 'video_call', 'telefone') DEFAULT 'video_call',
    link_reuniao VARCHAR(255),
    observacoes TEXT,
    status ENUM('agendada', 'realizada', 'cancelada', 'remarcada') DEFAULT 'agendada',
    avaliacao TEXT,
    nota DECIMAL(3,1),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (candidatura_id) REFERENCES candidaturas(id) ON DELETE CASCADE
);

-- Tabela de usuários administrativos
CREATE TABLE usuarios_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    nivel ENUM('admin', 'rh', 'viewer') DEFAULT 'rh',
    ativo BOOLEAN DEFAULT TRUE,
    ultimo_login TIMESTAMP NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de logs/atividades
CREATE TABLE atividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('candidatura', 'entrevista', 'aprovacao', 'rejeicao', 'vaga_criada', 'login_admin'),
    descricao TEXT NOT NULL,
    candidato_id INT NULL,
    vaga_id INT NULL,
    admin_id INT NULL,
    data_atividade TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE SET NULL,
    FOREIGN KEY (vaga_id) REFERENCES vagas(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES usuarios_admin(id) ON DELETE SET NULL
);

-- Tabela de contatos (Fale Conosco)
CREATE TABLE contatos (
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
);

-- Inserir usuário admin padrão
INSERT INTO usuarios_admin (nome, email, senha_hash, nivel) 
VALUES ('Administrador', 'admin@eniaclink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Inserir algumas vagas de exemplo
INSERT INTO vagas (titulo, empresa, descricao, requisitos, beneficios, salario_min, salario_max, tipo_contrato, modalidade, localizacao, nivel, area, data_publicacao, data_encerramento) VALUES
('Desenvolvedor Front-end React', 'TechCorp', 'Desenvolvedor especializado em React.js para projetos inovadores', 'React, JavaScript, HTML5, CSS3, Git', 'Vale alimentação, plano de saúde, home office', 4000.00, 7000.00, 'clt', 'híbrido', 'São Paulo, SP', 'pleno', 'Tecnologia', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY)),

('Analista de Marketing Digital', 'Marketing Pro', 'Profissional para gerenciar campanhas digitais e redes sociais', 'Google Ads, Facebook Ads, Analytics, SEO', 'Comissão por resultados, flexibilidade de horário', 3000.00, 5000.00, 'clt', 'remoto', 'Remote', 'pleno', 'Marketing', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 45 DAY)),

('Designer UX/UI', 'Creative Studio', 'Designer para criar interfaces incríveis e experiências únicas', 'Figma, Adobe XD, Sketch, prototipagem', 'Ambiente criativo, cursos pagos', 3500.00, 6000.00, 'clt', 'presencial', 'Rio de Janeiro, RJ', 'pleno', 'Design', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY)),

('Desenvolvedor Python/Django', 'DataTech', 'Desenvolvimento de aplicações web robustas com Python', 'Python, Django, PostgreSQL, Docker', 'Stock options, plano odontológico', 5000.00, 9000.00, 'clt', 'remoto', 'Remote', 'senior', 'Tecnologia', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY)),

('Analista de Dados', 'Analytics Corp', 'Análise de dados e criação de dashboards para tomada de decisão', 'SQL, Python, Power BI, Excel avançado', 'Curso de especialização pago', 4500.00, 7500.00, 'clt', 'híbrido', 'Belo Horizonte, MG', 'pleno', 'Dados', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY));

-- Índices para melhor performance
CREATE INDEX idx_candidatos_email ON candidatos(email);
CREATE INDEX idx_candidatos_status ON candidatos(status);
CREATE INDEX idx_vagas_status ON vagas(status);
CREATE INDEX idx_vagas_area ON vagas(area);
CREATE INDEX idx_candidaturas_status ON candidaturas(status);
CREATE INDEX idx_atividades_data ON atividades(data_atividade);
CREATE INDEX idx_contatos_status ON contatos(status);
CREATE INDEX idx_contatos_categoria ON contatos(categoria);
CREATE INDEX idx_contatos_data ON contatos(data_envio);

-- Tabela de avaliações/testimonials
CREATE TABLE avaliacoes (
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
    FOREIGN KEY (moderado_por) REFERENCES usuarios_admin(id),
    INDEX idx_avaliacoes_status (status),
    INDEX idx_avaliacoes_avaliacao (avaliacao),
    INDEX idx_avaliacoes_data (data_avaliacao)
);

-- Tabela de testimonials dos candidatos (para a seção "O que nossos candidatos dizem")
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
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE SET NULL,
    FOREIGN KEY (moderado_por) REFERENCES usuarios_admin(id),
    INDEX idx_testimonials_status (status),
    INDEX idx_testimonials_data (data_envio)
);
