<?php
require_once 'header.php';
require_once 'conexao.php';
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit(); }
$id_usuario_logado = $_SESSION['usuario_id'];
$tipo_usuario_logado = $_SESSION['usuario_tipo'];
$mensagem = ""; $mensagem_tipo = "sucesso";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['salvar_dados'])) {
        $nome = $_POST['nome']; $email = $_POST['email']; $telefone = $_POST['telefone']; $endereco = $_POST['endereco'];
        // Atualiza a tabela usuarios (comum a todos)
        $sql_user = "UPDATE usuarios SET nome = :nome, email = :email, endereco = :endereco WHERE id = :id";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([':nome' => $nome, ':email' => $email, ':endereco' => $endereco, ':id' => $id_usuario_logado]);
        
        // ### LÓGICA INTELIGENTE AQUI ###
        // Se o utilizador for funcionário ou superior, atualiza o telefone na tabela de detalhes
        if (in_array($tipo_usuario_logado, ['funcionario', 'gerente', 'rh', 'admin'])) {
            $sql_details = "UPDATE funcionarios_detalhes SET telefone = :telefone WHERE usuario_id = :id";
            $stmt_details = $pdo->prepare($sql_details);
            $stmt_details->execute([':telefone' => $telefone, ':id' => $id_usuario_logado]);
        } else { // Se for cliente, atualiza na tabela usuarios
            $sql_client = "UPDATE usuarios SET telefone = :telefone WHERE id = :id";
            $stmt_client = $pdo->prepare($sql_client);
            $stmt_client->execute([':telefone' => $telefone, ':id' => $id_usuario_logado]);
        }
        
        $_SESSION['usuario_nome'] = $nome;
        $mensagem = "Dados atualizados com sucesso!";
    }
    // (A lógica da palavra-passe continua a mesma)
}

// ### BUSCA INTELIGENTE AQUI ###
$sql = "";
if (in_array($tipo_usuario_logado, ['funcionario', 'gerente', 'rh', 'admin'])) {
    // Se for funcionário, busca com JOIN
    $sql = "SELECT u.nome, u.email, u.endereco, d.telefone FROM usuarios u LEFT JOIN funcionarios_detalhes d ON u.id = d.usuario_id WHERE u.id = :id";
} else {
    // Se for cliente, busca normal
    $sql = "SELECT nome, email, endereco, telefone FROM usuarios WHERE id = :id";
}
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_usuario_logado]);
$usuario_atual = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<title>Editar Meu Perfil</title>
<main class="container">
    <div class="form-page-container">
        <h1>Editar Meu Perfil</h1>
        <?php if (!empty($mensagem)): ?><div class="mensagem <?php echo $mensagem_tipo; ?>"><?php echo $mensagem; ?></div><?php endif; ?>
        <form action="editar_perfil.php" method="POST">
            <h3>Meus Dados</h3>
            <div class="form-group"><label for="nome">Nome Completo:</label><input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario_atual['nome']); ?>" required></div>
            <div class="form-group"><label for="email">Email:</label><input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario_atual['email']); ?>" required></div>
            <div class="form-group"><label for="telefone">Telemóvel (com indicativo):</label><input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($usuario_atual['telefone']); ?>" required></div>
            <div class="form-group"><label for="endereco">Morada:</label><textarea id="endereco" name="endereco" rows="3"><?php echo htmlspecialchars($usuario_atual['endereco']); ?></textarea></div>
            <button type="submit" name="salvar_dados" class="btn">Salvar Dados</button>
        </form>
        <hr style="margin: 40px 0;">
        <form action="editar_perfil.php" method="POST">
            <h3>Alterar Palavra-passe</h3>
            <div class="form-group"><label for="senha_atual">Palavra-passe Atual:</label><input type="password" id="senha_atual" name="senha_atual"></div>
            <div class="form-group"><label for="nova_senha">Nova Palavra-passe:</label><input type="password" id="nova_senha" name="nova_senha"></div>
            <div class="form-group"><label for="confirma_senha">Confirmar Nova Palavra-passe:</label><input type="password" id="confirma_senha" name="confirma_senha"></div>
            <button type="submit" name="salvar_senha" class="btn">Alterar Palavra-passe</button>
        </form>
    </div>
</main>
<?php require_once 'footer.php'; ?>