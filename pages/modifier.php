<?php
include_once "libs/maLibSecurisation.php";
include_once "libs/modele.php";

// Sécurité : il faut être connecté pour modifier
securiser("index.php?view=main");

// Récupération de l'ID et des données de la recette
$idRecette = valider("id");
$recette = getRecette($idRecette);

// Vérification : la recette existe et l'utilisateur est bien l'auteur
if (!$recette || $_SESSION["idUser"] != $recette["id_createur"]) {
    header("Location: index.php?view=main&msg=" . urlencode("Action non autorisée."));
    exit();
}

$categories = listerCategories(); //
$ingredientsExistants = listerIngredients(); //
$ingredientsRecette = getIngredientsRecette($idRecette); //
$etapesRecette = getEtapes($idRecette); //

// Image actuelle
$path = "ressources/recettes/" . $recette['id'] . "." . $recette['image_ext'];
$hasImage = ($recette['image_ext'] != 'none' && file_exists($path));
?>

<main class="pt-10 px-6 pb-20 max-w-5xl mx-auto">
    <div class="glass p-8 rounded-3xl text-white shadow-2xl">
        <h1 class="text-4xl font-black mb-2 italic text-orange-500 text-center md:text-left">Modifier ma recette</h1>
        <p class="text-white/60 mb-8 text-center md:text-left">Mettez à jour vos secrets de cuisine.</p>

        <form action="controleur.php" method="POST" enctype="multipart/form-data" class="space-y-10">
            <input type="hidden" name="id_recette" value="<?php echo $idRecette; ?>">
            
            <section class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase opacity-50 ml-1">Nom du plat</label>
                        <input type="text" name="nom" required value="<?php echo htmlspecialchars($recette['nom']); ?>" class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-5 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase opacity-50 ml-1">Catégorie</label>
                        <select name="id_categorie" class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-5 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php if($cat['id'] == $recette['id_categorie']) echo "selected"; ?> class="text-black">
                                    <?php echo $cat['nom']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase opacity-50 ml-1">Photo (laisser vide pour conserver l'actuelle)</label>
                    <div class="relative group border-2 border-dashed border-white/20 rounded-3xl h-full min-h-[140px] flex items-center justify-center hover:bg-white/5 cursor-pointer overflow-hidden transition-all">
                        <img id="img_prev" src="<?php echo $hasImage ? $path : '#'; ?>" class="absolute inset-0 w-full h-full object-cover <?php echo $hasImage ? '' : 'hidden'; ?>">
                        <div id="img_placeholder" class="text-center opacity-40 group-hover:opacity-100 transition-opacity <?php echo $hasImage ? 'hidden' : ''; ?>">
                            <i data-lucide="camera" class="w-8 h-8 mx-auto mb-1"></i>
                            <p class="text-xs">Changer la photo</p>
                        </div>
                        <input type="file" name="image_recette" id="img_in" accept="image/\*" class="absolute inset-0 opacity-0 cursor-pointer">
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <div class="flex items-center justify-between border-b border-white/10 pb-2">
                    <h2 class="text-lg font-bold flex items-center gap-2"><i data-lucide="shopping-cart" class="text-orange-500"></i> Ingrédients</h2>
                    <button type="button" id="add_ing" class="text-[10px] font-bold bg-white/10 px-4 py-2 rounded-xl hover:bg-orange-500">+ AJOUTER</button>
                </div>
                <div id="ing_list" class="space-y-3">
                    <?php if (empty($ingredientsRecette)): ?>
                        <div class="ing-row grid grid-cols-12 gap-3 items-center">
                            <input list="ings" name="ing_nom[]" placeholder="Nom" class="col-span-6 bg-white/5 border border-white/10 rounded-xl py-3 px-4 outline-none">
                            <input type="number" step="0.01" name="ing_qte[]" placeholder="Qté" class="col-span-2 bg-white/5 border border-white/10 rounded-xl py-3 px-4 outline-none text-center">
                            <input type="text" name="ing_unite[]" placeholder="Unité" class="col-span-3 bg-white/5 border border-white/10 rounded-xl py-3 px-4 outline-none">
                            <button type="button" class="del_row col-span-1 text-white/20 hover:text-red-500"><i data-lucide="trash-2"></i></button>
                        </div>
                    <?php else: ?>
                        <?php foreach($ingredientsRecette as $ing): ?>
                            <div class="ing-row grid grid-cols-12 gap-3 items-center">
                                <input list="ings" name="ing_nom[]" value="<?php echo htmlspecialchars($ing['nom']); ?>" class="col-span-6 bg-white/5 border border-white/10 rounded-xl py-3 px-4 outline-none">
                                <input type="number" step="0.01" name="ing_qte[]" value="<?php echo $ing['quantite']; ?>" class="col-span-2 bg-white/5 border border-white/10 rounded-xl py-3 px-4 outline-none text-center">
                                <input type="text" name="ing_unite[]" value="<?php echo htmlspecialchars($ing['unite']); ?>" class="col-span-3 bg-white/5 border border-white/10 rounded-xl py-3 px-4 outline-none">
                                <button type="button" class="del_row col-span-1 text-white/20 hover:text-red-500"><i data-lucide="trash-2"></i></button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <section class="space-y-4">
                <div class="flex items-center justify-between border-b border-white/10 pb-2">
                    <h2 class="text-lg font-bold flex items-center gap-2"><i data-lucide="list-ordered" class="text-orange-500"></i> Étapes</h2>
                    <button type="button" id="add_step" class="text-[10px] font-bold bg-white/10 px-4 py-2 rounded-xl hover:bg-orange-500">+ ÉTAPE</button>
                </div>
                <div id="step_list" class="space-y-4">
                    <?php foreach($etapesRecette as $idx => $etape): ?>
                        <div class="step-row flex gap-4 items-start">
                            <span class="step-num bg-orange-500 text-white font-black w-10 h-10 rounded-full flex items-center justify-center shrink-0 mt-1 shadow-lg"><?php echo $idx+1; ?></span>
                            <textarea name="etape_contenu[]" required rows="2" class="flex-grow bg-white/5 border border-white/10 rounded-xl py-4 px-5 outline-none"><?php echo htmlspecialchars($etape['contenu']); ?></textarea>
                            <button type="button" class="del_row text-white/20 hover:text-red-500 mt-4"><i data-lucide="trash-2"></i></button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase opacity-50 ml-1">Conseil du chef</label>
                <textarea name="description" rows="3" required class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-5 outline-none"><?php echo htmlspecialchars($recette['description']); ?></textarea>
            </div>

            <button type="submit" name="action" value="Modifier" class="w-full py-5 bg-orange-500 font-black rounded-2xl shadow-xl hover:bg-orange-600 transition-all uppercase tracking-widest">Enregistrer les modifications</button>
        </form>
    </div>
</main>

<script>
// Réutilisation des scripts JS de ajouter.php pour le dynamisme des lignes
$(document).ready(function() {
    lucide.createIcons();
    $('#img_in').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => { $('#img_prev').attr('src', e.target.result).removeClass('hidden'); $('#img_placeholder').addClass('hidden'); }
            reader.readAsDataURL(file);
        }
    });
    $('#add_ing').click(() => {
        const row = $('.ing-row:first').clone();
        row.find('input').val('');
        row.appendTo('#ing_list').hide().slideDown(200);
        lucide.createIcons();
    });
    $('#add_step').click(() => {
        const row = $('.step-row:first').clone();
        row.find('textarea').val('');
        row.appendTo('#step_list').hide().slideDown(200);
        updateSteps();
        lucide.createIcons();
    });
    $(document).on('click', '.del_row', function() {
        $(this).parent().remove();
        updateSteps();
    });
    function updateSteps() { 
        $('.step-num').each(function(i) { $(this).text(i + 1); }); 
    }
});
</script>
