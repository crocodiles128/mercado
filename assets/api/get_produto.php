<?php
require_once '../config/db.php';

if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];

    try {
        $stmt = $conn->prepare("SELECT nome, valor FROM PRODUTOS WHERE codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo);
        $stmt->execute();
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            echo json_encode($produto);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Produto não encontrado.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao buscar produto.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Código de barras não fornecido.']);
}
?>
