<?php
include_once "libs/modele.php";
$msg = valider("msg");
$recettes = listerRecettes();

// Image par défaut (on utilise une URL absolue fiable)
$fallback = "https://images.unsplash.com/photo-1495195129352-aed325a55b65?q=80&w=400&auto=format&fit=crop";
?>

<main class="pt-10 px-6 pb-20 max-w-7xl mx-auto">
    <?php if ($msg): ?>
        <div id="notification" class="mb-8 glass border-l-4 border-orange-500 p-4 rounded-xl flex items-center justify-between text-white cursor-pointer animate-in fade-in slide-in-from-top-4">
            <p><?php echo htmlspecialchars($msg); ?></p>
            <i data-lucide="x" class="w-4 h-4 opacity-50"></i>
        </div>
    <?php endif; ?>

    <!-- SECTION HERO AVEC L'IMAGE DE SOUPE RESTAURÉE -->
    <div class="mb-16 text-center md:text-left md:flex items-center justify-between gap-10">
        <div class="max-w-2xl">
            <h1 class="text-6xl font-black text-white italic mb-4">Cuisine Simple,<br><span class="text-orange-500">Plaisir Partagé.</span></h1>
            <p class="text-white/70 text-lg mb-8">Régale toi bien.</p>
            <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                <a href="./?view=ajouter" class="inline-flex items-center gap-3 px-8 py-4 bg-orange-500 hover:bg-orange-600 text-white font-black rounded-2xl shadow-xl transition-all transform hover:-translate-y-1 active:scale-95">
                    <i data-lucide="plus-circle"></i> AJOUTER UNE RECETTE
                </a>
            </div>
        </div>
        <div class="hidden lg:block w-1/3">
            <div class="glass p-3 rounded-[2.5rem] rotate-3 hover:rotate-0 transition-transform duration-500 shadow-2xl border-white/10">
                <!-- Restauration de l'image de soupe initiale -->
                <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                     class="rounded-[2rem] w-full h-80 object-cover" 
                     onerror="this.onerror=null; this.src='<?php echo $fallback; ?>';"
                     alt="[Soupe savoureuse]">
            </div>
        </div>
    </div>

    <!-- GRILLE DES RECETTES -->
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-white flex items-center gap-3">
            <i data-lucide="utensils" class="text-orange-500"></i> Dernières découvertes
        </h2>
        <span class="text-white/30 text-xs uppercase tracking-widest font-bold"><?php echo count($recettes); ?> Recettes partagées</span>
    </div>

    <?php if (empty($recettes)): ?>
        <div class="glass p-12 rounded-3xl text-center border-dashed border-2 border-white/10">
            <i data-lucide="chef-hat" class="w-16 h-16 mx-auto mb-4 opacity-20 text-white"></i>
            <p class="text-white/50 text-xl font-medium">Le carnet est encore vide...</p>
            <p class="text-white/30 text-sm mt-2">Soyez le premier à régaler tout le monde !</p>
            <a href="./?view=ajouter" class="mt-6 inline-block text-orange-500 hover:underline font-bold">Lancer les festivités</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php foreach ($recettes as $r): ?>
                <article class="glass rounded-[2rem] overflow-hidden group hover:-translate-y-2 transition-all duration-300 relative shadow-lg">
                    <div class="h-48 overflow-hidden relative bg-black/20">
                        <?php 
                            $path = "ressources/recettes/" . $r['id'] . "." . $r['image_ext'];
                            $img = ($r['image_ext'] != 'none' && file_exists($path)) ? $path : $fallback;
                        ?>
                        <img src="<?php echo $img; ?>" 
                             class="w-full h-full object-cover transition-transform group-hover:scale-110" 
                             onerror="this.onerror=null; this.src='<?php echo $fallback; ?>';"
                             alt="Image de la recette">
                        
                        <span class="absolute top-4 left-4 glass px-3 py-1 rounded-full text-[10px] text-orange-400 font-bold uppercase">
                            <?php echo htmlspecialchars($r['nom_categorie']); ?>
                        </span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-white font-bold text-lg mb-2 line-clamp-1"><?php echo htmlspecialchars($r['nom']); ?></h3>
                        <p class="text-white/50 text-xs line-clamp-2 italic mb-4">"<?php echo htmlspecialchars($r['description']); ?>"</p>
                        <div class="flex items-center gap-2 border-t border-white/10 pt-4">
                            <div class="w-6 h-6 rounded-full bg-orange-500 flex items-center justify-center text-[10px] font-bold text-white">
                                <?php echo strtoupper($r['nom_createur'][0]); ?>
                            </div>
                            <span class="text-[11px] text-white/60">Par <?php echo htmlspecialchars($r['nom_createur']); ?></span>
                        </div>
                    </div>
                    <a href="./?view=recette&id=<?php echo $r['id']; ?>" class="absolute inset-0 z-10"></a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<script>
    $(document).ready(function() {
        $('#notification').click(function() { $(this).fadeOut(); });
        setTimeout(() => $('#notification').fadeOut(), 5000);
        lucide.createIcons();
    });
</script>