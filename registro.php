<?php
// Inclui os ficheiros essenciais
require_once 'inicializar.php';
require_once 'conexao.php';

$mensagem = "";
// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pega todos os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha']; // Novo campo

    // Validação atualizada
    if (empty($nome) || empty($email) || empty($telefone) || empty($endereco) || empty($senha)) {
        $mensagem = "Todos os campos são obrigatórios!";
    } elseif ($senha !== $confirma_senha) {
        $mensagem = "As palavras-passe não coincidem!";
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (nome, email, telefone, endereco, senha) VALUES (:nome, :email, :telefone, :endereco, :senha)";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':telefone' => $telefone,
                ':endereco' => $endereco,
                ':senha' => $senha_hash
            ]);
            $mensagem = "Utilizador registado com sucesso! Agora já pode fazer login.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $mensagem = "Erro: Este e-mail já está registado.";
            } else {
                $mensagem = "Erro ao registar o utilizador: " . $e->getMessage();
            }
        }
    }
}

// Define o título da página
$pagina_atual = 'Registe a sua Conta - AdoteUmAmigo';
// Puxa o cabeçalho público
require_once 'header.php';
?>

<style>
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .grid-full-width {
        grid-column: 1 / -1; /* Ocupa a largura total da grelha */
    }
    /* Em telas pequenas, volta a ser uma coluna única */
    @media (max-width: 600px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container">
    <div class="form-page-container">
        <h1>Crie a sua Conta</h1>
        <p>Preencha os seus dados para começar o processo de adoção.</p>

        <?php if (!empty($mensagem)): ?>
            <div class="mensagem <?php echo (strpos($mensagem, 'sucesso') !== false) ? 'sucesso' : 'erro'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <div class="form-group grid-full-width">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="telefone">Nº de Telemóvel (com indicativo):</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="(+351) 9XX XXX XXX" required>
                </div>
            </div>

            <div class="form-group grid-full-width">
                <label for="endereco">Morada Completa:</label>
                <textarea id="endereco" name="endereco" rows="3" required></textarea>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="senha">Palavra-passe:</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                <div class="form-group">
                    <label for="confirma_senha">Confirmar Palavra-passe:</label>
                    <input type="password" id="confirma_senha" name="confirma_senha" required>
                </div>
            </div>
            
            <button type="submit" class="btn" style="width:100%; margin-top: 15px;">Registar</button>
        </form>
    </div>
</div>

<?php
// Puxa o rodapé público
require_once 'footer.php';
?>