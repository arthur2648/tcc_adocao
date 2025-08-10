<?php
// O header já inicia a sessão
require_once 'header.php';
require_once 'conexao.php'; // Inclui a conexão

// Segurança: Garante que apenas utilizadores LOGADOS possam ver esta página
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario_logado = $_SESSION['usuario_id'];
$mensagem = "";
$mensagem_tipo = "sucesso"; // Por padrão, a mensagem é de sucesso

// Lógica para ATUALIZAR os dados quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Atualizar dados básicos
    if (isset($_POST['salvar_dados'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone']; // ### CAMPO ADICIONADO ###
        $endereco = $_POST['endereco'];

        // ### TELEFONE ADICIONADO AO UPDATE ###
        $sql = "UPDATE usuarios SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([':nome' => $nome, ':email' => $email, ':telefone' => $telefone, ':endereco' => $endereco, ':id' => $id_usuario_logado])) {
            $_SESSION['usuario_nome'] = $nome; // Atualiza o nome na sessão também!
            $mensagem = "Dados atualizados com sucesso!";
            $mensagem_tipo = "sucesso";
        } else {
            $mensagem = "Erro ao atualizar os dados.";
            $mensagem_tipo = "erro";
        }
    }
    // Atualizar a senha
    if (isset($_POST['salvar_senha'])) {
        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];
        $confirma_senha = $_POST['confirma_senha'];

        // Busca o utilizador para verificar a senha atual
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
                    $mensagem = "Palavra-passe alterada com sucesso!";
                    $mensagem_tipo = "sucesso";
                } else {
                    $mensagem = "Erro ao alterar a palavra-passe.";
                    $mensagem_tipo = "erro";
                }
            } else {
                $mensagem = "A nova palavra-passe e a confirmação não são iguais.";
                $mensagem_tipo = "erro";
            }
        } else {
            $mensagem = "A palavra-passe atual está incorreta.";
            $mensagem_tipo = "erro";
        }
    }
}

// ### TELEFONE ADICIONADO À BUSCA ###
// Busca os dados atuais do utilizador para preencher o formulário
$stmt = $pdo->prepare("SELECT nome, email, telefone, endereco FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id_usuario_logado]);
$usuario_atual = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<title>Editar Meu Perfil</title>

<main class="container">
    <div class="form-page-container">
        <h1>Editar Meu Perfil</h1>
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem <?php echo $mensagem_tipo; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form action="editar_perfil.php" method="POST">
            <h3>Meus Dados</h3>
            <div class="form-group">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario_atual['nome']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario_atual['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telemóvel (com indicativo):</label>
                <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($usuario_atual['telefone']); ?>" required>
            </div>
            <div class="form-group">
                <label for="endereco">Morada:</label>
                <textarea id="endereco" name="endereco" rows="3"><?php echo htmlspecialchars($usuario_atual['endereco']); ?></textarea>
            </div>
            <button type="submit" name="salvar_dados" class="btn">Salvar Dados</button>
        </form>

        <hr style="margin: 40px 0;">

        <form action="editar_perfil.php" method="POST">
            <h3>Alterar Palavra-passe</h3>
            <div class="form-group">
                <label for="senha_atual">Palavra-passe Atual:</label>
                <input type="password" id="senha_atual" name="senha_atual">
            </div>
            <div class="form-group">
                <label for="nova_senha">Nova Palavra-passe:</label>
                <input type="password" id="nova_senha" name="nova_senha">
            </div>
            <div class="form-group">
                <label for="confirma_senha">Confirmar Nova Palavra-passe:</label>
                <input type="password" id="confirma_senha" name="confirma_senha">
            </div>
            <button type="submit" name="salvar_senha" class="btn">Alterar Palavra-passe</button>
        </form>
    </div>
</main>

<?php
require_once 'footer.php';
?>