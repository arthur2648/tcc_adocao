<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'conexao.php';
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_tipo'], ['funcionario', 'gerente', 'admin', 'rh'])) {
    header("Location: login.php");
    exit();
}
$nome_usuario = $_SESSION['usuario_nome'];
$tipo_usuario = $_SESSION['usuario_tipo'];
if (!isset($pagina_atual)) { $pagina_atual = ''; }
?>
<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pagina_atual; ?> - Painel ONG</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
<div class="admin-wrapper">
    <div class="sidebar" id="sidebar">
        <h2>Painel ONG</h2>
        <ul class="sidebar-menu">
            <?php if (in_array($tipo_usuario, ['rh', 'admin'])): ?>
                <li class="<?php if ($pagina_atual == 'Painel RH') echo 'active'; ?>"><a href="painel_rh.php"><i class="fas fa-tachometer-alt"></i> Dashboard RH</a></li>
                <li class="<?php if ($pagina_atual == 'Gerenciar Funcionários') echo 'active'; ?>"><a href="gerenciar_funcionarios.php"><i class="fas fa-users-cog"></i> Gerir Equipa</a></li>
                <li class="<?php if ($pagina_atual == 'Cadastrar Funcionário') echo 'active'; ?>"><a href="cadastrar_funcionario.php"><i class="fas fa-user-plus"></i> Cadastrar Funcionário</a></li>
            <?php else: ?>
                <li class="<?php if ($pagina_atual == 'Painel') echo 'active'; ?>"><a href="painel.php"><i class="fas fa-home"></i> Início</a></li>
                <li class="<?php if ($pagina_atual == 'Gerenciar Adoções') echo 'active'; ?>"><a href="gerenciar_adocoes.php"><i class="fas fa-heart"></i> Gerir Adoções</a></li>
                <li class="<?php if ($pagina_atual == 'Gerenciar Animais') echo 'active'; ?>"><a href="gerenciar_animais.php"><i class="fas fa-paw"></i> Gerir Animais</a></li>
                <li class="<?php if ($pagina_atual == 'Cadastrar Animal') echo 'active'; ?>"><a href="cadastrar_animal.php"><i class="fas fa-plus-circle"></i> Cadastrar Animal</a></li>
                <li class="<?php if ($pagina_atual == 'Gerenciar Lares') echo 'active'; ?>"><a href="gerenciar_lares.php"><i class="fas fa-hand-holding-heart"></i> Gerir Lares</a></li>
                <?php if ($tipo_usuario == 'gerente'): ?>
                    <li class="<?php if ($pagina_atual == 'Gerenciar Funcionários') echo 'active'; ?>"><a href="gerenciar_funcionarios.php"><i class="fas fa-users"></i> Ver Equipa</a></li>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (in_array($tipo_usuario, ['funcionario', 'gerente'])): ?>
                <li class="<?php if ($pagina_atual == 'Ponto Eletrônico') echo 'active'; ?>"><a href="ponto.php"><i class="fas fa-clock"></i> Meu Ponto</a></li>
            <?php endif; ?>
            
            <li><a href="index.php"><i class="fas fa-globe"></i> Ver Site Público</a></li>
        </ul>
    </div>
    <div class="main-content" id="main-content">
        <div class="top-header">
            <button class="menu-toggle" id="menu-toggle">☰</button>
            <div class="user-info">Bem-vindo(a), <?php echo htmlspecialchars($nome_usuario); ?>! (<?php echo $tipo_usuario; ?>)</div>
            <div class="logout"><a href="editar_perfil.php" style="margin-right: 20px; color: var(--accent-color);">Editar Perfil</a><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></div>
        </div>