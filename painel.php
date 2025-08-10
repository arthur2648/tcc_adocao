<?php
$pagina_atual = 'Painel';
require_once 'header_admin.php'; 

$stmt_animais = $pdo->query("SELECT COUNT(id) FROM animais WHERE status = 'disponivel'");
$total_animais_disponiveis = $stmt_animais->fetchColumn();
$stmt_lares = $pdo->query("SELECT COUNT(id) FROM lares_temporarios WHERE status_aprovacao = 'pendente'");
$total_lares_pendentes = $stmt_lares->fetchColumn();
$stmt_adocoes = $pdo->query("SELECT COUNT(id) FROM adocoes WHERE status = 'pendente'");
$total_adocoes_pendentes = $stmt_adocoes->fetchColumn();
?>

<title><?php echo $pagina_atual; ?></title>

<style>
    .stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-bottom: 30px; }
    .card { background-color: #fff; padding: 25px; border-radius: 10px; box-shadow: var(--shadow); display: flex; align-items: center; gap: 20px; }
    .card i { font-size: 2.5em; padding: 18px; border-radius: 50%; color: white; }
    .card .icon-animais { background-color: #17a2b8; }
    .card .icon-lares { background-color: #ffc107; }
    .card .icon-adocoes { background-color: #28a745; }
    .card .info h3 { margin: 0; font-size: 1em; color: var(--text-light); font-weight: 600; text-transform: uppercase; }
    .card .info p { margin: 5px 0 0 0; font-size: 2.2em; font-weight: 700; color: var(--text-color); }
</style>

<div class="stat-cards">
    <div class="card">
        <i class="fas fa-paw icon-animais"></i>
        <div class="info">
            <h3>Animais para Adoção</h3>
            <p><?php echo $total_animais_disponiveis; ?></p>
        </div>
    </div>
    <div class="card">
        <i class="fas fa-hand-holding-heart icon-lares"></i>
        <div class="info">
            <h3>Lares Pendentes</h3>
            <p><?php echo $total_lares_pendentes; ?></p>
        </div>
    </div>
    <div class="card">
        <i class="fas fa-heart icon-adocoes"></i>
        <div class="info">
            <h3>Adoções Pendentes</h3>
            <p><?php echo $total_adocoes_pendentes; ?></p>
        </div>
    </div>
</div>

<div class="content-box">
    <h2>Bem-vindo ao Painel Operacional!</h2>
    <p>Utilize o menu à sua esquerda para gerir as operações diárias da ONG.</p>
</div>

<?php 
require_once 'footer_admin.php'; 
?>