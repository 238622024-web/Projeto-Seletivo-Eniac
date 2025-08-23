<?php
require_once 'config.php';

echo "<h2>Debug - Teste de Criação de Vaga com Data de Encerramento</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Dados Recebidos do POST:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    try {
        $database = new Database();
        $pdo = $database->connect();
        
        $titulo = trim($_POST['titulo'] ?? '');
        $empresa = trim($_POST['empresa'] ?? '');
        $localizacao = trim($_POST['localizacao'] ?? '');
        $modalidade = trim($_POST['modalidade'] ?? '');
        $vagas_disponiveis = (int)($_POST['vagas_disponiveis'] ?? 1);
        $data_encerramento = trim($_POST['data_encerramento'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        
        echo "<h3>Dados Processados:</h3>";
        echo "<p><strong>Data de encerramento:</strong> '" . $data_encerramento . "'</p>";
        echo "<p><strong>Data vazia?</strong> " . (empty($data_encerramento) ? 'SIM' : 'NÃO') . "</p>";
        echo "<p><strong>Data válida?</strong> " . (strtotime($data_encerramento) ? 'SIM' : 'NÃO') . "</p>";
        
        if (!empty($data_encerramento)) {
            $data_hoje = date('Y-m-d');
            echo "<p><strong>Data hoje:</strong> " . $data_hoje . "</p>";
            echo "<p><strong>Data futura?</strong> " . ($data_encerramento > $data_hoje ? 'SIM' : 'NÃO') . "</p>";
        }
        
        // Inserir vaga de teste
        $stmt = $pdo->prepare("
            INSERT INTO vagas (titulo, empresa, localizacao, modalidade, vagas_disponiveis, data_encerramento, status, descricao, data_publicacao, data_criacao) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), NOW())
        ");
        
        $resultado = $stmt->execute([
            $titulo ?: 'Vaga Teste Debug',
            $empresa ?: 'Empresa Teste',
            $localizacao ?: 'São Paulo - SP',
            $modalidade ?: 'remoto',
            $vagas_disponiveis,
            $data_encerramento ?: null,
            'ativa',
            $descricao ?: 'Descrição da vaga teste'
        ]);
        
        if ($resultado) {
            $novo_id = $pdo->lastInsertId();
            echo "<p style='color: green;'>✓ Vaga criada com sucesso! ID: {$novo_id}</p>";
            
            // Verificar se foi salva corretamente
            $stmt = $pdo->prepare("SELECT * FROM vagas WHERE id = ?");
            $stmt->execute([$novo_id]);
            $vaga_salva = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<h3>Vaga Salva no Banco:</h3>";
            echo "<pre>";
            print_r($vaga_salva);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>✗ Erro ao criar vaga</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Debug - Teste Data Encerramento</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea, select { width: 100%; max-width: 400px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
    </style>
</head>
<body>
    <form method="POST">
        <div class="form-group">
            <label for="titulo">Título da Vaga:</label>
            <input type="text" id="titulo" name="titulo" value="Desenvolvedor PHP - TESTE">
        </div>
        
        <div class="form-group">
            <label for="empresa">Empresa:</label>
            <input type="text" id="empresa" name="empresa" value="Empresa Teste LTDA">
        </div>
        
        <div class="form-group">
            <label for="localizacao">Localização:</label>
            <input type="text" id="localizacao" name="localizacao" value="São Paulo - SP">
        </div>
        
        <div class="form-group">
            <label for="modalidade">Modalidade:</label>
            <select id="modalidade" name="modalidade">
                <option value="presencial">Presencial</option>
                <option value="remoto" selected>Remoto</option>
                <option value="híbrido">Híbrido</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="vagas_disponiveis">Vagas Disponíveis:</label>
            <input type="number" id="vagas_disponiveis" name="vagas_disponiveis" value="1" min="1">
        </div>
        
        <div class="form-group">
            <label for="data_encerramento">Data de Encerramento:</label>
            <input type="date" id="data_encerramento" name="data_encerramento" 
                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                   value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
        </div>
        
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao">Esta é uma vaga de teste para verificar se a data de encerramento está sendo salva corretamente no banco de dados.</textarea>
        </div>
        
        <button type="submit">Criar Vaga de Teste</button>
    </form>
</body>
</html>
