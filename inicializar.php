<?php
// Este ficheiro tem a única responsabilidade de iniciar a sessão corretamente.
// Ele será chamado por todos os outros ficheiros.

// Garante que a exibição de erros está ligada para depuração.
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>