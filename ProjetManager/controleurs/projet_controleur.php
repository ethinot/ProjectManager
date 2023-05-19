<!--Enzo EPHREM 
Edouard THINOT-->
<?php

$nom = '';
$prenom = '';


if (isset($_POST['respid'])){
	$nom_prenom = get_nomprenom_resp_byid($_POST['respid']);
	$nom = $nom_prenom[0]['nom'];
	$prenom = $nom_prenom[0]['prenom'];
}

if (isset($_POST['new_nom'])){
	$nom = $_POST['new_nom'];
	$prenom = $_POST['new_prenom'];
}

$resps = get_nomprenom_resp();

if ($nom != '' && $prenom != ''){
	$ens = get_nomprenom_ens();
	$ue = get_ue();
}

if (isset($_POST['input_nom_projet'])){

	create_project($_POST);

}

?>