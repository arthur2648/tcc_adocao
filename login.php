<?php
// O nosso inicializador mestre garante que a sessão comece sempre da forma correta.
require_once 'inicializar.php';
require_once 'conexao.php';

// Se o utilizador já estiver logado, redireciona para a página certa
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

// Puxa o cabeçalho público
require_once 'header.php';
?>
<title>Login - AdoteUmAmigo</title>

<style>
    .login-container {
        max-width: 400px;
        margin: 50px auto;
        padding: 40px;
        background-color: var(--white-color);
        box-shadow: var(--shadow);
        border-radius: var(--border-radius);
    }
    .login-container h1 {
        font-family: var(--font-heading);
        text-align: center;
        margin-bottom: 25px;
    }
    .input-group { margin-bottom: 15px; }
    .input-group label { display: block; margin-bottom: 5px; font-weight: 600; }
    .input-group input { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; box-sizing: border-box; }
    .btn-login { width: 100%; padding: 12px; background-color: var(--primary-color); color: white; border: none; border-radius: 50px; font-size: 16px; font-weight: 700; cursor: pointer; transition: background-color 0.2s; }
    .btn-login:hover { background-color: var(--primary-hover); }
    .mensagem-erro { color: #c0392b; text-align: center; margin-bottom: 15px; }
    /* ### NOVO ESTILO AQUI ### */
    .link-cadastro {
        text-align: center;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }
    .link-cadastro a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 700;
    }
    .link-cadastro a:hover {
        text-decoration: underline;
    }
</style>

<div class="container">
    <div class="login-container">
        <h1>Acesse sua Conta</h1>

        <?php if (!empty($mensagem)): ?>
            <p class="mensagem-erro"><?php echo $mensagem; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>

        <div class="link-cadastro">
            <p>Não tem uma conta? <a href="registro.php">Registe-se aqui</a></p>
        </div>

    </div>
</div>

<?php
// Puxa o rodapé público
require_once 'footer.php';
?>