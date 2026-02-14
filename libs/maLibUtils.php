<?php

/**
 * @file maLibUtils.php
 * Ce fichier définit des fonctions d'accès ou d'affichage pour les tableaux superglobaux
 */

/**
 * Vérifie l'existence et renvoie la valeur brute du paramètre.
 * CORRECTION : On ne protège plus systématiquement pour le HTML ici 
 * pour éviter les doubles encodages.
 */
function valider($nom,$type="REQUEST")
{	
	switch($type)
	{
		case 'array':
			if (isset($_REQUEST[$nom]) && is_array($_REQUEST[$nom])) {
				return $_REQUEST[$nom]; 
			}
			break;

		case 'REQUEST': 
		if(isset($_REQUEST[$nom]) && !($_REQUEST[$nom] == "")) 	
			return $_REQUEST[$nom]; 	
		break;
		case 'GET': 	
		if(isset($_GET[$nom]) && !($_GET[$nom] == "")) 			
			return $_GET[$nom]; 
		break;
		case 'POST': 	
		if(isset($_POST[$nom]) && !($_POST[$nom] == "")) 	
			return $_POST[$nom]; 		
		break;
		case 'COOKIE': 	
		if(isset($_COOKIE[$nom]) && !($_COOKIE[$nom] == "")) 	
			return $_COOKIE[$nom];	
		break;
		case 'SESSION': 
		if(isset($_SESSION[$nom]) && !($_SESSION[$nom] == "")) 	
			return $_SESSION[$nom]; 		
		break;
		case 'SERVER': 
		if(isset($_SERVER[$nom]) && !($_SERVER[$nom] == "")) 	
			return $_SERVER[$nom]; 		
		break;
	}
	return false;
}

/**
 * Fonction de protection pour SQL uniquement.
 * On utilise addslashes car PDO::quote n'est pas utilisé ici.
 */
function proteger($str)
{
	if (is_array($str))
	{
		$nextTab = array();
		foreach($str as $cle => $val)
		{
			$nextTab[$cle] = addslashes($val);
		}
		return $nextTab;
	}
	else 	
		return addslashes($str);
}

/**
 * Affiche un tableau de manière lisible pour le débogage
 */
function tprint($tab)
{
	echo "<pre>\n";
	print_r($tab);
	echo "</pre>\n";	
}

/**
 * Redirige l'utilisateur vers une URL avec des paramètres de requête éventuels
 */
function rediriger($url,$tabQS="")
{
	$qs =""; 

	if (is_array($tabQS)) {
		foreach($tabQS as $nom => $val) {
			$qs .= "$nom=" . urlencode($val) . "&";
		}
	}
	
	header("Location:$url?" . rtrim($qs, "&") );
	die(""); 
}

?>