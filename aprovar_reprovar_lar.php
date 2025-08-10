<?php
session_start();
require_once 'conexao.php';
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_tipo'], ['funcionario', 'gerente', 'admin', 'rh'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['id']) || !isset($_GET['acao'])) {
    header("Location: gerenciar_lares.php");
    exit();
}

$id_lar = $_GET['id'];
$acao = $_GET['acao'];
$id_funcionario_logado = $_SESSION['usuario_id']; // Pega o ID do funcionário logado
$novo_status = '';

if ($acao == 'aprovar') { $novo_status = 'aprovado'; } 
elseif ($acao == 'reprovar') { $novo_status = 'reprovado'; } 
else { header("Location: gerenciar_lares.php"); exit(); }

// ### CORREÇÃO AQUI: Adicionamos o 'gerenciado_por_id' ###
$sql = "UPDATE lares_temporarios SET status_aprovacao = :status, gerenciado_por_id = :id_funcionario WHERE id = :id";
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([':status' => $novo_status, ':id_funcionario' => $id_funcionario_logado, ':id' => $id_lar]);
    header("Location: gerenciar_lares.php");
    exit();
} catch (PDOException $e) {
    die("Erro ao atualizar o status: " . $e->getMessage());
}
?>