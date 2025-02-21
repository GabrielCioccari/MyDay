<?php
// adicionar_tarefa.php
session_start();
include 'conexao.php';

if (isset($_POST['pagina_id'])) {
    $pagina_id = $_POST['pagina_id'];

    // Adiciona uma nova tarefa ao banco de dados
    $sql = "INSERT INTO titulos (pagina_id, titulo) VALUES (?, '')"; // título vazio para criar a tarefa
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pagina_id]);

    // Pega o ID da nova tarefa
    $tarefa_id = $pdo->lastInsertId();

    echo json_encode(['success' => true, 'texto_id' => $tarefa_id]);
} else {
    echo json_encode(['success' => false]);
}

