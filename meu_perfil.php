<?php
// O header já inicia a sessão
require_once 'header.php';

// Segurança: Garante que apenas usuários LOGADOS possam ver esta página
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>

<title>Meu Perfil - AdoteUmAmigo</title>

<main class="container">
    <div style="text-align: center; padding: 60px 20px; background-color: #fff; border-radius: 15px; box-shadow: var(--shadow); margin-top: 40px;">
        
        <h1 style="font-family: var(--font-heading); font-size: 2.5em;">Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</h1>
        
        <p style="font-size: 1.2em; color: #555;">Este é o seu painel pessoal.</p>
        
        <p style="color: #777; max-width: 600px; margin: 15px auto 25px auto;">
            Em breve, você poderá ver aqui os animais que adotou e acompanhar o status dos seus processos de adoção.
        </p>

        <a href="editar_perfil.php" style="display: inline-block; padding: 12px 30px; background-color: var(--accent-color); color: white; text-decoration: none; border-radius: 50px; font-weight: 700; font-family: var(--font-heading);">Editar Meus Dados</a>

    </div>
</main>

<?php
// Puxa o rodapé
require_once 'footer.php';
?>