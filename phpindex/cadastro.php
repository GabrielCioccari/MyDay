<?php
include_once("../php/conexao.php");

$mensagem = "";

// Verificar se o método da requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Preparar as entradas de dados
    $nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografar a senha

    try {
        // Verificar se o e-mail já está cadastrado no banco de dados
        $verifica_email = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $verifica_email->bindParam(':email', $email);
        $verifica_email->execute();

        if ($verifica_email->rowCount() > 0) {
            $mensagem = "E-mail já cadastrado!";
        } else {
            // Inserir um novo usuário
            $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senha);

            if ($stmt->execute()) {
                // Redirecionar para a página de login após cadastro bem-sucedido
                header("Location: login.php");
                exit();
            } else {
                $mensagem = "Erro ao cadastrar. Tente novamente.";
            }
        }
    } catch (PDOException $e) {
        $mensagem = "Erro: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat+Underline:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="icon" type="image/png" href="../img/LogoMyDay.png">
    <title>Cadastro</title>

</head>
<body>

<div class="logo">
    <a href="index.php"><img src="../img/logoMyDayEscrito.png" alt=""></a>
 </div>

  <main>
    <h2>Cadastro</h2>
    <p class="subtit">Organize seu agora, conquiste o amanhã.</p>

    <div class="form" id="form-container">
        <form id="cadastro-form" action="cadastro.php" method="POST">

        <input type="text" name="nome" placeholder="Nome" required><br><br>

            <input type="email" name="email" placeholder="E-mail" required><br><br>

            <input type="password" name="senha" placeholder="Senha" required><br><br>

            <button type="submit" class="btnAmarelo">Cadastrar</button>

            <h3 class="msg"><?php echo $mensagem; ?></h3>

            <div class="separator"></div>

        </form>
    </div>

    <div class="poscont">
        <div class="perg">
            <p>Ja possui uma conta?</p>
            <a href="login.php">Entrar</a>
        </div>   
    </div>
  </main>

</body>
</html>
