<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php");
    die("");
}
$isConnected = valider("connecte", "SESSION");
$userPrenom = valider("prenom", "SESSION");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrokNotes</title>
    <link rel="icon" type="image/png" href="ressources/icones/couverts.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        #login-modal, #mobile-menu { display: none; }
        .glass { backdrop-filter: blur(16px); background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); }
        .tab-active { border-bottom: 2px solid #f97316; color: #f97316; }
        /* Style sombre pour les options des selects */
        select option { background: #1a1a1a; color: white; }
    </style>
</head>
<body class="min-h-screen bg-cover bg-center bg-fixed" style="background-image: url('https://images.unsplash.com/photo-1543353071-10c8ba85a904?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')">
    <div class="min-h-screen bg-black/50">
        <!-- HEADER -->
        <header class="z-50 px-4 py-4 md:px-6">
            <nav class="max-w-7xl mx-auto glass rounded-2xl shadow-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 h-16">
                    <!-- Logo -->
                    <a href="./?view=main" class="flex items-center gap-2 group">
                        <div class="p-2 bg-orange-500 rounded-lg shadow-lg">
                            <i data-lucide="utensils" class="text-white w-5 h-5"></i>
                        </div>
                        <span class="text-white font-bold text-xl tracking-tight">Crok<span class="text-orange-400">Notes</span></span>
                    </a>

                    <!-- Navigation Desktop -->
                    <div class="hidden md:flex items-center gap-8">
                        <a href="./?view=main" class="text-white hover:text-orange-400 flex items-center gap-2 text-sm transition-colors">
                            <i data-lucide="home" class="w-4 h-4"></i> Accueil
                        </a>
                        <a href="./?view=ajouter" class="btn-add-recipe flex items-center gap-2 px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-full text-sm font-bold transition-all shadow-md active:scale-95">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i> Ajouter une recette
                        </a>
                        <?php if($isConnected): ?>
                            <div class="flex items-center gap-3 border-l border-white/20 pl-6">
                                <span class="text-white text-xs font-medium italic opacity-80"><?php echo htmlspecialchars($userPrenom); ?></span>
                                <a href="controleur.php?action=Deconnexion" class="text-white/60 hover:text-white transition-colors"><i data-lucide="log-out" class="w-4 h-4"></i></a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Bouton Menu Mobile -->
                    <button id="mobile-toggle" class="md:hidden text-white p-2 hover:bg-white/10 rounded-lg">
                        <i data-lucide="menu" id="menu-icon"></i>
                    </button>
                </div>

                <!-- Menu Mobile -->
                <div id="mobile-menu" class="md:hidden border-t border-white/10 bg-black/20 backdrop-blur-xl">
                    <div class="p-4 space-y-4">
                        <a href="./?view=main" class="flex items-center gap-4 text-white p-3 hover:bg-white/10 rounded-xl transition-colors">
                            <i data-lucide="home" class="w-5 h-5"></i> Accueil
                        </a>
                        <a href="./?view=ajouter" class="btn-add-recipe flex items-center gap-4 text-orange-400 font-bold p-3 hover:bg-white/10 rounded-xl transition-colors">
                            <i data-lucide="plus-circle" class="w-5 h-5"></i> Ajouter une recette
                        </a>
                        <?php if($isConnected): ?>
                            <div class="pt-4 border-t border-white/10 flex items-center justify-between px-3">
                                <span class="text-white/60 italic text-sm"><?php echo htmlspecialchars($userPrenom); ?></span>
                                <a href="controleur.php?action=Deconnexion" class="text-red-400 flex items-center gap-2"><i data-lucide="log-out" class="w-4 h-4"></i> Quitter</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </header>

        <!-- MODAL D'AUTHENTIFICATION -->
        <div id="login-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
            <div class="glass w-full max-w-md p-8 rounded-3xl shadow-2xl animate-in fade-in zoom-in-95 duration-300 bg-[#121212]/80">
                <div class="flex justify-between items-center mb-8">
                    <div class="flex gap-6">
                        <button id="tab-login" class="pb-2 text-sm font-bold uppercase tracking-wider tab-active transition-all">Connexion</button>
                        <button id="tab-register" class="pb-2 text-sm font-bold uppercase tracking-wider text-white/40 hover:text-white transition-all">S'inscrire</button>
                    </div>
                    <button id="close-modal" class="text-white/60 hover:text-white p-1 hover:bg-white/10 rounded-lg"><i data-lucide="x"></i></button>
                </div>

                <!-- Formulaires inchangés -->
                <form id="form-login" action="controleur.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-white text-[10px] uppercase font-bold mb-1 ml-1 opacity-70">Email</label>
                        <input type="email" name="email" required class="w-full bg-white/10 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="chef@croknotes.fr">
                    </div>
                    <div>
                        <label class="block text-white text-[10px] uppercase font-bold mb-1 ml-1 opacity-70">Mot de passe</label>
                        <input type="password" name="password" required class="w-full bg-white/10 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="••••••••">
                    </div>
                    <button type="submit" name="action" value="Connexion" class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-lg transition-all active:scale-95">Se connecter</button>
                </form>

                <form id="form-register" action="controleur.php" method="POST" class="space-y-4 hidden">
                    <div>
                        <label class="block text-white text-[10px] uppercase font-bold mb-1 ml-1 opacity-70">Nom complet</label>
                        <input type="text" name="name" required class="w-full bg-white/10 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Jean Chef">
                    </div>
                    <div>
                        <label class="block text-white text-[10px] uppercase font-bold mb-1 ml-1 opacity-70">Email</label>
                        <input type="email" name="email" required class="w-full bg-white/10 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="nouveau@chef.fr">
                    </div>
                    <div>
                        <label class="block text-white text-[10px] uppercase font-bold mb-1 ml-1 opacity-70">Mot de passe</label>
                        <input type="password" name="password" required class="w-full bg-white/10 border border-white/10 rounded-xl py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="••••••••">
                    </div>
                    <button type="submit" name="action" value="Inscription" class="w-full py-4 bg-orange-400 hover:bg-orange-500 text-white font-bold rounded-xl shadow-lg transition-all active:scale-95">Créer mon compte</button>
                </form>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                lucide.createIcons();
                const isConnected = <?php echo $isConnected ? 'true' : 'false'; ?>;

                // Toggle Menu Mobile
                $('#mobile-toggle').on('click', function() {
                    const $menu = $('#mobile-menu');
                    const $icon = $('#menu-icon');
                    $menu.slideToggle(300);
                    
                    if ($icon.attr('data-lucide') === 'menu') {
                        $icon.attr('data-lucide', 'x');
                    } else {
                        $icon.attr('data-lucide', 'menu');
                    }
                    lucide.createIcons();
                });

                // Modal logic
                $('.btn-add-recipe').on('click', function(e) {
                    if (!isConnected) {
                        e.preventDefault();
                        $('#login-modal').fadeIn(200).css('display', 'flex');
                    }
                });

                $('#close-modal, #login-modal').on('click', function(e) {
                    if (e.target === this || $(e.target).closest('#close-modal').length) {
                        $('#login-modal').fadeOut(200);
                    }
                });

                $('#tab-login, #tab-register').on('click', function() {
                    const id = $(this).attr('id');
                    $('#tab-login, #tab-register').removeClass('tab-active').addClass('text-white/40');
                    $(this).addClass('tab-active').removeClass('text-white/40');
                    if (id === 'tab-login') {
                        $('#form-login').show(); $('#form-register').hide();
                    } else {
                        $('#form-register').show(); $('#form-login').hide();
                    }
                });
            });
        </script>