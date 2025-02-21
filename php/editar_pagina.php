<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pagina_id = $_GET['id'] ?? null;

// Verifica se a ação de criar página foi solicitada
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['acao'] ?? '') === 'criar_pagina') {
    $parent_id = $_GET['parent_id'] ?? null;

    // Cria uma nova página com parent_id (página filha)
    $sql_criar = "INSERT INTO paginas (titulo, user_id, parent_id) VALUES (NULL, ?, ?)";
    $stmt_criar = $pdo->prepare($sql_criar);
    $stmt_criar->execute([$user_id, $parent_id]);
    $pagina_id = $pdo->lastInsertId();

    // Redireciona para editar a nova página
    header("Location: editar_pagina.php?id=$pagina_id");
    exit;
}

// Busca as informações do usuário
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Verifica se o ID da página foi fornecido e busca os dados
if ($pagina_id) {
    $sql_pagina = "SELECT * FROM paginas WHERE id = ? AND user_id = ?";
    $stmt_pagina = $pdo->prepare($sql_pagina);
    $stmt_pagina->execute([$pagina_id, $user_id]);
    $pagina = $stmt_pagina->fetch();

    if (!$pagina) {
        exit("Página não encontrada.");
    }

    $titulo_pagina = htmlspecialchars($pagina['titulo'] ?? 'Sem título', ENT_QUOTES, 'UTF-8');

    // Atualiza o último acesso da página
    $sql_atualizar_acesso = "UPDATE paginas SET ultimo_acesso = NOW() WHERE id = ?";
    $pdo->prepare($sql_atualizar_acesso)->execute([$pagina_id]);
} else {
    exit("ID da página não fornecido.");
}

// Consulta todos os itens da página
$sql_all_items = "
    (SELECT 'tarefa' AS tipo, id, titulo AS nome, data_criacao FROM tarefas WHERE pagina_id = ?)
    UNION
    (SELECT 'texto' AS tipo, id, titulo AS nome, data_criacao FROM textos WHERE pagina_id = ?)
    UNION
    (SELECT 'titulo' AS tipo, id, titulo AS nome, data_criacao FROM titulos WHERE pagina_id = ?)
    UNION
    (SELECT 'pagina_filha' AS tipo, id, titulo AS nome, data_criacao FROM paginas WHERE parent_id = ?)
    UNION
    (SELECT 'imagem' AS tipo, id, caminho AS nome, data_criacao FROM imagens WHERE pagina_id = ?)
    ORDER BY data_criacao ASC;
";
$stmt_all_items = $pdo->prepare($sql_all_items);
$stmt_all_items->execute(array_fill(0, 5, $pagina_id));
$all_items = $stmt_all_items->fetchAll(PDO::FETCH_ASSOC);

// Verifica se é uma página pai e busca filhas
$is_pagina_filha = isset($pagina['parent_id']);
$paginas_filhas = [];

if (!$is_pagina_filha) {
    $sql_paginas_filhas = "SELECT * FROM paginas WHERE parent_id = ?";
    $stmt_paginas_filhas = $pdo->prepare($sql_paginas_filhas);
    $stmt_paginas_filhas->execute([$pagina_id]);
    $paginas_filhas = $stmt_paginas_filhas->fetchAll();
}

// Busca todas as páginas do usuário ordenadas por último acesso
$sql_paginas = "SELECT * FROM paginas WHERE user_id = ? ORDER BY ultimo_acesso DESC";
$stmt_paginas = $pdo->prepare($sql_paginas);
$stmt_paginas->execute([$user_id]);
$paginas = $stmt_paginas->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat+Underline:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/pesquisar.css">
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="stylesheet" href="../css/editar.css">

    <link rel="icon" type="image/png" href="../img/LogoMyDay.png">
    <title id="titulo">MyDay</title>
</head>
<body>

