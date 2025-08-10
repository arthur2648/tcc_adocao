<?php 
// Define o nome desta página
$pagina_atual = 'Ponto Eletrônico';
require_once 'header_admin.php'; 

$mensagem = "";

// ### LÓGICA DE DIRECIONAMENTO INTELIGENTE ###
// Determina qual funcionário estamos a ver
$id_alvo = $_SESSION['usuario_id']; // Por defeito, vemos o nosso próprio ponto
$nome_alvo = $_SESSION['usuario_nome'];
$visualizando_outro = false;

// Se um ID for passado na URL E o utilizador for gerente ou superior...
if (isset($_GET['id']) && in_array($_SESSION['usuario_tipo'], ['gerente', 'admin', 'rh'])) {
    $id_alvo = $_GET['id'];
    $visualizando_outro = true;

    // Busca o nome do funcionário-alvo para exibir no título
    $stmt_nome = $pdo->prepare("SELECT nome FROM usuarios WHERE id = :id");
    $stmt_nome->execute([':id' => $id_alvo]);
    $func_alvo = $stmt_nome->fetch(PDO::FETCH_ASSOC);
    if ($func_alvo) {
        $nome_alvo = $func_alvo['nome'];
    }
}

// Lógica para processar uma batida de ponto (SÓ SE ESTIVER A VER O PRÓPRIO PONTO)
if (!$visualizando_outro && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tipo_registro'])) {
    $tipo_registro = $_POST['tipo_registro'];
    $sql_insert = "INSERT INTO ponto_eletronico (funcionario_id, tipo_registro) VALUES (:id_funcionario, :tipo_registro)";
    $stmt_insert = $pdo->prepare($sql_insert);
    try {
        $stmt_insert->execute([':id_funcionario' => $id_alvo, ':tipo_registro' => $tipo_registro]);
        $mensagem = "Ponto de '" . htmlspecialchars($tipo_registro) . "' registrado com sucesso!";
    } catch (PDOException $e) {
        $mensagem = "Erro ao registrar o ponto: " . $e->getMessage();
    }
}

// Lógica para determinar o próximo botão (SÓ SE ESTIVER A VER O PRÓPRIO PONTO)
$proxima_acao = null;
$status_atual = 'N/A';
if (!$visualizando_outro) {
    $sql_ultimo_ponto = "SELECT tipo_registro FROM ponto_eletronico WHERE funcionario_id = :id_funcionario AND DATE(timestamp_registro) = CURDATE() ORDER BY timestamp_registro DESC LIMIT 1";
    $stmt_ultimo_ponto = $pdo->prepare($sql_ultimo_ponto);
    $stmt_ultimo_ponto->execute([':id_funcionario' => $id_alvo]);
    $ultimo_ponto = $stmt_ultimo_ponto->fetch(PDO::FETCH_ASSOC);
    $proxima_acao = 'entrada';
    $status_atual = 'Fora do trabalho';
    if ($ultimo_ponto) {
        switch ($ultimo_ponto['tipo_registro']) {
            case 'entrada': $proxima_acao = 'saida_almoco'; $status_atual = 'Trabalhando'; break;
            case 'saida_almoco': $proxima_acao = 'volta_almoco'; $status_atual = 'Em horário de almoço'; break;
            case 'volta_almoco': $proxima_acao = 'saida'; $status_atual = 'Trabalhando'; break;
            case 'saida': $proxima_acao = null; $status_atual = 'Expediente encerrado'; break;
        }
    }
}

// --- LÓGICA PARA O CALENDÁRIO MENSAL (agora usa $id_alvo) ---
$mes_atual = date('m');
$ano_atual = date('Y');
$numero_dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes_atual, $ano_atual);
$dias_semana_pt = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
$meses_pt = [1=>'Janeiro', 2=>'Fevereiro', 3=>'Março', 4=>'Abril', 5=>'Maio', 6=>'Junho', 7=>'Julho', 8=>'Agosto', 9=>'Setembro', 10=>'Outubro', 11=>'Novembro', 12=>'Dezembro'];
$mes_numero = date('n');
$titulo_mes_ano = $meses_pt[$mes_numero] . ' de ' . $ano_atual;

