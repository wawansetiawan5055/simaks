<?php
// quotes_index.php – List quotes with edit/delete actions
if (!isset($quotes)) $quotes = [];
?>
<div class="container my-4">
    <h2 class="mb-3">Manage Quotes</h2>
    <a href="<?= BASE_URL ?>landing_admin/quote_form" class="btn btn-primary mb-3">Add New Quote</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Quote</th>
                <th>Source</th>
                <th>Order</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($quotes as $q): ?>
                <tr>
                    <td><?= $q['id'] ?></td>
                    <td><?= htmlspecialchars($q['text']) ?></td>
                    <td><?= htmlspecialchars($q['source']) ?></td>
                    <td><?= $q['position'] ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>landing_admin/quote_form?id=<?= $q['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="<?= BASE_URL ?>landing_admin/quote_delete?id=<?= $q['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this quote?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
