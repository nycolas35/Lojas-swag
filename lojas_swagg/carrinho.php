<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['carrinho'])) { $_SESSION['carrinho'] = []; }

if (isset($_GET['mais'])) {
  $id = (int) $_GET['mais'];
  if (isset($_SESSION['carrinho'][$id])) { $_SESSION['carrinho'][$id]++; }
  header('Location: carrinho.php'); exit;
}

if (isset($_GET['menos'])) {
  $id = (int) $_GET['menos'];
  if (isset($_SESSION['carrinho'][$id])) {
    $_SESSION['carrinho'][$id]--;
    if ($_SESSION['carrinho'][$id] <= 0) { unset($_SESSION['carrinho'][$id]); }
  }
  header('Location: carrinho.php'); exit;
}

if (isset($_GET['remover'])) {
  unset($_SESSION['carrinho'][(int) $_GET['remover']]);
  header('Location: carrinho.php'); exit;
}

if (isset($_GET['limpar'])) {
  $_SESSION['carrinho'] = [];
  header('Location: carrinho.php'); exit;
}

$itens = [];
$subtotal_produtos = 0;
$total_pecas = 0;

if (!empty($_SESSION['carrinho'])) {
  $ids = implode(',', array_map('intval', array_keys($_SESSION['carrinho'])));
  $stmt = $pdo->query("SELECT * FROM produtos WHERE id IN ($ids)");
  foreach ($stmt as $p) {
    $qtd = min($_SESSION['carrinho'][$p['id']], $p['estoque']);
    $_SESSION['carrinho'][$p['id']] = $qtd;
    $sub = $p['preco'] * $qtd;
    $subtotal_produtos += $sub;
    $total_pecas += $qtd;
    $p['qtd'] = $qtd;
    $p['subtotal'] = $sub;
    $itens[] = $p;
  }
}

