<?php
session_start();

// Redireciona para login se não estiver logado, senão para o dashboard
if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
  header('Location: dashboard.php');
} else {
  header('Location: login.php');
}
exit;
?>

