<?php
include_once("maLibSQL.pdo.php");

/**
 * Utilisateurs
 */

/**
 * Récupère un utilisateur par son email pour vérification ultérieure du hachage
 */
function verifUserbdd($email) {
    $sql = "SELECT * FROM USER WHERE email='$email'";
    return parcoursRs(SQLSelect($sql));
}

/**
 * Récupère les infos de base d'un utilisateur via son email
 */
function getUserByEmail($email) {
    $sql = "SELECT id, name, email FROM USER WHERE email='$email'";
    $res = parcoursRs(SQLSelect($sql));
    return $res ? $res[0] : false;
}

/**
 * Crée un utilisateur avec un mot de passe haché
 * Renvoie l'ID de l'utilisateur ou false si l'email existe déjà
 */
function creerUser($name, $email, $password) {
    // ÉTAPE CRITIQUE : Vérifier si l'utilisateur existe déjà avant d'insérer
    if (getUserByEmail($email)) {
        return false;
    }

    // Hachage du mot de passe avec l'algorithme par défaut (BCRYPT)
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $nameSafe = addslashes($name);
    $emailSafe = addslashes($email);

    // On utilise 0 pour le booléen admin (FALSE)
    $sql = "INSERT INTO USER(name, email, password, PP_ext, admin) 
            VALUES ('$nameSafe', '$emailSafe', '$hash', 'None', 0)";
    
    return SQLInsert($sql);
}

/**
 * Catégories
 */
function listerCategories() {
    $sql = "SELECT * FROM CATEGORIE ORDER BY nom ASC";
    return parcoursRs(SQLSelect($sql));
}

/**
 * Recettes
 */
function creerRecette($id_createur, $id_categorie, $nom, $description, $image_ext) {
    $nomSafe = addslashes($nom);
    $descSafe = addslashes($description);
    $sql = "INSERT INTO RECETTE(id_createur, id_categorie, nom, image_ext, description) 
            VALUES ('$id_createur', '$id_categorie', '$nomSafe', '$image_ext', '$descSafe')";
    return SQLInsert($sql);
}

function listerRecettes() {
    $sql = "SELECT r.*, c.nom as nom_categorie, u.name as nom_createur 
            FROM RECETTE r 
            JOIN CATEGORIE c ON r.id_categorie = c.id 
            JOIN USER u ON r.id_createur = u.id 
            ORDER BY r.id DESC";
    return parcoursRs(SQLSelect($sql));
}

function getRecette($idRecette) {
    $sql = "SELECT r.*, c.nom as nom_categorie, u.name as nom_createur 
            FROM RECETTE r 
            JOIN CATEGORIE c ON r.id_categorie = c.id 
            JOIN USER u ON r.id_createur = u.id 
            WHERE r.id = $idRecette";
    $res = parcoursRs(SQLSelect($sql));
    return $res ? $res[0] : false;
}

/**
 * Étapes de préparation
 */
function creerEtape($idRecette, $contenu) {
    $contenuSafe = addslashes($contenu);
    $sql = "INSERT INTO ETAPE(id_recette, contenu) VALUES ('$idRecette', '$contenuSafe')";
    return SQLInsert($sql);
}

function getEtapes($idRecette) {
    $sql = "SELECT * FROM ETAPE WHERE id_recette = $idRecette ORDER BY id ASC";
    return parcoursRs(SQLSelect($sql));
}

/**
 * Ingrédients
 */
function listerIngredients() {
    $sql = "SELECT * FROM INGREDIENT ORDER BY nom ASC";
    return parcoursRs(SQLSelect($sql));
}

function getIngredientId($nom) {
    $sql = "SELECT id FROM INGREDIENT WHERE nom = '$nom'";
    return SQLGetChamp($sql);
}

function creerIngredient($nom) {
    $nomSafe = addslashes($nom);
    $sql = "INSERT INTO INGREDIENT(nom) VALUES ('$nomSafe')";
    return SQLInsert($sql);
}

function lierIngredientRecette($idRecette, $idIng, $quantite, $unite) {
    $sql = "INSERT INTO APPARTIENT(id_ingredient, id_recette, quantite, unite) 
            VALUES ('$idIng', '$idRecette', '$quantite', '$unite')";
    return SQLInsert($sql);
}

function getIngredientsRecette($idRecette) {
    $sql = "SELECT i.nom, a.quantite, a.unite 
            FROM INGREDIENT i 
            JOIN APPARTIENT a ON i.id = a.id_ingredient 
            WHERE a.id_recette = $idRecette";
    return parcoursRs(SQLSelect($sql));
}
?>