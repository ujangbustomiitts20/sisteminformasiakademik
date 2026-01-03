<?php
$source = new mysqli('192.168.120.121', 'root', 'bismillaH', 'itts_sikad');

echo "=== TABLES DI DATABASE itts_sikad ===\n\n";

$result = $source->query('SHOW TABLES');
$tables = [];
while($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

echo "Total: " . count($tables) . " tables\n\n";

// Group by prefix
$groups = [];
foreach($tables as $t) {
    $prefix = explode('_', $t)[0];
    if (!isset($groups[$prefix])) $groups[$prefix] = [];
    $groups[$prefix][] = $t;
}

ksort($groups);

foreach($groups as $prefix => $items) {
    echo "[$prefix] (" . count($items) . " tables):\n";
    foreach($items as $t) {
        // Get row count
        $cnt = $source->query("SELECT COUNT(*) as c FROM `$t`")->fetch_assoc()['c'];
        if ($cnt > 0) {
            echo "  - $t: $cnt rows\n";
        }
    }
    echo "\n";
}

$source->close();
