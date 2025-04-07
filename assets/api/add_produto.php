<?php
require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['nome'], $data['codigo'], $data['valor'])) {
    $nome = $data['nome'];
    $codigo = $data['codigo'];
    $valor = $data['valor'];

    try {
        $stmt = $conn->prepare("INSERT INTO PRODUTOS (nome, codigo, valor) VALUES (:nome, :codigo, :valor)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':valor', $valor);
        $stmt->execute();
        http_response_code(200);
        echo json_encode(['message' => 'Produto adicionado com sucesso.']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao adicionar produto.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos.']);
}
?>
