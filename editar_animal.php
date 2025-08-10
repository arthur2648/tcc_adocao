<?php 
$pagina_atual = 'Editar Animal';
require_once 'header_admin.php'; 

// Segurança
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_tipo'], ['funcionario', 'gerente', 'admin'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['id'])) {
    header("Location: gerenciar_animais.php");
    exit();
}
$id_animal = $_GET['id'];

// Lógica de UPDATE quando o formulário for enviado (AGORA COM OS NOVOS CAMPOS)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $especie = $_POST['especie'];
    $raca = $_POST['raca'];
    $sexo = $_POST['sexo']; // Novo
    $cor = $_POST['cor'];   // Novo
    $idade = $_POST['idade'];
    $vacinado = $_POST['vacinado']; // Novo
    $descricao = $_POST['descricao'];
    $status = $_POST['status'];

    // Prepara e executa o comando UPDATE com as novas colunas
    $sql = "UPDATE animais SET nome = :nome, especie = :especie, raca = :raca, sexo = :sexo, cor = :cor, idade = :idade, vacinado = :vacinado, descricao = :descricao, status = :status WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':especie' => $especie,
        ':raca' => $raca,
        ':sexo' => $sexo,
        ':cor' => $cor,
        ':idade' => $idade,
        ':vacinado' => $vacinado,
        ':descricao' => $descricao,
        ':status' => $status,
        ':id' => $id_animal
    ]);

    header("Location: gerenciar_animais.php");
    exit();
}

// Busca os dados atuais do animal para preencher o formulário
$sql_select = "SELECT * FROM animais WHERE id = :id";
$stmt_select = $pdo->prepare($sql_select);
$stmt_select->execute([':id' => $id_animal]);
$animal = $stmt_select->fetch(PDO::FETCH_ASSOC);

if (!$animal) {
    header("Location: gerenciar_animais.php");
    exit();
}
?>
<title>Editando <?php echo htmlspecialchars($animal['nome']); ?></title>
<style>
    .form-container { max-width: 800px; margin: auto; }
    .input-group { margin-bottom: 15px; }
    .input-group label { display: block; margin-bottom: 5px; font-weight: 600; }
    .input-group input, .input-group select, .input-group textarea { width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 5px; box-sizing: border-box; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    textarea { resize: vertical; height: 120px; }
</style>

<div class="content-box">
    <div class="form-container">
        <h2>Editando Ficha de: <?php echo htmlspecialchars($animal['nome']); ?></h2>
        
        <form action="editar_animal.php?id=<?php echo $animal['id']; ?>" method="POST">
            <div class="form-grid">
                <div class="input-group">
                    <label for="nome">Nome do Animal:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($animal['nome']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="especie">Espécie:</label>
                    <select id="especie" name="especie" required>
                        <option value="Cachorro" <?php if($animal['especie'] == 'Cachorro') echo 'selected'; ?>>Cachorro</option>
                        <option value="Gato" <?php if($animal['especie'] == 'Gato') echo 'selected'; ?>>Gato</option>
                        <option value="Outro" <?php if($animal['especie'] == 'Outro') echo 'selected'; ?>>Outro</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="raca">Raça:</label>
                    <input type="text" id="raca" name="raca" value="<?php echo htmlspecialchars($animal['raca']); ?>">
                </div>
                <div class="input-group">
                    <label for="cor">Cor Predominante:</label>
                    <input type="text" id="cor" name="cor" value="<?php echo htmlspecialchars($animal['cor']); ?>">
                </div>
                <div class="input-group">
                    <label for="sexo">Sexo:</label>
                    <select id="sexo" name="sexo">
                        <option value="Macho" <?php if($animal['sexo'] == 'Macho') echo 'selected'; ?>>Macho</option>
                        <option value="Fêmea" <?php if($animal['sexo'] == 'Fêmea') echo 'selected'; ?>>Fêmea</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="idade">Idade (anos):</label>
                    <input type="number" id="idade" name="idade" value="<?php echo htmlspecialchars($animal['idade']); ?>">
                </div>
            </div>

            <div class="input-group">
                <label for="vacinado">Status da Vacinação:</label>
                <select id="vacinado" name="vacinado">
                    <option value="Não" <?php if($animal['vacinado'] == 'Não') echo 'selected'; ?>>Não Vacinado</option>
                    <option value="Parcialmente" <?php if($animal['vacinado'] == 'Parcialmente') echo 'selected'; ?>>Parcialmente Vacinado</option>
                    <option value="Sim" <?php if($animal['vacinado'] == 'Sim') echo 'selected'; ?>>Totalmente Vacinado</option>
                </select>
            </div>

            <div class="input-group">
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao"><?php echo htmlspecialchars($animal['descricao']); ?></textarea>
            </div>

            <div class="input-group">
                <label for="status">Status de Adoção:</label>
                <select id="status" name="status" required>
                    <option value="disponivel" <?php if($animal['status'] == 'disponivel') echo 'selected'; ?>>Disponível</option>
                    <option value="em_processo" <?php if($animal['status'] == 'em_processo') echo 'selected'; ?>>Em Processo de Adoção</option>
                    <option value="adotado" <?php if($animal['status'] == 'adotado') echo 'selected'; ?>>Adotado</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success" style="width:100%;">Salvar Alterações</button>
        </form>
        <div class="nav-link" style="text-align: center; margin-top: 15px;">
            <p><a href="gerenciar_animais.php">Cancelar e Voltar</a></p>
        </div>
    </div>
</div>

<?php require_once 'footer_admin.php'; ?>