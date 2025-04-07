<?php
require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['nome'], $data['codigo'], $data['valor'])) { // Removido o ID e ajustado para usar o código de barras
    $nome = $data['nome'];
    $codigo = $data['codigo'];
    $valor = floatval($data['valor']); // Certifique-se de que o valor é um número decimal

    try {
        $stmt = $conn->prepare("UPDATE PRODUTOS SET nome = :nome, valor = :valor WHERE codigo = :codigo"); // Alterado para usar o código de barras
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':valor', $valor);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Produto atualizado com sucesso.']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Produto não encontrado ou dados não alterados.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao atualizar produto.', 'details' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos. Certifique-se de que todos os campos estão preenchidos.']);
}
?>
