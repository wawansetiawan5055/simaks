<?php
$public_path = __DIR__;
$cbt_symlink = $public_path . '/cbt';
$simaks_symlink = $public_path . '/simaks';

$results = [];

// Create cbt symlink
if (file_exists($cbt_symlink)) {
    if (is_link($cbt_symlink)) {
        unlink($cbt_symlink);
    } else {
        rename($cbt_symlink, $cbt_symlink . '_old_' . time());
    }
}
if (symlink('../cbt/public', $cbt_symlink)) {
    $results[] = 'Symlink cbt -> ../cbt/public created successfully.';
} else {
    $results[] = 'Failed to create symlink cbt.';
}

// Create simaks symlink (pointing to current directory)
if (file_exists($simaks_symlink)) {
    if (is_link($simaks_symlink)) {
        unlink($simaks_symlink);
    } else {
        // If it's a real directory, we might not want to delete it easily
        $results[] = 'simaks exists and is not a link. Skipping.';
    }
} else {
    if (symlink('.', $simaks_symlink)) {
        $results[] = 'Symlink simaks -> . created successfully.';
    } else {
        $results[] = 'Failed to create symlink simaks.';
    }
}

echo implode("\n", $results);
?>