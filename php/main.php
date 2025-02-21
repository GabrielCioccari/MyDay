<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../phpindex/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Usuário não encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['acao']) && $_GET['acao'] === 'criar_pagina') {
    $sql_criar = "INSERT INTO paginas (titulo, user_id) VALUES (NULL, ?)";
    $stmt_criar = $pdo->prepare($sql_criar);
    $stmt_criar->execute([$user_id]);

    $pagina_id = $pdo->lastInsertId();

    header("Location: editar_pagina.php?id=$pagina_id");
    exit;
}

$primeiraLetra = strtoupper(substr($user['nome'], 0, 1));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat+Underline:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/pesquisar.css">
    <link rel="stylesheet" href="../css/perfil.css">

    <link rel="icon" type="image/png" href="../img/LogoMyDay.png">
    <title>MyDay</title>
</head>
<body>

<!-- header onde fica os botoes -->
<header>
    <div class="header">
        <!-- aqui fica a foto de perfil e o nome do usuario -->
        <div class="user">
            <?php if (!empty($user['foto_perfil'])): ?>
                <img src="../img/<?php echo htmlspecialchars($user['foto_perfil']); ?>" alt="Foto de Perfil" class="profile-image">
            <?php else: ?>
                <div class="primeiraLetra">
                    <h2 ><?php echo strtoupper(substr(htmlspecialchars($user['nome']), 0, 1)); ?></h2>
                </div>
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($user['nome']); ?></h3>
        </div>
        
        <div class="separator"></div>

        <!-- aqui fica os botoes -->
        <button id="openModalBtnpesq" class="btnPesq"><img src="../img/procurar.png">Pesquisar</button>
        <button href="" class="selecionado"><img src="../img/casa.png">Página Inicial</button>
        <!-- aqui fica os projetos da header -->
        <div class="projetos">
            <h2>Projetos</h2>
            <ul id="lista_paginas">
            <?php
                $sql_paginas = "SELECT * FROM paginas WHERE user_id = ?";
                $stmt_paginas = $pdo->prepare($sql_paginas);
                $stmt_paginas->execute([$user_id]);
                $paginas = $stmt_paginas->fetchAll();
                $current_page_id = $_GET['id'] ?? null;

                foreach ($paginas as $pagina) {
                    $isActive = $current_page_id == $pagina['id'] ? 'active' : '';
                    $titulo_exibicao = !empty($pagina['titulo']) ? htmlspecialchars($pagina['titulo'], ENT_QUOTES, 'UTF-8') : 'Nova Página';
                    $icon_excluir = '../img/lixo.png'; // Altere para o caminho do ícone de lixeira

                    echo '<li id="pagina_' . $pagina['id'] . '" style="display: flex; align-items: center; justify-content: space-between;">';
                    echo '<a class="selecionado" class="' . $isActive . '" href="editar_pagina.php?id=' . $pagina['id'] . '" id="link_pagina_' . $pagina['id'] . '">';
                    echo $titulo_exibicao;
                    echo '</a>';
                    echo '<button onclick="excluirPagina(' . $pagina['id'] . ')" class="btn-excluir" style="background: none; border: none; cursor: pointer; height: 16px; width: 16px;">';
                    echo '<img src="' . $icon_excluir . '" alt="Excluir">'; // Ícone de lixeira
                    echo '</button>';
                    echo '</li>';
                }
            ?>
            </ul>
        </div>
        <button href="" class="selecionado"><img src="../img/amigos.png">Amigos</button>
        <button href="" class="selecionado"><img src="../img/pagina.png">Modelos</button>
        <button id="openModalBtn" class="btnconfig"><img src="../img/engrenagem.png">Configurações</button>
        <div class="btnpaglugar">
        <form action="nova_pagina.php" method="POST" style="display: inline;"><button type="submit" class="btnPag">Nova Página</button></form>
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
                    <div class="meuperfil">
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

<script src="../js/modal.js"></script>
<script src="../js/botao.js"></script>
<script src="../js/perfil.js"></script>
<script src="../js/pesquisar.js"></script>

</body>
</html>
