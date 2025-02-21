<?php
require 'conexao.php'; // Conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagem'])) {
    $pagina_id = $_POST['pagina_id'];

    // Pasta para salvar as imagens
    $diretorio = 'uploads/';
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0755, true);
    }

    $arquivo = $_FILES['imagem'];
    $caminho = $diretorio . uniqid() . '-' . basename($arquivo['name']);

    if (move_uploaded_file($arquivo['tmp_name'], $caminho)) {
        // Salva o caminho da imagem no banco de dados
        $sql = "INSERT INTO imagens (pagina_id, caminho) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$pagina_id, $caminho]);

        echo json_encode(['status' => 'sucesso', 'caminho' => $caminho]);
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Falha no upload.']);
    }
}
?>
