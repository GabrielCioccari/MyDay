<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tarefa_id = $_POST['tarefa_id'] ?? null;

    if ($tarefa_id) {
        $sql = "DELETE FROM tarefas WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tarefa_id]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
