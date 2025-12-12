<?php
require_once __DIR__ . '/db.php';
$result = $pdo->query('DESCRIBE events');
echo "Events table columns:\n";
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