<!-- Header -->
<header>
    <div class="header">
        <!-- Aqui fica a foto de perfil e o nome do usuário -->
        <div class="user">
            <?php if (!empty($user['foto_perfil'])): ?>
                <img src="../img/<?php echo htmlspecialchars($user['foto_perfil']); ?>" alt="Foto de Perfil" class="profile-image">
            <?php else: ?>
                <div class="primeiraLetra">
                    <h2><?php echo strtoupper(substr(htmlspecialchars($user['nome']), 0, 1)); ?></h2>
                </div>
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($user['nome']); ?></h3>
        </div>

        <div class="separator"></div>

        <!-- Aqui ficam os botões -->
        <button id="openModalBtnpesq" class="btnPesq"><img src="../img/procurar.png">Pesquisar</button>
        <button href="" class="selecionado"><img src="../img/casa.png">Página Inicial</button>

        <!-- Aqui ficam os projetos da header -->
        <div class="projetos">
        <h2>Projetos</h2>
            <ul id="lista_paginas">
                <?php
                // Função recursiva para exibir páginas
                function exibirPaginas($parent_id = null) {
                    global $pdo, $user_id;

                    // Verifica se o parent_id é null e ajusta os parâmetros da consulta
                    if ($parent_id === null) {
                        // Consulta para pegar todas as páginas com parent_id IS NULL
                        $sql_paginas = "SELECT * FROM paginas WHERE user_id = ? AND parent_id IS NULL";
                        $stmt_paginas = $pdo->prepare($sql_paginas);
                        $stmt_paginas->execute([$user_id]); // Apenas o user_id
                    } else {
                        // Consulta para pegar todas as páginas com um determinado parent_id
                        $sql_paginas = "SELECT * FROM paginas WHERE user_id = ? AND parent_id = ?";
                        $stmt_paginas = $pdo->prepare($sql_paginas);
                        $stmt_paginas->execute([$user_id, $parent_id]); // user_id e parent_id
                    }

                    $paginas = $stmt_paginas->fetchAll();

                    if (count($paginas) > 0) {
                        echo '<ul>';
                        foreach ($paginas as $pagina) {
                            echo '<li id="pagina_' . $pagina['id'] . '">';
                            echo '<div class="projeto-item">';
                            echo '<a class="selecionado" href="editar_pagina.php?id=' . $pagina['id'] . '" id="link_pagina_' . $pagina['id'] . '">';
                            
                            // Verifica se o título está vazio
                            if (empty(trim($pagina['titulo']))) {
                                echo "Nova Página"; // Exibe "Nova Página" se o título estiver vazio
                            } else {
                                echo htmlspecialchars($pagina['titulo'], ENT_QUOTES, 'UTF-8'); // Exibe o título normal
                            }
                            
                            echo '</a>';

                            // Botão de toggle para páginas com filhas
                            $stmt_paginas_filhas = $pdo->prepare("SELECT * FROM paginas WHERE parent_id = ?");
                            $stmt_paginas_filhas->execute([$pagina['id']]);
                            $paginas_filhas = $stmt_paginas_filhas->fetchAll();

                            if (!empty($paginas_filhas)) {
                                echo '<button class="btn-toggle" onclick="toggleFilhas(' . $pagina['id'] . ')">˅</button>';
                            }

                            // Botão de excluir
                            echo '<button onclick="excluirPagina(' . $pagina['id'] . ')" class="btn-excluir">';
                            echo '<img src="../img/lixo.png" alt="Excluir">';
                            echo '</button>';
                            echo '</div>';

                            // Exibe as páginas filhas (recursivamente)
                            if (!empty($paginas_filhas)) {
                                echo '<div id="filhas_pagina_' . $pagina['id'] . '" class="filhas" style="display: none; margin-left:-3rem;">';
                                exibirPaginas($pagina['id']); // Chamada recursiva para exibir as filhas
                                echo '</div>';
                            }

                            echo '</li>';
                        }
                        echo '</ul>';
                    }
                }

                // Exibe as páginas começando pela raiz (parent_id = NULL)
                exibirPaginas();
                ?>
            </ul>
        </div>

        <button href="" class="selecionado"><img src="../img/amigos.png">Amigos</button>
        <button href="" class="selecionado"><img src="../img/pagina.png">Modelos</button>
        <button id="openModalBtn" class="btnconfig"><img src="../img/engrenagem.png">Configurações</button>
        <div class="btnpaglugar">
            <form action="nova_pagina.php" method="POST" style="display: inline;">
                <button type="submit" class="btnPag">Nova Página</button>
            </form>
        </div>
    </div>
