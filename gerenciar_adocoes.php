<?php 
$pagina_atual = 'Gerenciar Adoções';
require_once 'header_admin.php'; 

// Lógica para buscar todas as candidaturas de adoção, juntando com os dados do animal
$sql = "SELECT 
            adocoes.id as adocao_id, 
            adocoes.interessado_nome, 
            adocoes.interessado_email, 
            adocoes.interessado_telefone, 
            adocoes.mensagem, 
            adocoes.data_solicitacao, 
            adocoes.status as status_adocao, 
            animais.nome as nome_animal, 
            animais.foto_url as foto_animal
        FROM adocoes 
        JOIN animais ON adocoes.animal_id = animais.id 
        ORDER BY adocoes.data_solicitacao DESC";

$stmt = $pdo->query($sql);
$adocoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<title><?php echo $pagina_atual; ?></title>
<style>
    .adoption-list { display: grid; grid-template-columns: 1fr; gap: 20px; }
    .adoption-card { background-color: #fff; border-radius: 8px; box-shadow: var(--shadow); display: flex; overflow: hidden; }
    .animal-thumbnail { width: 150px; height: 150px; object-fit: cover; }
    .adoption-info { padding: 20px; flex-grow: 1; }
    .adoption-info h3 { margin: 0 0 10px 0; font-family: var(--font-heading); }
    .adoption-info p { margin: 4px 0; color: var(--text-light); }
    .adoption-info p strong { color: var(--text-color); }
    .adoption-actions { margin-top: 15px; display: flex; gap: 10px; }
    .status-badge { padding: 5px 12px; border-radius: 15px; color: white; font-weight: 600; font-size: 0.8em; text-transform: uppercase; }
    .status-badge.pendente { background-color: #ffc107; color: #333; }
    .status-badge.aprovada { background-color: #28a745; }
    .status-badge.rejeitada { background-color: #dc3545; }
    .status-badge.em_analise { background-color: #17a2b8; }
</style>

<div class="content-box">
    <h2>Candidaturas de Adoção Recebidas</h2>

    <div class="adoption-list">
        <?php if (count($adocoes) > 0): ?>
            <?php foreach ($adocoes as $adocao): ?>
                <div class="adoption-card">
                    <img src="<?php echo htmlspecialchars($adocao['foto_animal']); ?>" alt="Foto de <?php echo htmlspecialchars($adocao['nome_animal']); ?>" class="animal-thumbnail">
                    <div class="adoption-info">
                        <h3>Interesse em: <?php echo htmlspecialchars($adocao['nome_animal']); ?></h3>
                        <p><strong>Candidato:</strong> <?php echo htmlspecialchars($adocao['interessado_nome']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($adocao['interessado_email']); ?></p>
                        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($adocao['interessado_telefone']); ?></p>
                        <p><strong>Status:</strong> <span class="status-badge <?php echo $adocao['status_adocao']; ?>"><?php echo ucfirst($adocao['status_adocao']); ?></span></p>
                        <div class="adoption-actions">
                            <?php if ($adocao['status_adocao'] == 'pendente'): ?>
                                <a href="#" class="btn btn-success">Aprovar</a>
                                <a href="#" class="btn btn-danger">Rejeitar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhuma candidatura de adoção recebida até o momento.</p>
        <?php endif; ?>
    </div>
</div>

<?php 
require_once 'footer_admin.php'; 
?>