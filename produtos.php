<?php
require_once 'auth.php';

$mensagem = '';
$tipo_mensagem = '';

// Processa ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $acao = isset($_POST['acao']) ? $_POST['acao'] : '';
  
  if ($acao === 'cadastrar') {
    $tabela = isset($_POST['tabela']) ? trim($_POST['tabela']) : '';
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $imagem_url = isset($_POST['imagem_url']) ? trim($_POST['imagem_url']) : '';
    
    if (empty($tabela) || empty($nome) || empty($imagem_url)) {
      $mensagem = 'Por favor, preencha todos os campos.';
      $tipo_mensagem = 'danger';
    } else {
      $tabela = escape($conn, $tabela);
      $nome = escape($conn, $nome);
      $imagem_url = escape($conn, $imagem_url);
      
      // Verifica se já existe
      $check = $conn->query("SELECT id FROM produtos WHERE tabela = '$tabela'");
      if ($check && $check->num_rows > 0) {
        $mensagem = 'Este produto já está cadastrado. Use a opção de editar.';
        $tipo_mensagem = 'warning';
      } else {
        $query = "INSERT INTO produtos (tabela, nome, imagem_url, ativo) VALUES ('$tabela', '$nome', '$imagem_url', 1)";
        if ($conn->query($query)) {
          $mensagem = 'Produto cadastrado com sucesso!';
          $tipo_mensagem = 'success';
        } else {
          $mensagem = 'Erro ao cadastrar produto: ' . $conn->error;
          $tipo_mensagem = 'danger';
        }
      }
    }
  }
  
  if ($acao === 'editar') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $imagem_url = isset($_POST['imagem_url']) ? trim($_POST['imagem_url']) : '';
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    if ($id > 0 && !empty($nome) && !empty($imagem_url)) {
      $nome = escape($conn, $nome);
      $imagem_url = escape($conn, $imagem_url);
      
      $query = "UPDATE produtos SET nome = '$nome', imagem_url = '$imagem_url', ativo = $ativo WHERE id = $id";
      if ($conn->query($query)) {
        $mensagem = 'Produto atualizado com sucesso!';
        $tipo_mensagem = 'success';
      } else {
        $mensagem = 'Erro ao atualizar produto: ' . $conn->error;
        $tipo_mensagem = 'danger';
      }
    } else {
      $mensagem = 'Por favor, preencha todos os campos.';
      $tipo_mensagem = 'danger';
    }
  }
  
  if ($acao === 'deletar') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
      $query = "DELETE FROM produtos WHERE id = $id";
      if ($conn->query($query)) {
        $mensagem = 'Produto deletado com sucesso!';
        $tipo_mensagem = 'success';
      } else {
        $mensagem = 'Erro ao deletar produto: ' . $conn->error;
        $tipo_mensagem = 'danger';
      }
    }
  }
}

// Busca produtos cadastrados
$produtos_cadastrados = [];
$query_produtos = "SELECT * FROM produtos ORDER BY nome ASC";
$result_produtos = $conn->query($query_produtos);
if ($result_produtos) {
  while ($row = $result_produtos->fetch_assoc()) {
    $produtos_cadastrados[] = $row;
  }
}

// Lista dinâmica de tabelas disponíveis: todas as tabelas *_rec do banco
$tabelas = [];
$sqlTabelas = "
  SELECT TABLE_NAME 
  FROM information_schema.TABLES 
  WHERE TABLE_SCHEMA = '" . escape($conn, $database) . "' 
    AND TABLE_NAME LIKE '%\\_rec'
  ORDER BY TABLE_NAME
";
$resTabelas = $conn->query($sqlTabelas);
if ($resTabelas) {
  while ($row = $resTabelas->fetch_assoc()) {
    // Remove o sufixo _rec para obter o nome da operação usado no dashboard
    $base = preg_replace('/_rec$/', '', $row['TABLE_NAME']);
    $tabelas[] = $base;
  }
}
$tabelas = array_unique($tabelas);
sort($tabelas);

