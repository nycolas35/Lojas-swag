<?php
session_start();
require 'conexao.php';

date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d');

// Busca todos os pedidos diretamente (LEFT JOIN evita que o pedido suma se o cliente faltar)
$sql = "SELECT p.*, c.nome AS cliente_nome, c.email AS cliente_email, c.telefone AS cliente_telefone 
        FROM pedidos p 
        LEFT JOIN clientes c ON p.cliente_id = c.id 
        ORDER BY p.id DESC";
$pedidos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$stmtItens = $pdo->prepare("
    SELECT ip.quantidade, ip.preco_unitario, pr.nome AS produto_nome, pr.tamanho 
    FROM itens_pedido ip 
    LEFT JOIN produtos pr ON ip.produto_id = pr.id 
    WHERE ip.pedido_id = ?
");

$faturamento_dia = 0;
$pedidos_dia = 0;
$faturamento_total = 0;

foreach ($pedidos as &$ped) {
    $faturamento_total += (float) $ped['total'];

    // Pega qualquer coluna de data existente ou considera hoje se estiver vazia
    $coluna_data = $ped['data_pedido'] ?? $ped['created_at'] ?? $ped['data'] ?? '';
    $data_somente = substr($coluna_data, 0, 10);

    $ped['eh_hoje'] = ($data_somente === $hoje);
    $ped['data_formatada'] = $coluna_data ? date('d/m/Y H:i', strtotime($coluna_data)) : date('d/m/Y');

    if ($ped['eh_hoje']) {
        $faturamento_dia += (float) $ped['total'];
        $pedidos_dia++;
    }

    // Garante que o nome apareça mesmo se o cliente não tiver vínculo
    if (empty($ped['cliente_nome'])) {
        $ped['cliente_nome'] = 'Cliente #' . $ped['cliente_id'];
        $ped['cliente_email'] = 'Não informado';
    }

    $stmtItens->execute([$ped['id']]);
    $ped['itens'] = $stmtItens->fetchAll(PDO::FETCH_ASSOC);
}
unset($ped);
?>
