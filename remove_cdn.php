<?php
$dirs = ['category', 'subcategory', 'brand', 'unit', 'zone', 'expense_categories', 'roles'];
$basePath = __DIR__ . '/resources/views/admin_panel/';

$count = 0;
foreach ($dirs as $dir) {
    $path = $basePath . $dir;
    if (is_dir($path)) {
        $files = scandir($path);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $filePath = $path . '/' . $file;
                $content = file_get_contents($filePath);
                
                $originalContent = $content;
                
                // Remove CDN lines
                $cdnLines = [
                    '<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">',
                    '<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>',
                    '<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>'
                ];
                
                foreach ($cdnLines as $line) {
                    $content = str_replace($line, '', $content);
                }
                
                // Also remove aria-hidden="true" from modals to be absolutely safe
                $content = str_replace('aria-hidden="true"', '', $content);
                
                // Remove duplicate jquery includes if they exist right after each other
                $content = preg_replace('/(<script src="\{\{ asset\(\'assets\/js\/jquery\.min\.js\'\) \}\}"\><\/script>\s*)+/', '<script src="{{ asset(\'assets/js/jquery.min.js\') }}"></script>' . "\n", $content);
                
                if ($content !== $originalContent) {
                    file_put_contents($filePath, $content);
                    echo "Cleaned: $filePath\n";
                    $count++;
                }
            }
        }
    }
}
echo "Total files cleaned: $count\n";