</header>

<!-- Modal de configuracoes -->
<div id="modal" class="modalconfig">
    <div class="modal-contentconfig">
            <span id="closeModalBtn" class="close" style="display:none">&times;</span>  

            <div class="sidebar">
                <button id="perfilBtn" class="nav-btn">Perfil</button>
                <button id="aparenciaBtn" class="nav-btn">Aparência</button>
            </div>

            <div id="content">
                <div id="perfilSection">
                    <h2>Meu Perfil</h2>                         
                    <?php if (isset($_SESSION['user_id']) && !empty($user['foto_perfil'])): ?>
                        <div class="profile-image-container">
                            <img src="../img/<?php echo $user['foto_perfil']; ?>" alt="Foto de Perfil Atual" width="150" height="150" id="fotoPerfilAtual" class="clickable-img" onclick="openFileDialog();">
                            <i class="fas fa-paint-brush edit-icon" onclick="openFileDialog(); event.stopPropagation();"></i>

                            <form action="atualizar_perfil.php" method="post" style="display: inline;">
                                <button type="submit" name="delete_photo" class="delete-photo-btn" onclick="event.stopPropagation();">✖</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="profile-image-container">
                            <div id="letraPerfil" class="profile-placeholder clickable-img" onclick="openFileDialog();">
                                <?php echo strtoupper(substr($user['nome'], 0, 1)); ?>
                            </div>
                            
                            <i class="fas fa-paint-brush edit-icon" onclick="openFileDialog(); event.stopPropagation();"></i>
                        </div>
                    <?php endif; ?>
                        

                    <form action="atualizar_perfil.php" method="post" enctype="multipart/form-data">
                        <div class="altfoto">
                            <input type="file" id="foto_perfil" name="foto_perfil" style="display:none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <!-- <button id="sair"><a href="../phpindex/index.php">Sair da sua conta</a></button> -->

                        <label for="">Nome:</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($user['nome'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        <h2>Minhas Informações</h2>                         

                        <label for="">E-mail:</label>

                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Alterar Email" required>
                        <label for="nova_senha">Senha:</label>
                        <input type="password" id="nova_senha" name="nova_senha" placeholder="Digite a nova senha">

                        <label for="confirma_senha">Confirmar Senha:</label>
                        <input type="password" id="confirma_senha" name="confirma_senha" placeholder="Confirme a nova senha">

                        <span id="erro_senha" style="color: red; display: none;">As senhas não coincidem</span>

                        <button id="salvar" type="submit" name="update">Salvar</button>
                    </form>
                </div>

                <div id="aparenciaSection" style="display: none;">
                        <h2>Aparência</h2>
                        <p>Aqui você pode mudar a aparência do seu perfil.</p>
                        <label for="tema">Escolha o tema:</label>
                        <select id="tema">
                            <option value="claro">Claro</option>
                            <option value="escuro">Escuro</option>
                        </select>
                </div>
            </div>

            <script>
                document.getElementById('salvar').addEventListener('click', function (event) {
                    const novaSenha = document.getElementById('nova_senha').value;
                    const confirmaSenha = document.getElementById('confirma_senha').value;
                    const erroSenha = document.getElementById('erro_senha');

                    if (novaSenha !== confirmaSenha) {
                        event.preventDefault();
                        erroSenha.style.display = 'block';
                    } else {
                        erroSenha.style.display = 'none';
                    }
                });

            </script>
    </div>
</div>

<!-- Modal de pesquisar -->
<div id="modalpesq" class="modalpesq">
    <div class="modal-content">
        <span id="closeModalBtnpesq" class="close" style="display:none">&times;</span>  

        <div class="pesqcenter">
            <img id="pesqimg" src="../img/procurar.png" alt=""> 
            <input type="text" id="searchInput" placeholder="Digite para pesquisar..." onkeyup="filterPages()">
        </div>
        
        <div class="separatorc"></div>

        <?php
            $sql_paginas = "SELECT * FROM paginas WHERE user_id = ?";
            $stmt_paginas = $pdo->prepare($sql_paginas);
            $stmt_paginas->execute([$user_id]);
            $paginas = $stmt_paginas->fetchAll();
            $current_page_id = $_GET['id'] ?? null;


            echo '<div class="divpag">'; // Move a abertura da div aqui
            echo '<h3 class="acessos-recentes">Suas Páginas</h3>';

            echo '<ul id="lista_paginaspesq" class="acess">'; // Início da lista

            foreach ($paginas as $pagina) {
            $isActive = $current_page_id == $pagina['id'] ? 'active' : '';
            $titulo_exibicao = !empty($pagina['titulo']) ? htmlspecialchars($pagina['titulo'], ENT_QUOTES, 'UTF-8') : 'Nova Página';
            $icon_excluir = '../img/lixo.png'; // Altere para o caminho do ícone de lixeira

            echo '<li id="paginaa_' . $pagina['id'] . '" class="pagina-item">';
            echo '<a class="selecionado" class="' . $isActive . '" href="editar_pagina.php?id=' . $pagina['id'] . '" id="link_paginaa_' . $pagina['id'] . '">';
            echo $titulo_exibicao;
            echo '</a>';
            echo '<button onclick="excluirPagina(' . $pagina['id'] . ')" class="btn-excluir" style="background: none; border: none; cursor: pointer; height: 16px; width: 16px;">';
            echo '<img src="' . $icon_excluir . '" alt="Excluir">'; // Ícone de lixeira
            echo '</button>';
            echo '</li>';
            }

            echo '</ul>'; // Fim da lista
            echo '</div>'; // Fim da div
        ?>
    </div>
</div>

<!-- scrpit de excluir pagina -->
<script>
    function excluirPagina(paginaId) {
    if (confirm("Tem certeza de que deseja excluir esta página e todas as suas tarefas?")) {
        fetch('excluir_pagina.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ pagina_id: paginaId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove o item da lista na interface
                document.getElementById('pagina_' + paginaId).remove();
                alert("Página e tarefas excluídas com sucesso!");
            } else {
                alert("Erro ao excluir a página.");
            }
        })
        .catch(error => console.error("Erro:", error));
    }
    }
