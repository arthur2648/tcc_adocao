<?php
// Segurança e conexão
session_start();
require_once 'conexao.php';

// Verifica permissão de funcionário/admin
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_tipo'], ['funcionario', 'gerente', 'admin'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o ID do animal foi passado pela URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: gerenciar_animais.php");
    exit();
}
$id_animal = $_GET['id'];

try {
    // PASSO 1: ANTES DE DELETAR, PRECISAMOS PEGAR O CAMINHO DA FOTO PARA APAGÁ-LA DO SERVIDOR
    $sql_select_foto = "SELECT foto_url FROM animais WHERE id = :id";
    $stmt_select_foto = $pdo->prepare($sql_select_foto);
    $stmt_select_foto->execute([':id' => $id_animal]);
    $animal = $stmt_select_foto->fetch(PDO::FETCH_ASSOC);

    if ($animal && !empty($animal['foto_url'])) {
        // Verifica se o arquivo de foto existe e, se existir, apaga
        if (file_exists($animal['foto_url'])) {
            unlink($animal['foto_url']); // Apaga o arquivo da foto da pasta 'uploads'
        }
    }

    // PASSO 2: AGORA, DELETAMOS O REGISTRO DO ANIMAL DO BANCO DE DADOS
    $sql_delete = "DELETE FROM animais WHERE id = :id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute([':id' => $id_animal]);

    // Redireciona de volta para a página de gerenciamento com uma mensagem de sucesso (opcional)
    header("Location: gerenciar_animais.php");
    exit();

} catch (PDOException $e) {
    // Em caso de erro, redireciona de volta com uma mensagem de erro (opcional)
    // Em um sistema real, registraríamos o erro em um log.
    die("Erro ao excluir o animal: " . $e->getMessage());
}
?>