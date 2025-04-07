<?php
// Início do arquivo PHP, caso seja necessário adicionar lógica no futuro.
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Caixa - Leitura de Código de Barras</title>
    <link rel="stylesheet" href="../assets/css/caixa.css">
</head>
<body>
    <div class="container">
        <h1>Nome do Mercadinho!!</h1>
        <div id="cliente_identificado" class="cliente-label" style="display: none;">Cliente: <span id="nome_cliente"></span></div> <!-- Label para o cliente -->
        <ul id="lista_produtos"></ul> <!-- Lista de produtos movida para cima -->
        <div id="total">Total: R$ 0.00</div> <!-- Adicionado elemento para exibir o total -->
        <div class="content">
            <p>Escaneie o código de barras do produto. O código será lido automaticamente.</p>

            <div id="codigo_produto">Código do Produto: <input id="codigo" type="text" readonly></div>
            <div id="resultado"></div>
        </div>
    </div>

    <div id="modal_remover" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Remover Produto</h2>
            <p>Digite o código de barras do produto e a senha de um GESTOR para confirmar a remoção:</p>
            <input type="text" id="codigo_remover" placeholder="Código de Barras">
            <input type="password" id="senha_gestor" placeholder="Senha do GESTOR">
            <button id="confirmar_remocao">Confirmar</button>
            <button id="cancelar_remocao">Cancelar</button>
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

    <div id="modal_cliente" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Identificar Cliente</h2>
            <p>Digite o CPF do cliente:</p>
            <input type="text" id="cpf_cliente" placeholder="CPF">
            <button id="confirmar_cliente">Confirmar</button>
        </div>
    </div>

    <script>
        let codigoProduto = '';
        let resultadoElement = document.getElementById('resultado');
        let codigoElement = document.getElementById('codigo');
        let listaProdutos = document.getElementById('lista_produtos');
        let totalElement = document.getElementById('total'); // Referência ao elemento do total
        let total = 0; // Variável para armazenar o total
        let modalAberto = false; // Variável para rastrear se um modal está aberto

        // Função para buscar produto pelo código de barras
        async function buscarProduto(codigo) {
            try {
                const response = await fetch(`../assets/api/get_produto.php?codigo=${codigo}`);
                if (response.ok) {
                    return await response.json();
                } else {
                    return null;
                }
            } catch (error) {
                console.error('Erro ao buscar produto:', error);
                return null;
            }
        }

        // Evento para abrir o modal ao pressionar "C"
        document.addEventListener('keydown', function (event) {
            if ((event.key === 'C' || event.key === 'c') && !modalAberto) {
                event.preventDefault(); // Evita o comportamento padrão e impede que o "C" seja adicionado ao código de barras
                abrirModalCliente();
            }
        });

        // Função que será chamada toda vez que uma tecla for pressionada
        document.addEventListener('keydown', async function(event) {
            // Ignora eventos de teclado se qualquer modal estiver aberto
            if (modalAberto) {
                return;
            }

            // Verifica se o cliente está identificado
            const clienteIdentificado = document.getElementById('cliente_identificado').style.display !== 'none';


            if (event.key.length === 1) {
                // Adiciona o caractere ao código
                codigoProduto += event.key;
                // Atualiza o valor do input com o código sendo lido
                codigoElement.value = codigoProduto;
            }

            // Quando pressionar Enter, consideramos o código lido completo
            if (event.key === 'Enter') {
                event.preventDefault(); // Evita comportamento padrão do Enter
                const produto = await buscarProduto(codigoProduto.trim());
                if (produto) {
                    resultadoElement.textContent = `Produto: ${produto.nome}, Preço: R$ ${parseFloat(produto.valor).toFixed(2)}`;
                    let item = document.createElement('li');
                    item.textContent = `${produto.nome} - R$ ${parseFloat(produto.valor).toFixed(2)}`;
                    item.setAttribute('data-codigo', codigoProduto.trim()); // Adiciona o código de barras como atributo
                    listaProdutos.prepend(item); // Adiciona o novo item no topo da lista

                    // Atualiza o total
                    total += parseFloat(produto.valor);
                    totalElement.textContent = `Total: R$ ${total.toFixed(2)}`;
                } else {
                    resultadoElement.textContent = `Produto com código ${codigoProduto} não encontrado.`;
                }
                // Limpa o código após o processamento
                codigoProduto = '';
                codigoElement.value = 'Nenhum código lido';
            }
        });

        // Referências ao modal e seus elementos
        const modalRemover = document.getElementById('modal_remover');
        const codigoRemoverInput = document.getElementById('codigo_remover');
        const senhaGestorInput = document.getElementById('senha_gestor');
        const confirmarRemocaoBtn = document.getElementById('confirmar_remocao');
        const cancelarRemocaoBtn = document.getElementById('cancelar_remocao');

        // Função para abrir o modal
        function abrirModalRemover() {
            modalAberto = true; // Define que um modal está aberto
            modalRemover.style.display = 'block';
            codigoRemoverInput.value = ''; // Clear the input field
            senhaGestorInput.value = ''; // Clear the password field
            codigoRemoverInput.focus();
        }

        // Função para fechar o modal
        function fecharModalRemover() {
            modalAberto = false; // Define que nenhum modal está aberto
            modalRemover.style.display = 'none';
        }

        // Evento para cancelar a remoção
        cancelarRemocaoBtn.addEventListener('click', fecharModalRemover);

        // Evento para confirmar a remoção
        confirmarRemocaoBtn.addEventListener('click', async function () {
            const codigo = codigoRemoverInput.value.trim();
            const senha = senhaGestorInput.value;

            if (!codigo) {
                alert('Por favor, insira o código de barras do produto.');
                return;
            }

            // Função para verificar a senha do GESTOR ou ADM
            try {
                const response = await fetch('../assets/api/verificar_gestor.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ senha })
                });

                if (response.ok) {
                    const gestorValido = await response.json();
                    if (gestorValido) {
                        // Procura o produto na lista usando o atributo data-codigo
                        const itens = Array.from(listaProdutos.children);
                        const itemEncontrado = itens.find(item => item.getAttribute('data-codigo') === codigo);

                        if (itemEncontrado) {
                            // Remove o produto da lista e atualiza o total
                            const precoRemovido = parseFloat(itemEncontrado.textContent.split('R$ ')[1]);
                            total -= precoRemovido;
                            totalElement.textContent = `Total: R$ ${total.toFixed(2)}`;
                            listaProdutos.removeChild(itemEncontrado);
                            alert('Produto removido com sucesso.');
                            fecharModalRemover();
                        } else {
                            alert('Produto com o código informado não encontrado na lista.');
                        }
                    } else {
                        alert('Senha inválida. Apenas GESTOR ou ADM podem remover produtos.');
                    }
                } else {
                    alert('Erro ao verificar a senha.');
                }
            } catch (error) {
                console.error('Erro ao verificar a senha:', error);
                alert('Erro ao verificar a senha.');
            }
        });

        // Evento para abrir o modal ao pressionar "R"
        document.addEventListener('keydown', function (event) {
            if (event.key === 'R' || event.key === 'r') {
                event.preventDefault(); // Prevents the default behavior of the key press
                abrirModalRemover();
            }
        });

        const modalLogout = document.getElementById('modal_logout');
        const confirmarLogout = document.getElementById('confirmar_logout');
        const cancelarLogout = document.getElementById('cancelar_logout');

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                modalAberto = true; // Define que um modal está aberto
                modalLogout.style.display = 'block';
            }
        });

        confirmarLogout.addEventListener('click', function () {
            modalAberto = false; // Define que nenhum modal está aberto
            window.location.href = 'index.php';
        });

        cancelarLogout.addEventListener('click', function () {
            modalAberto = false; // Define que nenhum modal está aberto
            modalLogout.style.display = 'none';
        });

        // Referências ao modal de cliente e seus elementos
        const modalCliente = document.getElementById('modal_cliente');
        const cpfClienteInput = document.getElementById('cpf_cliente');
        const confirmarClienteBtn = document.getElementById('confirmar_cliente');

        // Função para abrir o modal de cliente
        function abrirModalCliente() {
            modalAberto = true; // Define que um modal está aberto
            modalCliente.style.display = 'block';
            cpfClienteInput.value = ''; // Limpa o campo de CPF
            cpfClienteInput.focus();
        }

        // Função para fechar o modal de cliente
        function fecharModalCliente() {
            modalAberto = false; // Define que nenhum modal está aberto
            modalCliente.style.display = 'none';
        }

        // Evento para confirmar a identificação do cliente
        confirmarClienteBtn.addEventListener('click', async function () {
            const cpf = cpfClienteInput.value.trim();
            if (!cpf) {
                alert('Por favor, insira o CPF do cliente.');
                return;
            }

            try {
                const response = await fetch('../assets/api/get_cliente.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ cpf })
                });

                if (response.ok) {
                    const cliente = await response.json();
                    if (cliente) {
                        document.getElementById('nome_cliente').textContent = cliente.nome;
                        document.getElementById('cliente_identificado').style.display = 'block'; // Exibe a label
                        alert(`Cliente identificado: ${cliente.nome}`);
                    } else {
                        alert('Cliente não encontrado.');
                    }
                } else {
                    alert('Erro ao buscar cliente.');
                }
            } catch (error) {
                console.error('Erro ao buscar cliente:', error);
                alert('Erro ao buscar cliente.');
            }

            fecharModalCliente();
        });
    </script>
    <style>
        /* Estilo básico para o modal */
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

        .cliente-label {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-content input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .modal-content button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .modal-content button#confirmar_remocao {
            background-color: #28a745;
            color: white;
        }

        .modal-content button#cancelar_remocao {
            background-color: #dc3545;
            color: white;
        }

        .modal-content button:first-child {
            background-color: #dc3545;
            color: white;
        }

        .modal-content button:last-child {
            background-color: #6c757d;
            color: white;
        }

        .modal-content button#confirmar_cliente {
            background-color: #007bff;
            color: white;
        }
    </style>

</body>
</html>