</script>

<!-- main para exibir os itens -->
<main class="mainForm">
   <div class="itensEdicao">
        <form id="form-editar" method="POST">
            <input type="text" id="titulo_pagina" name="titulo_pagina" placeholder="Nova Página" value="<?php echo $titulo_pagina; ?>" required oninput="atualizarTitulo()">      
        </form>

        <div id="tarefas-container" style="display: flex; flex-direction: column; gap: 10px;">
            <?php foreach ($all_items as $item): ?>
                <?php if ($item['tipo'] == 'tarefa'): ?>
                    <div class="tarefa" data-id="<?php echo $item['id']; ?>">
                        <button type="button" class="remover-tarefa" onclick="removerTarefa(<?php echo $item['id']; ?>)">
                            <img src="../img/lixo.png" alt="Excluir">
                        </button>
                        <input type="checkbox" name="tarefa_completa[]" class="checkbox-tarefa" value="<?php echo $item['id']; ?>">
                        <input type="text" class="tarefa-texto" placeholder="Nova Tarefa" name="titulo_tarefa[]" value="<?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        <!-- <textarea class="tarefa-texto" placeholder="Nova Tarefa" name="titulo_tarefa[]" required><?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?></textarea> -->
                    </div>
                <?php elseif ($item['tipo'] == 'texto'): ?>
                    <div class="texto" data-id="<?php echo $item['id']; ?>">
                        <textarea name="titulo_texto[]" placeholder="Novo Texto" required><?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <button type="button" class="remover-texto" onclick="removerTexto(<?php echo $item['id']; ?>)">
                            <img src="../img/lixo.png" alt="Excluir">
                        </button>
                    </div>
                <?php elseif ($item['tipo'] == 'titulo'): ?>
                    <div class="titulo" data-id="<?php echo $item['id']; ?>">
                        <input type="text" class="titulo-texto" placeholder="Novo Título" name="titulo_titulo_input[]" value="<?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        <button type="button" class="remover-titulo" onclick="removerTitulo(<?php echo $item['id']; ?>)">
                            <img src="../img/lixo.png" alt="Excluir">
                        </button>
                    </div>
                <?php elseif ($item['tipo'] == 'pagina_filha'): ?>
                    <div class="pagina-filha" data-id="<?php echo $item['id']; ?>">
                        <button type="button" class="remover-pagina-filha" onclick="removerPaginaFilha(<?php echo $item['id']; ?>)">
                            <img src="../img/lixo.png" alt="Excluir">
                        </button>
                        <a href="editar_pagina.php?id=<?php echo $item['id']; ?>" class="pagina-titulo">
                            <?php echo empty(trim($item['nome'])) ? "Nova Página" : htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </div>
                <?php elseif ($item['tipo'] == 'imagem'): ?>
                    <div class="imagem" data-id="<?php echo $item['id']; ?>">
                        <img src="<?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?>" alt="Imagem" style="max-width: 100%; border-radius: 8px;">
                        <button type="button" onclick="removerImagem(<?php echo $item['id']; ?>)">Remover</button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>    
        </div>

        <button id="showOptionsBtn"><img src="../img/mais2.png" alt=""> Mostrar Opções</button>

        <div id="optionsModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="btnedit">
                    <button type="button" id="adicionar-titulo"><img src="../img/.png" alt="">🖋️ Subtitulo</button>
                    <button type="button" id="adicionar-texto"><img src="../img/.png" alt="">✏️ Texto</button>
                    <button type="button" id="adicionar-tarefa"><img src="../img/.png" alt="">✔️ Tarefa</button>
                    <button type="button" id="adicionar-pagina-filha"><img src="../img/.png" alt="">📄 Página</button> 
                    <!-- <button type="button" id="adicionar-imagem">Imagem</button>
                    <input type="file" id="input-imagem" accept="image/*" style="display: none;">
                    <button>Tabela</button> -->
                </div>
            </div>
        </div> 
   </div>
