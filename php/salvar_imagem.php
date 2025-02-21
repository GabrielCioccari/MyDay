<?php
// salvar_imagem.php

include 'conexao.php'; // Inclua o arquivo de conexão com o banco de dados

$imagem_id = $_POST['imagem_id'];
$imagem = $_FILES['imagem'];

// Define o diretório onde a imagem será salva
$target_dir = "../img/uploads/";
$target_file = $target_dir . basename($imagem["name"]);
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Verifica se o arquivo é uma imagem
$check = getimagesize($imagem["tmp_name"]);
if ($check === false) {
    echo json_encode(['success' => false, 'message' => 'O arquivo não é uma imagem.']);
    exit;
}

// Verifica se o arquivo já existe
if (file_exists($target_file)) {
    echo json_encode(['success' => false, 'message' => 'O arquivo já existe.']);
    exit;
}

// Verifica o tamanho do arquivo (5MB máximo)
if ($imagem["size"] > 5000000) {
    echo json_encode(['success' => false, 'message' => 'O arquivo é muito grande.']);
    exit;
}

// Permite apenas certos formatos de arquivo
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
    echo json_encode(['success' => false, 'message' => 'Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.']);
    exit;
}

// Move o arquivo para o diretório de uploads
if (move_uploaded_file($imagem["tmp_name"], $target_file)) {
    // Atualiza o banco de dados com o caminho da imagem
    $sql = "UPDATE imagens SET caminho = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $target_file, $imagem_id);
    $stmt->execute();

    echo json_encode(['success' => true, 'caminho' => $target_file]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload da imagem.']);
}

$stmt->close();
$conn->close();
?>