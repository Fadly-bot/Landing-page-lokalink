<?php
/**
 * Lokalink - Admin Login
 */
require_once __DIR__ . '/auth.php';

if (admin_get_user() !== null) {
    redirect('/admin/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $error = 'Sesi tidak valid. Silakan coba lagi.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $validUser = admin_env('ADMIN_USERNAME');
        $validHash = admin_env('ADMIN_PASSWORD_HASH');

        if ($validUser !== null && $validHash !== null
            && hash_equals($validUser, $username)
            && password_verify($password, $validHash)
        ) {
            admin_set_cookie($username);
            redirect('/admin/');
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Lokalink</title>
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
<body class="bg-slate-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md px-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
            <div class="text-center mb-8">
                <img src="/src/img/logo/logo.png" alt="Lokalink" class="h-10 mx-auto mb-4">
                <h1 class="text-xl font-bold text-slate-900">Admin Login</h1>
            </div>

            <?php if ($error !== ''): ?>
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm" role="alert">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/admin/login.php" class="space-y-5">
                <?= csrf_field() ?>

                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="username"
                           class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-brand-600 focus:ring-2 focus:ring-brand-200 outline-none">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                           class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-brand-600 focus:ring-2 focus:ring-brand-200 outline-none">
                </div>

                <button type="submit"
                        class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>
</html>
