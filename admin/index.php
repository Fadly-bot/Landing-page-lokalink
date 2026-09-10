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

// --- Handle delete (POST only, admin already authenticated above) ---
$flash      = '';
$flashError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $leadId = filter_input(INPUT_POST, 'lead_id', FILTER_VALIDATE_INT);
        if ($leadId === false || $leadId === null || $leadId <= 0) {
            $flashError = 'ID lead tidak valid.';
        } elseif (!csrf_verify($_POST['csrf_token'] ?? null)) {
            $flashError = 'Sesi tidak valid. Silakan coba lagi.';
        } else {
            try {
                $stmt = $pdo->prepare('DELETE FROM leads WHERE id = :id');
                $stmt->execute([':id' => $leadId]);
                redirect('/admin/index.php?status=deleted');
            } catch (PDOException $e) {
                error_log('[Lokalink] Admin delete error: ' . $e->getMessage());
                $flashError = 'Gagal menghapus data.';
            }
        }
    }
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

    <?php if (($_GET['status'] ?? '') === 'deleted'): ?>
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm" role="status">
            Lead berhasil dihapus.
        </div>
    <?php endif; ?>
    <?php if ($flashError !== ''): ?>
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm" role="alert">
            <?= e($flashError) ?>
        </div>
    <?php endif; ?>

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
                                <td class="px-4 py-3 text-slate-600 hidden md:table-cell"><?= e(($lead['needs'] ?? null) !== null ? ($labelNeeds[$lead['needs']] ?? $lead['needs']) : '—') ?></td>
                                <td class="px-4 py-3 text-slate-500 whitespace-nowrap"><?= e(date('d M Y', strtotime($lead['created_at']))) ?></td>
                                <td class="px-4 py-3"><?= status_badge($lead['status']) ?></td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a href="/admin/detail.php?id=<?= e((string) $lead['id']) ?>"
                                           aria-label="Lihat detail lead <?= e($lead['name']) ?>"
                                           title="Lihat Detail"
                                           class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-brand-600 hover:text-brand-700 hover:bg-brand-50 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6" aria-hidden="true">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                        <?php
                                        $waNum = preg_replace('/\D/', '', $lead['whatsapp']);
                                        if (preg_match('/^62\d{8,13}$/', $waNum)):
                                        ?>
                                            <a href="https://wa.me/<?= e($waNum) ?>" target="_blank" rel="noopener noreferrer"
                                               aria-label="Chat WhatsApp <?= e($lead['name']) ?>"
                                               title="Chat WhatsApp"
                                               class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-green-600 hover:text-green-700 hover:bg-green-50 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6" aria-hidden="true">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path>
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                        <form method="post" action="/admin/index.php" class="inline m-0">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="lead_id" value="<?= e((string) $lead['id']) ?>">
                                            <?= csrf_field() ?>
                                            <button type="submit"
                                                    aria-label="Hapus lead"
                                                    title="Hapus lead"
                                                    data-confirm="Apakah Anda yakin ingin menghapus lead ini? Data yang dihapus tidak dapat dikembalikan."
                                                    class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-red-600 hover:text-red-700 hover:bg-red-50 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6" aria-hidden="true">
                                                    <path d="M3 6h18"></path>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                                    <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </form>
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

<script>
    (function () {
        document.querySelectorAll('[data-confirm]').forEach(function (btn) {
            var form = btn.closest('form');
            if (!form) {
                return;
            }
            form.addEventListener('submit', function (e) {
                if (!window.confirm(btn.getAttribute('data-confirm'))) {
                    e.preventDefault();
                    return;
                }
                btn.disabled = true;
            });
        });
    })();
</script>

</body>
</html>
