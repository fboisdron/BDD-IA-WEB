<?php

declare(strict_types=1);

require_once __DIR__ . '/_init.php';

// Already logged in → redirect
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$error   = null;
$isSetup = ($authRepository->countUsers() === 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Requête invalide, veuillez réessayer.';
    } elseif ($isSetup) {
        // First-run: create admin account
        $username  = trim($_POST['username'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Le nom d\'utilisateur et le mot de passe sont obligatoires.';
        } elseif (strlen($password) < 8) {
            $error = 'Le mot de passe doit contenir au moins 8 caractères.';
        } elseif ($password !== $password2) {
            $error = 'Les mots de passe ne correspondent pas.';
        } else {
            $authRepository->create($username, $email, $password, 'admin');
            $user = $authRepository->verify($username, $password);
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']];
            header('Location: index.php');
            exit;
        }
    } else {
        // Normal login
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $user = $authRepository->verify($username, $password);

        if ($user === null) {
            $error = 'Identifiants incorrects.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']];
            $redirect = $_GET['redirect'] ?? 'index.php';
            header('Location: ' . htmlspecialchars($redirect));
            exit;
        }
    }
}

// Regenerate CSRF token on each GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $error !== null) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];
?>
<!doctype html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?> – Connexion</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Assistant:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@400,0&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#012d1d',
                        secondary: '#2c694e',
                        'surface-container-low': '#f3f4f1',
                        'outline-variant': '#c1c8c2',
                        'on-surface-variant': '#414844',
                        'secondary-fixed': '#b1f0ce',
                        error: '#ba1a1a',
                    },
                    fontFamily: { sans: ['Assistant', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Assistant', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#e8f5e9] to-[#f0faf3] flex items-center justify-center px-4">

<div class="w-full max-w-md">
    <!-- Logo / Title -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-primary shadow-lg mb-4">
            <span class="material-symbols-outlined text-white text-3xl">park</span>
        </div>
        <h1 class="text-2xl font-extrabold text-primary tracking-tight"><?= htmlspecialchars(APP_NAME) ?></h1>
        <p class="text-sm text-on-surface-variant mt-1">
            <?= $isSetup ? 'Bienvenue ! Créez le compte administrateur.' : 'Connectez-vous pour accéder à l\'application.' ?>
        </p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-outline-variant p-8">
        <?php if ($isSetup): ?>
            <div class="flex items-center gap-2 mb-6 p-3 bg-[#e8f5e9] rounded-lg border border-[#b1f0ce]">
                <span class="material-symbols-outlined text-secondary text-lg">info</span>
                <p class="text-sm text-secondary font-medium">Premier démarrage — aucun utilisateur n'existe encore.</p>
            </div>
        <?php endif; ?>

        <?php if ($error !== null): ?>
            <div class="flex items-center gap-2 mb-6 p-3 bg-red-50 rounded-lg border border-red-200">
                <span class="material-symbols-outlined text-error text-lg">error</span>
                <p class="text-sm text-error"><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">Nom d'utilisateur</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">person</span>
                    <input type="text" name="username" required autofocus autocomplete="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary transition-all">
                </div>
            </div>

            <?php if ($isSetup): ?>
            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">Email <span class="font-normal opacity-60">(optionnel)</span></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">mail</span>
                    <input type="email" name="email" autocomplete="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary transition-all">
                </div>
            </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">
                    Mot de passe <?= $isSetup ? '<span class="font-normal opacity-60">(min. 8 caractères)</span>' : '' ?>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">lock</span>
                    <input type="password" name="password" required autocomplete="<?= $isSetup ? 'new-password' : 'current-password' ?>"
                        class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary transition-all">
                </div>
            </div>

            <?php if ($isSetup): ?>
            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">Confirmer le mot de passe</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">lock_reset</span>
                    <input type="password" name="password2" required autocomplete="new-password"
                        class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary transition-all">
                </div>
            </div>
            <?php endif; ?>

            <button type="submit"
                class="w-full bg-primary text-white py-3 rounded-lg font-bold text-sm flex items-center justify-center gap-2 hover:bg-secondary transition-all active:scale-95 shadow-md">
                <span class="material-symbols-outlined text-lg"><?= $isSetup ? 'person_add' : 'login' ?></span>
                <?= $isSetup ? 'Créer le compte et continuer' : 'Se connecter' ?>
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-on-surface-variant mt-6 opacity-60"><?= htmlspecialchars(APP_NAME) ?> &copy; <?= date('Y') ?></p>
</div>

</body>
</html>
