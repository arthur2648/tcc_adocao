<?php 
$pagina_atual = 'Cadastrar Animal';
require_once 'header_admin.php'; 

// Lógica PHP de processamento do formulário (sem alterações)
$mensagem = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $especie = $_POST['especie'];
    $raca = $_POST['raca'];
    $sexo = $_POST['sexo'];
    $cor = $_POST['cor'];
    $idade = $_POST['idade'];
    $vacinado = $_POST['vacinado'];
    $descricao = $_POST['descricao'];
    $foto_url = '';
    $id_funcionario_logado = $_SESSION['usuario_id'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $diretorio_uploads = 'uploads/';
        $nome_foto = uniqid() . '_' . basename($_FILES['foto']['name']);
        $caminho_foto = $diretorio_uploads . $nome_foto;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_foto)) {
            $foto_url = $caminho_foto;
        } else {
            $mensagem = "Erro ao fazer upload da foto.";
        }
    }

    if (empty($mensagem)) {
        $sql = "INSERT INTO animais (nome, especie, raca, sexo, cor, idade, vacinado, descricao, foto_url, status, cadastrado_por_id) 
                VALUES (:nome, :especie, :raca, :sexo, :cor, :idade, :vacinado, :descricao, :foto_url, 'disponivel', :id_funcionario)";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':nome' => $nome, ':especie' => $especie, ':raca' => $raca, ':sexo' => $sexo, ':cor' => $cor, ':idade' => $idade, ':vacinado' => $vacinado, ':descricao' => $descricao, ':foto_url' => $foto_url, ':id_funcionario' => $id_funcionario_logado]);
            $mensagem = "Animal cadastrado com sucesso!";
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar o animal: " . $e->getMessage();
        }
    }
}
?>

<title><?php echo $pagina_atual; ?></title>

<style>
    .form-container { max-width: 800px; margin: auto; }
    .form-section { margin-bottom: 25px; }
    .form-section h4 { 
        font-family: var(--font-heading); 
        color: var(--primary-color); 
        border-bottom: 2px solid var(--border-color); 
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .input-group { margin-bottom: 15px; }
    .input-group label { display: block; margin-bottom: 5px; font-weight: 600; }
    .input-group input, .input-group select, .input-group textarea { 
        width: 100%; 
        padding: 10px; 
        border: 1px solid var(--border-color); 
        border-radius: 5px; 
        box-sizing: border-box; 
    }
    textarea { resize: vertical; height: 120px; }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .grid-full-width {
        grid-column: 1 / -1; /* Ocupa a largura total da grelha */
    }
    .mensagem { text-align: center; padding: 10px; border-radius: 5px; margin-bottom: 15px; color: white; }
    .sucesso { background-color: #28a745; }
    .erro { background-color: #dc3545; }
</style>

<div class="content-box">
    <div class="form-container">
        <h2>Cadastrar Novo Animal para Adoção</h2>

        <?php if (!empty($mensagem)): ?>
            <div class="mensagem <?php echo (strpos($mensagem, 'sucesso') !== false) ? 'sucesso' : 'erro'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
        <form action="cadastrar_animal.php" method="POST" enctype="multipart/form-data">

            <div class="form-section">
                <h4>Dados Básicos</h4>
                <div class="input-group">
                    <label for="nome">Nome do Animal:</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div class="form-grid">
                    <div class="input-group">
                        <label for="especie">Espécie:</label>
                        <select id="especie" name="especie" required>
                            <option value="">Selecione</option>
                            <option value="Cachorro">Cachorro</option>
                            <option value="Gato">Gato</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="sexo">Sexo:</label>
                        <select id="sexo" name="sexo">
                            <option value="">Selecione</option>
                            <option value="Macho">Macho</option>
                            <option value="Fêmea">Fêmea</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="raca">Raça:</label>
                        <input type="text" id="raca" name="raca">
                    </div>
                    <div class="input-group">
                        <label for="idade">Idade (anos):</label>
                        <input type="number" id="idade" name="idade">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4>Saúde e Aparência</h4>
                <div class="form-grid">
                    <div class="input-group">
                        <label for="cor">Cor Predominante:</label>
                        <input type="text" id="cor" name="cor">
                    </div>
                    <div class="input-group">
                        <label for="vacinado">Status da Vacinação:</label>
                        <select id="vacinado" name="vacinado">
                            <option value="Não">Não Vacinado</option>
                            <option value="Parcialmente">Parcialmente Vacinado</option>
                            <option value="Sim">Totalmente Vacinado</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h4>Informações Adicionais</h4>
                <div class="input-group grid-full-width">
                    <label for="descricao">Descrição (história, personalidade, etc.):</label>
                    <textarea id="descricao" name="descricao"></textarea>
                </div>
                <div class="input-group grid-full-width">
                    <label for="foto">Foto do Animal:</label>
                    <input type="file" id="foto" name="foto">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; padding: 15px; font-size: 1.1em;">Cadastrar Animal</button>
        </form>
    </div>
</div>

<?php 
require_once 'footer_admin.php'; 
?>