</main>


<!-- Salva automaticamente o título da página via AJAX -->
<script>
    document.getElementById('titulo_pagina').addEventListener('input', function() {
        const titulo = this.value;
        const paginaId = "<?php echo $pagina_id; ?>";

        // Atualiza o título na lista de projetos
        const linkPagina = document.querySelector(`#lista_paginas #link_pagina_${paginaId}`);
        if (linkPagina) {
            linkPagina.textContent = titulo;
        }

        // Salva o título no banco de dados
        fetch('salvar_pagina.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `titulo=${encodeURIComponent(titulo)}&pagina_id=${paginaId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("Título salvo com sucesso.");
            } else {
                console.error("Erro ao salvar o título.");
            }
        });
    });
</script>

<!-- Salva automaticamente as alterações via AJAX -->
<script>
//tarefas
    document.querySelectorAll('input[name="titulo_tarefa[]"]').forEach(input => {
        input.addEventListener('input', function() {
            const tarefaId = this.closest('.tarefa').getAttribute('data-id');
            const titulo = this.value;

            fetch('salvar_tarefa.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `tarefa_id=${tarefaId}&titulo=${encodeURIComponent(titulo)}`
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Tarefa salva com sucesso.");
                } else {
                    console.error("Erro ao salvar a tarefa.");
                }
            });
        });
    });
