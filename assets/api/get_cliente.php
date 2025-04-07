<?php
require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['cpf'])) {
    $cpf = $data['cpf'];

    try {
        $stmt = $conn->prepare("SELECT nome FROM clientes WHERE CPF = :cpf");
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            echo json_encode($cliente);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Cliente não encontrado.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao buscar cliente.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'CPF não fornecido.']);
}
?>
