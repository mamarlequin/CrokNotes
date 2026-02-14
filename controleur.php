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

            if ($idUser = creerUser($name, $email, $password)) {
                // Connexion automatique après inscription
                if (verifUser($email, $password)) {
                    $qs = array("view" => "main", "msg" => "Bienvenue $name ! Votre compte est créé.");
                }
            } else {
                $qs = array("view" => "main", "msg" => "Erreur lors de l'inscription. L'email est peut-être déjà utilisé.");
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