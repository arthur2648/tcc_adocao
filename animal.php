<?php
require_once 'conexao.php';
require_once 'inicializar.php';
$mensagem = "";
if (!isset($_GET['id']) || empty($_GET['id'])) { header("Location: index.php"); exit(); }
$id_animal = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome']; $email = $_POST['email']; $telefone = $_POST['telefone']; $mensagem_interesse = $_POST['mensagem'];
    if (empty($nome) || empty($email) || empty($telefone)) {
        $mensagem = "<div class='mensagem erro'>Por favor, preencha todos os campos obrigatórios.</div>";
    } else {
        $sql_insert = "INSERT INTO adocoes (animal_id, interessado_nome, interessado_email, interessado_telefone, mensagem) VALUES (:animal_id, :nome, :email, :telefone, :mensagem)";
        try {
            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->execute([':animal_id' => $id_animal, ':nome' => $nome, ':email' => $email, ':telefone' => $telefone, ':mensagem' => $mensagem_interesse]);
            $mensagem = "<div class='mensagem sucesso'>O seu interesse foi registado com sucesso! A nossa equipa entrará em contacto em breve. Obrigado!</div>";
        } catch (PDOException $e) { $mensagem = "<div class='mensagem erro'>Ocorreu um erro ao registar o seu interesse. Tente novamente.</div>"; }
    }
}
$sql = "SELECT * FROM animais WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_animal]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$animal) { header("Location: index.php"); exit(); }
$pagina_atual = "Conheça " . htmlspecialchars($animal['nome']);
require_once 'header.php';
?>
<main class="container">
    <div class="animal-detail-section">
        <div><img src="<?php echo htmlspecialchars($animal['foto_url']); ?>" alt="Foto de <?php echo htmlspecialchars($animal['nome']); ?>" class="animal-detail-img"></div>
        <div class="animal-detail-info">
            <h1><?php echo htmlspecialchars($animal['nome']); ?></h1>
            <p class="description"><?php echo nl2br(htmlspecialchars($animal['descricao'])); ?></p>
            <h3>Ficha Técnica</h3>
            <table class="info-table">
                <tr><td>Espécie:</td><td><strong><?php echo htmlspecialchars($animal['especie']); ?></strong></td></tr>
                <tr><td>Sexo:</td><td><strong><?php echo htmlspecialchars($animal['sexo']); ?></strong></td></tr>
                <tr><td>Idade:</td><td><strong><?php echo htmlspecialchars($animal['idade']); ?> anos</strong></td></tr>
                <tr><td>Raça:</td><td><strong><?php echo htmlspecialchars($animal['raca']); ?></strong></td></tr>
                <tr><td>Cor:</td><td><strong><?php echo htmlspecialchars($animal['cor']); ?></strong></td></tr>
                <tr><td>Vacinação:</td><td><strong><?php echo htmlspecialchars($animal['vacinado']); ?></strong></td></tr>
            </table>
            <div class="adoption-form-container">
                <h3>Manifeste o seu Interesse!</h3>
                <?php echo $mensagem; ?>
                <form action="animal.php?id=<?php echo $animal['id']; ?>" method="POST">
                    <div class="form-group"><label for="nome">O seu Nome Completo:</label><input type="text" id="nome" name="nome" required></div>
                    <div class="form-group"><label for="email">O seu Melhor Email:</label><input type="email" id="email" name="email" required></div>
                    <div class="form-group"><label for="telefone">O seu Telefone (com DDD):</label><input type="text" id="telefone" name="telefone" required></div>
                    <div class="form-group"><label for="mensagem">Mensagem (opcional):</label><textarea name="mensagem" id="mensagem" rows="3" placeholder="Conte-nos um pouco sobre si e por que quer adotar."></textarea></div>
                    <button type="submit" class="btn" style="width: 100%;">Enviar Interesse na Adoção</button>
                </form>
            </div>
        </div>
    </section>
</main>
<?php require_once 'footer.php'; ?>