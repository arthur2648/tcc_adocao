<?php
// Agora, a primeira coisa que fazemos é chamar o nosso inicializador mestre
require_once 'inicializar.php';

require_once 'conexao.php';

// Se o usuário já estiver logado, redireciona para a página certa
if (isset($_SESSION['usuario_id'])) {
    if (in_array($_SESSION['usuario_tipo'], ['rh', 'admin'])) {
        header("Location: painel_rh.php");
    } elseif (in_array($_SESSION['usuario_tipo'], ['funcionario', 'gerente'])) {
        header("Location: painel.php");
    } else {
        header("Location: meu_perfil.php");
    }
    exit();
}

$mensagem = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_tipo'] = $usuario['tipo_usuario'];
        
        // Redireciona para o painel correto
        if (in_array($usuario['tipo_usuario'], ['rh', 'admin'])) {
            header("Location: painel_rh.php");
        } elseif (in_array($usuario['tipo_usuario'], ['funcionario', 'gerente'])) {
            header("Location: painel.php");
        } else {
            header("Location: meu_perfil.php");
        }
        exit();
    } else {
        $mensagem = "Email ou senha inválidos.";
    }
}

require_once 'header.php'; // O header PÚBLICO
?>
<title>Login - AdoteUmAmigo</title>
<style>.login-container{max-width:400px;margin:50px auto;padding:40px;background-color:var(--white-color);box-shadow:var(--shadow);border-radius:15px}.login-container h1{font-family:var(--font-heading);text-align:center;margin-bottom:25px}.input-group{margin-bottom:15px}.input-group label{display:block;margin-bottom:5px;font-weight:600}.input-group input{width:100%;padding:12px;border:1px solid var(--border-color);border-radius:5px;box-sizing:border-box}.btn-login{width:100%;padding:12px;background-color:var(--primary-color);color:white;border:none;border-radius:50px;font-size:16px;font-weight:700;cursor:pointer;transition:background-color .2s}.btn-login:hover{background-color:#e56a3f}.mensagem-erro{color:#dc3545;text-align:center;margin-bottom:15px}</style>
<div class="container"><div class="login-container"><h1>Acesse sua Conta</h1><?php if(!empty($mensagem)):?><p class="mensagem-erro"><?php echo $mensagem;?></p><?php endif;?><form action="login.php" method="POST"><div class="input-group"><label for="email">Email:</label><input type="email" id="email" name="email" required></div><div class="input-group"><label for="senha">Senha:</label><input type="password" id="senha" name="senha" required></div><button type="submit" class="btn-login">Entrar</button></form></div></div>
<?php require_once 'footer.php'; ?>