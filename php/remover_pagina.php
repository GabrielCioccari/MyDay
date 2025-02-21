<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pagina_id = $_POST['pagina_id'];

    // Verifica se o usuário está autenticado e autorizado
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
        exit;
    }

    try {
        // Função recursiva para excluir as páginas filhas e o conteúdo associado
        function excluirPaginaRecursiva($pagina_id, $user_id, $pdo) {
            // Excluir tarefas associadas à página
            $sql_excluir_tarefas = "DELETE FROM tarefas WHERE pagina_id = ?";
            $stmt_tarefas = $pdo->prepare($sql_excluir_tarefas);
            $stmt_tarefas->execute([$pagina_id]);

            // Excluir textos associados à página
            $sql_excluir_textos = "DELETE FROM textos WHERE pagina_id = ?";
            $stmt_textos = $pdo->prepare($sql_excluir_textos);
            $stmt_textos->execute([$pagina_id]);

            // Excluir títulos associados à página
            $sql_excluir_titulos = "DELETE FROM titulos WHERE pagina_id = ?";
            $stmt_titulos = $pdo->prepare($sql_excluir_titulos);
            $stmt_titulos->execute([$pagina_id]);

            // Excluir as páginas filhas (recursivamente)
            $sql_filhas = "SELECT id FROM paginas WHERE parent_id = ?";
            $stmt_filhas = $pdo->prepare($sql_filhas);
            $stmt_filhas->execute([$pagina_id]);
            $filhas = $stmt_filhas->fetchAll();

            // Chama a função recursivamente para cada página filha
            foreach ($filhas as $pagina_filha) {
                excluirPaginaRecursiva($pagina_filha['id'], $user_id, $pdo); // Chamada recursiva para excluir as filhas
            }

            // Excluir a própria página
            $sql_excluir_pagina = "DELETE FROM paginas WHERE id = ? AND user_id = ?";
            $stmt_pagina = $pdo->prepare($sql_excluir_pagina);
            $stmt_pagina->execute([$pagina_id, $user_id]);
        }

        // Iniciar a exclusão da página principal (e suas filhas)
        excluirPaginaRecursiva($pagina_id, $user_id, $pdo);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir a página.']);
    }
}
?>
