<?php
require_once '../assets/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $cargo = $_POST['cargo'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if ($nome && $cargo && $senha) {
        try {
            $hashedPassword = password_hash($senha, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO USERS (nome, cargo, senha) VALUES (:nome, :cargo, :senha)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cargo', $cargo);
            $stmt->bindParam(':senha', $hashedPassword);
            $stmt->execute();

            echo "Usuário adicionado com sucesso!";
        } catch (PDOException $e) {
            echo "Erro ao adicionar usuário: " . $e->getMessage();
        }
    } else {
        echo "Por favor, preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Usuário</title>
</head>
<body>
    <h1>Adicionar Usuário</h1>
    <form method="POST">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="cargo">Cargo:</label>
        <input type="text" id="cargo" name="cargo" required><br><br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required><br><br>

        <button type="submit">Adicionar</button>
    </form>
</body>
</html>