// Tabelas já cadastradas em produtos (não devem aparecer no select)
$tabelas_cadastradas = [];
foreach ($produtos_cadastrados as $prod) {
  $tabelas_cadastradas[] = $prod['tabela'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciar Produtos - Dashboard MAIVER</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    body {
      min-height: 100vh;
      background-color: #f5f5f7;
    }
    .layout-wrapper {
      display: flex;
      min-height: 100vh;
    }
    .sidebar {
      width: 230px;
      background: #1f2933;
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 20px 15px;
    }
    .sidebar .logo {
      font-weight: 700;
      font-size: 1.1rem;
      margin-bottom: 1.5rem;
    }
    .sidebar .nav-link {
      color: #cbd2d9;
      padding: 8px 10px;
      border-radius: 6px;
      margin-bottom: 4px;
      font-size: 0.95rem;
    }
    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
      background: #3e4c59;
      color: #fff;
    }
    .sidebar .nav-link i {
      margin-right: 6px;
    }
    .content-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .navbar-custom {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .user-info {
      color: white;
      margin-right: 15px;
    }
    .product-img-preview {
      max-width: 100px;
      max-height: 100px;
      object-fit: contain;
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 5px;
    }
    .form-section {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 30px;
    }
    .table-responsive {
      max-height: 600px;
      overflow-y: auto;
    }
  </style>
</head>
<body>
  <div class="layout-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        MAIVER Dashboard
      </div>
      <nav class="nav flex-column mb-auto">
        <a href="dashboard.php" class="nav-link">
          📊 Dashboard
        </a>
        <a href="produtos.php" class="nav-link active">
          📦 Produtos
        </a>
      </nav>
      <div class="mt-auto">
        <hr class="border-secondary">
        <div class="small mb-2">
          👤 <?= htmlspecialchars($_SESSION['usuario_nome']) ?>
        </div>
        <a href="logout.php" class="btn btn-outline-light btn-sm w-100">
          Sair
        </a>
      </div>
    </aside>

    <div class="content-wrapper">
      <!-- Navbar superior -->
      <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
          <a class="navbar-brand" href="produtos.php">
            <strong>📦 Gerenciar Produtos</strong>
          </a>
        </div>
      </nav>

      <div class="container mt-4">
    <?php if ($mensagem): ?>
      <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($mensagem) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

        <!-- Formulário de Cadastro -->
        <div class="form-section">
      <h3 class="mb-4"><i class="bi bi-plus-circle"></i> Cadastrar Novo Produto</h3>
      <form method="POST" action="">
        <input type="hidden" name="acao" value="cadastrar">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="tabela" class="form-label">Tabela <span class="text-danger">*</span></label>
            <select class="form-select" id="tabela" name="tabela" required>
              <option value="">Selecione uma tabela</option>
              <?php foreach ($tabelas as $tab): ?>
                <?php if (!in_array($tab, $tabelas_cadastradas)): ?>
                  <option value="<?= htmlspecialchars($tab) ?>"><?= htmlspecialchars($tab) ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
            <small class="text-muted">Tabelas já cadastradas não aparecem aqui</small>
          </div>
          <div class="col-md-4 mb-3">
            <label for="nome" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nome" name="nome" required placeholder="Ex: Bioxcell">
          </div>
          <div class="col-md-4 mb-3">
            <label for="imagem_url" class="form-label">URL da Imagem <span class="text-danger">*</span></label>
            <input type="url" class="form-control" id="imagem_url" name="imagem_url" required placeholder="https://...">
            <small class="text-muted">URL completa da imagem do produto</small>
          </div>
        </div>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save"></i> Cadastrar Produto
        </button>
      </form>
    </div>

        <!-- Lista de Produtos Cadastrados -->
        <div class="form-section">
      <h3 class="mb-4"><i class="bi bi-list-ul"></i> Produtos Cadastrados (<?= count($produtos_cadastrados) ?>)</h3>
      
      <?php if (empty($produtos_cadastrados)): ?>
        <div class="alert alert-info">
          Nenhum produto cadastrado ainda. Use o formulário acima para cadastrar.
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Tabela</th>
                <th>Nome</th>
                <th>Imagem</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($produtos_cadastrados as $prod): ?>
                <tr>
                  <td><?= $prod['id'] ?></td>
                  <td><code><?= htmlspecialchars($prod['tabela']) ?></code></td>
                  <td><?= htmlspecialchars($prod['nome']) ?></td>
                  <td>
                    <img src="<?= htmlspecialchars($prod['imagem_url']) ?>" 
                         alt="<?= htmlspecialchars($prod['nome']) ?>" 
                         class="product-img-preview"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27%3E%3Crect fill=%27%23ddd%27 width=%27100%27 height=%27100%27/%3E%3Ctext fill=%27%23999%27 x=%2750%25%27 y=%2750%25%27 text-anchor=%27middle%27 dy=%27.3em%27%3ESem imagem%3C/text%3E%3C/svg%3E'">
                  </td>
                  <td>
                    <?php if ($prod['ativo']): ?>
                      <span class="badge bg-success">Ativo</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Inativo</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $prod['id'] ?>">
                      <i class="bi bi-pencil"></i> Editar
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $prod['id'] ?>">
                      <i class="bi bi-trash"></i> Deletar
                    </button>
                  </td>
                </tr>

                <!-- Modal de Edição -->
                <div class="modal fade" id="editModal<?= $prod['id'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST" action="">
                        <input type="hidden" name="acao" value="editar">
                        <input type="hidden" name="id" value="<?= $prod['id'] ?>">
                        <div class="modal-header">
                          <h5 class="modal-title">Editar Produto</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label class="form-label">Tabela</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($prod['tabela']) ?>" disabled>
                            <small class="text-muted">A tabela não pode ser alterada</small>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($prod['nome']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">URL da Imagem</label>
                            <input type="url" class="form-control" name="imagem_url" value="<?= htmlspecialchars($prod['imagem_url']) ?>" required>
                            <div class="mt-2">
                              <img src="<?= htmlspecialchars($prod['imagem_url']) ?>" 
                                   alt="Preview" 
                                   class="product-img-preview"
                                   onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27%3E%3Crect fill=%27%23ddd%27 width=%27100%27 height=%27100%27/%3E%3Ctext fill=%27%23999%27 x=%2750%25%27 y=%2750%25%27 text-anchor=%27middle%27 dy=%27.3em%27%3ESem imagem%3C/text%3E%3C/svg%3E'">
                            </div>
                          </div>
                          <div class="mb-3">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" name="ativo" id="ativo<?= $prod['id'] ?>" <?= $prod['ativo'] ? 'checked' : '' ?>>
                              <label class="form-check-label" for="ativo<?= $prod['id'] ?>">
                                Produto Ativo
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                          <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Modal de Confirmação de Exclusão -->
                <div class="modal fade" id="deleteModal<?= $prod['id'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST" action="">
                        <input type="hidden" name="acao" value="deletar">
                        <input type="hidden" name="id" value="<?= $prod['id'] ?>">
                        <div class="modal-header">
                          <h5 class="modal-title">Confirmar Exclusão</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <p>Tem certeza que deseja deletar o produto <strong><?= htmlspecialchars($prod['nome']) ?></strong>?</p>
                          <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                          <button type="submit" class="btn btn-danger">Deletar</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Preview de imagem ao digitar URL (ponto de extensão futuro)
    document.getElementById('imagem_url')?.addEventListener('input', function(e) {
      const url = e.target.value;
      if (url) {
        // Você pode adicionar preview aqui se quiser
      }
    });
  </script>
</body>
</html>

