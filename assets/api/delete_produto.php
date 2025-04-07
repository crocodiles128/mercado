<?php
require_once '../config/db.php';

// Adiciona log para verificar o conteúdo recebido
error_log("Dados recebidos no script: " . file_get_contents('php://input'));

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['codigo']) && !empty($data['codigo'])) { // Verifica se o código de barras foi fornecido e não está vazio
    $codigo = $data['codigo']; // Captura o código de barras

    try {
        // Adiciona mensagem de depuração para verificar o código recebido
        error_log("Código recebido para deletar: " . $codigo);

        $stmt = $conn->prepare("DELETE FROM PRODUTOS WHERE codigo = :codigo"); // Usa o código de barras para deletar
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Produto deletado com sucesso.']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Produto não encontrado.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Erro ao deletar produto.',
            'details' => $e->getMessage() // Inclui detalhes do erro para depuração
        ]);
        // Adiciona mensagem de erro no log
        error_log("Erro ao deletar produto: " . $e->getMessage());
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Código de barras do produto não fornecido ou inválido.']);
    // Adiciona mensagem de erro no log
    error_log("Código de barras não fornecido ou inválido.");
}
?>
