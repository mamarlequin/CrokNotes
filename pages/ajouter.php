<?php
include_once "libs/maLibSecurisation.php";
include_once "libs/modele.php";

securiser("index.php?view=main");

$categories = listerCategories();
$ingredientsExistants = listerIngredients();
?>

<main class="pt-10 px-6 pb-20 max-w-5xl mx-auto">
    <div class="glass p-8 rounded-3xl text-white shadow-2xl animate-in fade-in slide-in-from-bottom-4 duration-500">
        <h1 class="text-4xl font-black mb-2 italic text-orange-500 text-center md:text-left">Publier une recette</h1>
        <p class="text-white/60 mb-8 text-center md:text-left">Détaillez votre préparation pas à pas.</p>

        <form action="controleur.php" method="POST" enctype="multipart/form-data" class="space-y-10">
            
            <section class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase opacity-50 ml-1">Nom du plat</label>
                        <input type="text" name="nom" required class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-5 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all" placeholder="Ex: Lasagnes Maison">
                    </div>
			<div class="space-y-1">
			    <label class="text-[10px] font-bold uppercase opacity-50 ml-1">Catégorie</label>
			    <select name="id_categorie" class="w-full bg-white/10 border border-white/10 rounded-2xl py-4 px-5 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all text-white appearance-none">
			        <?php foreach($categories as $cat): ?>
			            <option value="<?php echo $cat['id']; ?>" <?php if(isset($recette) && $cat['id'] == $recette['id_categorie']) echo "selected"; ?> class="bg-slate-900 text-white">
			                <?php echo htmlspecialchars($cat['nom']); ?>
			            </option>
			        <?php endforeach; ?>
			    </select>
			</div>                    
		    </div>
               

                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase opacity-50 ml-1">Photo de couverture</label>
                    <div class="relative group border-2 border-dashed border-white/20 rounded-3xl h-full min-h-[140px] flex items-center justify-center hover:bg-white/5 cursor-pointer overflow-hidden transition-all">
                        <img id="img_prev" src="#" class="absolute inset-0 w-full h-full object-cover hidden">
                        <div id="img_placeholder" class="text-center opacity-40 group-hover:opacity-100 transition-opacity">
                            <i data-lucide="camera" class="w-8 h-8 mx-auto mb-1"></i>
                            <p class="text-xs">Cliquez pour choisir</p>
                        </div>
                        <input type="file" name="image_recette" id="img_in" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                    </div>
                </div>
            </section>

            <!-- SECTION INGRÉDIENTS -->
            <section class="space-y-4">
                <div class="flex items-center justify-between border-b border-white/10 pb-2">
                    <h2 class="text-lg font-bold flex items-center gap-2"><i data-lucide="shopping-cart" class="w-5 h-5 text-orange-500"></i> Ingrédients</h2>
                    <button type="button" id="add_ing" class="text-[10px] font-bold bg-white/10 px-4 py-2 rounded-xl hover:bg-orange-500 transition-all">+ AJOUTER</button>
                </div>
                <div id="ing_list" class="space-y-3">
                    <div class="ing-row grid grid-cols-12 gap-3 items-center">
                        <input list="ings" name="ing_nom[]" placeholder="Nom de l'ingrédient" class="col-span-6 bg-white/5 border border-white/10 rounded-xl py-3 px-4 outline-none focus:ring-1 focus:ring-orange-500" required>
                        <input type="number" step="0.01" name="ing_qte[]" placeholder="Qté" class="col-span-2 bg-white/5 border border-white/10 rounded-xl py-3 px-4 outline-none text-center" required>
                        <input type="text" name="ing_unite[]" placeholder="Unité" class="col-span-3 bg-white/5 border border-white/10 rounded-xl py-3 px-4 outline-none">
                        <button type="button" class="del_row col-span-1 text-white/20 hover:text-red-500 transition-colors"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                    </div>
                </div>
                <datalist id="ings">
                    <?php foreach($ingredientsExistants as $i): ?> <option value="<?php echo htmlspecialchars($i['nom']); ?>"> <?php endforeach; ?>
                </datalist>
            </section>

            <!-- SECTION ÉTAPES -->
            <section class="space-y-4">
                <div class="flex items-center justify-between border-b border-white/10 pb-2">
                    <h2 class="text-lg font-bold flex items-center gap-2"><i data-lucide="list-ordered" class="w-5 h-5 text-orange-500"></i> Étapes de préparation</h2>
                    <button type="button" id="add_step" class="text-[10px] font-bold bg-white/10 px-4 py-2 rounded-xl hover:bg-orange-500 transition-all">+ ÉTAPE</button>
                </div>
                <div id="step_list" class="space-y-4">
                    <div class="step-row flex gap-4 items-start group">
                        <span class="step-num bg-orange-500 text-white font-black w-10 h-10 rounded-full flex items-center justify-center shrink-0 mt-1 shadow-lg">1</span>
                        <textarea name="etape_contenu[]" required rows="2" class="flex-grow bg-white/5 border border-white/10 rounded-xl py-4 px-5 outline-none focus:ring-1 focus:ring-orange-500 transition-all" placeholder="Décrivez cette étape..."></textarea>
                        <button type="button" class="del_row text-white/20 hover:text-red-500 transition-colors mt-4"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                    </div>
                </div>
            </section>

            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase opacity-50 ml-1">Histoire de la recette ou conseil du chef</label>
                <textarea name="description" rows="3" required class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-5 outline-none focus:ring-1 focus:ring-orange-500" placeholder="Racontez-nous..."></textarea>
            </div>

            <button type="submit" name="action" value="Publier" class="w-full py-5 bg-orange-500 font-black rounded-2xl shadow-xl hover:bg-orange-600 transition-all transform active:scale-[0.98] uppercase tracking-widest">Envoyer ma recette</button>
        </form>
    </div>
</main>

<script>
$(document).ready(function() {
    lucide.createIcons();

    // Aperçu de l'image
    $('#img_in').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => { $('#img_prev').attr('src', e.target.result).removeClass('hidden'); $('#img_placeholder').addClass('hidden'); }
            reader.readAsDataURL(file);
        }
    });

    // Ajouter un ingrédient
    $('#add_ing').click(() => {
        const row = $('.ing-row:first').clone();
        row.find('input').val('');
        row.appendTo('#ing_list').hide().slideDown(200);
        lucide.createIcons();
    });

    // Ajouter une étape
    $('#add_step').click(() => {
        const row = $('.step-row:first').clone();
        row.find('textarea').val('');
        row.appendTo('#step_list').hide().slideDown(200);
        updateSteps();
        lucide.createIcons();
    });

    // Supprimer une ligne
    $(document).on('click', '.del_row', function() {
        const type = $(this).closest('.ing-row').length ? '.ing-row' : '.step-row';
        if ($(this).closest('#ing_list, #step_list').find(type).length > 1) {
            $(this).closest(type).slideUp(200, function() { 
                $(this).remove(); 
                if (type === '.step-row') updateSteps();
            });
        }
    });

    function updateSteps() { 
        $('.step-num').each(function(i) { $(this).text(i + 1); }); 
    }
});
</script>
