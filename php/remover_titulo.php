<?php
require_once 'conexao.php';

$titulo_id = $_POST['titulo_id'];

$sql = "DELETE FROM titulos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $titulo_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>