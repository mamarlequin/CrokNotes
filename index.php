<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    // ob_start() permet de stocker l'affichage en mémoire tampon.
    // Cela évite l'erreur "Headers already sent" si on redirige après avoir inclus du HTML.
    ob_start();

    session_start();

    // Inclusion des bibliothèques nécessaires
    include_once "libs/maLibUtils.php";
    include_once "libs/modele.php";
    include_once "libs/maLibForms.php";
    include_once "libs/maLibSecurisation.php"; 

    // Tentative de reconnexion automatique via cookie si la session est vide
    verifierCookie();

    // Récupération de la vue demandée
    $view = valider("view");

    // S'il est vide, on charge la vue d'accueil par défaut
    if (!$view) $view = "main";

    // --- GESTION DES ERREURS DE VUE (404) ---
    // On définit le chemin du fichier de la vue
    $viewPath = "./pages/$view.php";

    // Si la vue n'est pas "main" et que le fichier n'existe pas physiquement
    if ($view !== "main" && !file_exists($viewPath)) {
        // On redirige vers l'accueil avec un message d'erreur
        header("Location: index.php?view=main&msg=" . urlencode("La page demandée est introuvable."));
        exit();
    }

    // --- RENDU DE LA PAGE ---
    // On inclut le header (contient le début du HTML)
    include("./pages/header.php");

    // Chargement de la vue
    switch($view)
    {		
        case "main" : 
            include("./pages/main.php");
        break;

        default :
            // On a déjà vérifié l'existence du fichier au-dessus
            include($viewPath);
        break;
    }

    // On envoie tout le contenu stocké dans le tampon vers le navigateur
    ob_end_flush();
?>
