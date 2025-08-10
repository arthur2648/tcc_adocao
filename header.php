<?php
// O nosso inicializador mestre garante que a sessão comece sempre da forma correta.
require_once 'inicializar.php'; 

// LÓGICA INTELIGENTE: Descobre qual é a página atual automaticamente
// basename($_SERVER['SCRIPT_NAME']) pega o nome do ficheiro atual (ex: "index.php", "lares.php")
$pagina_atual_arquivo = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo isset($pagina_atual) ? htmlspecialchars($pagina_atual) : 'AdoteUmAmigo'; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Nunito:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="public-header">
        <a href="index.php" class="logo">Adote<span>Um</span>Amigo</a>
        <nav class="main-nav">
            <a href="index.php" class="<?php if ($pagina_atual_arquivo == 'index.php') echo 'active'; ?>">Adote</a>
            <a href="lares.php" class="<?php if ($pagina_atual_arquivo == 'lares.php') echo 'active'; ?>">Lares Temporários</a>

            <?php if (isset($_SESSION['usuario_id'])): ?>
                <?php // O utilizador ESTÁ logado. Verificamos o seu tipo.
                    $user_type = $_SESSION['usuario_tipo'];
                    if (in_array($user_type, ['rh', 'admin'])) {
                        echo '<a href="painel_rh.php"><strong>Painel</strong></a>';
                    } elseif (in_array($user_type, ['funcionario', 'gerente'])) {
                        echo '<a href="painel.php"><strong>Painel</strong></a>';
                    } else { // Se for 'cliente'
                        echo '<a href="meu_perfil.php" class="' . ($pagina_atual_arquivo == 'meu_perfil.php' ? 'active' : '') . '">Meu Perfil</a>';
                    }
                ?>
                <a href="logout.php">Sair</a>
            <?php else: ?>
                <?php // O utilizador NÃO está logado. ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
            
        </nav>
    </header>
