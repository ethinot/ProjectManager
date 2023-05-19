<!--Enzo EPHREM 
Edouard THINOT-->
<?php

$message_enc = "";
$message_projEnc = "";
$message_nbJalon = "";
$message_nbEquipe = "";

/***********************************/
/* Gestion de la liste des encadrants */
/***********************************/

$encadrants = get_nomprenom_enc();

if($encadrants == null || count($encadrants) == 0) {
    $message_enc = "Aucun encadrants à été trouver dans la base de données !";
}

/*******************************************/
/* Gestion après selection d'un encadrants */
/*******************************************/

// modifie automatiquement le liste des projet assoicer à un encadrant
if(isset($_POST['encadrantsId'])){ 
    // récupère la valeur selectionner dans les liste des encadrant
    $selectedEncadrant = mysqli_real_escape_string($connexion, trim($_POST['encadrantsId']));
    //appelle à la fonction qui récupère les projet d'un encadrant passé en paramètre
    if ($selectedEncadrant != ""){
        $projEncradrant = get_pojet_enc($selectedEncadrant);
    
    } else $projEncradrant = null;
    
    if($projEncradrant == null || count($projEncradrant) == 0){
        $message_projEnc = "Aucun projet lié à l'encadrant d'id: $selectedEncadrant !";
    }
}

/*******************************************************/
/* Gestion liste des jalon après selection d'un projet */
/*******************************************************/


if(isset($_POST['submitEncadrantProj'])) {

    $idp = mysqli_real_escape_string($connexion, trim($_POST['encadrantProjet']));
    $encadrantId = $selectedEncadrant;
    
    $tabJalon = get_jalon_list($idp, $encadrantId);
    
    if($tabJalon == null){
		$message_nbJalon = "Aucun jalon disponible pour le projet d'id: $idp !";
	}else{
		if($idp == 'data' && count($tabJalon) == 0){
			$message_nbJalon = "La table lié au jalon du projet d'id $idp est vide!";
		}
	}

    $tabEquipe = get_equipe_list($idp, $encadrantId);

    if($tabEquipe == null){
		$message_nbEquipe = "Aucune equipe en lien avec le projet d'id: $idp !";
	}else{
		if($idp == 'data' && count($tabEquipe) == 0){
			$message_nbEquipe = "La table lié aux équipes du projet d'id $idp est vide!";
		}
	}

}

?>





