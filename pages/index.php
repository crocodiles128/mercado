<?php
require_once '../assets/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        try {
            $stmt = $conn->prepare("SELECT cargo, senha FROM USERS WHERE nome = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['senha'])) {
                session_start();
                $_SESSION['loggedIn'] = true;
                $_SESSION['userRole'] = $user['cargo'];

                if ($user['cargo'] === 'CAIXA') {
                    header('Location: caixa.php');
                } elseif ($user['cargo'] === 'GESTOR') {
                    header('Location: gestor.php');
                } elseif ($user['cargo'] === 'ADM') {
                    header('Location: adm.php');
                }
                exit;
            } else {
                $error = 'Login inválido. Verifique suas credenciais.';
            }
        } catch (PDOException $e) {
            $error = 'Erro ao validar login. Tente novamente mais tarde.';
        }
    } else {
        $error = 'Por favor, preencha todos os campos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Acessar caixa</h2>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Nome de usuário" required>
            <input type="password" name="password" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>

    <div id="modal_encerrar" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Encerrar Sistema</h2>
            <p>Deseja realmente encerrar o sistema?</p>
            <button id="confirmar_encerrar">Sim</button>
            <button id="cancelar_encerrar">Não</button>
        </div>
    </div>

    <script>
        const modalEncerrar = document.getElementById('modal_encerrar');
        const confirmarEncerrar = document.getElementById('confirmar_encerrar');
        const cancelarEncerrar = document.getElementById('cancelar_encerrar');

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                modalEncerrar.style.display = 'block';
            }
        });

        confirmarEncerrar.addEventListener('click', function () {
            window.close();
        });

        cancelarEncerrar.addEventListener('click', function () {
            modalEncerrar.style.display = 'none';
        });
    </script>

    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-content button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .modal-content button:first-child {
            background-color: #dc3545;
            color: white;
        }

        .modal-content button:last-child {
            background-color: #6c757d;
            color: white;
        }
    </style>
</body>
</html>
