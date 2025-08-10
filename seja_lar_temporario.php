<?php
// (Lógica PHP - com adição do campo 'cpf')
require_once 'conexao.php';
$mensagem = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $cpf = $_POST['cpf']; // Novo campo
    $endereco = $_POST['endereco'];
    $tipo_animal = $_POST['tipo_animal'];

    if (empty($nome) || empty($email) || empty($telefone) || empty($endereco) || empty($cpf)) {
        $mensagem = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        // SQL atualizado para incluir o CPF
        $sql = "INSERT INTO lares_temporarios (nome_responsavel, email_contato, telefone, cpf, endereco, tipo_animal_aceito) VALUES (:nome, :email, :telefone, :cpf, :endereco, :tipo_animal)";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':telefone' => $telefone,
                ':cpf' => $cpf, // Novo valor
                ':endereco' => $endereco,
                ':tipo_animal' => $tipo_animal
            ]);
            $mensagem = "Sua solicitação foi enviada com sucesso! Entraremos em contato em breve. Muito obrigado!";
        } catch (PDOException $e) {
            $mensagem = "Ocorreu um erro ao enviar sua solicitação. Tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seja um Lar Temporário</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .container { background-color: white; padding: 20px 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 100%; max-width: 600px; }
        h2 { text-align: center; color: #333; }
        .intro-text { text-align: center; color: #555; margin-bottom: 20px; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; color: #555; }
        .input-group input, .input-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background-color: #ffc107; color: #212529; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold; }
        .btn:hover { background-color: #e0a800; }
        .mensagem { text-align: center; padding: 10px; border-radius: 5px; margin-bottom: 15px; color: white; }
        .sucesso { background-color: #28a745; }
        .erro { background-color: #dc3545; }
        .nav-link { text-align: center; margin-top: 15px; }
        .nav-link a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Seja um Anjo na Vida de um Pet</h2>
        <p class="intro-text">Oferecer um lar temporário é um ato de amor que ajuda a salvar vidas. Preencha o formulário abaixo para se candidatar.</p>
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem <?php echo (strpos($mensagem, 'sucesso') !== false) ? 'sucesso' : 'erro'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        <form action="seja_lar_temporario.php" method="POST">
            <div class="input-group">
                <label for="nome">Seu Nome Completo:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="input-group">
                <label for="email">Seu Melhor Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="telefone">Seu Telefone (com DDD):</label>
                <input type="text" id="telefone" name="telefone" required>
            </div>
             <div class="input-group">
                <label for="cpf">Seu CPF:</label>
                <input type="text" id="cpf" name="cpf" required placeholder="000.000.000-00">
            </div>
            <div class="input-group">
                <label for="endereco">Seu Endereço Completo:</label>
                <textarea id="endereco" name="endereco" rows="3" required></textarea>
            </div>
            <div class="input-group">
                <label for="tipo_animal">Que tipo de animal você tem mais afinidade para acolher?</label>
                <input type="text" id="tipo_animal" name="tipo_animal" placeholder="Ex: Cães de porte pequeno, gatos, etc.">
            </div>
            <button type="submit" class="btn">Quero Ser um Lar Temporário</button>
        </form>
         <div class="nav-link">
            <p><a href="index.php">&larr; Voltar para o Início</a></p>
        </div>
    </div>
</body>
</html>