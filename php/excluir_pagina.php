<?php
// excluir_pagina.php
session_start();
include 'conexao.php';

$data = json_decode(file_get_contents('php://input'), true);
$pagina_id = $data['pagina_id'];

// Verifica se o usuário está autenticado e autorizado
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

try {
    // Excluir tarefas associadas à página e suas páginas filhas
    $sql_excluir_tarefas = "DELETE FROM tarefas WHERE pagina_id IN (
        SELECT id FROM paginas WHERE id = ? OR parent_id = ?
    )";
    $stmt_tarefas = $pdo->prepare($sql_excluir_tarefas);
    $stmt_tarefas->execute([$pagina_id, $pagina_id]);

    // Excluir textos associados à página e suas páginas filhas
    $sql_excluir_textos = "DELETE FROM textos WHERE pagina_id IN (
        SELECT id FROM paginas WHERE id = ? OR parent_id = ?
    )";
    $stmt_textos = $pdo->prepare($sql_excluir_textos);
    $stmt_textos->execute([$pagina_id, $pagina_id]);

    // Excluir títulos associados à página e suas páginas filhas
    $sql_excluir_titulos = "DELETE FROM titulos WHERE pagina_id IN (
        SELECT id FROM paginas WHERE id = ? OR parent_id = ?
    )";
    $stmt_titulos = $pdo->prepare($sql_excluir_titulos);
    $stmt_titulos->execute([$pagina_id, $pagina_id]);

    // Excluir páginas filhas
    $sql_excluir_paginas_filhas = "DELETE FROM paginas WHERE parent_id = ?";
    $stmt_paginas_filhas = $pdo->prepare($sql_excluir_paginas_filhas);
    $stmt_paginas_filhas->execute([$pagina_id]);

    // Excluir a própria página
    $sql_excluir_pagina = "DELETE FROM paginas WHERE id = ? AND user_id = ?";
    $stmt_pagina = $pdo->prepare($sql_excluir_pagina);
    $stmt_pagina->execute([$pagina_id, $user_id]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir a página.']);
}
?>