$sql_mes = "SELECT DATE(timestamp_registro) as dia, tipo_registro, timestamp_registro FROM ponto_eletronico WHERE funcionario_id = :id_alvo AND MONTH(timestamp_registro) = :mes AND YEAR(timestamp_registro) = :ano ORDER BY timestamp_registro ASC";
$stmt_mes = $pdo->prepare($sql_mes);
$stmt_mes->execute([':id_alvo' => $id_alvo, ':mes' => $mes_atual, ':ano' => $ano_atual]);
$registros_mes_raw = $stmt_mes->fetchAll(PDO::FETCH_ASSOC);
$registros_por_dia = [];
foreach ($registros_mes_raw as $registro) {
    $registros_por_dia[$registro['dia']][] = $registro;
}

$sql_jornada = "SELECT horario_entrada_padrao, horario_saida_padrao, horas_almoco_padrao, dias_de_trabalho FROM funcionarios_detalhes WHERE usuario_id = :id_alvo";
$stmt_jornada = $pdo->prepare($sql_jornada);
$stmt_jornada->execute([':id_alvo' => $id_alvo]);
$jornada_padrao = $stmt_jornada->fetch(PDO::FETCH_ASSOC);
$dias_trabalho_array = $jornada_padrao ? explode(',', $jornada_padrao['dias_de_trabalho']) : [];

$total_extras_mes_seg = 0;
$total_negativas_mes_seg = 0;
?>

<title><?php echo $pagina_atual; ?></title>

