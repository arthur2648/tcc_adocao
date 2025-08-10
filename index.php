<?php
// PARTE 1: LÓGICA E DADOS
require_once 'conexao.php';
$pagina_atual = 'Adote um Amigo - Encontre seu Novo Companheiro';
$pagina_slug = 'adote';
$sql = "SELECT * FROM animais WHERE status = 'disponivel' ORDER BY data_cadastro DESC";
$stmt = $pdo->query($sql);
$animais = $stmt->fetchAll(PDO::FETCH_ASSOC);
require_once 'header.php';
?>
<main>
    <section class="hero-section">
        <h1>O Amor Espera por Si</h1>
        <p>Encontre um amigo fiel para toda a vida. A adoção é um ato de amor que transforma a vida de um pet e a sua.</p>
        <a href="#galeria" class="btn">Ver os Animais</a>
    </section>
    <div class="container" id="galeria">
        <h2 class="gallery-title">Nossos Anjinhos Esperando um Lar</h2>
        <div class="gallery-container">
            <?php if (count($animais) > 0): ?>
                <?php foreach ($animais as $animal): ?>
                    <div class="animal-card">
                        <img src="<?php echo htmlspecialchars($animal['foto_url']); ?>" alt="Foto de <?php echo htmlspecialchars($animal['nome']); ?>" class="animal-img">
                        <div class="animal-info">
                            <h3><?php echo htmlspecialchars($animal['nome']); ?></h3>
                            <p><?php echo htmlspecialchars($animal['especie']); ?> | <?php echo htmlspecialchars($animal['idade'] ?? 'Idade desconhecida'); ?> anos</p>
                            <a href="animal.php?id=<?php echo $animal['id']; ?>" class="btn">Conhecer</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum animalzinho cadastrado para adoção no momento. Volte em breve!</p>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>