<?php
session_start();

// Configurações do banco de dados
$host = 'srv1893.hstgr.io';
$user = 'u671655541_bd_maiver';
$password = 'Maivernew2025@';
$database = 'u671655541_bd_maiver';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
  die("Erro de conexão: " . $conn->connect_error);
}

// Função para escapar strings
function escape($conn, $str) {
  return $conn->real_escape_string($str);
}

// Função para verificar se usuário está logado
function verificarLogin() {
  if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    header('Location: login.php');
    exit;
  }
}

// Função para fazer login
function fazerLogin($conn, $username, $password) {
  $username = escape($conn, $username);
  $password = escape($conn, $password);
  
  $query = "SELECT id, username, password FROM usuarios WHERE username = '$username' AND ativo = 1";
  $result = $conn->query($query);
  
  if ($result && $result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    
    // Verifica a senha (usando password_verify se estiver usando hash, ou comparação direta)
    if (password_verify($password, $usuario['password']) || $usuario['password'] === $password) {
      $_SESSION['usuario_logado'] = true;
      $_SESSION['usuario_id'] = $usuario['id'];
      $_SESSION['usuario_nome'] = $usuario['username'];
      return true;
    }
  }
  
  return false;
}
?>