$valor_impostos = round($subtotal_produtos * 0.12, 2);
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($itens)) {
  $nome        = trim($_POST['nome'] ?? '');
  $email       = trim($_POST['email'] ?? '');
  $telefone    = trim($_POST['telefone'] ?? '');
  $cep         = trim($_POST['cep'] ?? '');
  $rua         = trim($_POST['rua'] ?? '');
  $numero      = trim($_POST['numero'] ?? '');
  $complemento = trim($_POST['complemento'] ?? '');
  $bairro      = trim($_POST['bairro'] ?? '');
  $cidade      = trim($_POST['cidade'] ?? '');
  $estado      = strtoupper(trim($_POST['estado'] ?? ''));
  $valor_frete = (float) ($_POST['valor_frete'] ?? 0);
  $pgto        = trim($_POST['forma_pagamento'] ?? 'Pix');

  if ($nome && $email && $cep && $rua && $numero && $bairro && $cidade && $estado) {
    $desc_pix = ($pgto === 'Pix') ? round($subtotal_produtos * 0.05, 2) : 0;
    $total_final = ($subtotal_produtos - $desc_pix) + $valor_impostos + $valor_frete;

    $stmtCli = $pdo->prepare("SELECT id FROM clientes WHERE email = ?");
    $stmtCli->execute([$email]);
    $cli = $stmtCli->fetch();

    if ($cli) {
      $cliente_id = $cli['id'];
      $pdo->prepare("UPDATE clientes SET nome = ?, telefone = ? WHERE id = ?")->execute([$nome, $telefone, $cliente_id]);
    } else {
      $pdo->prepare("INSERT INTO clientes (nome, email, telefone) VALUES (?, ?, ?)")->execute([$nome, $email, $telefone]);
      $cliente_id = $pdo->lastInsertId();
    }

    $pdo->prepare("INSERT INTO pedidos (cliente_id, cep, rua, numero, complemento, bairro, cidade, estado, valor_impostos, valor_frete, forma_pagamento, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
        ->execute([$cliente_id, $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $valor_impostos, $valor_frete, $pgto, $total_final]);
    $pedido_id = $pdo->lastInsertId();

    $insItem = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
    $updEst  = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ? AND estoque >= ?");

    foreach ($itens as $item) {
      $insItem->execute([$pedido_id, $item['id'], $item['qtd'], $item['preco']]);
      $updEst->execute([$item['qtd'], $item['id'], $item['qtd']]);
    }

    $_SESSION['carrinho'] = [];
    $itens = [];
    $mensagem = "Pedido #$pedido_id finalizado via $pgto! Entrega em $rua, $numero - $cidade/$estado (CEP: $cep).";
  } else {
    $erro = "Preencha todos os campos obrigatórios de contato e endereço.";
  }
}

$html_cards = '';
foreach ($itens as $item) {
  $img = !empty($item['imagem'])
    ? '<img src="' . htmlspecialchars($item['imagem']) . '" class="foto-card">'
    : '<span>' . htmlspecialchars(mb_substr($item['nome'], 0, 1)) . '</span>';

  $html_cards .= '<article class="card">'
    . '<div class="card-img">' . $img . '</div>'
    . '<h3>' . htmlspecialchars($item['nome']) . '</h3>'
    . '<p class="tam">Tamanho: ' . htmlspecialchars($item['tamanho']) . ' &bull; Unitário: ' . preco($item['preco']) . '</p>'
    . '<div class="controle-qtd"><span>Qtd:</span>'
    . '<a href="carrinho.php?menos=' . $item['id'] . '" class="btn btn-vazio btn-mini">-</a>'
    . '<strong>' . $item['qtd'] . '</strong>'
    . '<a href="carrinho.php?mais=' . $item['id'] . '" class="btn btn-vazio btn-mini">+</a></div>'
    . '<div class="card-rodape"><strong class="preco">' . preco($item['subtotal']) . '</strong>'
    . '<a href="carrinho.php?remover=' . $item['id'] . '" class="esgotado">Remover</a></div>'
    . '</article>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Carrinho & Pagamento | Swag</title>
  <link rel="preconnect" href="https://googleapis.com">
  <link href="https://googleapis.com/css2?family=Anton&family=Manrope:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="estilo.css">
  <style>
    .oculto { display: none; }
    .foto-card { width: 100%; height: 200px; object-fit: cover; border-radius: 8px; }
    .controle-qtd { display: flex; align-items: center; gap: 10px; margin: 12px 0; }
    .btn-mini { padding: 4px 10px; }
    .checkout-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-top: 32px; }
    .box-checkout { padding: 24px; background: #141414; border-radius: 8px; border: 1px solid #262626; display: flex; flex-direction: column; gap: 12px; }
    .campo { width: 100%; padding: 12px; border-radius: 6px; border: 1px solid #333; background: #0d0d0d; color: #fff; font-family: inherit; }
    .campo:focus { outline: none; border-color: #00f0ff; }
    .linha-dupla { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .label-form { font-size: 0.85rem; color: #aaa; margin-bottom: 4px; display: block; }
    .opcao-pgto { display: flex; align-items: center; gap: 10px; padding: 12px; border: 1px solid #2a2a2a; border-radius: 6px; background: #0d0d0d; cursor: pointer; margin-bottom: 8px; }
    .opcao-pgto:hover { border-color: #00f0ff; }
    .linha-resumo { display: flex; justify-content: space-between; margin-bottom: 10px; color: #ccc; font-size: 0.95rem; }
    .total-destaque { display: flex; justify-content: space-between; align-items: center; font-size: 1.4rem; margin-top: 14px; padding-top: 14px; border-top: 1px solid #262626; }
    .alerta-ok { padding: 20px; border: 1px solid #00f0ff; background: #141414; border-radius: 8px; margin-bottom: 24px; }
    .alerta-erro { padding: 14px; border: 1px solid #ff4d4d; background: #1a0d0d; border-radius: 8px; margin-bottom: 20px; color: #ff6b6b; }
  </style>
</head>
<body>
  <header class="topo">
    <a class="logo" href="index.php">LOJAS SWAG</a>
    <nav>
      <a href="index.php#roupas">Roupas</a>
      <a href="index.php#sapatos">Sapatos</a>
      <a class="btn" href="index.php" style="padding: 8px 16px;">&larr; Continuar comprando</a>
    </nav>
  </header>

  <main class="vitrine">
    <h2>Seu Carrinho</h2>

    <?php if ($mensagem !== '') { ?>
      <div class="alerta-ok">
        <p class="ok" style="margin-bottom: 12px; font-size: 1.1rem;"><?php echo htmlspecialchars($mensagem); ?></p>
        <a class="btn" href="index.php">Voltar para a vitrine</a>
      </div>
    <?php } ?>

    <?php if ($erro !== '') { ?>
      <div class="alerta-erro"><?php echo htmlspecialchars($erro); ?></div>
    <?php } ?>

    <?php if (empty($itens)) { ?>
      <p class="vazio">Seu carrinho está vazio no momento. <a href="index.php">Escolher roupas e sapatos.</a></p>
    <?php } else { ?>
      <div style="margin-bottom: 16px;">
        <a href="carrinho.php?limpar=1" class="btn btn-vazio btn-mini">Limpar carrinho</a>
      </div>

      <div class="grade"><?php echo $html_cards; ?></div>

      <form method="POST" class="checkout-grid">
        <div class="box-checkout">
          <h3 style="color: #00f0ff;">1. Dados e Endereço de Entrega</h3>
          <div>
            <label class="label-form">Nome completo *</label>
            <input type="text" name="nome" class="campo" required>
          </div>
          <div class="linha-dupla">
            <div>
              <label class="label-form">E-mail *</label>
              <input type="email" name="email" class="campo" required>
            </div>
            <div>
              <label class="label-form">Telefone</label>
              <input type="text" name="telefone" class="campo">
            </div>
          </div>
          <div class="linha-dupla">
            <div>
              <label class="label-form">CEP *</label>
              <input type="text" name="cep" class="campo" required>
            </div>
            <div>
              <label class="label-form">Estado (UF) *</label>
              <input type="text" name="estado" maxlength="2" class="campo" placeholder="PR" required>
            </div>
          </div>
          <div>
            <label class="label-form">Rua *</label>
            <input type="text" name="rua" class="campo" required>
          </div>
          <div class="linha-dupla">
            <div>

            </div>
            <div>

            </div>
          </div>
          <div class="linha-dupla">
            <div>

    <!-- Linha de Número e Complemento (mantém igual) -->
    <div class="linha-dupla">
        <div>
            <label class="label-form">Número *</label>
            <input type="text" name="numero" class="campo" value="<?php echo htmlspecialchars($_POST['numero'] ?? ''); ?>" required>
        </div>
        <div>
            <label class="label-form">Complemento</label>
            <input type="text" name="complemento" class="campo" value="<?php echo htmlspecialchars($_POST['complemento'] ?? ''); ?>">
        </div>
    </div>

    <!-- Linha de Bairro e Cidade (corrigida, sem div extra) -->
    <div class="linha-dupla">
        <div>
            <label class="label-form">Bairro *</label>
            <input type="text" name="bairro" class="campo" value="<?php echo htmlspecialchars($_POST['bairro'] ?? ''); ?>" required>
        </div>
        <div>
            <label class="label-form">Cidade *</label>
            <input type="text" name="cidade" class="campo" value="<?php echo htmlspecialchars($_POST['cidade'] ?? ''); ?>" required>
        </div>
    </div>

    <!-- Botão embaixo ocupando 100% da largura -->
    <button type="submit" name="btn_confirmar_endereco" value="1" class="btn" style="width: 202%; margin-top: 12px; background: #00f0ff; color: #000; font-weight: 600;">
        ✓ Confirmar Endereço
    </button>

    <?php if (isset($_POST['btn_confirmar_endereco'])): ?>
        <p style="color: #00f0ff; font-weight: 600; margin-top: 8px;">
            ✓ Endereço confirmado: <?php echo htmlspecialchars($_POST['rua'] ?? ''); ?>, <?php echo htmlspecialchars($_POST['numero'] ?? ''); ?> - <?php echo htmlspecialchars($_POST['cidade'] ?? ''); ?>/<?php echo strtoupper(htmlspecialchars($_POST['estado'] ?? '')); ?>
        </p>
    <?php endif; ?>
</div>





      </form>
    <?php } ?>
  </main>
</body>
</html>
