<?php 
$pagina_atual = 'Gerenciar Funcionários';
require_once 'header_admin.php'; 

// Segurança: Gerentes, RH e Admins podem ver
if (!in_array($_SESSION['usuario_tipo'], ['gerente', 'admin', 'rh'])) {
    echo "<script>alert('Acesso negado!'); window.location.href = 'painel.php';</script>";
    exit();
}

// Lógica para buscar os funcionários e suas estatísticas
$sql = "SELECT 
            u.id, 
            u.nome, 
            u.email, 
            u.tipo_usuario,
            (SELECT COUNT(*) FROM animais WHERE cadastrado_por_id = u.id) as animais_cadastrados,
            (SELECT COUNT(*) FROM lares_temporarios WHERE gerenciado_por_id = u.id AND status_aprovacao = 'aprovado') as lares_aprovados
        FROM usuarios u 
        WHERE u.tipo_usuario IN ('funcionario', 'gerente') 
        ORDER BY u.nome ASC";
$stmt = $pdo->query($sql);
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<title><?php echo $pagina_atual; ?></title>

<style>
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 25px;
    }
    .employee-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: var(--shadow);
        border-left: 5px solid var(--primary-color);
        overflow: hidden;
    }
    .employee-card.gerente { border-left-color: #6f42c1; }

    .card-header {
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f8f9fa;
        border-bottom: 1px solid var(--border-color);
    }
    .card-header h3 { margin: 0; font-size: 1.2em; }
    .cargo-tag {
        font-size: 0.8em;
        font-weight: 600;
        color: #fff;
        background-color: #6c757d;
        padding: 4px 10px;
        border-radius: 15px;
    }
    .cargo-tag.gerente { background-color: #6f42c1; }

    .card-body { padding: 20px; }
    .card-body p { margin: 0 0 15px 0; color: var(--text-light); }
    .card-body p strong { color: var(--text-color); }
    
    .card-stats {
        display: flex;
        gap: 20px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid var(--border-color);
    }
    .stat { text-align: center; }
    .stat .count { font-size: 1.5em; font-weight: 700; color: var(--primary-color); }
    .stat .label { font-size: 0.8em; color: var(--text-light); }

    .card-actions {
        padding: 0 20px 20px 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .card-actions a {
        padding: 8px 12px;
        font-size: 0.9em;
        border-radius: 5px;
    }
</style>

<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Equipa e Produtividade</h2>
        <?php if (in_array($_SESSION['usuario_tipo'], ['rh', 'admin'])): ?>
            <a href="cadastrar_funcionario.php" class="btn btn-success"><i class="fas fa-plus"></i> Cadastrar Novo Funcionário</a>
        <?php endif; ?>
    </div>

    <div class="team-grid">
        <?php if (count($funcionarios) > 0): ?>
            <?php foreach ($funcionarios as $funcionario): ?>
                <div class="employee-card <?php echo $funcionario['tipo_usuario']; ?>">
                    <div class="card-header">
                        <h3><?php echo htmlspecialchars($funcionario['nome']); ?></h3>
                        <span class="cargo-tag <?php echo $funcionario['tipo_usuario']; ?>"><?php echo ucfirst(htmlspecialchars($funcionario['tipo_usuario'])); ?></span>
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($funcionario['email']); ?></p>
                        
                        <div class="card-stats">
                            <div class="stat">
                                <p class="count"><?php echo htmlspecialchars($funcionario['animais_cadastrados']); ?></p>
                                <p class="label">Animais Cadastrados</p>
                            </div>
                            <div class="stat">
                                <p class="count"><?php echo htmlspecialchars($funcionario['lares_aprovados']); ?></p>
                                <p class="label">Lares Aprovados</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-actions">
                        <a href="ver_funcionario.php?id=<?php echo $funcionario['id']; ?>" class="btn" style="background-color: #6c757d;">Ver Detalhes</a>
                        <a href="ponto.php?id=<?php echo $funcionario['id']; ?>" class="btn" style="background-color: #17a2b8;">Ver Ponto</a>
                        <?php if (in_array($_SESSION['usuario_tipo'], ['rh', 'admin'])): ?>
                            <a href="editar_funcionario.php?id=<?php echo $funcionario['id']; ?>" class="btn btn-primary">Editar</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhum funcionário encontrado.</p>
        <?php endif; ?>
    </div>
</div>

<?php 
require_once 'footer_admin.php'; 
?>