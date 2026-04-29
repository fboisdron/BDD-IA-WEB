<?php

declare(strict_types=1);

$currentPage = $currentPage ?? 'home';
?>
<!doctype html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Assistant:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container-low": "#f3f4f1",
                        "surface-container-high": "#e8e8e5",
                        "surface-tint": "#3f6653",
                        "primary-fixed-dim": "#a5d0b9",
                        "tertiary-fixed-dim": "#a4d1b4",
                        "on-tertiary-fixed": "#002112",
                        "primary": "#012d1d",
                        "background": "#f9faf6",
                        "secondary-fixed-dim": "#95d4b3",
                        "on-error-container": "#93000a",
                        "secondary": "#2c694e",
                        "primary-container": "#1b4332",
                        "tertiary": "#002d1a",
                        "error-container": "#ffdad6",
                        "surface-container-highest": "#e2e3e0",
                        "surface-variant": "#e2e3e0",
                        "primary-fixed": "#c1ecd4",
                        "surface-dim": "#dadad7",
                        "on-error": "#ffffff",
                        "surface": "#f9faf6",
                        "on-primary-container": "#86af99",
                        "tertiary-container": "#1a432e",
                        "on-background": "#1a1c1a",
                        "secondary-fixed": "#b1f0ce",
                        "secondary-container": "#aeeecb",
                        "surface-container": "#eeeeeb",
                        "outline-variant": "#c1c8c2",
                        "on-primary": "#ffffff",
                        "on-primary-fixed-variant": "#274e3d",
                        "on-primary-fixed": "#002114",
                        "on-surface": "#1a1c1a",
                        "on-secondary-fixed": "#002114",
                        "inverse-surface": "#2f312f",
                        "on-secondary-fixed-variant": "#0e5138",
                        "surface-bright": "#f9faf6",
                        "on-tertiary": "#ffffff",
                        "error": "#ba1a1a",
                        "on-surface-variant": "#414844",
                        "on-tertiary-fixed-variant": "#264f39",
                        "on-tertiary-container": "#84b095",
                        "on-secondary": "#ffffff",
                        "inverse-primary": "#a5d0b9",
                        "inverse-on-surface": "#f0f1ee",
                        "on-secondary-container": "#316e52",
                        "tertiary-fixed": "#c0edd0",
                        "outline": "#717973",
                        "surface-container-lowest": "#ffffff"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "spacing": {
                        "stack-md": "16px",
                        "gutter": "16px",
                        "stack-lg": "32px",
                        "unit": "8px",
                        "stack-sm": "8px",
                        "container-padding": "24px"
                    },
                    "fontFamily": {
                        "body-lg": ["Assistant"],
                        "label-sm": ["Assistant"],
                        "body-md": ["Assistant"],
                        "h3": ["Assistant"],
                        "h1": ["Assistant"],
                        "h2": ["Assistant"]
                    },
                    "fontSize": {
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "label-sm": ["14px", {"lineHeight": "20px", "fontWeight": "600"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "h3": ["24px", {"lineHeight": "32px", "letterSpacing": "0", "fontWeight": "600"}],
                        "h1": ["40px", {"lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "h2": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}]
                    }
                }
            }
        }
    </script>
    <style>
        .custom-shadow {
            box-shadow: 0 10px 15px 5px rgba(27, 67, 50, 0.4);
        }
        .signature-shadow {
            box-shadow: 0 10px 15px -3px rgba(27, 67, 50, 0.4);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .inner-focus:focus {
            outline: none;
            border-color: #012d1d;
            box-shadow: 0 0 0 2px rgba(1, 45, 29, 0.1);
        }
    </style>
</head>
<body class="bg-background text-on-background font-body-md min-h-screen flex flex-col">
<!-- TopAppBar -->
<header class="bg-white dark:bg-slate-900 docked full-width top-0 sticky border-b-2 border-emerald-100 dark:border-emerald-800 shadow-[0_10px_15px_-3px_rgba(6,78,59,0.1)]" style="z-index: 10001;">
    <div class="flex justify-between items-center w-full px-8 py-4 max-w-[1280px] mx-auto">
        <div class="text-xl font-black tracking-tight text-emerald-800 dark:text-emerald-100">
            Saint-Quentin
        </div>
        <nav class="hidden md:flex items-center gap-8 font-sans antialiased text-sm font-medium">
            <a class="<?= $currentPage === 'home' ? 'text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-700 dark:border-emerald-400 pb-1 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all duration-200 active:scale-95' ?>" href="index.php">Accueil</a>
            <a class="<?= $currentPage === 'arbres' ? 'text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-700 dark:border-emerald-400 pb-1 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all duration-200 active:scale-95' ?>" href="arbres.php">Arbres</a>
            <a class="<?= $currentPage === 'cartes' ? 'text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-700 dark:border-emerald-400 pb-1 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all duration-200 active:scale-95' ?>" href="cartes.php">Cartes</a>
            <a class="<?= $currentPage === 'ajout' ? 'text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-700 dark:border-emerald-400 pb-1 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all duration-200 active:scale-95' ?>" href="ajout.php">Ajouter un arbre</a>
            <a class="<?= $currentPage === 'besoins' ? 'text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-700 dark:border-emerald-400 pb-1 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all duration-200 active:scale-95' ?>" href="besoins.php">Besoins clients</a>
        </nav>
        <div class="flex items-center gap-3">
            <?php if (isset($_SESSION['user'])): ?>
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800">
                    <span class="material-symbols-outlined text-emerald-700 dark:text-emerald-400 text-base">person</span>
                    <span class="text-sm font-semibold text-emerald-800 dark:text-emerald-200"><?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <span class="text-[10px] font-bold uppercase bg-emerald-700 text-white px-1.5 py-0.5 rounded-full">admin</span>
                    <?php endif; ?>
                </div>
                <a href="logout.php"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-outline-variant text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-base">logout</span>
                    <span class="hidden sm:inline">Déconnexion</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="flex-grow">
