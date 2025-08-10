<?php
// Liga a "fábrica de crachás" para poder mexer neles
session_start();

// Destrói todas as informações do crachá (a sessão)
session_destroy();

// Manda o usuário de volta para a página de login
header("Location: login.php");
exit(); // Garante que o script pare de ser executado aqui
?>