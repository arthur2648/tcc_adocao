<?php 
$pagina_atual = 'Editar Funcionário';
require_once 'header_admin.php'; 

// Segurança
if (!in_array($_SESSION['usuario_tipo'], ['rh', 'admin'])) {
    echo "<script>alert('Acesso negado!'); window.location.href = 'painel.php';</script>";
    exit();
}
if (!isset($_GET['id'])) { header("Location: gerenciar_funcionarios.php"); exit(); }
$id_funcionario = $_GET['id'];

// Lógica de UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome']; $email = $_POST['email']; $tipo_usuario = $_POST['tipo_usuario'];
    $telefone = $_POST['telefone']; // Pega o telefone do formulário
    $salario_base = $_POST['salario_base']; $horario_entrada = $_POST['horario_entrada']; $horario_saida = $_POST['horario_saida'];
    $valor_hora_extra = $_POST['valor_hora_extra'];
    $dias_de_trabalho = isset($_POST['dias_de_trabalho']) ? implode(',', $_POST['dias_de_trabalho']) : '';
    
    $pdo->beginTransaction();
    try {
        $sql_user = "UPDATE usuarios SET nome = :nome, email = :email, tipo_usuario = :tipo_usuario WHERE id = :id";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([':nome' => $nome, ':email' => $email, ':tipo_usuario' => $tipo_usuario, ':id' => $id_funcionario]);

        // ### CORREÇÃO AQUI: Atualiza o telefone na tabela funcionarios_detalhes ###
        $sql_details = "UPDATE funcionarios_detalhes SET telefone = :telefone, salario_base = :salario_base, horario_entrada_padrao = :horario_entrada, horario_saida_padrao = :horario_saida, valor_hora_extra_percentual = :valor_hora_extra, dias_de_trabalho = :dias_de_trabalho WHERE usuario_id = :id";
        $stmt_details = $pdo->prepare($sql_details);
        $stmt_details->execute([':telefone' => $telefone, ':salario_base' => $salario_base, ':horario_entrada' => $horario_entrada, ':horario_saida' => $horario_saida, ':valor_hora_extra' => $valor_hora_extra, ':dias_de_trabalho' => $dias_de_trabalho, ':id' => $id_funcionario]);
        
        $pdo->commit();
        header("Location: gerenciar_funcionarios.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $mensagem = "Erro ao atualizar funcionário: " . $e->getMessage();
    }
}

// ### CORREÇÃO AQUI: Busca o telefone da tabela funcionarios_detalhes ###
$sql = "SELECT u.id, u.nome, u.email, u.tipo_usuario, d.telefone, d.salario_base, d.horario_entrada_padrao, d.horario_saida_padrao, d.valor_hora_extra_percentual, d.dias_de_trabalho 
        FROM usuarios u 
        LEFT JOIN funcionarios_detalhes d ON u.id = d.usuario_id 
        WHERE u.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_funcionario]);
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$funcionario) { header("Location: gerenciar_funcionarios.php"); exit(); }
$dias_trabalho_atuais = explode(',', $funcionario['dias_de_trabalho']);
?>
<title><?php echo $pagina_atual; ?></title>
<style>.form-container{max-width:800px;margin:auto}.input-group{margin-bottom:15px}.input-group label{display:block;margin-bottom:5px;font-weight:600}.input-group input,.input-group select{width:100%;padding:10px;border:1px solid var(--border-color);border-radius:5px;box-sizing:border-box}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}.dias-semana{display:flex;flex-wrap:wrap;gap:15px;align-items:center}.dias-semana label{font-weight:normal}</style>
<div class="content-box">
    <div class="form-container">
        <h2>Editando Funcionário: <?php echo htmlspecialchars($funcionario['nome']); ?></h2>
        <form action="editar_funcionario.php?id=<?php echo $funcionario['id']; ?>" method="POST">
            <h4>Dados Pessoais e de Acesso</h4>
            <div class="form-grid">
                <div class="input-group"><label for="nome">Nome Completo:</label><input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($funcionario['nome']); ?>" required></div>
                <div class="input-group"><label for="email">Email (para login):</label><input type="email" id="email" name="email" value="<?php echo htmlspecialchars($funcionario['email']); ?>" required></div>
                <div class="input-group"><label for="telefone">Telefone:</label><input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($funcionario['telefone']); ?>"></div>
                <div class="input-group"><label for="tipo_usuario">Cargo:</label><select id="tipo_usuario" name="tipo_usuario"><option value="funcionario" <?php if($funcionario['tipo_usuario'] == 'funcionario') echo 'selected'; ?>>Funcionário</option><option value="gerente" <?php if($funcionario['tipo_usuario'] == 'gerente') echo 'selected'; ?>>Gerente</option></select></div>
            </div>
            <h4>Dados Contratuais e de Jornada</h4>
            <div class="form-grid">
                <div class="input-group"><label for="salario_base">Salário Base (R$):</label><input type="number" step="0.01" id="salario_base" name="salario_base" value="<?php echo htmlspecialchars($funcionario['salario_base']); ?>"></div>
                <div class="input-group"><label for="valor_hora_extra">Valor Hora Extra (%):</label><input type="number" id="valor_hora_extra" name="valor_hora_extra" value="<?php echo htmlspecialchars($funcionario['valor_hora_extra_percentual']); ?>"></div>
                <div class="input-group"><label for="horario_entrada">Entrada Padrão:</label><input type="time" id="horario_entrada" name="horario_entrada" value="<?php echo htmlspecialchars($funcionario['horario_entrada_padrao']); ?>"></div>
                <div class="input-group"><label for="horario_saida">Saída Padrão:</label><input type="time" id="horario_saida" name="horario_saida" value="<?php echo htmlspecialchars($funcionario['horario_saida_padrao']); ?>"></div>
            </div>
            <h4>Dias de Trabalho</h4>
            <div class="input-group dias-semana">
                <label><input type="checkbox" name="dias_de_trabalho[]" value="1" <?php if(in_array('1', $dias_trabalho_atuais)) echo 'checked'; ?>> Seg</label>
                <label><input type="checkbox" name="dias_de_trabalho[]" value="2" <?php if(in_array('2', $dias_trabalho_atuais)) echo 'checked'; ?>> Ter</label>
                <label><input type="checkbox" name="dias_de_trabalho[]" value="3" <?php if(in_array('3', $dias_trabalho_atuais)) echo 'checked'; ?>> Qua</label>
                <label><input type="checkbox" name="dias_de_trabalho[]" value="4" <?php if(in_array('4', $dias_trabalho_atuais)) echo 'checked'; ?>> Qui</label>
                <label><input type="checkbox" name="dias_de_trabalho[]" value="5" <?php if(in_array('5', $dias_trabalho_atuais)) echo 'checked'; ?>> Sex</label>
                <label><input type="checkbox" name="dias_de_trabalho[]" value="6" <?php if(in_array('6', $dias_trabalho_atuais)) echo 'checked'; ?>> Sáb</label>
                <label><input type="checkbox" name="dias_de_trabalho[]" value="7" <?php if(in_array('7', $dias_trabalho_atuais)) echo 'checked'; ?>> Dom</label>
            </div>
            <button type="submit" class="btn btn-success" style="width:100%; margin-top: 20px;">Salvar Alterações</button>
        </form>
    </div>
</div>
<?php require_once 'footer_admin.php'; ?>