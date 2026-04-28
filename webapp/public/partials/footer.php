<!-- Footer -->
<footer class="bg-slate-50 dark:bg-slate-950 full-width mt-auto border-t border-emerald-200 dark:border-emerald-900">
    <div class="flex flex-col md:flex-row justify-between items-center w-full px-8 py-12 max-w-[1280px] mx-auto gap-4">
        <div class="flex flex-col items-center md:items-start gap-2">
            <div class="text-lg font-bold text-slate-900 dark:text-white"><?= htmlspecialchars(APP_NAME) ?></div>
            <p class="text-xs text-slate-500 dark:text-slate-400">© 2024 Ville de Saint-Quentin. Patrimoine, analyse et décisions en temps réel.</p>
        </div>
        <nav class="flex flex-wrap justify-center gap-8 text-xs uppercase tracking-widest font-semibold">
            <a class="text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors opacity-100 hover:opacity-80" href="#">Charte municipale</a>
            <a class="text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors opacity-100 hover:opacity-80" href="#">Données environnementales</a>
            <a class="text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors opacity-100 hover:opacity-80" href="#">Contacter arboriste</a>
        </nav>
        <div class="flex gap-4">
            <button class="p-2 text-emerald-800 dark:text-emerald-500 hover:opacity-80">
                <span class="material-symbols-outlined">public</span>
            </button>
            <button class="p-2 text-emerald-800 dark:text-emerald-500 hover:opacity-80">
                <span class="material-symbols-outlined">mail</span>
            </button>
        </div>
    </div>
</footer>
</main>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