<style>
    .ponto-header { text-align: center; margin-bottom: 20px; }
    .ponto-actions { display: flex; justify-content: center; align-items: center; gap: 20px; padding: 20px; background-color: #f8f9fa; border-radius: 8px; margin-bottom: 20px; }
    .btn-ponto { padding: 12px 25px; font-size: 1.1em; color: white; border: none; border-radius: 5px; cursor: pointer; }
    .table-responsive { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
    th { background-color: #e9ecef; font-weight: 600; }
    .weekend { background-color: #f8f9fa; color: #6c757d; }
    .summary-table { margin-top: 20px; float: right; width: auto; }
    .summary-table th, .summary-table td { text-align: left; }
    .horas-extras { color: #28a745; font-weight: bold; }
    .horas-negativas { color: #dc3545; font-weight: bold; }
</style>

<div class="content-box">
    <div class="ponto-header">
        <h2>
            <?php if($visualizando_outro): ?>
                Relatório de Ponto de: <?php echo htmlspecialchars($nome_alvo); ?>
            <?php else: ?>
                Ponto Eletrônico - <?php echo $titulo_mes_ano; ?>
            <?php endif; ?>
        </h2>
        <?php if(!$visualizando_outro): ?>
            <p>Seu status atual: <strong style="color: var(--primary-color);"><?php echo $status_atual; ?></strong></p>
        <?php endif; ?>
    </div>

    <?php if (!$visualizando_outro && $proxima_acao): ?>
    <div class="ponto-actions">
        <?php if (!empty($mensagem)): ?><p><?php echo $mensagem; ?></p><?php endif; ?>
        <form action="ponto.php" method="POST" style="margin: 0;">
            <input type="hidden" name="tipo_registro" value="<?php echo $proxima_acao; ?>">
            <button type="submit" class="btn btn-ponto" style="background-color: #28a745;">
                <?php echo ['entrada' => 'Registrar Entrada','saida_almoco' => 'Sair para Almoço','volta_almoco' => 'Voltar do Almoço','saida' => 'Registrar Saída'][$proxima_acao]; ?>
            </button>
        </form>
    </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table>
            <thead><tr><th>Dia</th><th>Entrada</th><th>Saída Almoço</th><th>Volta Almoço</th><th>Saída</th><th>Total Trabalhado</th><th class="horas-extras">Horas Extras</th><th class="horas-negativas">Horas Negativas</th></tr></thead>
            <tbody>
                <?php for ($dia = 1; $dia <= $numero_dias_mes; $dia++): 
                    $data_corrente_str = sprintf('%s-%s-%02d', $ano_atual, $mes_atual, $dia);
                    $data_corrente_obj = new DateTime($data_corrente_str);
                    $dia_semana_num_php = $data_corrente_obj->format('w');
                    $dia_semana_num_sql = $data_corrente_obj->format('N');
                    $classe_fds = ($dia_semana_num_php == 0 || $dia_semana_num_php == 6) ? 'weekend' : '';
                    $registros_do_dia = isset($registros_por_dia[$data_corrente_str]) ? $registros_por_dia[$data_corrente_str] : [];
                    $entrada_t = $saida_almoco_t = $volta_almoco_t = $saida_t = '---';
                    $total_segundos_dia = 0;
                    $horas_extras_formatado = '---';
                    $horas_negativas_formatado = '---';
                    if(!empty($registros_do_dia) && $jornada_padrao) {
                        $entrada = $saida_almoco = $volta_almoco = $saida = null;
                        foreach($registros_do_dia as $r){
                            if($r['tipo_registro'] == 'entrada') {$entrada = new DateTime($r['timestamp_registro']); $entrada_t = $entrada->format('H:i');}
                            if($r['tipo_registro'] == 'saida_almoco') {$saida_almoco = new DateTime($r['timestamp_registro']); $saida_almoco_t = $saida_almoco->format('H:i');}
                            if($r['tipo_registro'] == 'volta_almoco') {$volta_almoco = new DateTime($r['timestamp_registro']); $volta_almoco_t = $volta_almoco->format('H:i');}
                            if($r['tipo_registro'] == 'saida') {$saida = new DateTime($r['timestamp_registro']); $saida_t = $saida->format('H:i');}
                        }
                        if ($entrada && $saida_almoco) { $total_segundos_dia += $saida_almoco->getTimestamp() - $entrada->getTimestamp(); }
                        if ($volta_almoco && $saida) { $total_segundos_dia += $saida->getTimestamp() - $volta_almoco->getTimestamp(); } 
                        elseif ($entrada && $saida && !$saida_almoco && !$volta_almoco) { $total_segundos_dia = $saida->getTimestamp() - $entrada->getTimestamp(); }
                        $saldo_em_segundos = 0;
                        if (in_array($dia_semana_num_sql, $dias_trabalho_array)) {
                            $segundos_jornada_padrao = (new DateTime($jornada_padrao['horario_saida_padrao']))->getTimestamp() - (new DateTime($jornada_padrao['horario_entrada_padrao']))->getTimestamp();
                            $segundos_almoco_padrao = $jornada_padrao['horas_almoco_padrao'] * 3600;
                            $segundos_esperados_trabalho = $segundos_jornada_padrao - $segundos_almoco_padrao;
                            $saldo_em_segundos = $total_segundos_dia - $segundos_esperados_trabalho;
                        } else { $saldo_em_segundos = $total_segundos_dia; }
                        if ($saldo_em_segundos > 0) {
                            $total_extras_mes_seg += $saldo_em_segundos;
                            $horas_extras_formatado = sprintf('+%02d:%02d', floor($saldo_em_segundos / 3600), floor(($saldo_em_segundos % 3600) / 60));
                        } elseif ($saldo_em_segundos < 0) {
                            $saldo_abs = abs($saldo_em_segundos);
                            $total_negativas_mes_seg += $saldo_abs;
                            $horas_negativas_formatado = sprintf('-%02d:%02d', floor($saldo_abs / 3600), floor(($saldo_abs % 3600) / 60));
                        }
                    }
                ?>
                <tr class="<?php echo $classe_fds; ?>">
                    <td><?php echo sprintf('%02d', $dia) . ' (' . $dias_semana_pt[$dia_semana_num_php] . ')'; ?></td>
                    <td><?php echo $entrada_t; ?></td>
                    <td><?php echo $saida_almoco_t; ?></td>
                    <td><?php echo $volta_almoco_t; ?></td>
                    <td><?php echo $saida_t; ?></td>
                    <td><strong><?php echo ($total_segundos_dia > 0) ? sprintf('%02d:%02d', floor($total_segundos_dia / 3600), floor(($total_segundos_dia % 3600) / 60)) : '---'; ?></strong></td>
                    <td class="horas-extras"><?php echo $horas_extras_formatado; ?></td>
                    <td class="horas-negativas"><?php echo $horas_negativas_formatado; ?></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>

    <table class="summary-table">
        </table>
    <div style="clear:both;"></div>
</div>

<?php 
require_once 'footer_admin.php'; 
?>