//textos
    document.querySelectorAll('textarea[name="titulo_texto[]"]').forEach(textarea => {
        textarea.addEventListener('input', function() {
            const textoId = this.closest('.texto').getAttribute('data-id');
            const titulo = this.value;

            fetch('salvar_texto.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `texto_id=${textoId}&titulo=${encodeURIComponent(titulo)}`
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Texto salvo com sucesso.");
                } else {
                    console.error("Erro ao salvar o texto.");
                }
            });
        });
    });
//titulo
    document.querySelectorAll('input[name="titulo_titulo_input[]"]').forEach(input => {
        input.addEventListener('input', function() {
                const tituloId = this.closest('.titulo').getAttribute('data-id');
                const titulo = this.value;

                fetch('salvar_titulo.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `titulo_id=${tituloId}&titulo=${encodeURIComponent(titulo)}`
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("Tarefa salva com sucesso.");
                    } else {
                        console.error("Erro ao salvar a tarefa.");
                    }
                });
            });
        });
//pagina
    document.querySelectorAll('.pagina-titulo').forEach(link => {
        link.addEventListener('input', function() {
            const paginaId = this.closest('.pagina-filha').getAttribute('data-id');
            const titulo = this.textContent;

            fetch('salvar_pagina.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `pagina_id=${paginaId}&titulo=${encodeURIComponent(titulo)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Título da página salvo com sucesso.");
                } else {
                    console.error("Erro ao salvar o título da página.");
                }
            });
        });
    });
//imagem
    document.querySelectorAll('input[name="imagem_upload[]"]').forEach(input => {
        input.addEventListener('change', function() {
            const imagemId = this.closest('.imagem').getAttribute('data-id');
            const file = this.files[0];

            // Cria um objeto FormData para enviar o arquivo
            const formData = new FormData();
            formData.append('imagem_id', imagemId);
            formData.append('imagem', file);

            // Envia a requisição para salvar a imagem
            fetch('salvar_imagem.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Imagem salva com sucesso.");

                    // Exibe a imagem no DOM após o upload
                    const imgElement = document.createElement('img');
                    imgElement.src = data.caminho; // O caminho da imagem retornado pelo servidor
                    imgElement.style.maxWidth = '100px'; // Estilo opcional para limitar o tamanho
                    imgElement.style.maxHeight = '100px'; // Estilo opcional para limitar o tamanho

                    // Adiciona a imagem ao container da imagem
                    const imagemContainer = this.closest('.imagem');
                    imagemContainer.appendChild(imgElement);
                } else {
                    console.error("Erro ao salvar a imagem.");
                }
            });
        });
    });
</script>

<!-- Função para adicionar via AJAX -->
<script>
//tarefa
    document.getElementById('adicionar-tarefa').addEventListener('click', function() {
        const paginaId = "<?php echo $pagina_id; ?>";
        fetch('adicionar_tarefa.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `pagina_id=${paginaId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tarefasContainer = document.getElementById('tarefas-container');

                // Cria o novo elemento de tarefa
                const novaTarefa = document.createElement('div');
                novaTarefa.classList.add('tarefa');
                novaTarefa.setAttribute('data-id', data.tarefa_id);

                // Define o conteúdo do novo elemento
                novaTarefa.innerHTML = `
                    <button type="button" class="remover-tarefa" onclick="removerTarefa(${data.tarefa_id})">
                        <img src="../img/lixo.png" alt="Excluir">
                    </button>
                    <input type="checkbox" name="tarefa_completa[]" class="checkbox-tarefa" value="${data.tarefa_id}">
                    <input type="text" class="tarefa-texto" name="titulo_tarefa[]" placeholder="Nova Tarefa" required>
                `;

                // Adiciona o novo elemento ao container
                tarefasContainer.appendChild(novaTarefa);

                // Adiciona o evento de input para salvar automaticamente
                const inputNovoTitulo = novaTarefa.querySelector('input[name="titulo_tarefa[]"]');
                inputNovoTitulo.addEventListener('input', function() {
                    const tarefaId = data.tarefa_id;
                    const titulo = this.value;

                    fetch('salvar_tarefa.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `tarefa_id=${tarefaId}&titulo=${encodeURIComponent(titulo)}`
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log("Tarefa salva com sucesso.");
                        } else {
                            console.error("Erro ao salvar a tarefa.");
                        }
                    });
                });
            }
        });
    });
