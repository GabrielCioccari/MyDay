<?php
// session_start();
// include 'conexao.php';

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

// $user_id = $_SESSION['user_id'];

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $sql_criar = "INSERT INTO paginas (titulo, user_id) VALUES ('Nova Página', ?)";
//     $stmt_criar = $pdo->prepare($sql_criar);
//     $stmt_criar->execute([$user_id]);

//     $pagina_id = $pdo->lastInsertId();

//     header("Location: editar_pagina.php?id=$pagina_id");
//     exit;
// }
?>

<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $parent_id = $_POST['parent_id'] ?? null;

    $sql = "INSERT INTO paginas (user_id, titulo, parent_id) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$user_id, $titulo, $parent_id])) {
        $pagina_id = $pdo->lastInsertId();
        header("Location: editar_pagina.php?id=$pagina_id");
        exit;
    } else {
        header("Location: erro.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>

