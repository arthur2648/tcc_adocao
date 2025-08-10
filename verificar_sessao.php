<?php
// Liga a fábrica de crachás para a gente poder ler o que está neles
session_start();

echo "<h1>Conteúdo do Crachá (Sessão)</h1>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>