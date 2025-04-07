<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Produtos</title>
    <link rel="stylesheet" href="../assets/css/gestor.css">
</head>
<body>
    <div class="container">
        <h1>Gestor de Produtos</h1>
        <div class="form-container">
            <h2>Adicionar Produto</h2>
            <form id="form_produto">
                <label for="nome_produto">Nome do Produto:</label>
                <input type="text" id="nome_produto" required>
                
                <label for="codigo_barras">Código de Barras:</label>
                <input type="text" id="codigo_barras" required>
                
                <label for="preco">Preço:</label>
                <input type="number" id="preco" step="0.01" required>
                
                <button type="submit">Adicionar</button>
            </form>
        </div>
        <div class="table-container">
            <h2>Lista de Produtos</h2>
            <input type="text" id="pesquisa_produto" placeholder="Pesquisar por nome ou código de barras...">
            <table id="tabela_produtos">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Código de Barras</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Produtos serão adicionados dinamicamente aqui -->
                </tbody>
            </table>
        </div>
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
        const formProduto = document.getElementById('form_produto');
        const tabelaProdutos = document.getElementById('tabela_produtos').querySelector('tbody');
        const pesquisaProduto = document.getElementById('pesquisa_produto');

        // Função para carregar produtos do banco de dados
        async function carregarProdutos() {
            const response = await fetch('../assets/api/get_produtos.php');
            const produtos = await response.json();
            produtos.forEach(produto => {
                const novaLinha = document.createElement('tr');
                novaLinha.innerHTML = `
                    <td><input type="text" value="${produto.nome}" class="edit-nome"></td>
                    <td><input type="text" value="${produto.codigo}" class="edit-codigo"></td>
                    <td><input type="number" step="0.01" value="${parseFloat(produto.valor).toFixed(2)}" class="edit-preco"></td>
                    <td>
                        <button class="salvar" data-id="${produto.id}">Salvar</button>
                        <button class="deletar" data-id="${produto.id}">Deletar</button>
                    </td>
                `;
                tabelaProdutos.appendChild(novaLinha);
            });
            adicionarEventosBotoes();
        }

        // Função para adicionar eventos aos botões de salvar e deletar
        function adicionarEventosBotoes() {
            document.querySelectorAll('.salvar').forEach(button => {
                button.addEventListener('click', async function () {
                    const id = this.dataset.id;
                    const linha = this.closest('tr');
                    const nome = linha.querySelector('.edit-nome').value;
                    const codigo = linha.querySelector('.edit-codigo').value;
                    const preco = linha.querySelector('.edit-preco').value;

                    const response = await fetch('../assets/api/update_produto.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id, nome, codigo, valor: preco })
                    });

                    if (response.ok) {
                        alert('Produto atualizado com sucesso!');
                    } else {
                        alert('Erro ao atualizar produto.');
                    }
                });
            });

            document.querySelectorAll('.deletar').forEach(button => {
                button.addEventListener('click', async function () {
                    const codigo = this.closest('tr').querySelector('.edit-codigo').value; // Captura o código de barras

                    const response = await fetch('../assets/api/delete_produto.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ codigo }) // Envia o campo correto
                    });

                    if (response.ok) {
                        this.closest('tr').remove();
                        alert('Produto deletado com sucesso!');
                    } else {
                        const error = await response.json();
                        console.error('Erro ao deletar produto:', error); // Log detalhado do erro
                        alert('Erro ao deletar produto.');
                    }
                });
            });
        }

        // Evento de submissão do formulário
        formProduto.addEventListener('submit', async function(event) {
            event.preventDefault();

            // Captura os valores dos campos
            const nome = document.getElementById('nome_produto').value;
            const codigo = document.getElementById('codigo_barras').value;
            const preco = document.getElementById('preco').value;

            // Envia o produto para o banco de dados
            const response = await fetch('../assets/api/add_produto.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nome, codigo, valor: preco })
            });

            if (response.ok) {
                const novaLinha = document.createElement('tr');
                novaLinha.innerHTML = `
                    <td>${nome}</td>
                    <td>${codigo}</td>
                    <td>R$ ${parseFloat(preco).toFixed(2)}</td>
                `;
                tabelaProdutos.appendChild(novaLinha);
                formProduto.reset();
            } else {
                alert('Erro ao adicionar produto.');
            }
        });

        // Evento de pesquisa em tempo real
        pesquisaProduto.addEventListener('input', function() {
            const termo = this.value.toLowerCase();
            const linhas = tabelaProdutos.querySelectorAll('tr');
            linhas.forEach(linha => {
                const nome = linha.querySelector('.edit-nome')?.value.toLowerCase() || '';
                const codigo = linha.querySelector('.edit-codigo')?.value.toLowerCase() || '';
                if (nome.includes(termo) || codigo.includes(termo)) {
                    linha.style.display = '';
                } else {
                    linha.style.display = 'none';
                }
            });
        });

        // Carrega os produtos ao carregar a página
        carregarProdutos();

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
