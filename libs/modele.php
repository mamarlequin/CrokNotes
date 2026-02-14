<?php
include_once("maLibSQL.pdo.php");

/**
 * Utilisateurs
 */

function verifUserbdd($email) {
    $e = proteger($email);
    $sql = "SELECT * FROM USER WHERE email='$e'";
    return parcoursRs(SQLSelect($sql));
}

function getUserByEmail($email) {
    $e = proteger($email);
    $sql = "SELECT id, name, email FROM USER WHERE email='$e'";
    $res = parcoursRs(SQLSelect($sql));
    return $res ? $res[0] : false;
}

function creerUser($name, $email, $password) {
    if (getUserByEmail($email)) return false;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $n = proteger($name);
    $e = proteger($email);
    $sql = "INSERT INTO USER(name, email, password, PP_ext, admin) 
            VALUES ('$n', '$e', '$hash', 'None', 0)";
    return SQLInsert($sql);
}

/**
 * Recettes & Suppression
 */

function supprimerRecette($idRecette) {
    $id = intval($idRecette);
    
    // 1. On supprime les dépendances (étapes, liens ingrédients, paniers)
    // On ne supprime PAS la table INGREDIENT car ils peuvent servir à d'autres recettes
    $sqlSteps = "DELETE FROM ETAPE WHERE id_recette = $id";
    SQLDelete($sqlSteps);
    
    $sqlLinks = "DELETE FROM APPARTIENT WHERE id_recette = $id";
    SQLDelete($sqlLinks);
    
    $sqlPanier = "DELETE FROM PANIER WHERE id_recette = $id";
    SQLDelete($sqlPanier);
    
    // 2. On supprime la recette elle-même
    $sqlRecette = "DELETE FROM RECETTE WHERE id = $id";
    return SQLDelete($sqlRecette);
}

function listerCategories() {
    $sql = "SELECT * FROM CATEGORIE ORDER BY nom ASC";
    return parcoursRs(SQLSelect($sql));
}

function creerRecette($id_createur, $id_categorie, $nom, $description, $image_ext) {
    $n = proteger($nom);
    $d = proteger($description);
    $sql = "INSERT INTO RECETTE(id_createur, id_categorie, nom, image_ext, description) 
            VALUES ('$id_createur', '$id_categorie', '$n', '$image_ext', '$d')";
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
    $id = intval($idRecette);
    $sql = "SELECT r.*, c.nom as nom_categorie, u.name as nom_createur 
            FROM RECETTE r 
            JOIN CATEGORIE c ON r.id_categorie = c.id 
            JOIN USER u ON r.id_createur = u.id 
            WHERE r.id = $id";
    $res = parcoursRs(SQLSelect($sql));
    return $res ? $res[0] : false;
}

function creerEtape($idRecette, $contenu) {
    $c = proteger($contenu);
    $sql = "INSERT INTO ETAPE(id_recette, contenu) VALUES ('$idRecette', '$c')";
    return SQLInsert($sql);
}

function getEtapes($idRecette) {
    $id = intval($idRecette);
    $sql = "SELECT * FROM ETAPE WHERE id_recette = $id ORDER BY id ASC";
    return parcoursRs(SQLSelect($sql));
}

function listerIngredients() {
    $sql = "SELECT * FROM INGREDIENT ORDER BY nom ASC";
    return parcoursRs(SQLSelect($sql));
}

function getIngredientId($nom) {
    $n = proteger($nom);
    $sql = "SELECT id FROM INGREDIENT WHERE nom = '$n'";
    return SQLGetChamp($sql);
}

function creerIngredient($nom) {
    $n = proteger($nom);
    $sql = "INSERT INTO INGREDIENT(nom) VALUES ('$n')";
    return SQLInsert($sql);
}

function lierIngredientRecette($idRecette, $idIng, $quantite, $unite) {
    $u = proteger($unite);
    $sql = "INSERT INTO APPARTIENT(id_ingredient, id_recette, quantite, unite) 
            VALUES ('$idIng', '$idRecette', '$quantite', '$u')";
    return SQLInsert($sql);
}

function getIngredientsRecette($idRecette) {
    $id = intval($idRecette);
    $sql = "SELECT i.nom, a.quantite, a.unite 
            FROM INGREDIENT i 
            JOIN APPARTIENT a ON i.id = a.id_ingredient 
            WHERE a.id_recette = $id";
    return parcoursRs(SQLSelect($sql));
}
?>