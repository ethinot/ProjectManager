<!--Enzo EPHREM 
Edouard THINOT-->
<?php 
$message = "";

if(isset($_POST['selectedStat']) && isset($_POST["nb_equipier"])){
	$stats = get_statistiques($_POST['selectedStat'], $_POST["nb_equipier"]);
	if($stats == null || count($stats) == 0) {
		$message .= "Aucune statistique n'est disponible!";
	}
}else {
	$stats = get_statistiques(0, 2);
	if($stats == null || count($stats) == 0) {
		$message .= "Aucune statistique n'est disponible!";
	}
}
?>
