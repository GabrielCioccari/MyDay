<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pagina_id = $_POST['pagina_id'];
    $titulo = $_POST['titulo'];

    $sql = "UPDATE paginas SET titulo = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$titulo, $pagina_id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>