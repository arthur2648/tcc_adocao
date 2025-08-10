<?php 
$pagina_atual = 'Cadastrar Funcionário';
require_once 'header_admin.php'; 

// SEGURANÇA ATUALIZADA: Apenas 'rh' e 'admin' podem acessar
if (!in_array($_SESSION['usuario_tipo'], ['rh', 'admin'])) {
    echo "<script>alert('Acesso negado! Você não tem permissão para cadastrar funcionários.'); window.location.href = 'painel.php';</script>";
    exit();
}

// O resto do código continua exatamente o mesmo...
$mensagem = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Dados para a tabela USUARIOS
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo_usuario = 'funcionario';

    // Dados para a tabela FUNCIONARIOS_DETALHES
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $cep = $_POST['cep'];
    $salario_base = $_POST['salario_base'];
    $horario_entrada = $_POST['horario_entrada'];
    $horario_saida = $_POST['horario_saida'];
    $horas_almoco = $_POST['horas_almoco'];
    $valor_hora_extra = $_POST['valor_hora_extra'];
    $dias_de_trabalho = implode(',', $_POST['dias_de_trabalho']);
    
    $pdo->beginTransaction();
    try {
        // 1. INSERIR NA TABELA USUARIOS
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql_user = "INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES (:nome, :email, :senha, :tipo_usuario)";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([':nome' => $nome, ':email' => $email, ':senha' => $senha_hash, ':tipo_usuario' => $tipo_usuario]);
        $novo_usuario_id = $pdo->lastInsertId();

        // Lógica do Upload de Currículo
        $curriculo_url = '';
        if (isset($_FILES['curriculo']) && $_FILES['curriculo']['error'] == 0) {
            $diretorio_curriculos = 'curriculos/';
            if (!is_dir($diretorio_curriculos)) { mkdir($diretorio_curriculos, 0755, true); }
            $nome_curriculo = uniqid() . '_' . basename($_FILES['curriculo']['name']);
            $caminho_curriculo = $diretorio_curriculos . $nome_curriculo;
            if (move_uploaded_file($_FILES['curriculo']['tmp_name'], $caminho_curriculo)) {
                $curriculo_url = $caminho_curriculo;
            }
        }

        // 2. INSERIR NA TABELA FUNCIONARIOS_DETALHES
        $sql_details = "INSERT INTO funcionarios_detalhes (usuario_id, cpf, telefone, cep, salario_base, curriculo_url, horario_entrada_padrao, horario_saida_padrao, horas_almoco_padrao, valor_hora_extra_percentual, dias_de_trabalho) VALUES (:usuario_id, :cpf, :telefone, :cep, :salario_base, :curriculo_url, :horario_entrada, :horario_saida, :horas_almoco, :valor_hora_extra, :dias_de_trabalho)";
        $stmt_details = $pdo->prepare($sql_details);
        $stmt_details->execute([':usuario_id' => $novo_usuario_id, ':cpf' => $cpf, ':telefone' => $telefone, ':cep' => $cep, ':salario_base' => $salario_base, ':curriculo_url' => $curriculo_url, ':horario_entrada' => $horario_entrada, ':horario_saida' => $horario_saida, ':horas_almoco' => $horas_almoco, ':valor_hora_extra' => $valor_hora_extra, ':dias_de_trabalho' => $dias_de_trabalho]);
        
        $pdo->commit();
        $mensagem = "Funcionário cadastrado com sucesso!";

    } catch (PDOException $e) {
        $pdo->rollBack();
        $mensagem = "Erro ao cadastrar funcionário: " . $e->getMessage();
    }
}
?>
<title><?php echo $pagina_atual; ?></title>
<style>.form-container{max-width:800px;margin:auto}.input-group{margin-bottom:15px}.input-group label{display:block;margin-bottom:5px;font-weight:600}.input-group input,.input-group select{width:100%;padding:10px;border:1px solid var(--border-color);border-radius:5px;box-sizing:border-box}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}.dias-semana{display:flex;gap:10px;align-items:center}.mensagem{text-align:center;padding:10px;border-radius:5px;margin-bottom:15px;color:white}.sucesso{background-color:#28a745}.erro{background-color:#dc3545}</style>
<div class="content-box">
    <div class="form-container">
        <h2>Cadastrar Novo Funcionário</h2>
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem <?php echo (strpos($mensagem, 'sucesso') !== false) ? 'sucesso' : 'erro'; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>
        <form action="cadastrar_funcionario.php" method="POST" enctype="multipart/form-data">
            <h4>Dados Pessoais e de Acesso</h4>
            <div class="form-grid"><div class="input-group"><label for="nome">Nome Completo:</label><input type="text" id="nome" name="nome" required></div><div class="input-group"><label for="email">Email (para login):</label><input type="email" id="email" name="email" required></div><div class="input-group"><label for="cpf">CPF:</label><input type="text" id="cpf" name="cpf"></div><div class="input-group"><label for="telefone">Telefone:</label><input type="text" id="telefone" name="telefone"></div><div class="input-group"><label for="cep">CEP:</label><input type="text" id="cep" name="cep"></div><div class="input-group"><label for="senha">Senha Inicial:</label><input type="password" id="senha" name="senha" required></div></div>
            <h4>Dados Contratuais e de Jornada</h4>
            <div class="form-grid"><div class="input-group"><label for="salario_base">Salário Base (R$):</label><input type="number" step="0.01" id="salario_base" name="salario_base"></div><div class="input-group"><label for="valor_hora_extra">Valor Hora Extra (%):</label><input type="number" id="valor_hora_extra" name="valor_hora_extra" value="50"></div><div class="input-group"><label for="horario_entrada">Entrada Padrão:</label><input type="time" id="horario_entrada" name="horario_entrada" value="09:00"></div><div class="input-group"><label for="horario_saida">Saída Padrão:</label><input type="time" id="horario_saida" name="horario_saida" value="18:00"></div><div class="input-group"><label for="horas_almoco">Horas de Almoço:</label><input type="number" step="0.5" id="horas_almoco" name="horas_almoco" value="1.0"></div><div class="input-group"><label for="curriculo">Currículo (PDF, DOCX):</label><input type="file" id="curriculo" name="curriculo"></div></div>
            <h4>Dias de Trabalho</h4>
            <div class="input-group dias-semana"><label><input type="checkbox" name="dias_de_trabalho[]" value="1" checked> Seg</label><label><input type="checkbox" name="dias_de_trabalho[]" value="2" checked> Ter</label><label><input type="checkbox" name="dias_de_trabalho[]" value="3" checked> Qua</label><label><input type="checkbox" name="dias_de_trabalho[]" value="4" checked> Qui</label><label><input type="checkbox" name="dias_de_trabalho[]" value="5" checked> Sex</label><label><input type="checkbox" name="dias_de_trabalho[]" value="6"> Sáb</label><label><input type="checkbox" name="dias_de_trabalho[]" value="7"> Dom</label></div>
            <button type="submit" class="btn btn-primary" style="width:100%; margin-top: 20px;">Cadastrar Funcionário</button>
        </form>
    </div>
</div>
<?php require_once 'footer_admin.php'; ?>