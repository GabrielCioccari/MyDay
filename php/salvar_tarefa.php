<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tarefa_id = $_POST['tarefa_id'];
    $titulo = $_POST['titulo'];

    $sql = "UPDATE tarefas SET titulo = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titulo, $tarefa_id]);

    echo json_encode(['success' => true]);
}
?>
