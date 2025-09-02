<?php
require_once 'bootstrap.php';

echo "=== ESTRUCTURA DE LA TABLA CURSOS ===\n";
$db = Core\DB::getInstance();
$result = $db->query('DESCRIBE cursos');
foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $column) {
    echo $column['Field'] . ' - ' . $column['Type'] . "\n";
}
