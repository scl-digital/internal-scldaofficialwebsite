<?php
require_once 'db.php';
$invoiceId = $_GET['invoice_id'] ?? null;
if (!$invoiceId) {
    http_response_code(400);
    echo 'invoice_id required';
    exit;
}
$stmt = $pdo->prepare('SELECT i.*, c.name AS client_name, c.email AS client_email, c.company FROM invoices i JOIN clients c ON c.id = i.client_id WHERE i.id = ?');
$stmt->execute([$invoiceId]);
$inv = $stmt->fetch();
if (!$inv) { http_response_code(404); echo 'Invoice not found'; exit; }
$itemsStmt = $pdo->prepare('SELECT * FROM invoice_items WHERE invoice_id = ?');
$itemsStmt->execute([$invoiceId]);
$items = $itemsStmt->fetchAll();

// Get company settings
$companyStmt = $pdo->query("SELECT * FROM company_settings ORDER BY id DESC LIMIT 1");
$company = $companyStmt->fetch() ?: [
    'company_name' => 'SCL Digital Agency (Pty) Ltd',
    'company_email' => 'info@softwarecreativelabs.com',
    'company_phone' => '+27 21 123 4567',
    'company_address' => 'Cape Town, South Africa',
    'company_website' => 'https://softwarecreativelabs.com',
    'logo_url' => '/images/SCL_Blue.png'
];

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice <?= htmlspecialchars($inv['invoice_number']) ?></title>
<style>
 body{font-family: Arial, sans-serif; margin:40px; background:#fff;}
 h1{margin:0 0 10px; color:#2c3e50;}
 .muted{color:#666}
 table{width:100%;border-collapse:collapse;margin-top:20px}
 th,td{border:1px solid #ddd;padding:8px;text-align:left}
 th{background:#f8f9fa; font-weight:bold; color:#2c3e50;}
 tbody tr:nth-child(even){background:#f9f9f9;}
 tbody tr:hover{background:#e3f2fd;}
 tfoot td{font-weight:bold; background:#f8f9fa;}
 .totals{margin-top:20px}
 .header{display:flex;justify-content:space-between;align-items:flex-start; margin-bottom:30px; padding-bottom:20px; border-bottom:2px solid #e9ecef;}
 .company-info{text-align:right; color:#666; font-size:14px;}
 .company-logo{max-height:80px; max-width:200px; margin-bottom:10px;}
 .invoice-title{color:#2c3e50; font-size:28px; font-weight:bold;}
 .client-info{background:#f8f9fa; padding:15px; border-radius:5px; margin:20px 0;}
 .invoice-details{display:flex; justify-content:space-between; margin:20px 0;}
 .invoice-meta{background:#e9ecef; padding:10px; border-radius:5px;}
</style>
</head>
<body>
  <div class="header">
    <div>
      <div class="invoice-title">Invoice <?= htmlspecialchars($inv['invoice_number']) ?></div>
      <div class="invoice-details">
        <div class="invoice-meta">
          <div><strong>Issue Date:</strong> <?= htmlspecialchars($inv['issue_date']) ?></div>
          <div><strong>Due Date:</strong> <?= htmlspecialchars($inv['due_date']) ?></div>
          <div><strong>Status:</strong> <span style="color: <?= $inv['status'] === 'paid' ? 'green' : ($inv['status'] === 'overdue' ? 'red' : 'green') ?>"><?= htmlspecialchars($inv['status']) ?></span></div>
        </div>
      </div>
    </div>
    <div class="company-info">
      <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="Company Logo" class="company-logo" onerror="this.style.display='none'">
      <div><strong><?= htmlspecialchars($company['company_name']) ?></strong></div>
      <div><?= htmlspecialchars($company['company_email']) ?></div>
      <div><?= htmlspecialchars($company['company_phone']) ?></div>
      <div><?= htmlspecialchars($company['company_address']) ?></div>
      <div><a href="<?= htmlspecialchars($company['company_website']) ?>" target="_blank"><?= htmlspecialchars($company['company_website']) ?></a></div>
    </div>
  </div>
  
  <div class="client-info">
    <h3>Bill To:</h3>
    <div><strong><?= htmlspecialchars($inv['company'] ?: $inv['client_name']) ?></strong></div>
    <div><?= htmlspecialchars($inv['client_email']) ?></div>
  </div>
  <?php if (!empty($inv['description'])): ?>
    <p><?= nl2br(htmlspecialchars($inv['description'])) ?></p>
  <?php endif; ?>
  
  <h3 style="color:#2c3e50; margin:20px 0 10px 0; font-size:18px;">Services Provided</h3>
  <table>
    <thead>
      <tr><th>Description</th><th>Qty</th><th>Unit</th><th>Total</th></tr>
    </thead>
    <tbody>
      <?php if ($items && count($items) > 0): foreach ($items as $it): ?>
        <tr>
          <td><strong><?= htmlspecialchars($it['description']) ?></strong></td>
          <td><?= htmlspecialchars($it['quantity']) ?></td>
          <td>R <?= number_format((float)$it['unit_price'],2) ?></td>
          <td><strong>R <?= number_format((float)$it['total_price'],2) ?></strong></td>
        </tr>
      <?php endforeach; else: ?>
        <tr>
          <td><strong>Service</strong></td>
          <td>1</td>
          <td>R <?= number_format((float)$inv['amount'],2) ?></td>
          <td><strong>R <?= number_format((float)$inv['amount'],2) ?></strong></td>
        </tr>
      <?php endif; ?>
    </tbody>
    <tfoot>
      <tr><td colspan="3">Amount</td><td>R <?= number_format((float)$inv['amount'],2) ?></td></tr>
      <tr><td colspan="3">Tax</td><td>R <?= number_format((float)$inv['tax_amount'],2) ?></td></tr>
      <tr><td colspan="3">Total</td><td>R <?= number_format((float)$inv['total_amount'],2) ?></td></tr>
    </tfoot>
  </table>
  <div class="totals">
    <button onclick="window.print()">Print / Save as PDF</button>
  </div>
</body>
</html>
