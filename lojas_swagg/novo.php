<?php
require 'conexao.php';

$msg = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoria = (int)($_POST['categoria_id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $tamanho = trim($_POST['tamanho'] ?? '');
    $preco = (float)str_replace(',', '.', $_POST['preco'] ?? '0');
    $estoque = (int)($_POST['estoque'] ?? 0);

    if ($nome === '' || $categoria < 1 || $preco <= 0) {
        $erro = 'Preencha o nome, a categoria e um preço maior que zero.';
    } else {
        $sql = "INSERT INTO produtos (categoria_id, nome, descricao, tamanho, preco, estoque)
                VALUES (?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$categoria, $nome, $descricao, $tamanho, $preco, $estoque]);
        $msg = 'Produto cadastrado.';
    }
}

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Novo produto | Swag</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Manrope:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
  <header class="topo">
    <a class="logo" href="index.php">SWAG</a>
    <nav><a href="index.php">Voltar à loja</a></nav>
  </header>

  <main class="pagina-form">
    <h2>Novo produto</h2>
    <?php if ($msg): ?><p class="aviso sucesso"><?= $msg ?> <a href="index.php">Ver na loja</a></p><?php endif; ?>
    <?php if ($erro): ?><p class="aviso erro"><?= $erro ?></p><?php endif; ?>

    <form method="post" class="form">
      <label>Categoria
        <select name="categoria_id" required>
          <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Nome do produto
        <input type="text" name="nome" maxlength="100" required>
      </label>
      <label>Descrição
        <input type="text" name="descricao" maxlength="255">
      </label>
      <label>Tamanhos
        <input type="text" name="tamanho" maxlength="20" placeholder="Ex.: P ao GG ou 38 ao 44">
      </label>
      <div class="linha">
        <label>Preço (R$)
          <input type="text" name="preco" inputmode="decimal" placeholder="99,90" required>
        </label>
        <label>Estoque
          <input type="number" name="estoque" min="0" value="0">
        </label>
      </div>
      <button class="btn" type="submit">Salvar produto</button>
    </form>
  </main>
</body>
</html>