<?php
$dirs = ['category', 'subcategory', 'brand', 'unit', 'zone'];
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
                
                // Replace data-bs- attributes
                $content = str_replace('data-bs-toggle="modal"', 'data-toggle="modal"', $content);
                $content = str_replace('data-bs-target="#', 'data-target="#', $content);
                $content = str_replace('data-bs-dismiss="modal"', 'data-dismiss="modal"', $content);
                
                // Also add the blur script if missing
                $blurScript = "
        // Fix ARIA focus warning on modal close
        $('.modal').on('hide.bs.modal', function () {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });
";
                // Only add if not already present and if there's a script tag
                if (strpos($content, '<script>') !== false && strpos($content, 'document.activeElement.blur()') === false) {
                    $content = str_replace('<script>', "<script>" . $blurScript, $content);
                }
                
                if ($content !== $originalContent) {
                    file_put_contents($filePath, $content);
                    echo "Updated: $filePath\n";
                    $count++;
                }
            }
        }
    }
}
echo "Total files updated: $count\n";
