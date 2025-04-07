<?php
session_start();
if (!isset($_SESSION['loggedIn']) || $_SESSION['userRole'] !== 'ADM') {
    header('Location: index.php');
    exit;
}

require_once '../assets/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $cargo = $_POST['cargo'] ?? '';
    $senha = '';
    for ($i = 0; $i < 23; $i++) {
        $senha .= random_int(0, 9); // Gera um dígito aleatório de 0 a 9
    }

    if ($nome && $cargo) {
        try {
            $hashedPassword = password_hash($senha, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO USERS (nome, cargo, senha) VALUES (:nome, :cargo, :senha)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cargo', $cargo);
            $stmt->bindParam(':senha', $hashedPassword);
            $stmt->execute();

            $success = "Usuário adicionado com sucesso! Senha: $senha";
        } catch (PDOException $e) {
            $error = "Erro ao adicionar usuário: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador - Adicionar Usuário</title>
    <link rel="stylesheet" href="../assets/css/adm.css">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Adicionar Usuário</h1>
        <?php if (!empty($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required><br><br>

            <label for="cargo">Cargo:</label>
            <select id="cargo" name="cargo" required>
                <option value="" disabled selected>SELECIONE UM CARGO</option>
                <option value="GESTOR">GESTOR</option>
                <option value="CAIXA">CAIXA</option>
            </select><br><br>

            <button type="submit">Adicionar</button>
        </form>
        <?php if (!empty($success)): ?>
            <div class="badge-container" id="badge">
                <div class="badge-header">Crachá de Identificação</div>
                <div class="badge-content">
                    <p><strong>Nome:</strong> <?= htmlspecialchars($nome) ?></p>
                    <p><strong>Cargo:</strong> <?= htmlspecialchars($cargo) ?></p>
                </div>
                <div class="badge-barcode">
                    <svg id="barcode"></svg>
                </div>
            </div>
            <button onclick="printBadge()">Imprimir Crachá</button>
            <script>
                JsBarcode("#barcode", "<?= htmlspecialchars($senha ?? '') ?>", {
                    format: "CODE128",
                    displayValue: true,
                    fontSize: 14,
                    height: 50
                });

                function printBadge() {
                    const badge = document.getElementById('badge').outerHTML;
                    const currentDateTime = new Date().toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
                    const admUser = "<?= htmlspecialchars($_SESSION['username'] ?? 'ADM') ?>"; // Assuming 'username' is stored in the session

                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                        <html>
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <link rel="stylesheet" href="../assets/css/adm.css">
                            <style>
                                .print-footer {
                                    margin-top: 20px;
                                    text-align: center;
                                    font-size: 12px;
                                    font-family: Arial, sans-serif;
                                }
                            </style>
                        </head>
                        <body>
                            <div class="print-container">
                                ${badge}
                                <div class="print-footer">
                                    <p>Data e Hora da Impressão: ${currentDateTime}</p>
                                    <p>Impresso por: ${admUser}</p>
                                </div>
                            </div>
                        </body>
                        </html>
                    `);
                    printWindow.document.close();
                    printWindow.focus();
                    printWindow.print();
                    printWindow.close();
                }
            </script>
        <?php endif; ?>
    </div>

    <div id="modal_logout" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Logout</h2>
            <p>Deseja realmente sair?</p>
            <button id="confirmar_logout">Sim</button>
            <button id="cancelar_logout">Não</button>
        </div>
    </div>

    <script>
        const modalLogout = document.getElementById('modal_logout');
        const confirmarLogout = document.getElementById('confirmar_logout');
        const cancelarLogout = document.getElementById('cancelar_logout');

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                modalLogout.style.display = 'block';
            }
        });

        confirmarLogout.addEventListener('click', function () {
            window.location.href = 'index.php';
        });

        cancelarLogout.addEventListener('click', function () {
            modalLogout.style.display = 'none';
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
