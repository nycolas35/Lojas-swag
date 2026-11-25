<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['carrinho'])) {
  $_SESSION['carrinho'] = [];
}

if (isset($_GET['adicionar'])) {
  $id = (int) $_GET['adicionar'];
  if ($id > 0) {
    $stmtCheck = $pdo->prepare("SELECT estoque FROM produtos WHERE id = ?");
    $stmtCheck->execute([$id]);
    $prod = $stmtCheck->fetch();

    if ($prod && $prod['estoque'] > 0) {
      $qtd_atual = $_SESSION['carrinho'][$id] ?? 0;
      if ($qtd_atual < $prod['estoque']) {
        $_SESSION['carrinho'][$id] = $qtd_atual + 1;
      }
    }
  }
  header('Location: index.php');
  exit;
}

$total_itens = 0;
$soma_carrinho = 0.00;

if (!empty($_SESSION['carrinho'])) {
  $ids = implode(',', array_map('intval', array_keys($_SESSION['carrinho'])));
  $stmtSoma = $pdo->query("SELECT id, preco FROM produtos WHERE id IN ($ids)");
  foreach ($stmtSoma as $item) {
    $qtd = $_SESSION['carrinho'][$item['id']];
    $total_itens += $qtd;
    $soma_carrinho += ($item['preco'] * $qtd);
  }
}

$stmt = $pdo->query("SELECT p.*, c.nome AS categoria
                     FROM produtos p JOIN categorias c ON c.id = p.categoria_id
                     ORDER BY c.id, p.nome");
$grupos = ['Roupas' => [], 'Sapatos' => []];
foreach ($stmt as $p) { $grupos[$p['categoria']][] = $p; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Swag | Moda masculina</title>
  <link rel="preconnect" href="https://googleapis.com">
  <link href="https://googleapis.com/css2?family=Anton&family=Manrope:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
  <header class="topo">
    <a class="logo" href="index.php">LOJAS SWAG</a>
    <nav>
      <a href="#roupas">Roupas</a>
      <a href="#sapatos">Sapatos</a>
      <a class="btn-nav" href="novo.php">Novo produto</a>
      <a class="btn" href="carrinho.php" style="padding: 8px 16px; margin-left: 8px;">
        🛒 Carrinho (<?= $total_itens ?>) &bull; <?= preco($soma_carrinho) ?>
      </a>
    </nav>
  </header>

  <section class="hero">
    <h1>SWAG</h1>
    <p>Roupas e sapatos masculinos com atitude. Peças pretas, detalhes em ciano.</p>
    <div class="hero-links">
      <a class="btn" href="#roupas">Ver roupas</a>
      <a class="btn btn-vazio" href="#sapatos">Ver sapatos</a>
    </div>
  </section>

  <main>
    <?php foreach ($grupos as $nome => $lista): ?>
      <section class="vitrine" id="<?= strtolower($nome) ?>">
        <h2><?= $nome ?></h2>
        <?php if (!$lista): ?>
          <p class="vazio">Nenhum produto cadastrado. <a href="novo.php">Cadastre o primeiro.</a></p>
        <?php else: ?>
          <div class="grade">
            <?php foreach ($lista as $p): ?>
              <?php $no_carrinho = $_SESSION['carrinho'][$p['id']] ?? 0; ?>
              <article class="card">
                <div class="card-img">
                  <?php if (!empty($p['imagem'])): ?>
                    <img src="<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>" style="width: 100%; height: 220px; object-fit: cover; border-radius: 8px;">
                  <?php else: ?>
                    <span><?= htmlspecialchars(mb_substr($p['nome'], 0, 1)) ?></span>
                  <?php endif; ?>
                </div>
                <h3><?= htmlspecialchars($p['nome']) ?></h3>
                <p class="desc"><?= htmlspecialchars($p['descricao']) ?></p>
                <p class="tam">Tamanhos: <?= htmlspecialchars($p['tamanho']) ?></p>
                <div class="card-rodape">
                  <strong class="preco"><?= preco($p['preco']) ?></strong>
                  <span class="<?= $p['estoque'] > 0 ? 'ok' : 'esgotado' ?>">
                    <?= $p['estoque'] > 0 ? $p['estoque'] . ' em estoque' : 'Esgotado' ?>
                  </span>
                </div>

                <?php if ($p['estoque'] > 0): ?>
                  <a class="btn" href="index.php?adicionar=<?= $p['id'] ?>" style="display: block; text-align: center; margin-top: 12px;">
                    <?= $no_carrinho > 0 ? "Adicionar mais ($no_carrinho no carrinho)" : "Adicionar ao carrinho" ?>
                  </a>
                <?php else: ?>
                  <span class="btn btn-vazio" style="display: block; text-align: center; margin-top: 12px; opacity: 0.5; cursor: not-allowed;">
                    Indisponível
                  </span>
                <?php endif; ?>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
    <?php endforeach; ?>
  </main>

  <footer class="rodape">Swag &copy; <?= date('Y') ?>. Moda masculina.</footer>
</body>
</html>