//texto
    document.getElementById('adicionar-texto').addEventListener('click', function() {
        const paginaId = "<?php echo $pagina_id; ?>";
        fetch('adicionar_texto.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `pagina_id=${paginaId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tarefasContainer = document.getElementById('tarefas-container');

                // Cria o novo elemento de texto
                const novoTexto = document.createElement('div');
                novoTexto.classList.add('texto');
                novoTexto.setAttribute('data-id', data.texto_id);

                // Define o conteúdo do novo elemento
                novoTexto.innerHTML = `
                    <textarea name="titulo_texto[]" placeholder="Novo Texto" required></textarea>
                    <button type="button" class="remover-texto" onclick="removerTexto(${data.texto_id})">
                        <img src="../img/lixo.png" alt="Excluir">
                    </button>
                `;

                // Adiciona o novo elemento ao container
                tarefasContainer.appendChild(novoTexto);

                // Adiciona o evento de input para salvar automaticamente
                const textareaNovoTexto = novoTexto.querySelector('textarea[name="titulo_texto[]"]');
                textareaNovoTexto.addEventListener('input', function() {
                    const textoId = data.texto_id;
                    const titulo = this.value;

                    fetch('salvar_texto.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `texto_id=${textoId}&titulo=${encodeURIComponent(titulo)}`
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log("Texto salvo com sucesso.");
                        } else {
                            console.error("Erro ao salvar o texto.");
                        }
                    });
                });
            }
        });
    });
//titulo
    document.getElementById('adicionar-titulo').addEventListener('click', function() {
        const paginaId = "<?php echo $pagina_id; ?>";
        fetch('adicionar_titulo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `pagina_id=${paginaId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tarefasContainer = document.getElementById('tarefas-container');

                // Cria o novo elemento de título
                const novoTitulo = document.createElement('div');
                novoTitulo.classList.add('titulo');
                novoTitulo.setAttribute('data-id', data.titulo_id);

                // Define o conteúdo do novo elemento
                novoTitulo.innerHTML = `
                    <input type="text" class="titulo-texto" name="titulo_titulo[]" placeholder="Novo Título" required>
                    <button type="button" class="remover-titulo" onclick="removerTitulo(${data.titulo_id})">
                        <img src="../img/lixo.png" alt="Excluir">
                    </button>
                `;

                // Adiciona o novo elemento ao container
                tarefasContainer.appendChild(novoTitulo);

                // Adiciona o evento de input para salvar automaticamente
                const inputNovoTitulo = novoTitulo.querySelector('input[name="titulo_titulo[]"]');
                inputNovoTitulo.addEventListener('input', function() {
                    const tituloId = data.titulo_id;
                    const titulo = this.value;

                    fetch('salvar_titulo.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `titulo_id=${tituloId}&titulo=${encodeURIComponent(titulo)}`
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log("Título salvo com sucesso.");
                        } else {
                            console.error("Erro ao salvar o título.");
                        }
                    });
                });
            }
        });
    });
