<?php
// Configurações de conexão com o banco de dados
class Database {
    private $host = 'localhost';
    private $dbname = 'eniac_link';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    public function connect() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            return new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}

// Funções auxiliares
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validateCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) != 11) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    // Validação do primeiro dígito verificador
    for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--) {
        $soma += $cpf[$i] * $j;
    }
    
    $resto = $soma % 11;
    if ($cpf[9] != ($resto < 2 ? 0 : 11 - $resto)) {
        return false;
    }
    
    // Validação do segundo dígito verificador
    for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--) {
        $soma += $cpf[$i] * $j;
    }
    
    $resto = $soma % 11;
    return $cpf[10] == ($resto < 2 ? 0 : 11 - $resto);
}

function formatCurrency($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

function uploadFile($file, $allowedTypes = ['pdf', 'doc', 'docx'], $maxSize = 5242880) {
    $uploadDir = 'uploads/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileName = $file['name'];
    $fileTemp = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    
    if ($fileError !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Erro no upload do arquivo.'];
    }
    
    if ($fileSize > $maxSize) {
        return ['success' => false, 'message' => 'Arquivo muito grande. Máximo 5MB.'];
    }
    
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExt, $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipo de arquivo não permitido.'];
    }
    
    $newFileName = uniqid() . '_' . time() . '.' . $fileExt;
    $destination = $uploadDir . $newFileName;
    
    if (move_uploaded_file($fileTemp, $destination)) {
        return ['success' => true, 'filename' => $newFileName, 'path' => $destination];
    } else {
        return ['success' => false, 'message' => 'Erro ao salvar arquivo.'];
    }
}

function sendEmail($to, $subject, $message, $from = 'noreply@eniaclink.com') {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: ENIAC LINK+ <{$from}>" . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

function logActivity($db, $tipo, $descricao, $candidato_id = null, $vaga_id = null, $admin_id = null) {
    try {
        $sql = "INSERT INTO atividades (tipo, descricao, candidato_id, vaga_id, admin_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$tipo, $descricao, $candidato_id, $vaga_id, $admin_id]);
    } catch (Exception $e) {
        error_log("Erro ao registrar atividade: " . $e->getMessage());
    }
}

function generateApiResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    return json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('c')
    ]);
}

// Classe para gerenciar sessões
class SessionManager {
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        self::start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    public static function remove($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    public static function destroy() {
        self::start();
        session_destroy();
    }
}
?>
