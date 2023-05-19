<!--Enzo EPHREM 
Edouard THINOT-->
<?php

$message_equipe = "";
$message_projEq = "";
$message_jalonsNom = "";
$message_jalons = "";

/************************************/
/* Gestion de la liste des  equipes */
/************************************/

$equipes = get_nom_equipe();

if($equipes == null || count($equipes) == 0) {
    $message_equipe = "Aucune équipes à été trouver dans la base de données !";
}

/****************************************/
/* Gestion après selection d'une équipe */
/****************************************/

// modifie automatiquement le liste des projet assoicer à une équipe
if(isset($_POST['equipesId'])){ 
    // récupère la valeur selectionner dans les liste des encadrant
    $selectedEquipe = mysqli_real_escape_string($connexion, trim($_POST['equipesId']));
    //appelle à la fonction qui récupère les projet d'un encadrant passé en paramètre
    if ($selectedEquipe != ""){
        $projEquipe = get_pojet_eq($selectedEquipe);
    
    } else $projEquipe = null;
    
    if($projEquipe == null || count($projEquipe) == 0){
        $message_projEq = "Aucun projet lié à l'encadrant d'id: $selectedEquipe !";
    }
}

/***************************************/
/* Gestion après selection d'un projet */
/***************************************/

if(isset($_POST['submitEquipeProj'])) {
    $idp = mysqli_real_escape_string($connexion, trim($_POST['equipeProjet']));

    $titreProjet = get_projet_title($idp);

    if($titreProjet == null){
        $message_jalonsNom = "Aucun titre & resumé disponible pour le projet d'id: $idp";
    }

    $jalonListe = get_projet_jalons($idp);

    if($jalonListe == null){
        $message_jalons = "Aucun jalon trouver pour le projet d'id: $idp";
    }
   
    /* 
    foreach ($jalonListe as &$jl){ //premet d'ajouter une colonne pour savoir de qu'elle type de jalons il s'agit
        
        switch($jl['rang']){
            case "1":
                $jl->type = "Avancement";
                break;
            case "2":
                $jl->type = "Rapport";
                break;
            case "3":
                $jl->type = "Soutenance";
                break;
            case "4":
                $jl->type = "Code";
                break;
        }
    }*/
}

?>