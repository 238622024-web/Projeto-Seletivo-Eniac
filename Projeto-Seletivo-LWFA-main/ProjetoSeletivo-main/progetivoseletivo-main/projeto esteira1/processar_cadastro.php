<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = (new Database())->connect();
        
        // Validar dados obrigatórios
        $nome = sanitizeInput($_POST['nome'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $telefone = sanitizeInput($_POST['telefone'] ?? '');
        $cpf = sanitizeInput($_POST['cpf'] ?? '');
        
        if (empty($nome) || empty($email) || empty($telefone)) {
            throw new Exception('Nome, email e telefone são obrigatórios.');
        }
        
        if (!validateEmail($email)) {
            throw new Exception('Email inválido.');
        }
        
        if (!empty($cpf) && !validateCPF($cpf)) {
            throw new Exception('CPF inválido.');
        }
        
        // Verificar se email já existe
        $stmt = $db->prepare("SELECT id FROM candidatos WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception('Este email já está cadastrado.');
        }
        
        // Verificar se CPF já existe (se informado)
        if (!empty($cpf)) {
            $stmt = $db->prepare("SELECT id FROM candidatos WHERE cpf = ?");
            $stmt->execute([$cpf]);
            if ($stmt->fetch()) {
                throw new Exception('Este CPF já está cadastrado.');
            }
        }
        
        // Upload do currículo
        $curriculo_arquivo = null;
        if (isset($_FILES['curriculo']) && $_FILES['curriculo']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['curriculo']);
            if ($uploadResult['success']) {
                $curriculo_arquivo = $uploadResult['filename'];
            } else {
                throw new Exception($uploadResult['message']);
            }
        }
        
        // Preparar dados para inserção
        $dados = [
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
            'cpf' => $cpf ?: null,
            'data_nascimento' => $_POST['data_nascimento'] ?: null,
            'endereco' => sanitizeInput($_POST['endereco'] ?? ''),
            'cep' => sanitizeInput($_POST['cep'] ?? ''),
            'cidade' => sanitizeInput($_POST['cidade'] ?? ''),
            'estado' => sanitizeInput($_POST['estado'] ?? ''),
            'escolaridade' => $_POST['escolaridade'] ?? null,
            'curso' => sanitizeInput($_POST['curso'] ?? ''),
            'instituicao' => sanitizeInput($_POST['instituicao'] ?? ''),
            'experiencia' => sanitizeInput($_POST['experiencia'] ?? ''),
            'habilidades' => sanitizeInput($_POST['habilidades'] ?? ''),
            'linkedin' => sanitizeInput($_POST['linkedin'] ?? ''),
            'github' => sanitizeInput($_POST['github'] ?? ''),
            'curriculo_arquivo' => $curriculo_arquivo,
            'carta_apresentacao' => sanitizeInput($_POST['carta_apresentacao'] ?? ''),
            'pretensao_salarial' => $_POST['pretensao_salarial'] ? floatval($_POST['pretensao_salarial']) : null,
            'disponibilidade' => $_POST['disponibilidade'] ?? 'imediata'
        ];
        
        // Inserir candidato
        $sql = "INSERT INTO candidatos (
            nome, email, telefone, cpf, data_nascimento, endereco, cep, cidade, estado,
            escolaridade, curso, instituicao, experiencia, habilidades, linkedin, github,
            curriculo_arquivo, carta_apresentacao, pretensao_salarial, disponibilidade
        ) VALUES (
            :nome, :email, :telefone, :cpf, :data_nascimento, :endereco, :cep, :cidade, :estado,
            :escolaridade, :curso, :instituicao, :experiencia, :habilidades, :linkedin, :github,
            :curriculo_arquivo, :carta_apresentacao, :pretensao_salarial, :disponibilidade
        )";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($dados);
        
        $candidato_id = $db->lastInsertId();

        // Extrair texto do PDF e salvar em pdf_text (IA)
        if ($curriculo_arquivo) {
            $pdfPath = __DIR__ . '/uploads/' . $curriculo_arquivo;
            if (file_exists($pdfPath)) {
                if (file_exists(__DIR__ . '/vendor/autoload.php')) {
                    require_once __DIR__ . '/vendor/autoload.php';
                    if (class_exists('Smalot\\PdfParser\\Parser')) {
                        try {
                            $parser = new Smalot\PdfParser\Parser();
                            $pdf = $parser->parseFile($pdfPath);
                            $pdfText = $pdf->getText();
                            $update = $db->prepare("UPDATE candidatos SET pdf_text = ? WHERE id = ?");
                            $update->execute([$pdfText, $candidato_id]);
                        } catch (Exception $e) {
                            // Se der erro, ignora
                        }
                    }
                }
            }
        }
        
        // Registrar atividade
        logActivity($db, 'candidatura', "Novo candidato cadastrado: {$nome}", $candidato_id);
        
        // Se uma vaga específica foi selecionada, criar candidatura
        if (!empty($_POST['vaga_id'])) {
            $vaga_id = intval($_POST['vaga_id']);
            
            $stmt = $db->prepare("INSERT INTO candidaturas (candidato_id, vaga_id) VALUES (?, ?)");
            $stmt->execute([$candidato_id, $vaga_id]);
            
            logActivity($db, 'candidatura', "Candidatura enviada para vaga ID: {$vaga_id}", $candidato_id, $vaga_id);
        }
        
        // Enviar email de confirmação
        $emailSubject = "Cadastro realizado com sucesso - ENIAC LINK+";
        $emailMessage = "
        <h2>Olá, {$nome}!</h2>
        <p>Seu cadastro foi realizado com sucesso na ENIAC LINK+.</p>
        <p>Em breve entraremos em contato caso encontremos oportunidades que combinem com seu perfil.</p>
        <p>Agradecemos seu interesse!</p>
        <br>
        <p><strong>Equipe ENIAC LINK+</strong></p>
        ";
        
        sendEmail($email, $emailSubject, $emailMessage);
        
        // Retornar sucesso
        if (isset($_POST['ajax'])) {
            echo generateApiResponse(true, 'Cadastro realizado com sucesso!', ['candidato_id' => $candidato_id]);
        } else {
            header('Location: cadastro.php?success=1');
        }
        
    } catch (Exception $e) {
        if (isset($_POST['ajax'])) {
            echo generateApiResponse(false, $e->getMessage());
        } else {
            header('Location: cadastro.php?error=' . urlencode($e->getMessage()));
        }
    }
} else {
    header('Location: cadastro.php');
}
?>
