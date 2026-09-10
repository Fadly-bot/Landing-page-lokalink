<?php
/**
 * Lokalink - Admin Dashboard (Lead Management)
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../src/db.php';

admin_auth_require();

$pdo = db_connect();
if ($pdo === null) {
    echo '<!DOCTYPE html><html><head><title>Error</title></head><body><p>Database tidak tersedia.</p></body></html>';
    exit;
}

// --- Input sanitization ---
$search     = trim((string) ($_GET['search'] ?? ''));
$page       = max(1, (int) ($_GET['page'] ?? 1));
$perPage    = 20;
$offset     = ($page - 1) * $perPage;
$filterType = $_GET['type'] ?? '';
$filterNeed = $_GET['need'] ?? '';

$allowedTypes = ['umkm-toko', 'kuliner', 'jasa', 'barbershop-salon', 'lainnya'];
$allowedNeeds = ['website-bisnis', 'qr-review-card', 'google-business', 'qr-menu', 'whatsapp', 'belum-tahu'];

if (!in_array($filterType, $allowedTypes, true)) {
    $filterType = '';
}
if (!in_array($filterNeed, $allowedNeeds, true)) {
    $filterNeed = '';
}

// --- Stats ---
try {
    $totalLeads = (int) $pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();
    $todayLeads = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE DATE(created_at) = CURDATE()")->fetchColumn();
    $weekLeads  = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetchColumn();
} catch (PDOException $e) {
    error_log('[Lokalink] Admin stats error: ' . $e->getMessage());
    $totalLeads = $todayLeads = $weekLeads = 0;
}

// --- Build query ---
$where  = [];
$params = [];

if ($search !== '') {
    $where[]  = '(name LIKE :search OR business_name LIKE :search2 OR whatsapp LIKE :search3)';
    $s        = '%' . $search . '%';
    $params[':search']  = $s;
    $params[':search2'] = $s;
    $params[':search3'] = $s;
}

if ($filterType !== '') {
    $where[]           = 'business_type = :type';
    $params[':type']   = $filterType;
}

if ($filterNeed !== '') {
    $where[]           = 'needs = :need';
    $params[':need']   = $filterNeed;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Count total for pagination
$countSQL = "SELECT COUNT(*) FROM leads $whereSQL";
try {
    $stmtCount = $pdo->prepare($countSQL);
    $stmtCount->execute($params);
    $totalRows = (int) $stmtCount->fetchColumn();
} catch (PDOException $e) {
    error_log('[Lokalink] Admin count error: ' . $e->getMessage());
    $totalRows = 0;
}

$totalPages = max(1, (int) ceil($totalRows / $perPage));
$page       = min($page, $totalPages);

// Fetch leads
$querySQL = "SELECT id, name, business_name, business_type, whatsapp, needs, created_at,
                    COALESCE(status, 'Baru') AS status
             FROM leads $whereSQL
             ORDER BY created_at DESC
             LIMIT :limit OFFSET :offset";

$leads = [];
try {
    $stmt = $pdo->prepare($querySQL);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $leads = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[Lokalink] Admin query error: ' . $e->getMessage());
}

// --- Helper: build base query string for pagination ---
function admin_base_qs(array $overrides = []): string {
    $params = [
        'search' => $_GET['search'] ?? '',
        'type'   => $_GET['type'] ?? '',
        'need'   => $_GET['need'] ?? '',
        'page'   => $_GET['page'] ?? '1',
    ];
    $params = array_merge($params, $overrides);
    $parts = [];
    foreach ($params as $k => $v) {
        if ($v !== '' && $v !== null) {
            $parts[] = rawurlencode($k) . '=' . rawurlencode($v);
        }
    }
    return $parts ? '?' . implode('&', $parts) : '';
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

// Status badge colors
function status_badge(string $status): string {
    $map = [
        'Baru'             => 'bg-blue-100 text-blue-800',
        'Sudah Dihubungi'  => 'bg-yellow-100 text-yellow-800',
        'Negosiasi'        => 'bg-orange-100 text-orange-800',
        'Deal'             => 'bg-green-100 text-green-800',
        'Selesai'          => 'bg-slate-100 text-slate-600',
    ];
    $cls = $map[$status] ?? 'bg-slate-100 text-slate-600';
    return '<span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium ' . $cls . '">' . e($status) . '</span>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Lokalink</title>
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

<!-- Header -->
<header class="bg-white border-b border-slate-200 sticky top-0 z-40">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
        <a href="/admin/" class="flex items-center gap-2">
            <img src="/src/img/logo/logo.png" alt="Lokalink" class="h-7 w-auto">
            <span class="text-sm font-semibold text-slate-700">Admin</span>
        </a>
        <div class="flex items-center gap-3">
            <a href="/" class="text-sm text-slate-500 hover:text-slate-700">Lihat Website</a>
            <form method="post" action="/admin/logout.php" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">Logout</button>
            </form>
        </div>
    </nav>
</header>

<main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <!-- Stats -->
    <div class="grid gap-4 sm:grid-cols-3 mb-8">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Total Leads</p>
            <p class="mt-1 text-3xl font-bold text-slate-900"><?= e((string) $totalLeads) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Leads Hari Ini</p>
            <p class="mt-1 text-3xl font-bold text-brand-600"><?= e((string) $todayLeads) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Leads Minggu Ini</p>
            <p class="mt-1 text-3xl font-bold text-brand-600"><?= e((string) $weekLeads) ?></p>
        </div>
    </div>

    <!-- Search + Filters -->
    <form method="get" action="/admin/" class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="<?= e($search) ?>"
                       placeholder="Cari nama, bisnis, atau WhatsApp..."
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-600 focus:ring-2 focus:ring-brand-200 outline-none">
            </div>
            <div>
                <select name="type" class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:border-brand-600 outline-none">
                    <option value="">Semua Jenis</option>
                    <?php foreach ($allowedTypes as $t): ?>
                        <option value="<?= e($t) ?>" <?= $filterType === $t ? 'selected' : '' ?>><?= e($labelTypes[$t]) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <select name="need" class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:border-brand-600 outline-none">
                    <option value="">Semua Kebutuhan</option>
                    <?php foreach ($allowedNeeds as $n): ?>
                        <option value="<?= e($n) ?>" <?= $filterNeed === $n ? 'selected' : '' ?>><?= e($labelNeeds[$n]) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                Cari
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <?php if (empty($leads)): ?>
            <div class="p-8 text-center text-slate-500">Tidak ada lead ditemukan.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-left">
                            <th class="px-4 py-3 font-medium text-slate-600">ID</th>
                            <th class="px-4 py-3 font-medium text-slate-600">Nama</th>
                            <th class="px-4 py-3 font-medium text-slate-600 hidden sm:table-cell">Nama Bisnis</th>
                            <th class="px-4 py-3 font-medium text-slate-600 hidden md:table-cell">Jenis</th>
                            <th class="px-4 py-3 font-medium text-slate-600 hidden lg:table-cell">WhatsApp</th>
                            <th class="px-4 py-3 font-medium text-slate-600 hidden md:table-cell">Kebutuhan</th>
                            <th class="px-4 py-3 font-medium text-slate-600">Tanggal</th>
                            <th class="px-4 py-3 font-medium text-slate-600">Status</th>
                            <th class="px-4 py-3 font-medium text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($leads as $lead): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3 text-slate-500"><?= e((string) $lead['id']) ?></td>
                                <td class="px-4 py-3 font-medium text-slate-900 max-w-[140px] truncate"><?= e($lead['name']) ?></td>
                                <td class="px-4 py-3 text-slate-700 hidden sm:table-cell max-w-[160px] truncate"><?= e($lead['business_name']) ?></td>
                                <td class="px-4 py-3 text-slate-600 hidden md:table-cell"><?= e($labelTypes[$lead['business_type']] ?? $lead['business_type']) ?></td>
                                <td class="px-4 py-3 text-slate-600 hidden lg:table-cell font-mono text-xs"><?= e($lead['whatsapp']) ?></td>
                                <td class="px-4 py-3 text-slate-600 hidden md:table-cell"><?= e($labelNeeds[$lead['needs']] ?? ($lead['needs'] ?? '—')) ?></td>
                                <td class="px-4 py-3 text-slate-500 whitespace-nowrap"><?= e(date('d M Y', strtotime($lead['created_at']))) ?></td>
                                <td class="px-4 py-3"><?= status_badge($lead['status']) ?></td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a href="/admin/detail.php?id=<?= e((string) $lead['id']) ?>"
                                           class="text-brand-600 hover:text-brand-700 text-xs font-medium">Detail</a>
                                        <?php
                                        $waNum = preg_replace('/\D/', '', $lead['whatsapp']);
                                        if (preg_match('/^62\d{8,13}$/', $waNum)):
                                        ?>
                                            <a href="https://wa.me/<?= e($waNum) ?>" target="_blank" rel="noopener noreferrer"
                                               class="text-green-600 hover:text-green-700 text-xs font-medium">Chat WA</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="px-4 py-3 border-t border-slate-200 flex items-center justify-between text-sm">
                    <span class="text-slate-500">
                        Halaman <?= e((string) $page) ?> dari <?= e((string) $totalPages) ?>
                        (<?= e((string) $totalRows) ?> lead)
                    </span>
                    <div class="flex items-center gap-2">
                        <?php if ($page > 1): ?>
                            <a href="<?= e(admin_base_qs(['page' => (string)($page - 1)])) ?>"
                               class="px-3 py-1 rounded-lg border border-slate-300 hover:bg-slate-100 transition">&laquo; Prev</a>
                        <?php endif; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="<?= e(admin_base_qs(['page' => (string)($page + 1)])) ?>"
                               class="px-3 py-1 rounded-lg border border-slate-300 hover:bg-slate-100 transition">Next &raquo;</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</main>

</body>
</html>
