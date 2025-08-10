<?php 
$pagina_atual = 'Detalhes do Funcionário';
require_once 'header_admin.php'; 

// ### CORREÇÃO AQUI ###
// Segurança ATUALIZADA: Agora 'rh' também pode ver os detalhes.
if (!in_array($_SESSION['usuario_tipo'], ['gerente', 'admin', 'rh'])) {
    echo "<script>alert('Acesso negado!'); window.location.href = 'painel.php';</script>";
    exit();
}

// Verifica se o ID do funcionário foi passado pela URL
if (!isset($_GET['id'])) {
    header("Location: gerenciar_funcionarios.php");
    exit();
}
$id_funcionario = $_GET['id'];

// Busca os dados completos do funcionário usando JOIN
$sql = "SELECT u.*, d.* FROM usuarios u 
        LEFT JOIN funcionarios_detalhes d ON u.id = d.usuario_id 
        WHERE u.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_funcionario]);
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não encontrar o funcionário, volta para a lista
if (!$funcionario) {
    header("Location: gerenciar_funcionarios.php");
    exit();
}

// Pequena função para traduzir os dias da semana
function formatarDiasTrabalho($dias_str) {
    if (empty($dias_str)) return 'Não definido';
    $dias_map = ['1' => 'Seg', '2' => 'Ter', '3' => 'Qua', '4' => 'Qui', '5' => 'Sex', '6' => 'Sáb', '7' => 'Dom'];
    $dias_array = explode(',', $dias_str);
    $dias_formatados = [];
    foreach ($dias_array as $dia) {
        if (isset($dias_map[$dia])) {
            $dias_formatados[] = $dias_map[$dia];
        }
    }
    return implode(', ', $dias_formatados);
}
?>

<title><?php echo $pagina_atual; ?></title>

<style>
    .profile-container {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 30px;
    }
    .profile-sidebar { text-align: center; }
    .profile-sidebar i { font-size: 8em; color: var(--primary-color); }
    .profile-sidebar h2 { margin-top: 15px; }
    .profile-sidebar .cargo { font-weight: 600; color: #fff; background-color: var(--primary-color); padding: 5px 15px; border-radius: 15px; display: inline-block; }
    
    .profile-details h3 {
        font-family: var(--font-heading);
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: 10px;
        margin-top: 30px;
    }
    .profile-details h3:first-child { margin-top: 0; }
    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px 30px;
    }
    .details-grid p { margin: 5px 0; color: #555; }
    .details-grid strong { color: var(--text-color); }
</style>

<div class="content-box">
    <div class="profile-container">
        <div class="profile-sidebar">
            <i class="fas fa-user-circle"></i>
            <h2><?php echo htmlspecialchars($funcionario['nome']); ?></h2>
            <span class="cargo"><?php echo ucfirst(htmlspecialchars($funcionario['tipo_usuario'])); ?></span>
            <br><br>
            <?php if (in_array($_SESSION['usuario_tipo'], ['rh', 'admin'])): ?>
                <a href="editar_funcionario.php?id=<?php echo $funcionario['id']; ?>" class="btn btn-primary">Editar Cadastro</a>
            <?php endif; ?>
        </div>

        <div class="profile-details">
            <h3>Dados Pessoais e de Contato</h3>
            <div class="details-grid">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($funcionario['email']); ?></p>
                <p><strong>Telefone:</strong> <?php echo htmlspecialchars($funcionario['telefone'] ?? 'Não informado'); ?></p>
                <p><strong>CPF:</strong> <?php echo htmlspecialchars($funcionario['cpf'] ?? 'Não informado'); ?></p>
                <p><strong>CEP:</strong> <?php echo htmlspecialchars($funcionario['cep'] ?? 'Não informado'); ?></p>
            </div>

            <h3>Dados Contratuais e de Jornada</h3>
            <div class="details-grid">
                <p><strong>Salário Base:</strong> R$ <?php echo number_format($funcionario['salario_base'] ?? 0, 2, ',', '.'); ?></p>
                <p><strong>Hora Extra:</strong> <?php echo htmlspecialchars($funcionario['valor_hora_extra_percentual'] ?? 'N/A'); ?>%</p>
                <p><strong>Entrada Padrão:</strong> <?php echo $funcionario['horario_entrada_padrao'] ? date('H:i', strtotime($funcionario['horario_entrada_padrao'])) : 'N/A'; ?></p>
                <p><strong>Saída Padrão:</strong> <?php echo $funcionario['horario_saida_padrao'] ? date('H:i', strtotime($funcionario['horario_saida_padrao'])) : 'N/A'; ?></p>
                <p><strong>Jornada de Trabalho:</strong> <?php echo formatarDiasTrabalho($funcionario['dias_de_trabalho']); ?></p>
            </div>

            <?php if (!empty($funcionario['curriculo_url'])): ?>
            <h3>Documentos</h3>
            <p><a href="<?php echo htmlspecialchars($funcionario['curriculo_url']); ?>" target="_blank" class="btn" style="background-color:#6c757d;">Ver Currículo</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
require_once 'footer_admin.php'; 
?>