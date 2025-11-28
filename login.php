<?php
session_start();
require_once 'config.php';

$erro = '';

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
  header('Location: dashboard.php');
  exit;
}

// Processa o formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = isset($_POST['username']) ? trim($_POST['username']) : '';
  $password = isset($_POST['password']) ? $_POST['password'] : '';
  
  if (empty($username) || empty($password)) {
    $erro = 'Por favor, preencha todos os campos.';
  } else {
    if (fazerLogin($conn, $username, $password)) {
      header('Location: dashboard.php');
      exit;
    } else {
      $erro = 'Usuário ou senha incorretos.';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Dashboard MAIVER</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-container {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
      padding: 40px;
      max-width: 400px;
      width: 100%;
    }
    .login-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .login-header h2 {
      color: #667eea;
      font-weight: bold;
    }
    .btn-login {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      width: 100%;
      padding: 12px;
      font-weight: bold;
      transition: transform 0.2s;
    }
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    .alert {
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-header">
      <h2>🔐 Dashboard MAIVER</h2>
      <p class="text-muted">Faça login para continuar</p>
    </div>

    <?php if ($erro): ?>
      <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($erro) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label for="username" class="form-label">Usuário</label>
        <input 
          type="text" 
          class="form-control" 
          id="username" 
          name="username" 
          required 
          autofocus
          placeholder="Digite seu usuário"
        >
      </div>

      <div class="mb-4">
        <label for="password" class="form-label">Senha</label>
        <input 
          type="password" 
          class="form-control" 
          id="password" 
          name="password" 
          required
          placeholder="Digite sua senha"
        >
      </div>

      <button type="submit" class="btn btn-primary btn-login">
        Entrar
      </button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

