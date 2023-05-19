<!--Enzo EPHREM 
Edouard THINOT-->
<div class="panneau">
	<div style="background-color:rgba(100, 100, 100, 0.2);">
		<div class="row g-3">

			<div class="col">
				<h2>Creation d'un projet</h2>

				<h5>Nom Responsable</h5>
				<form method="POST" action='#'>
					<select name="respid" id="respid" onchange="submit();">
				
						<?php $respId = $_POST["respid"]; ?>
					
						<option value="">Selectionnez un responssable</option>
					
							<?php foreach($resps as $r) { ?>
								<!-- Liste déroulante des nom et prenom des enseigants concaténer -->
								<option <?php if ( (isset($respId)) && ($respId == $r['idEs']) ) { ?>selected="true" <?php }; ?>value="<?= $r['idEs']?>"> 
								<?= $r['nom'] . " " . $r['prenom'] ?> </option>
							<?php } ?>

					</select>
				</form>
			</div>
		</div>
		</br>
		<form method="POST" action='#'>
		<p> Votre nom ne figure pas ??</p>
			<div class = "row g-3">
				<div class="col-5">
					<input type="text" name="new_nom" class="form-control" placeholder= "Nom" value="" required>
					<input type="text" name="new_prenom" class="form-control" placeholder= "Prenom" value=""  required>
				</div>
				<div class="col">
					<button type="submit" class="btn btn-primary">Utiliser ce Nom et Prenom</button>
				</div>
			</div>
		</form>
	</div>
		

	<div class = "panneau-details"> 


		<?php 
			
		if(isset($_POST['new_nom'])) { 

			$nom =  $_POST['new_nom'];
			$prenom = $_POST['new_prenom'];
		}


		if($nom != '' && $prenom != '') { ?>

			<form method= "POST" action='#' class="row g-3">

			<?php if(isset($_POST['respid'])) { ?> <input type="text" class="form-control" name="input_id" value="<?=$_POST['respid']?>" hidden> <?php } ?>

				<div class="col-md-6">
					<label for="display_nom" class="form-label">Nom</label>
					<input type="text" class="form-control" name="display_nom" value="<?=$nom?>" disabled>
					<input type="text" class="form-control" name="input_nom" value="<?=$nom?>" hidden>
				</div>
				<div class="col-md-6">
					<label for="display_prenom" class="form-label">Prenom</label>
					<input type="text" class="form-control" name="display_prenom" value="<?=$prenom?>" disabled>
					<input type="text" class="form-control" name="input_prenom" value="<?=$prenom?>" hidden>
				</div>
				<div class="col-3">
					<label for="input_nom_projet" class="form-label">Nom du projet</label>
					<input type="text" class="form-control" name="input_nom_projet" required>
				</div>
				<div class="col-3">
					<label for="input_ue" class="form-label">UE:</label>
					<select name="input_ue" class="form-select">
						<option> Choose... </option>

							<?php foreach($ue as $u) { ?>
								<!-- Liste déroulante des nom et prenom des enseigants concaténer -->
								<option value="<?= $u['code_apoge']?>"> <?= $u['ue_libelle'] ?> </option>

							<?php } ?>

					</select>
				</div>
				<div class="col-6">
					<label for="input_link" class="form-label"> Lien du projet</label>
					<input type="text" class="form-control" name="input_link">
				</div>
				<div class="col-md-6">
					<label for="input_res" class="form-label">Resume</label>
					<input type="text" class="form-control" name="input_res">
				</div>
				<div class="col-md-6">
					<label for="input_equipe" class="form-label">Equipe Pedagogique (Ctrl/Command + click)</label>
					<select name="input_equipe[]" id="input_equipe" class="form-select" size="5" multiple="multiple">
						<option> Choose... </option>

							<?php foreach($ens as $e) { ?>
								<!-- Liste déroulante des nom et prenom des enseigants concaténer -->
								<option value="<?= $e['idEs']?>"> <?= $e['nom'] . " " . $e['prenom'] ?> </option>

							<?php } ?>

					</select>
				</div>

				<div class="col-1">
					<label for="input_nombre_equipe" class="form-label">Etudiant/Equipe</label>
					<input type="number" min="0" max="8" class="form-control" name="input_nombre_equipe">
				</div>
				
				<div class="col-2">
					<input class="form-check-input" type="checkbox" name="av" value="1">
					<label class="form-check-label" for="av">Jalon Avancement </label>
					</br>
					<label class="form-date-label" for="dateav">Date Limite </label>
					<input class="form-date-input" type="date" name="dateav">
					<label class="form-label" for="noteav">Note</label>
					<input class="form-number" type="number" min="0" max="20" step="1" name="noteav" value="0">
				</div>
				<div class="col-2">
					<input class="form-check-input" type="checkbox" name="ques" value="2">
					<label class="form-check-label" for="ques">Jalon Questionnaire</label>
					</br>
					<label class="form-date-label" for="dateques">Date Limite </label>
					<input class="form-date-input" type="date" name="dateques">
					<label class="form-label" for="noteques">Note</label>
					<input class="form-number" type="number" min="0" max="20" step="1" name="noteques" value="0">
				</div>
				<div class="col-2">
					<input class="form-check-input" type="checkbox" name="rap" value="3">
					<label class="form-check-label" for="rap">Jalon Rapport</label>
					</br>
					<label class="form-date-label" for="daterap">Date Limite </label>
					<input class="form-date-input" type="date" name="daterap">
					<label class="form-label" for="noterap">Note</label>
					<input class="form-number" type="number" min="0" max="20" step="1" name="noterap" value="0">
				</div>
				<div class="col-2">
					<input class="form-check-input" type="checkbox" name="cod" value="4">
					<label class="form-check-label" for="cod">Jalon Code</label>
					</br>
					<label class="form-date-label" for="datecod">Date Limite </label>
					<input class="form-date-input" type="date" name="datecod">
					<label class="form-label" for="notecod">Note</label>
					<input class="form-number" type="number" min="0" max="20" step="1" name="notecod" value="0">
				</div>
				<div class="col-2">
					<input class="form-check-input" type="checkbox" name="sout" value="5">
					<label class="form-check-label" for="sout">Jalon Soutenance</label>
					</br>
					<label class="form-date-label" for="datesout">Date Limite </label>
					<input class="form-date-input" type="date" name="datesout">
					<label class="form-label" for="notesout">Note</label>
					<input class="form-number" type="number" min="0" max="20" step="1" name="notesout" value="0">

				</div>
				<div class="col-12">
					<button type="submit" class="btn btn-primary">Create Project</button>
				</div>

			</form>

		<?php } ?>

		<?php 
			if (isset($_POST['input_nom_projet'])){
				echo "<h2> Projet a bien etait cree </h2>";
			}
		?>


	</div>

	


</div>