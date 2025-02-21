<?php
session_start();
include 'conexao.php'; // Conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['update'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    // Atualiza o nome e o email no banco de dados
    $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $email, $user_id]);

    // Verifica se foi fornecida uma nova senha
    if (!empty($_POST['nova_senha'])) {
        $novaSenha = $_POST['nova_senha'];
        $confirmaSenha = $_POST['confirma_senha'];

        // Verifica se as senhas coincidem
        if ($novaSenha === $confirmaSenha) {
            // Criptografa a nova senha
            $hashSenha = password_hash($novaSenha, PASSWORD_DEFAULT);

            // Atualiza a senha no banco de dados
            $sql = "UPDATE usuarios SET senha = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$hashSenha, $user_id]);
        } else {
            $_SESSION['msg'] = "As senhas não coincidem.";
            header("Location: perfil.php");
            exit;
        }
    }

    // Verifica se uma nova foto de perfil foi enviada
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto_perfil']['tmp_name'];
        $fileName = $_FILES['foto_perfil']['name'];
        $fileSize = $_FILES['foto_perfil']['size'];
        $fileType = $_FILES['foto_perfil']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Define o novo nome da imagem com o ID do usuário para evitar conflito de nomes
            $newFileName = $user_id . '.' . $fileExtension;

            $uploadFileDir = __DIR__ . '/../img/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Atualiza o banco de dados para incluir a nova foto
                $sql = "UPDATE usuarios SET foto_perfil = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$newFileName, $user_id]);
            } else {
                echo "Erro ao mover o arquivo para o diretório de uploads.";
            }
        } else {
            echo "Tipo de arquivo não permitido.";
        }
    }

    header("Location: main.php");
    exit;
}

// Lógica para deletar a foto de perfil
if (isset($_POST['delete_photo'])) {
    $sql = "SELECT foto_perfil FROM usuarios WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Verifica se há uma foto de perfil para deletar
    if (!empty($user['foto_perfil'])) {
        $filePath = __DIR__ . '/../img/' . $user['foto_perfil'];

        // Deleta o arquivo da foto de perfil
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Atualiza o banco de dados para remover a foto de perfil
        $sql = "UPDATE usuarios SET foto_perfil = NULL WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);

        header("Location: main.php");
        exit;
    } else {
        echo "Não há foto de perfil para deletar.";
    }
}
?>
