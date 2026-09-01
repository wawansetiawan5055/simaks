<tr>
    <td class="text-center align-middle font-weight-bold text-muted small"><?= $no++ ?></td>
    <td class="align-middle">
        <span class="font-weight-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($p['nama_peran']) ?></span>
    </td>
    <td class="text-center align-middle">
        <div class="btn-group">
            <a href="<?= BASE_URL ?>peran/form?id=<?= $p['id_peran'] ?>" 
               class="btn btn-xs btn-outline-warning border-0 p-1 mr-1" 
               style="background: #fffbeb; width: 28px; height: 28px; border-radius: 8px; color: #d97706;" 
               title="Edit"><i class="fas fa-pencil-alt" style="font-size: 0.8rem;"></i></a>
            
            <?php if (!in_array($p['id_peran'], [1, 2, 3])): ?>
                <a href="<?= BASE_URL ?>peran/delete_action?id=<?= $p['id_peran'] ?>" 
                   class="btn btn-xs btn-outline-danger border-0 p-1" 
                   style="background: #fef2f2; width: 28px; height: 28px; border-radius: 8px; color: #dc2626;" 
                   title="Hapus" onclick="return confirmDelete(event)">
                    <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                </a>
            <?php else: ?>
                <button class="btn btn-xs btn-secondary border-0 p-1" 
                        style="background: #f1f5f9; width: 28px; height: 28px; border-radius: 8px; color: #94a3b8;" 
                        title="Sistem Default (Locked)" disabled>
                    <i class="fas fa-lock" style="font-size: 0.8rem;"></i>
                </button>
            <?php endif; ?>
        </div>
    </td>
</tr>
