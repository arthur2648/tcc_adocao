<?php
// (Lógica PHP - com adição do campo 'endereco')
require_once 'conexao.php';
$mensagem = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $endereco = $_POST['endereco']; // Novo campo

    if (empty($nome) || empty($email) || empty($senha) || empty($endereco)) {
        $mensagem = "Todos os campos são obrigatórios!";
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        // SQL atualizado para incluir o endereço
        $sql = "INSERT INTO usuarios (nome, email, senha, endereco) VALUES (:nome, :email, :senha, :endereco)";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':senha' => $senha_hash,
                ':endereco' => $endereco // Novo valor
            ]);
            $mensagem = "Usuário cadastrado com sucesso!";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $mensagem = "Erro: Este e-mail já está cadastrado.";
            } else {
                $mensagem = "Erro ao cadastrar o usuário: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário - Site de Adoção</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .container { background-color: white; padding: 20px 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #333; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; color: #555; }
        .input-group input, .input-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        .btn:hover { background-color: #0056b3; }
        .mensagem { text-align: center; padding: 10px; border-radius: 5px; margin-bottom: 15px; color: white; }
        .sucesso { background-color: #28a745; }
        .erro { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Crie sua Conta</h2>
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem <?php echo (strpos($mensagem, 'sucesso') !== false) ? 'sucesso' : 'erro'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        <form action="registro.php" method="POST">
            <div class="input-group">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="endereco">Endereço Completo:</label>
                <textarea id="endereco" name="endereco" rows="3" required></textarea>
            </div>
            <div class="input-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn">Cadastrar</button>
        </form>
    </div>
</body>
</html>