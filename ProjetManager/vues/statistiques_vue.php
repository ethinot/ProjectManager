<!--Enzo EPHREM 
Edouard THINOT-->
<div class="panneau">

<div>  <!-- Premier bloc permettant l'affichage de la liste des tables -->

  <h2>Liste des statistiques</h2>

  <?php if( $message != "" ) { ?>

	  <p class="notification"><?= $message  ?></p>

  <?php }else{?>

  <table class="table_resultat">
	  <thead>
		  <tr><th>Statistique disponible</th></tr>
	  </thead>
	  <tbody>
	  	<form class="bloc_commandes" method="post" action="#">	
			<tr> <td>
			<div class="row">
  				<div class="col">
					<button type="submit" name="selectedStat" value=0> Projets Actifs </button>
  				</div>
			</div>
			<div class="row">
  				<div class="col">
					<button type="submit" name="selectedStat" value=1> UE/Projet/Etats </button>
  				</div>
			</div>

			<div class="row">
  				<div class="col">
				  	<button type="submit" name="selectedStat" value=2> Nombre d'UE avec equipes + de</button>  
					<input type="number" name="nb_equipier" value=
					<?php 
					if (isset($_POST["nb_equipier"])) echo $_POST["nb_equipier"];
					else echo 2; ?> min=0 max=1000 step=1/>
  				</div>	
			</div>
			<div class="row">
  				<div class="col">
					<button type="submit" name="selectedStat" value=3> UE avec le + de projets </button>
  				</div>
			</div>
			<div class="row">
  				<div class="col">
					<button type="submit" name="selectedStat" value=4> Enseignant avec le plus de Projet </button> 
  				</div>
			</div>
			<div class="row">
  				<div class="col">
					<button type="submit" name="selectedStat" value=5> Best note </button> 
  				</div>
			</div>
			
				
			
		</form>
  </tbody>
  </table>

  <?php }?>

</div>

<div class="panneau_details"> <!-- Second bloc permettant l'affichage du détail d'une table -->

  <h2>Détails du resultats</h2>

  <div>
	  <?php if( isset($stats) ){
		  
		  if( is_array($stats) ){ 
	  ?>
		  <table class="table table_resultat">
			  <thead>
				  <tr>
				  <?php
					  //var_dump($resultats);
					  foreach($stats['schema'] as $att) {  // pour parcourir les attributs
			  
						  echo '<th>';
							  echo $att['nom'];
						  echo '</th>';
			  
					  }
				  ?>	
				  </tr>	
			  </thead>
			<tbody>

			  <?php
				  foreach($stats['instances'] as $row) {  // pour parcourir les n-uplets
			  
				  echo '<tr>';
				  foreach($row as $valeur) { // pour parcourir chaque valeur de n-uplets
			  
					  echo '<td>'. $valeur . '</td>';
				  }
				  echo '</tr>';
			  }
		  ?>
		  </tbody>
		  	<?php if (isset($_POST["selectedStat"])){
				  if ($_POST["selectedStat"] == 2) echo "<caption><p><strong>Nombre Totale d'UE: </strong>". count($stats['instances']) . "</p> </caption>" ;
			  }
			?>
	  </table>
	  
	  <?php }else{ ?>

		  <p class="notification"><?= $message_details . 'TOOT' ?></p>	

	  <?php }

  } ?>
  </div>


</div>

</div>