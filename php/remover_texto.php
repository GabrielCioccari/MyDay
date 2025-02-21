<?php

include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $textoId = $_POST['texto_id'];

    $sql = "DELETE FROM textos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$textoId]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
