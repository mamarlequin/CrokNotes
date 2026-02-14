<?php

include_once "maLibUtils.php";
include_once "modele.php";

/**
 * Initialise la session à partir d'un tableau utilisateur
 */
function chargerSession($user) {
    $_SESSION["idUser"] = $user["id"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["connecte"] = true;
    $_SESSION["heureConnexion"] = date("H:i:s");

    $nomComplet = $user["name"];
    $parts = explode(" ", $nomComplet);
    $_SESSION["prenom"] = $parts[0];
    $_SESSION["nom"] = isset($parts[1]) ? $parts[1] : "";
}

/**
 * Vérifie l'utilisateur via password_verify et initialise la session
 */
function verifUser($email, $password, $remember = true)
{
    // On récupère l'utilisateur par son email uniquement
    $res = verifUserbdd($email);

    if (!$res) return false; 

    $user = $res[0]; 
    
    // Vérification du hachage du mot de passe
    if (password_verify($password, $user["password"])) {
        chargerSession($user);

        if ($remember) {
            $token = base64_encode($user["email"]); 
            setcookie("croknotes_user", $token, time() + (3600 * 24 * 30), "/"); 
        }
        return true;
    }

    return false;
}

/**
 * Vérifie si un cookie existe pour restaurer une session expirée
 */
function verifierCookie() {
    if (!valider("connecte", "SESSION") && isset($_COOKIE["croknotes_user"])) {
        $email = base64_decode($_COOKIE["croknotes_user"]);
        $res = getUserByEmail($email);
        
        if ($res) {
            chargerSession($res[0]);
            return true;
        }
    }
    return false;
}

function securiser($urlBad, $urlGood = false)
{
    if (!valider("connecte", "SESSION")) {
        rediriger($urlBad);
        die("");
    } else {
        if ($urlGood) rediriger($urlGood);
    }
}
?>