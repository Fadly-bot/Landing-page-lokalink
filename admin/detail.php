<?php
/**
 * Lokalink - Admin Lead Detail
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../src/db.php';

admin_auth_require();

$pdo = db_connect();
if ($pdo === null) {
    echo '<!DOCTYPE html><html><head><title>Error</title></head><body><p>Database tidak tersedia.</p></body></html>';
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('/admin/');
}

// Fetch lead
try {
    $stmt = $pdo->prepare('SELECT * FROM leads WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $lead = $stmt->fetch();
} catch (PDOException $e) {
    error_log('[Lokalink] Admin detail error: ' . $e->getMessage());
    $lead = false;
}

if (!$lead) {
    redirect('/admin/');
}

$statusOptions = ['Baru', 'Sudah Dihubungi', 'Negosiasi', 'Deal', 'Selesai'];
$currentStatus = $lead['status'] ?? 'Baru';
$adminNotes    = $lead['admin_notes'] ?? '';

// Handle status/notes update
$success = '';
$updateError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $updateError = 'Sesi tidak valid. Silakan coba lagi.';
    } else {
        $newStatus = $_POST['status'] ?? $currentStatus;
        $newNotes  = trim((string) ($_POST['admin_notes'] ?? ''));

        if (!in_array($newStatus, $statusOptions, true)) {
            $newStatus = $currentStatus;
        }

        try {
            $upd = $pdo->prepare('UPDATE leads SET status = :status, admin_notes = :notes WHERE id = :id');
            $upd->execute([
                ':status' => $newStatus,
                ':notes'  => $newNotes,
                ':id'     => $id,
            ]);
            $currentStatus = $newStatus;
            $adminNotes    = $newNotes;
            $success = 'Perubahan berhasil disimpan.';
            // Re-fetch to ensure consistency
            $stmt = $pdo->prepare('SELECT * FROM leads WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $lead = $stmt->fetch();
        } catch (PDOException $e) {
            error_log('[Lokalink] Admin update error: ' . $e->getMessage());
            $updateError = 'Gagal menyimpan perubahan.';
        }
    }
}

$labelTypes = [
    'umkm-toko' => 'UMKM / Toko', 'kuliner' => 'Kuliner',
    'jasa' => 'Jasa', 'barbershop-salon' => 'Barbershop / Salon', 'lainnya' => 'Lainnya',
];
$labelNeeds = [
    'website-bisnis' => 'Website Bisnis', 'qr-review-card' => 'QR Review Card',
    'google-business' => 'Google Business', 'qr-menu' => 'QR Menu',
    'whatsapp' => 'WhatsApp', 'belum-tahu' => 'Belum Tahu',
];

function status_badge(string $status): string {
    $map = [
        'Baru'             => 'bg-blue-100 text-blue-800',
        'Sudah Dihubungi'  => 'bg-yellow-100 text-yellow-800',
        'Negosiasi'        => 'bg-orange-100 text-orange-800',
        'Deal'             => 'bg-green-100 text-green-800',
        'Selesai'          => 'bg-slate-100 text-slate-600',
    ];
    $cls = $map[$status] ?? 'bg-slate-100 text-slate-600';
    return '<span class="inline-block px-2.5 py-1 rounded-full text-xs font-medium ' . $cls . '">' . e($status) . '</span>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lead — Admin Lokalink</title>
    <link rel="icon" type="image/png" href="/src/img/logo/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:'#f0fdfa',100:'#ccfbf1',200:'#99f6e4',
                            400:'#2dd4bf',500:'#14b8a6',600:'#0d9488',700:'#0f766e',900:'#134e4a'
                        }
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-slate-50 min-h-screen">

<header class="bg-white border-b border-slate-200 sticky top-0 z-40">
    <nav class="max-w-5xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
        <a href="/admin/" class="flex items-center gap-2">
            <img src="/src/img/logo/logo.png" alt="Lokalink" class="h-7 w-auto">
            <span class="text-sm font-semibold text-slate-700">Admin</span>
        </a>
        <div class="flex items-center gap-3">
            <a href="/admin/" class="text-sm text-slate-500 hover:text-slate-700">Dashboard</a>
            <form method="post" action="/admin/logout.php" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">Logout</button>
            </form>
        </div>
    </nav>
</header>

<main class="max-w-5xl mx-auto px-4 sm:px-6 py-8">

    <a href="/admin/" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 mb-6">
        &larr; Kembali ke Dashboard
    </a>

    <?php if ($success): ?>
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm" role="status">
            <?= e($success) ?>
        </div>
    <?php endif; ?>
    <?php if ($updateError): ?>
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm" role="alert">
            <?= e($updateError) ?>
        </div>
    <?php endif; ?>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Lead Info -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-lg font-bold text-slate-900">Detail Lead</h1>
                <?= status_badge($currentStatus) ?>
            </div>

            <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                <div>
                    <dt class="text-slate-500">Nama</dt>
                    <dd class="mt-0.5 font-medium text-slate-900"><?= e($lead['name']) ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500">Nama Bisnis</dt>
                    <dd class="mt-0.5 font-medium text-slate-900"><?= e($lead['business_name']) ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500">Jenis Bisnis</dt>
                    <dd class="mt-0.5 font-medium text-slate-900"><?= e($labelTypes[$lead['business_type']] ?? $lead['business_type']) ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500">WhatsApp</dt>
                    <dd class="mt-0.5 font-mono text-slate-900">
                        <?= e($lead['whatsapp']) ?>
                        <?php
                        $waNum = preg_replace('/\D/', '', $lead['whatsapp']);
                        if (preg_match('/^62\d{8,13}$/', $waNum)):
                        ?>
                            <a href="https://wa.me/<?= e($waNum) ?>" target="_blank" rel="noopener noreferrer"
                               class="ml-2 text-green-600 hover:text-green-700 text-xs font-medium">Chat WhatsApp</a>
                        <?php endif; ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Kebutuhan</dt>
                    <dd class="mt-0.5 font-medium text-slate-900"><?= e($labelNeeds[$lead['needs']] ?? ($lead['needs'] ?? '—')) ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500">Source</dt>
                    <dd class="mt-0.5 font-medium text-slate-900"><?= e($lead['source'] ?? '—') ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500">Tanggal Dibuat</dt>
                    <dd class="mt-0.5 font-medium text-slate-900"><?= e($lead['created_at']) ?></dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-slate-500">Pesan</dt>
                    <dd class="mt-0.5 text-slate-900 whitespace-pre-wrap"><?= e($lead['message'] ?? '—') ?></dd>
                </div>
            </dl>
        </div>

        <!-- Status & Notes Form -->
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Kelola Lead</h2>
            <form method="post" action="/admin/detail.php?id=<?= e((string) $id) ?>" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                    <select id="status" name="status"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 bg-white focus:border-brand-600 outline-none text-sm">
                        <?php foreach ($statusOptions as $opt): ?>
                            <option value="<?= e($opt) ?>" <?= $currentStatus === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="admin_notes" class="block text-sm font-medium text-slate-700">Catatan Admin</label>
                    <textarea id="admin_notes" name="admin_notes" rows="6"
                              class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-brand-600 focus:ring-2 focus:ring-brand-200 outline-none text-sm"
                              placeholder="Tambahkan catatan tentang lead ini..."><?= e($adminNotes) ?></textarea>
                </div>

                <button type="submit"
                        class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2.5 rounded-lg transition text-sm">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

</main>

</body>
</html>
