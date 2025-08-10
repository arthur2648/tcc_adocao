<?php
// O header já inicia a sessão
require_once 'header.php';
require_once 'conexao.php'; // Inclui a conexão

// Segurança: Garante que apenas usuários LOGADOS possam ver esta página
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario_logado = $_SESSION['usuario_id'];
$mensagem = "";

// Lógica para ATUALIZAR os dados quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Atualizar dados básicos
    if (isset($_POST['salvar_dados'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $endereco = $_POST['endereco'];

        $sql = "UPDATE usuarios SET nome = :nome, email = :email, endereco = :endereco WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([':nome' => $nome, ':email' => $email, ':endereco' => $endereco, ':id' => $id_usuario_logado])) {
            $_SESSION['usuario_nome'] = $nome; // Atualiza o nome na sessão também!
            $mensagem = "Dados atualizados com sucesso!";
        } else {
            $mensagem = "Erro ao atualizar os dados.";
        }
    }
    // Atualizar a senha
    if (isset($_POST['salvar_senha'])) {
        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];
        $confirma_senha = $_POST['confirma_senha'];

        // Busca o usuário para verificar a senha atual
        $sql_senha = "SELECT senha FROM usuarios WHERE id = :id";
        $stmt_senha = $pdo->prepare($sql_senha);
        $stmt_senha->execute([':id' => $id_usuario_logado]);
        $usuario = $stmt_senha->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha_atual, $usuario['senha'])) {
            if ($nova_senha == $confirma_senha) {
                $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $sql_update_senha = "UPDATE usuarios SET senha = :senha WHERE id = :id";
                $stmt_update_senha = $pdo->prepare($sql_update_senha);
                if ($stmt_update_senha->execute([':senha' => $nova_senha_hash, ':id' => $id_usuario_logado])) {
                    $mensagem = "Senha alterada com sucesso!";
                } else {
                    $mensagem = "Erro ao alterar a senha.";
                }
            } else {
                $mensagem = "A nova senha e a confirmação não são iguais.";
            }
        } else {
            $mensagem = "A senha atual está incorreta.";
        }
    }
}

// Busca os dados atuais do usuário para preencher o formulário
$stmt = $pdo->prepare("SELECT nome, email, endereco FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id_usuario_logado]);
$usuario_atual = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<title>Editar Meu Perfil</title>

<style>
    .form-container { max-width: 600px; margin: 40px auto; padding: 40px; background-color: var(--white-color); box-shadow: var(--shadow); border-radius: 15px; }
    .form-container h1 { font-family: var(--font-heading); text-align: center; margin-bottom: 25px; }
    .input-group { margin-bottom: 15px; }
    .input-group label { display: block; margin-bottom: 5px; font-weight: 600; }
    .input-group input, .input-group textarea { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 5px; box-sizing: border-box; }
    .btn-submit { width: 100%; padding: 12px; background-color: var(--primary-color); color: white; border: none; border-radius: 50px; font-size: 16px; font-weight: 700; cursor: pointer; transition: background-color 0.2s; }
    .btn-submit:hover { background-color: #e56a3f; }
    .mensagem { text-align: center; padding: 10px; border-radius: 5px; margin-bottom: 15px; color: white; background-color: #28a745; }
</style>

<main class="container">
    <div class="form-container">
        <h1>Editar Meu Perfil</h1>
        <?php if (!empty($mensagem)): ?>
            <p class="mensagem"><?php echo $mensagem; ?></p>
        <?php endif; ?>

        <form action="editar_perfil.php" method="POST">
            <h3>Meus Dados</h3>
            <div class="input-group">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario_atual['nome']); ?>" required>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario_atual['email']); ?>" required>
            </div>
            <div class="input-group">
                <label for="endereco">Endereço:</label>
                <textarea id="endereco" name="endereco" rows="3"><?php echo htmlspecialchars($usuario_atual['endereco']); ?></textarea>
            </div>
            <button type="submit" name="salvar_dados" class="btn-submit">Salvar Dados</button>
        </form>

        <hr style="margin: 30px 0;">

        <form action="editar_perfil.php" method="POST">
            <h3>Alterar Senha</h3>
            <div class="input-group">
                <label for="senha_atual">Senha Atual:</label>
                <input type="password" id="senha_atual" name="senha_atual" required>
            </div>
            <div class="input-group">
                <label for="nova_senha">Nova Senha:</label>
                <input type="password" id="nova_senha" name="nova_senha" required>
            </div>
            <div class="input-group">
                <label for="confirma_senha">Confirmar Nova Senha:</label>
                <input type="password" id="confirma_senha" name="confirma_senha" required>
            </div>
            <button type="submit" name="salvar_senha" class="btn-submit">Alterar Senha</button>
        </form>
    </div>
</main>

<?php
require_once 'footer.php';
?>