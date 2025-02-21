<?php
// remover_imagem.php

include 'conexao.php'; // Inclua o arquivo de conexão com o banco de dados

$imagem_id = $_POST['imagem_id'];

// Busca o caminho da imagem no banco de dados
$sql = "SELECT caminho FROM imagens WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $imagem_id);
$stmt->execute();
$stmt->bind_result($caminho);
$stmt->fetch();

// Remove a imagem do servidor
if (file_exists($caminho)) {
    unlink($caminho);
}

// Remove a imagem do banco de dados
$sql = "DELETE FROM imagens WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $imagem_id);
$stmt->execute();

echo json_encode(['success' => true]);

$stmt->close();
$conn->close();
?>