<?php 
// Define o nome desta página
$pagina_atual = 'Gerenciar Lares';

// Puxa o cabeçalho e o menu
require_once 'header_admin.php'; 

// Lógica para buscar as candidaturas (sem alterações)
$sql = "SELECT * FROM lares_temporarios ORDER BY data_solicitacao DESC";
$stmt = $pdo->query($sql);
$lares = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<title><?php echo $pagina_atual; ?></title>

<style>
    .lares-list {
        display: grid;
        grid-template-columns: 1fr; /* Apenas uma coluna */
        gap: 20px;
    }
    .lar-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: var(--shadow);
        padding: 20px;
        border-left: 5px solid #ffc107; /* Cor Padrão (Pendente) */
    }
    .lar-card.status-aprovado { border-left-color: #28a745; }
    .lar-card.status-reprovado { border-left-color: #dc3545; }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .card-header h3 { margin: 0; font-size: 1.2em; }
    .status { padding: 5px 12px; border-radius: 15px; color: white; font-weight: 600; font-size: 0.8em; text-transform: uppercase; }
    .status.pendente { background-color: #ffc107; color: #333; }
    .status.aprovado { background-color: #28a745; }
    .status.reprovado { background-color: #dc3545; }

    .card-body p { margin: 5px 0; color: var(--text-light); }
    .card-body p strong { color: var(--text-color); }
    
    .lar-details {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid var(--border-color);
        display: none; /* Começa escondido */
    }

    .card-actions {
        margin-top: 20px;
        display: flex;
        gap: 10px;
    }
    .btn-details { background-color: #6c757d; }
</style>

<div class="content-box" style="text-align: left;">
    <h2>Candidaturas de Lares Temporários</h2>
    
    <div class="lares-list">
        <?php if (count($lares) > 0): ?>
            <?php foreach ($lares as $lar): ?>
                <div class="lar-card status-<?php echo htmlspecialchars($lar['status_aprovacao']); ?>">
                    <div class="card-header">
                        <h3><?php echo htmlspecialchars($lar['nome_responsavel']); ?></h3>
                        <span class="status <?php echo htmlspecialchars($lar['status_aprovacao']); ?>">
                            <?php echo ucfirst(htmlspecialchars($lar['status_aprovacao'])); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($lar['email_contato']); ?></p>
                        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($lar['telefone']); ?></p>
                        
                        <div class="lar-details" id="details-<?php echo $lar['id']; ?>">
                            <p><strong>Endereço:</strong> <?php echo htmlspecialchars($lar['endereco']); ?></p>
                            <p><strong>Afinidade:</strong> <?php echo htmlspecialchars($lar['tipo_animal_aceito']); ?></p>
                            <p><strong>CPF:</strong> <?php echo htmlspecialchars($lar['cpf']); ?></p>
                        </div>
                    </div>
                    <div class="card-actions">
                        <button class="btn btn-details" onclick="toggleDetails(<?php echo $lar['id']; ?>)">Ver Detalhes</button>
                        <?php if ($lar['status_aprovacao'] == 'pendente'): ?>
                            <a href="aprovar_reprovar_lar.php?id=<?php echo $lar['id']; ?>&acao=aprovar" class="btn btn-success">Aprovar</a>
                            <a href="aprovar_reprovar_lar.php?id=<?php echo $lar['id']; ?>&acao=reprovador" class="btn btn-danger">Reprovar</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhuma candidatura recebida até o momento.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleDetails(id) {
        const detailsDiv = document.getElementById('details-' + id);
        if (detailsDiv.style.display === 'none' || detailsDiv.style.display === '') {
            detailsDiv.style.display = 'block';
        } else {
            detailsDiv.style.display = 'none';
        }
    }
</script>

<?php 
// Puxa o rodapé
require_once 'footer_admin.php'; 
?>