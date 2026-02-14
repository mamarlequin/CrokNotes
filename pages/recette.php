<?php
include_once "libs/modele.php";

// Récupération de l'ID de la recette depuis l'URL
$id = valider("id");
$recette = getRecette($id);

// Si la recette n'existe pas, retour à l'accueil avec un message
if (!$recette) {
    header("Location: index.php?view=main&msg=" . urlencode("Désolé, cette recette est introuvable."));
    exit();
}

// Récupération des données liées
$ingredients = getIngredientsRecette($id);
$etapes = getEtapes($id);

// Image par défaut si aucune image n'est trouvée
$fallback = "https://images.unsplash.com/photo-1495195129352-aed325a55b65?q=80&w=800&auto=format&fit=crop";

// Vérification si l'utilisateur est l'auteur pour afficher le bouton modifier
$isAuthor = (valider("connecte", "SESSION") && $_SESSION["idUser"] == $recette["id_createur"]);
?>

<main class="pt-24 px-6 pb-20 max-w-6xl mx-auto">
    <div class="glass rounded-[3rem] overflow-hidden shadow-2xl animate-in fade-in duration-700">
        
        <div class="relative h-[450px]">
            <?php 
                $path = "ressources/recettes/" . $recette['id'] . "." . $recette['image_ext'];
                $img = ($recette['image_ext'] != 'none' && file_exists($path)) ? $path : $fallback;
            ?>
            <img src="<?php echo $img; ?>" 
                 class="w-full h-full object-cover" 
                 onerror="this.onerror=null; this.src='<?php echo $fallback; ?>';"
                 alt="<?php echo htmlspecialchars($recette['nom']); ?>">
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
            
            <div class="absolute bottom-12 left-12 right-12">
                <div class="flex items-center justify-between">
                    <div class="space-y-4">
                        <span class="glass px-4 py-1.5 rounded-full text-orange-400 text-[10px] font-black uppercase tracking-[0.2em]">
                            <?php echo htmlspecialchars($recette['nom_categorie']); ?>
                        </span>
                        <h1 class="text-5xl md:text-7xl font-black text-white italic drop-shadow-2xl">
                            <?php echo htmlspecialchars($recette['nom']); ?>
                        </h1>
                    </div>
                    
                    <?php if ($isAuthor): ?>
                    <a href="./?view=modifier&id=<?php echo $recette['id']; ?>" class="flex items-center gap-2 px-6 py-3 bg-white/10 hover:bg-orange-500 text-white rounded-2xl font-bold transition-all backdrop-blur-md border border-white/20">
                        <i data-lucide="edit-3" class="w-5 h-5"></i>
                        <span>Modifier</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="p-8 md:p-12 grid grid-cols-1 lg:grid-cols-3 gap-16">
            
            <div class="lg:col-span-1 space-y-8">
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-3 border-b border-white/10 pb-4">
                        <i data-lucide="shopping-basket" class="text-orange-500 w-6 h-6"></i> Ingrédients
                    </h2>
                    
                    <?php if (empty($ingredients)): ?>
                        <p class="text-white/40 italic text-sm">Aucun ingrédient listé.</p>
                    <?php else: ?>
                        <ul class="space-y-3">
                            <?php foreach($ingredients as $ing): ?>
                                <li class="flex items-center justify-between glass p-4 rounded-2xl border-white/5 hover:bg-white/5 transition-colors group">
                                    <span class="text-white font-medium group-hover:text-orange-400 transition-colors">
                                        <?php echo htmlspecialchars($ing['nom']); ?>
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-orange-500 font-black text-lg"><?php echo $ing['quantite']; ?></span>
                                        <span class="text-white/40 text-xs font-bold uppercase"><?php echo htmlspecialchars($ing['unite']); ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="glass p-6 rounded-[2rem] border-orange-500/20 bg-orange-500/5 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-orange-500 flex items-center justify-center text-white font-black text-2xl shadow-lg">
                        <?php echo strtoupper($recette['nom_createur'][0]); ?>
                    </div>
                    <div>
                        <p class="text-[10px] text-white/40 font-bold uppercase tracking-widest">Recette de</p>
                        <p class="text-white font-bold text-xl">Chef <?php echo htmlspecialchars($recette['nom_createur']); ?></p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-12">
                <div class="relative">
                    <i data-lucide="quote" class="absolute -top-4 -left-4 w-10 h-10 text-white/5 -rotate-12"></i>
                    <p class="text-xl text-white/80 leading-relaxed font-medium italic pl-4">
                        <?php echo nl2br(htmlspecialchars($recette['description'])); ?>
                    </p>
                </div>

                <div class="space-y-8">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-3 border-b border-white/10 pb-4">
                        <i data-lucide="chef-hat" class="text-orange-500 w-6 h-6"></i> Préparation
                    </h2>

                    <?php if (empty($etapes)): ?>
                        <div class="glass p-8 rounded-3xl text-center border-dashed border-2 border-white/10">
                            <p class="text-white/30 italic text-sm">Les étapes de préparation n'ont pas encore été détaillées.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-10">
                            <?php foreach($etapes as $index => $etape): ?>
                                <div class="flex gap-6 items-start group">
                                    <div class="w-12 h-12 rounded-full bg-orange-500 text-white font-black flex items-center justify-center shrink-0 shadow-[0_0_20px_rgba(249,115,22,0.4)] group-hover:scale-110 transition-transform duration-300">
                                        <?php echo $index + 1; ?>
                                    </div>
                                    <div class="glass p-8 rounded-[2.5rem] flex-grow border-l-4 border-orange-500/30 hover:border-orange-500 transition-all duration-300">
                                        <p class="text-white text-lg leading-relaxed">
                                            <?php echo nl2br(htmlspecialchars($etape['contenu'])); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="pt-8 flex justify-center">
                    <a href="./?view=main" class="flex items-center gap-2 text-white/40 hover:text-white transition-colors text-sm font-bold uppercase tracking-widest">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Retour aux recettes
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    $(document).ready(function() {
        lucide.createIcons();
    });
</script>
