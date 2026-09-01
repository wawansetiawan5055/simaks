<?php
require 'config/env.php';
require 'config/db.php';
$pdo = connect_db();

$stmt = $pdo->query('SELECT id_menu, link FROM app_menu');
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count = 0;
foreach ($menus as $menu) {
    $link = $menu['link'];
    if (strpos($link, 'index.php?mod=') === 0) {
        $parsed = parse_url($link);
        parse_str($parsed['query'] ?? '', $query);
        
        $new_link = $query['mod'] ?? '';
        if (isset($query['act']) && $query['act'] !== 'index' && $query['act'] !== '') {
            $new_link .= '/' . $query['act'];
        }
        
        // Remove mod and act from query to append the rest
        unset($query['mod']);
        unset($query['act']);
        
        if (!empty($query)) {
            $new_link .= '?' . http_build_query($query);
        }
        
        $update = $pdo->prepare('UPDATE app_menu SET link = ? WHERE id_menu = ?');
        $update->execute([$new_link, $menu['id_menu']]);
        $count++;
        echo "Updated menu ID {$menu['id_menu']}: $link -> $new_link\n";
    }
}
echo "Total database links updated: $count\n";
