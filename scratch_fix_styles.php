<?php
$createFile = 'c:/xampp/htdocs/atif_traderss/resources/views/admin_panel/sale/add_sale222.blade.php';
$editFile = 'c:/xampp/htdocs/atif_traderss/resources/views/admin_panel/sale/edit_sale.blade.php';

$createContent = file_get_contents($createFile);
$editContent = file_get_contents($editFile);

preg_match_all('/<style>.*?<\/style>/s', $createContent, $createStyles);
$allCreateStyles = implode("\n    ", $createStyles[0]);

$newEditContent = preg_replace('/(<style>.*?<\/style>\s*)+/s', $allCreateStyles . "\n\n", $editContent, 1);

file_put_contents($editFile, $newEditContent);
echo "Replaced successfully.\n";
