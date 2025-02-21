<?php
session_start();
include_once("../php/conexao.php"); // Conexão usando PDO
require __DIR__ . "/../vendor/autoload.php"; // Autoload do Google API

$mensagem = "";

// Configurar o cliente do Google
$client = new Google_Client();
$client->setClientId("3076413959-fk4gfcbheafumo8dkuk6pia5mqjp4ev1.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://127.0.0.1/projetosPHP/MyDayDesign/phpindex/login.php");
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    // Recebe o código de autenticação
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $_SESSION['access_token'] = $token['access_token'];

        $google_service = new Google_Service_Oauth2($client);
        $google_user = $google_service->userinfo->get();

        $google_email = $google_user->email;
        $google_nome = $google_user->name;
        $google_foto = null;

        // Verifica se a foto existe
        if (!empty($google_user->picture)) {
            $foto_url = $google_user->picture;

            // Caminho para salvar a imagem
            $nome_arquivo = uniqid() . '.jpg'; // Nome único
            $caminho_destino = "../img/" . $nome_arquivo;

            // Baixa a imagem e salva no servidor
            $conteudo_imagem = file_get_contents($foto_url);
            if ($conteudo_imagem) {
                file_put_contents($caminho_destino, $conteudo_imagem);
                $google_foto = $nome_arquivo;
            }
        }

        // Verifica se o usuário já está no banco
        $sql_check = "SELECT * FROM usuarios WHERE email = :email";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->bindParam(':email', $google_email);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            // Usuário já cadastrado, faz login
            $usuario = $stmt_check->fetch(PDO::FETCH_ASSOC);
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['foto_perfil'] = $usuario['foto_perfil'];
        } else {
            // Usuário novo, cadastra
            $sql_insert = "INSERT INTO usuarios (nome, email, foto_perfil) VALUES (:nome, :email, :foto_perfil)";
            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->bindParam(':nome', $google_nome);
            $stmt_insert->bindParam(':email', $google_email);
            $stmt_insert->bindParam(':foto_perfil', $google_foto, PDO::PARAM_STR);
            $stmt_insert->execute();

            // Configura sessão para novo usuário
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['usuario_nome'] = $google_nome;
            $_SESSION['foto_perfil'] = $google_foto;
        }

        // Redireciona para main.php
        header('Location: ../php/main.php');
        exit();
    } else {
        $mensagem = "Erro ao autenticar com o Google.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Login normal com email e senha
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    // Busca o usuário pelo email
    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($senha, $usuario['senha'])) {
            // Login bem-sucedido
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['foto_perfil'] = $usuario['foto_perfil'];

            header('Location: ../php/main.php');
            exit();
        } else {
            $mensagem = "Senha incorreta!";
        }
    } else {
        $mensagem = "E-mail não encontrado!";
    }
}

// Gerar URL de autenticação do Google
$url = $client->createAuthUrl();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat+Underline:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="icon" type="image/png" href="../img/LogoMyDay.png">
    <title>Login</title>
</head>
<body>

<div class="logo">
    <a href="index.php"><img src="../img/logoMyDayEscrito.png" alt=""></a>
 </div>
    
<main>
    <h2>Entrar</h2>
    <p>Organize seu agora, conquiste o amanhã.</p>

    <div class="form" id="form-container">
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="E-mail" required><br><br>

            <input type="password" name="senha" placeholder="Senha" required><br><br>

            <button type="submit" class="btnAmarelo">Entrar</button>

            <h3 class="msg"><?php echo $mensagem; ?></h3>

            <div class="separator"></div>

            <a class="google" href="<?= $url ?>"><img src="../img/google.png" alt="">Entrar com Google</a>

        </form>
    </div>

    <div class="poscont">
        <div class="perg">
            <p>Não tem uma conta?</p>
            <a href="cadastro.php">Cadastre-se</a>
        </div>
        <div class="perg">
            <p>Esqueceu sua Senha?</p>
            <a href="esqueceu_senha.php">Recuperar</a>
        </div>    
    </div>
</main>

</body>
</html>
