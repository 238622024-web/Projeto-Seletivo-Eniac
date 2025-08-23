<?php
require_once 'config.php';

try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Primeiro, vamos criar uma vaga simples com data de encerramento
    $data_encerramento = date('Y-m-d', strtotime('+15 days')); // 15 dias no futuro
    
    echo "<h2>Teste Direto de Inserção</h2>";
    echo "<p><strong>Data de encerramento a ser inserida:</strong> {$data_encerramento}</p>";
    
    $stmt = $pdo->prepare("
        INSERT INTO vagas (titulo, empresa, localizacao, modalidade, vagas_disponiveis, data_encerramento, status, descricao, data_publicacao, data_criacao) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), NOW())
    ");
    
    $resultado = $stmt->execute([
        'Desenvolvedor PHP - Teste Manual',
        'Empresa Teste LTDA',
        'São Paulo - SP',
        'remoto',
        1,
        $data_encerramento,
        'ativa',
        'Esta é uma vaga de teste criada diretamente para verificar se a data de encerramento funciona.'
    ]);
    
    if ($resultado) {
        $novo_id = $pdo->lastInsertId();
        echo "<p style='color: green;'>✓ Vaga criada com sucesso! ID: {$novo_id}</p>";
        
        // Verificar se foi salva corretamente
        $stmt = $pdo->prepare("SELECT id, titulo, data_encerramento, data_publicacao FROM vagas WHERE id = ?");
        $stmt->execute([$novo_id]);
        $vaga_salva = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h3>Dados Salvos:</h3>";
        echo "<p><strong>ID:</strong> {$vaga_salva['id']}</p>";
        echo "<p><strong>Título:</strong> {$vaga_salva['titulo']}</p>";
        echo "<p><strong>Data de Publicação:</strong> {$vaga_salva['data_publicacao']}</p>";
        echo "<p><strong>Data de Encerramento:</strong> " . ($vaga_salva['data_encerramento'] ?: 'NULL') . "</p>";
        
        if ($vaga_salva['data_encerramento']) {
            echo "<p style='color: green;'>✓ Data de encerramento foi salva corretamente!</p>";
            echo "<p><strong>Formatada:</strong> " . date('d/m/Y', strtotime($vaga_salva['data_encerramento'])) . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Data de encerramento não foi salva!</p>";
        }
        
        echo "<hr>";
        echo "<h3>Teste de Exibição como no vagas.php:</h3>";
        if (!empty($vaga_salva['data_encerramento'])) {
            echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0;'>";
            echo "<h4><i class='fas fa-clock'></i> Prazo</h4>";
            echo "<p>Até " . date('d/m/Y', strtotime($vaga_salva['data_encerramento'])) . "</p>";
            echo "</div>";
        } else {
            echo "<p>Seção de prazo não seria exibida (data vazia)</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Erro ao criar vaga</p>";
    }
    
    echo "<hr>";
    echo "<h3>Últimas 5 vagas no banco:</h3>";
    $stmt = $pdo->query("SELECT id, titulo, data_encerramento FROM vagas ORDER BY id DESC LIMIT 5");
    $vagas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($vagas)) {
        echo "<p>Nenhuma vaga encontrada.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Título</th><th>Data Encerramento</th><th>Formatada</th></tr>";
        
        foreach ($vagas as $vaga) {
            echo "<tr>";
            echo "<td>{$vaga['id']}</td>";
            echo "<td>{$vaga['titulo']}</td>";
            echo "<td>" . ($vaga['data_encerramento'] ?: 'NULL') . "</td>";
            echo "<td>" . ($vaga['data_encerramento'] ? date('d/m/Y', strtotime($vaga['data_encerramento'])) : 'N/A') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { margin-top: 10px; }
    th, td { padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>
