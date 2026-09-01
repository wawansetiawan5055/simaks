<?php
require_once __DIR__ . '/../config/db.php';
$pdo = connect_db();

try {
    $pdo->beginTransaction();

    // Hapus semua program yang ada
    $pdo->exec("TRUNCATE TABLE landing_programs");

    $programs = [
        [
            'title' => 'Be Moeslempreneur',
            'description' => 'Kegiatan untuk membentuk pengusaha muda yang memiliki jiwa enterpreneur berbasis pengetahuan islam',
            'icon' => 'fas fa-store',
            'order_display' => 1
        ],
        [
            'title' => 'Tahfidz Al Quran',
            'description' => 'Kegiatan untuk membentuk siswa dan siswi yang memiliki pemahaman dan hafalan alquran',
            'icon' => 'fas fa-book-open',
            'order_display' => 2
        ],
        [
            'title' => 'Pembiasaan Ibadah',
            'description' => 'Kegiatan untuk membentuk siswa dan siswi supaya terbiasa dalam melaksanakan ibadah seperti tadarus, solat dhuha dan solat berjamaah',
            'icon' => 'fas fa-mosque',
            'order_display' => 3
        ],
        [
            'title' => 'Kajian Kitab Kuning',
            'description' => 'Kegiatan untuk memperdalam pengetahuan islam',
            'icon' => 'fas fa-book',
            'order_display' => 4
        ]
    ];

    $stmt = $pdo->prepare("INSERT INTO landing_programs (title, description, icon, order_display, is_active, created_at, updated_at) VALUES (:title, :description, :icon, :order_display, 1, NOW(), NOW())");

    foreach ($programs as $program) {
        $stmt->execute([
            ':title' => $program['title'],
            ':description' => $program['description'],
            ':icon' => $program['icon'],
            ':order_display' => $program['order_display']
        ]);
    }

    $pdo->commit();
    echo "Program unggulan berhasil diupdate.\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Gagal: " . $e->getMessage() . "\n";
}