//pagina
    document.getElementById('adicionar-pagina-filha').addEventListener('click', function() {
        const paginaId = "<?php echo $pagina_id; ?>"; // ID da página atual (pai)

        // Envia uma requisição POST para criar a página filha
        fetch('nova_pagina.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `parent_id=${paginaId}`
        })
        .then(response => {
            // Redireciona para a página de edição após a criação
            if (response.redirected) {
                window.location.href = response.url;
            }
        })
        .catch(error => {
            console.error("Erro ao criar a página filha:", error);
        });
    });
//imagem
    document.getElementById('adicionar-imagem').addEventListener('click', function() {
        document.getElementById('input-imagem').click();
    });

    document.getElementById('input-imagem').addEventListener('change', function(event) {
        const arquivo = event.target.files[0];
        if (arquivo) {
            const formData = new FormData();
            formData.append('imagem', arquivo);
            formData.append('pagina_id', <?php echo $pagina_id; ?>); // ID da página atual

            fetch('upload_imagem.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'sucesso') {
                    adicionarImagem(data.caminho);
                } else {
                    alert('Erro ao enviar a imagem: ' + data.mensagem);
                }
            })
            .catch(error => console.error('Erro:', error));
        }
    });

    function adicionarImagem(src) {
        const container = document.createElement('div');
        container.className = 'imagem';

        const img = document.createElement('img');
        img.src = src;
        img.alt = 'Imagem carregada';
        img.style.maxWidth = '100%';
        img.style.borderRadius = '8px';

        const botaoRemover = document.createElement('button');
        botaoRemover.textContent = 'Remover';
        botaoRemover.addEventListener('click', function() {
            container.remove();
            // Código para remover do banco de dados pode ser adicionado aqui
        });

        container.appendChild(img);
        container.appendChild(botaoRemover);

        document.getElementById('tarefas-container').appendChild(container);
    }
</script>

<!-- Função para remover via AJAX -->
<script>
//tarefa
    function removerTarefa(tarefaId) {
    fetch('remover_tarefa.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `tarefa_id=${tarefaId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tarefaElement = document.querySelector(`.tarefa[data-id="${tarefaId}"]`);
            if (tarefaElement) {
                tarefaElement.remove();
            }
        }
    });
    }
//texto
    function removerTexto(textoId) {
        fetch('remover_texto.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `texto_id=${textoId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const textoElement = document.querySelector(`.texto[data-id="${textoId}"]`);
                if (textoElement) {
                    textoElement.remove();
                }
            }
        });
    }
//titulo
    function removerTitulo(tituloId) {
        fetch('remover_titulo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `titulo_id=${tituloId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tituloElement = document.querySelector(`.titulo[data-id="${tituloId}"]`);
                if (tituloElement) {
                    tituloElement.remove();
                }
            }
        });
    }
//pagina
    function removerPaginaFilha(paginaId) {
        if (confirm("Tem certeza de que deseja excluir esta página filha?")) {
            fetch('remover_pagina.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `pagina_id=${paginaId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const paginaElement = document.querySelector(`.pagina-filha[data-id="${paginaId}"]`);
                    if (paginaElement) {
                        paginaElement.remove();
                    }
                }
            });
        }
    }
//imagem
    function removerImagem(imagemId) {
        fetch('remover_imagem.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `imagem_id=${imagemId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const imagemElement = document.querySelector(`.imagem[data-id="${imagemId}"]`);
                if (imagemElement) {
                    imagemElement.remove();
                }
            }
        });
    }
</script>

<!-- botao -->
<script>
    document.getElementById("showOptionsBtn").addEventListener("click", function() {
        var options = document.getElementById("options");
        if (options.style.display === "none") {
            options.style.display = "block";
        } else {
            options.style.display = "none";
        }
    });
</script>

<script src="../js/modal.js"></script>
<script src="../js/botao.js"></script>
<script src="../js/perfil.js"></script>
<script src="../js/pesquisar.js"></script>

</body>
</html>