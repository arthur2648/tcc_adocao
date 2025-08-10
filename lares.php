<?php
// Linhas de depuração para nos mostrar qualquer erro
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclui a conexão e o cabeçalho
require_once 'conexao.php';
require_once 'header.php';

// Busca no banco de dados todos os lares temporários que foram APROVADOS
$sql = "SELECT * FROM lares_temporarios WHERE status_aprovacao = 'aprovado' ORDER BY nome_responsavel ASC";
$stmt = $pdo->query($sql);
$lares = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<title>Nossos Lares Temporários</title>

<style>
    .lar-card {
        background-color: var(--white-color);
        border-radius: 15px;
        box-shadow: var(--shadow);
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 25px;
    }
    .lar-card i {
        font-size: 3em;
        color: var(--primary-color);
    }
    .lar-card-info h3 {
        font-family: var(--font-heading);
        margin: 0 0 5px 0;
    }
    .lar-card-info p {
        margin: 0;
        color: #777;
    }
    .cta-section {
        background-color: var(--primary-color);
        color: white;
        text-align: center;
        padding: 50px 20px;
        margin: 40px 0;
        border-radius: 15px;
    }
    .cta-section h2 {
        font-family: var(--font-heading);
        font-size: 2em;
        margin-top: 0;
    }
    .cta-section .btn-cta {
        display: inline-block;
        padding: 15px 35px;
        background-color: white;
        color: var(--primary-color);
        text-decoration: none;
        border-radius: 50px;
        font-weight: 700;
        transition: transform 0.2s;
    }
    .cta-section .btn-cta:hover {
        transform: scale(1.1);
    }
</style>

<main class="container">
    <section class="hero-section">
        <h1>Nossos Lares Temporários</h1>
        <p>Conheça os heróis anônimos que abrem suas casas e corações para cuidar dos nossos resgatados enquanto eles esperam por uma família definitiva. Eles são a ponte de amor para uma nova vida!</p>
    </section>

    <div class="gallery-container">
        <?php if (count($lares) > 0): ?>
            <?php foreach ($lares as $lar): ?>
                <div class="lar-card">
                    <i class="fas fa-house-user"></i> <div class="lar-card-info">
                        <h3><?php echo htmlspecialchars(explode(' ', $lar['nome_responsavel'])[0]); ?></h3>
                        <p>Ajuda a acolher: <?php echo htmlspecialchars($lar['tipo_animal_aceito']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Ainda não temos lares temporários aprovados para exibir. Seja o primeiro!</p>
        <?php endif; ?>
    </div>

    <section class="cta-section">
        <h2>Você também pode ser um anjo!</h2>
        <p>Sua casa pode ser o recomeço que um animalzinho precisa.</p>
        <a href="seja_lar_temporario.php" class="btn-cta">Quero Ser um Lar Temporário</a>
    </section>
</main>

<?php
// Puxa o rodapé
require_once 'footer.php';
?>