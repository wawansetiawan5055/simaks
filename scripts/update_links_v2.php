<?php
$dir = __DIR__ . '/../app/views';

function process_directory($dir) {
    $files = scandir($dir);
    $count = 0;
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            $count += process_directory($path);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $content = file_get_contents($path);
            
            // Regex to find index.php?mod=X&act=Y&Z... or just index.php?mod=X
            $new_content = preg_replace_callback('/index\.php\?mod=([a-zA-Z0-9_]+)(?:&amp;|&)act=([a-zA-Z0-9_]+)((?:&amp;|&)[^"\'\s>]+)?/', function($matches) {
                $mod = $matches[1];
                $act = $matches[2];
                $rest = $matches[3] ?? '';
                if ($rest !== '') {
                    // Convert the first & or &amp; to ?
                    $rest = preg_replace('/^(?:&amp;|&)/', '?', $rest);
                }
                return "<?= BASE_URL ?>$mod/$act$rest";
            }, $content);

            $new_content = preg_replace_callback('/index\.php\?mod=([a-zA-Z0-9_]+)((?:&amp;|&)[^"\'\s>]+)?/', function($matches) {
                $mod = $matches[1];
                $rest = $matches[2] ?? '';
                if ($rest !== '') {
                    $rest = preg_replace('/^(?:&amp;|&)/', '?', $rest);
                }
                return "<?= BASE_URL ?>$mod$rest";
            }, $new_content);

            if ($content !== $new_content) {
                file_put_contents($path, $new_content);
                echo "Updated $path\n";
                $count++;
            }
        }
    }
    return $count;
}

$updated = process_directory($dir);
echo "Total files updated: $updated\n";
