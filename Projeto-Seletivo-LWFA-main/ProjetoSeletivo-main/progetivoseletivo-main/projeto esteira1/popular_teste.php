<?php
// Script para popular o banco de dados com exemplos para o dashboard
require_once 'config.php';

try {
    $database = new Database();
    $pdo = $database->connect();

    // Inserir vagas
    $pdo->exec("INSERT INTO vagas (titulo, empresa, descricao, status, data_criacao) VALUES
      ('Desenvolvedor PHP', 'Empresa Teste', 'Vaga para desenvolvedor PHP', 'ativa', NOW()),
      ('Designer', 'Empresa Teste', 'Vaga para designer', 'pausada', NOW()),
      ('Analista', 'Empresa Teste', 'Vaga para analista', 'encerrada', NOW())");

    // Inserir candidatos
    $pdo->exec("INSERT INTO candidatos (nome, email, curriculo_arquivo, data_cadastro) VALUES
      ('João Silva', 'joao@email.com', 'joao.pdf', NOW()),
      ('Maria Souza', 'maria@email.com', 'maria.pdf', NOW())");

    // Inserir candidaturas
    $pdo->exec("INSERT INTO candidaturas (candidato_id, vaga_id, status, data_candidatura) VALUES
      (1, 1, 'enviada', NOW()),
      (2, 1, 'enviada', NOW()),
      (2, 2, 'enviada', NOW())");

    echo 'Dados de teste inseridos com sucesso!';
} catch (PDOException $e) {
    echo 'Erro ao inserir dados: ' . $e->getMessage();
}
