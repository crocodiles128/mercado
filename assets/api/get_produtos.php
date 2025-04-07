<?php
require_once '../config/db.php';

try {
    $stmt = $conn->query("SELECT nome, codigo, valor FROM PRODUTOS");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($produtos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar produtos.']);
}
?>
