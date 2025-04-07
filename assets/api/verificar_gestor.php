<?php
require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['senha'])) {
    $senha = $data['senha'];

    try {
        // Busca usuários com o cargo de GESTOR ou ADM
        $stmt = $conn->prepare("SELECT senha FROM USERS WHERE cargo IN ('GESTOR', 'ADM')");
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verifica se alguma senha corresponde
        foreach ($usuarios as $usuario) {
            if (password_verify($senha, $usuario['senha'])) {
                echo json_encode(true);
                exit;
            }
        }

        // Se nenhuma senha corresponder
        http_response_code(401);
        echo json_encode(false);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao verificar senha.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Senha não fornecida.']);
}
?>
