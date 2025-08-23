<?php
// Teste de leitura de PDF com smalot/pdfparser
require_once __DIR__ . '/vendor/autoload.php';

use Smalot\PdfParser\Parser;

$arquivo = isset($_GET['arquivo']) ? $_GET['arquivo'] : null;
if (!$arquivo || !file_exists($arquivo)) {
    echo 'Arquivo não encontrado.';
    exit;
}

$parser = new Parser();
$pdf = $parser->parseFile($arquivo);
$text = $pdf->getText();

header('Content-Type: text/plain; charset=utf-8');
echo $text;
