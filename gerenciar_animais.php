<?php 
// Define o nome desta página
$pagina_atual = 'Gerenciar Animais';

// Puxa o cabeçalho e o menu
require_once 'header_admin.php'; 

// Lógica para buscar todos os animais no banco de dados
$sql = "SELECT * FROM animais ORDER BY nome ASC";
$stmt = $pdo->query($sql);
$animais = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<title><?php echo $pagina_atual; ?></title>

<style>
    .table-container {
        background-color: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: var(--shadow);
    }
    .table-responsive {
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 15px;
        border-bottom: 1px solid var(--border-color);
        text-align: left;
        vertical-align: middle;
    }
    th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.9em;
    }
    tr:hover {
        background-color: #f1f3f5;
    }
    .animal-thumb {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    .status-badge {
        padding: 5px 12px;
        border-radius: 15px;
        color: white;
        font-weight: 600;
        font-size: 0.8em;
        text-transform: capitalize;
    }
    .status-badge.disponivel { background-color: #28a745; }
    .status-badge.em_processo { background-color: #ffc107; color: #333; }
    .status-badge.adotado { background-color: #6c757d; }
    .actions-cell a {
        padding: 8px 12px;
        font-size: 0.9em;
        margin-right: 5px;
    }
</style>

<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Animais Cadastrados no Sistema</h2>
        <a href="cadastrar_animal.php" class="btn btn-success"><i class="fas fa-plus"></i> Cadastrar Novo Animal</a>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Espécie</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($animais) > 0): ?>
                        <?php foreach ($animais as $animal): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($animal['foto_url']); ?>" alt="Foto de <?php echo htmlspecialchars($animal['nome']); ?>" class="animal-thumb">
                                </td>
                                <td><strong><?php echo htmlspecialchars($animal['nome']); ?></strong></td>
                                <td><?php echo htmlspecialchars($animal['especie']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo str_replace(' ', '_', $animal['status']); ?>">
                                        <?php echo str_replace('_', ' ', htmlspecialchars($animal['status'])); ?>
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <a href="editar_animal.php?id=<?php echo $animal['id']; ?>" class="btn btn-primary">Editar</a>
                                    <a href="excluir_animal.php?id=<?php echo $animal['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este animal?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 20px;">Nenhum animal cadastrado no sistema.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
// Puxa o rodapé
require_once 'footer_admin.php'; 
?>