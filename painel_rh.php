<?php 
// Linhas de depuração para nos mostrar qualquer erro
ini_set('display_errors', 1);
error_reporting(E_ALL);

// O resto do código começa aqui
$pagina_atual = 'Painel RH';
require_once 'header_admin.php'; 

// Segurança: Apenas 'rh' e 'admin' podem ver esta página
if (!in_array($_SESSION['usuario_tipo'], ['rh', 'admin'])) {
    echo "<script>alert('Acesso negado!'); window.location.href = 'painel.php';</script>";
    exit();
}

// Lógica do Dashboard de RH (contar funcionários e gerentes)
$stmt_funcionarios = $pdo->query("SELECT COUNT(id) FROM usuarios WHERE tipo_usuario IN ('funcionario', 'gerente')");
$total_funcionarios = $stmt_funcionarios->fetchColumn();

// Lógica para contar cargos de RH e Admin
$stmt_admins = $pdo->query("SELECT COUNT(id) FROM usuarios WHERE tipo_usuario IN ('rh', 'admin')");
$total_admins_rh = $stmt_admins->fetchColumn();

// 4. Buscar as 5 últimas atividades (ex: novos animais ou novos lares)
$sql_atividades = "(SELECT 'animal' as tipo, nome, data_cadastro FROM animais ORDER BY data_cadastro DESC LIMIT 5)
                   UNION ALL
                   (SELECT 'lar' as tipo, nome_responsavel as nome, data_solicitacao as data_cadastro FROM lares_temporarios ORDER BY data_solicitacao DESC LIMIT 5)
                   ORDER BY data_cadastro DESC LIMIT 5";
$stmt_atividades = $pdo->query($sql_atividades);
$atividades_recentes = $stmt_atividades->fetchAll(PDO::FETCH_ASSOC);

?>

<title><?php echo $pagina_atual; ?></title>

<style>
    .stat-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    .card {
        background-color: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .card i {
        font-size: 2.5em;
        padding: 18px;
        border-radius: 50%;
        color: white;
    }
    .card .icon-equipe { background-color: #6f42c1; } /* Roxo */
    .card .icon-gestao { background-color: #28a745; } /* Verde */
    .card .info h3 {
        margin: 0;
        font-size: 1em;
        color: var(--text-light);
        font-weight: 600;
        text-transform: uppercase;
    }
    .card .info p {
        margin: 5px 0 0 0;
        font-size: 2.2em;
        font-weight: 700;
        color: var(--text-color);
    }
    .actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }
    .action-card {
        background: linear-gradient(45deg, var(--primary-color), #f98a61);
        color: white;
        text-decoration: none;
        padding: 30px;
        border-radius: 10px;
        box-shadow: var(--shadow);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .action-card i {
        font-size: 2em;
        margin-bottom: 10px;
    }
    .action-card h3 {
        margin: 0;
        font-family: var(--font-heading);
        font-size: 1.4em;
    }
    .recent-hires-list li {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
    }
    .recent-hires-list li:last-child { border-bottom: none; }
    .recent-hires-list span { color: var(--text-light); }
</style>

<div class="stat-cards">
    <div class="card">
        <i class="fas fa-users icon-equipe"></i>
        <div class="info">
            <h3>Funcionários e Gerentes</h3>
            <p><?php echo $total_funcionarios; ?></p>
        </div>
    </div>
    <div class="card">
        <i class="fas fa-user-shield icon-gestao"></i>
        <div class="info">
            <h3>Equipa de Gestão (RH/Admin)</h3>
            <p><?php echo $total_admins_rh; ?></p>
        </div>
    </div>
</div>

<div class="actions-grid">
    <a href="gerenciar_funcionarios.php" class="action-card">
        <i class="fas fa-users-cog"></i>
        <h3>Gerir Equipa</h3>
    </a>
    <a href="cadastrar_funcionario.php" class="action-card">
        <i class="fas fa-user-plus"></i>
        <h3>Cadastrar Novo Funcionário</h3>
    </a>
</div>

<div class="content-box">
    <h3>Atividades Recentes</h3>
    <ul class="recent-hires-list">
        <?php if (count($atividades_recentes) > 0): ?>
            <?php foreach($atividades_recentes as $atividade): ?>
                <li>
                    <?php if ($atividade['tipo'] == 'animal'): ?>
                        <i class="fas fa-paw"></i>
                        Novo animal cadastrado: <strong><?php echo htmlspecialchars($atividade['nome']); ?></strong>
                    <?php else: ?>
                        <i class="fas fa-house-user"></i>
                        Nova candidatura de lar temporário: <strong><?php echo htmlspecialchars($atividade['nome']); ?></strong>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Nenhuma atividade recente.</li>
        <?php endif; ?>
    </ul>
</div>

<?php 
require_once 'footer_admin.php'; 
?>