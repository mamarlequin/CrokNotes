<?php
session_start();
include_once "libs/maLibUtils.php";
include_once "libs/maLibSecurisation.php";
include_once "libs/modele.php";

$qs = array();

if ($action = valider("action")) {
    switch($action) {
        case 'Connexion':
            $email = valider("email");
            $password = valider("password");
            $remember = valider("remember") ? true : false;
            if (verifUser($email, $password, $remember)) {
                $qs = array("view" => "main");
            } else {
                $qs = array("view" => "main", "msg" => "Identifiants incorrects");
            }
        break;

	case 'Inscription':
	    $name = valider("name");
	    $email = valider("email");
	    $password = valider("password");

	    if ($name && $email && $password) {
	        if (creerUser($name, $email, $password)) {
	            // Connexion automatique après inscription
	            if ($user = connecterUser($email, $password)) {
	                $qs = array("view" => "main", "msg" => "Bienvenue !");
	            }
	        } else {
	            $qs = array("view" => "inscription", "msg" => "Cet email est déjà utilisé.");
	        }
	    }
	break;

	case 'Modifier':
	    securiser("index.php?view=main");
	    $idRecette = valider("id_recette");
	    $idUser = $_SESSION["idUser"];

	    $recetteActuelle = getRecette($idRecette);
	    
	    if ($recetteActuelle["id_createur"] == $idUser) {
	        $nom = valider("nom");
	        $cat = valider("id_categorie");
	        $desc = valider("description");
	        
	        // Récupération des tableaux d'ingrédients et étapes
	        $ings_noms = valider("ing_nom", "array");
	        $ings_qtes = valider("ing_qte", "array");
	        $ings_unites = valider("ing_unite", "array");
	        $etapes = valider("etape_contenu", "array");

	        $image_ext = false; 
	        if (isset($_FILES["image_recette"]) && $_FILES["image_recette"]["error"] == 0) {
	            $ext = strtolower(pathinfo($_FILES["image_recette"]["name"], PATHINFO_EXTENSION));
	            if (in_array($ext, array("jpg", "jpeg", "png", "webp"))) {
	                $image_ext = $ext;
	                move_uploaded_file($_FILES["image_recette"]["tmp_name"], "ressources/recettes/" . $idRecette . "." . $image_ext);
	            }
	        }

	        // 1. Mise à jour des infos de base
	        modifierRecette($idRecette, $nom, $desc, $cat, $image_ext);

	        // 2. MISE À JOUR DES INGRÉDIENTS (Nettoyage + Réinsertion)
	        supprimerIngredientsRecette($idRecette);
	        if ($ings_noms) {
	            foreach($ings_noms as $key => $nomIng) {
	                if (empty(trim($nomIng))) continue;
	                $idIng = getIngredientId($nomIng) ?: creerIngredient($nomIng);
	                
	                // Correction pour éviter l'erreur SQL "Incorrect integer value" si la quantité est vide
	                $qte = ($ings_qtes[$key] !== "") ? $ings_qtes[$key] : 0;
	                lierIngredientRecette($idRecette, $idIng, $qte, $ings_unites[$key]);
	            }
	        }

	        // 3. MISE À JOUR DES ÉTAPES (Nettoyage + Réinsertion)
	        supprimerEtapesRecette($idRecette);
	        if ($etapes) {
	            foreach($etapes as $contenuEtape) {
	                if (empty(trim($contenuEtape))) continue;
	                creerEtape($idRecette, $contenuEtape);
	            }
	        }

	        $qs = array("view" => "recette", "id" => $idRecette, "msg" => "Recette mise à jour avec succès !");
	    }
	break;
        case 'SupprimerRecette':
            securiser("index.php?view=main");
            $idRecette = valider("id");
            $idUser = $_SESSION["idUser"];
            
            // On vérifie que la recette appartient bien à l'utilisateur
            $recette = getRecette($idRecette);
            if ($recette && $recette['id_createur'] == $idUser) {
                // Suppression de l'image sur le serveur si elle existe
                $path = "ressources/recettes/" . $idRecette . "." . $recette['image_ext'];
                if ($recette['image_ext'] != 'none' && file_exists($path)) {
                    unlink($path);
                }
                
                if (supprimerRecette($idRecette)) {
                    $qs = array("view" => "main", "msg" => "Recette supprimée avec succès.");
                } else {
                    $qs = array("view" => "main", "msg" => "Erreur lors de la suppression.");
                }
            } else {
                $qs = array("view" => "main", "msg" => "Action non autorisée.");
            }
        break;

	case 'Supprimer':
	    securiser("index.php?view=main");
	    $idRecette = valider("id");
	    $idUser = $_SESSION["idUser"];

	    // Vérification de sécurité : seul l'auteur peut supprimer
	    $recette = getRecette($idRecette);
	    if ($recette && $recette["id_createur"] == $idUser) {
	        // Optionnel : supprimer le fichier image sur le disque
	        $imagePath = "ressources/recettes/" . $idRecette . "." . $recette["image_ext"];
	        if (file_exists($imagePath)) unlink($imagePath);

	        supprimerRecette($idRecette);
	        $qs = array("view" => "main", "msg" => "La recette a été supprimée.");
	    } else {
	        $qs = array("view" => "main", "msg" => "Action non autorisée.");
	    }
	break;

        case 'Publier':
            securiser("index.php?view=main");
            
            $nom = valider("nom");
            $cat = valider("id_categorie");
            $desc = valider("description");
            $idUser = $_SESSION["idUser"];
            
            $ings_noms = valider("ing_nom", "array");
            $ings_qtes = valider("ing_qte", "array");
            $ings_unites = valider("ing_unite", "array");
            $etapes = valider("etape_contenu", "array");

            $image_ext = "none";
            $has_image = false;
            if (isset($_FILES["image_recette"]) && $_FILES["image_recette"]["error"] == 0) {
                $ext = strtolower(pathinfo($_FILES["image_recette"]["name"], PATHINFO_EXTENSION));
                if (in_array($ext, array("jpg", "jpeg", "png", "webp"))) {
                    $image_ext = $ext;
                    $has_image = true;
                }
            }

            if ($idRecette = creerRecette($idUser, $cat, $nom, $desc, $image_ext)) {
                if ($has_image) {
                    $target_dir = "ressources/recettes/";
                    if (!file_exists($target_dir)) @mkdir($target_dir, 0777, true);
                    move_uploaded_file($_FILES["image_recette"]["tmp_name"], $target_dir . $idRecette . "." . $image_ext);
                }

                if ($ings_noms) {
                    foreach($ings_noms as $key => $nomIng) {
                        if (empty(trim($nomIng))) continue;

			$qte = (isset($ings_qtes[$key]) && $ings_qtes[$key] !== "") ? $ings_qtes[$key] : 0;
        		$unite = isset($ings_unites[$key]) ? $ings_unites[$key] : "";

                        $idIng = getIngredientId($nomIng) ?: creerIngredient($nomIng);
                        lierIngredientRecette($idRecette, $idIng, $ings_qtes[$key], $ings_unites[$key]);
                    }
                }

                if ($etapes) {
                    foreach($etapes as $contenuEtape) {
                        if (empty(trim($contenuEtape))) continue;
                        creerEtape($idRecette, $contenuEtape);
                    }
                }

                $qs = array("view" => "main", "msg" => "Recette publiée avec succès !");
            } else {
                $qs = array("view" => "ajouter", "msg" => "Erreur lors de la création de la recette.");
            }
        break;
        
        case 'Deconnexion':
            session_destroy();
            setcookie("croknotes_user", "", time() - 3600, "/");
            $qs = array("view" => "main");
        break;
    }
}

$urlBase = dirname($_SERVER["PHP_SELF"]) . "/index.php";
rediriger($urlBase, $qs ?: array("view" => "main"));
?>
