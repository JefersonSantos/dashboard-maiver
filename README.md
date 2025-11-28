# Dashboard MAIVER com Controle de Acesso

Sistema de dashboard com controle de acesso por login para visualização de leads de produtos.

## 📋 Requisitos

- PHP 7.4 ou superior
- MySQL/MariaDB
- Servidor web (Apache/Nginx)

## 🚀 Instalação

### 1. Configuração do Banco de Dados

Execute os scripts SQL para criar as tabelas necessárias:

**Tabela de Usuários:**
```sql
-- Execute o arquivo create_users_table.sql no seu banco de dados
```

**Tabela de Produtos:**
```sql
-- Execute o arquivo create_produtos_table.sql no seu banco de dados
```

Ou execute diretamente no MySQL:

```bash
mysql -u seu_usuario -p nome_do_banco < create_users_table.sql
mysql -u seu_usuario -p nome_do_banco < create_produtos_table.sql
```

### 2. Configuração de Acesso

O arquivo `config.php` já está configurado com as credenciais do banco de dados. Se necessário, ajuste as configurações:

```php
$host = 'srv1893.hstgr.io';
$user = 'u671655541_bd_maiver';
$password = 'Maivernew2025@';
$database = 'u671655541_bd_maiver';
```

### 3. Usuário Padrão

Após executar o script SQL, você terá um usuário padrão:
- **Usuário:** `admin`
- **Senha:** `admin123`

**⚠️ IMPORTANTE:** Altere a senha padrão após o primeiro login!

## 📁 Estrutura de Arquivos

```
├── config.php              # Configurações do banco e funções de autenticação
├── auth.php                 # Verificação de autenticação
├── login.php                # Página de login
├── logout.php               # Página de logout
├── dashboard.php            # Dashboard principal (protegido)
├── produtos.php             # Módulo de gerenciamento de produtos
├── index.php                # Redirecionamento automático
├── create_users_table.sql   # Script SQL para criar tabela de usuários
├── create_produtos_table.sql # Script SQL para criar tabela de produtos
├── .htaccess                # Configurações de segurança (opcional)
└── README.md                # Este arquivo
```

## 🔐 Sistema de Autenticação

### Funcionalidades

- ✅ Login com usuário e senha
- ✅ Proteção de páginas com verificação de sessão
- ✅ Logout seguro
- ✅ Sessão PHP para manter autenticação

### Como Funciona

1. **Login:** O usuário acessa `login.php` e faz login
2. **Sessão:** Após login bem-sucedido, uma sessão PHP é criada
3. **Proteção:** O arquivo `auth.php` verifica se o usuário está logado
4. **Dashboard:** Apenas usuários autenticados podem acessar `dashboard.php`
5. **Logout:** O usuário pode fazer logout através do botão "Sair"

## 👤 Gerenciamento de Usuários

### Criar Novo Usuário

Execute no MySQL:

```sql
INSERT INTO usuarios (username, password, ativo) 
VALUES ('novo_usuario', 'senha123', 1);
```

### Usar Senha Criptografada (Recomendado)

Para maior segurança, use `password_hash()` do PHP:

```php
$senha_hash = password_hash('senha123', PASSWORD_DEFAULT);
// Use $senha_hash no INSERT
```

### Desativar Usuário

```sql
UPDATE usuarios SET ativo = 0 WHERE username = 'usuario';
```

## 📦 Módulo de Gerenciamento de Produtos

### Funcionalidades

- ✅ Cadastro de novos produtos com seleção de tabela
- ✅ Edição de produtos existentes (nome, imagem, status)
- ✅ Exclusão de produtos
- ✅ Visualização de todos os produtos cadastrados
- ✅ Integração automática com o dashboard
- ✅ Preview de imagens

### Como Usar

1. **Acessar o Módulo:** Clique em "📦 Gerenciar Produtos" no dashboard
2. **Cadastrar Produto:**
   - Selecione a tabela do produto
   - Digite o nome do produto
   - Cole a URL da imagem do produto
   - Clique em "Cadastrar Produto"
3. **Editar Produto:** Clique no botão "Editar" na lista de produtos
4. **Deletar Produto:** Clique no botão "Deletar" e confirme a exclusão

### Integração com Dashboard

- Os produtos cadastrados aparecem automaticamente no dashboard
- Se um produto não estiver cadastrado, o sistema usa os dados padrão (fallback)
- Produtos inativos não aparecem no dashboard
- As imagens são exibidas diretamente no dashboard

## 🔒 Segurança

- As senhas são verificadas usando `password_verify()` quando possível
- As sessões são gerenciadas pelo PHP
- As queries SQL usam `escape()` para prevenir SQL injection
- O dashboard só é acessível após autenticação
- O módulo de produtos também é protegido por autenticação

## 📝 Notas

- O sistema suporta senhas em texto simples ou hash (password_hash)
- A função `fazerLogin()` verifica ambos os métodos
- Usuários inativos (ativo = 0) não podem fazer login

## 🐛 Troubleshooting

### Erro de Conexão com Banco de Dados
- Verifique as credenciais em `config.php`
- Confirme que o servidor MySQL está rodando

### Não consigo fazer login
- Verifique se o usuário existe na tabela `usuarios`
- Confirme que o campo `ativo` está como `1`
- Verifique se a senha está correta

### Sessão expira muito rápido
- Ajuste as configurações de sessão do PHP no `php.ini`

### Produtos não aparecem no dashboard
- Verifique se a tabela `produtos` foi criada no banco de dados
- Confirme que o produto está marcado como "Ativo" no módulo de gerenciamento
- Verifique se a tabela do produto está correta (deve corresponder ao nome da tabela no banco)

## 📞 Suporte

Para dúvidas ou problemas, verifique:
1. Logs do servidor web
2. Logs do MySQL
3. Configurações de PHP

