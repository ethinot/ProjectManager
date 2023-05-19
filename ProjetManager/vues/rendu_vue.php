<!--Enzo EPHREM 
Edouard THINOT-->
<div class="panneau">
            
    <div> <!-- Premier bloc permetant l'affichage des équipes --> 
        
        <h2>Rendu de jalons</h2>

        </br>

        <form method="post" action="#">
            
            <?php if( $message_equipe != "" ) { ?>
                
                <p class="notification"><?= $message_equipe  ?></p>
            
            <?php } else {?>    

                <div> <!-- bloc correspondant à la liste des encadrant -->

                    <h5>Les équipes</h5>
                    
                    <select name="equipesId" id="equipesId" onchange="submit();">
                     
                        <?php $equipeId = $_POST["equipesId"]; ?>
                        
                        <option value="">Selectionnez une équipe</option>
                        
                        <?php foreach($equipes as $eq) { ?>
                            <!-- Liste déroulante des nom et prenom des enseigants concaténer -->
                            <option <?php if ( (isset($equipeId)) && ($equipeId == $eq['idEq']) ) { ?>selected="true" <?php }; ?>value="<?= $eq['idEq']?>"> 
                                <?= $eq['nom_equipe']?> 
                            </option>
                        <?php } ?>
                    
                    </select>
                        
                </div>
    
            <?php }?>

            </br>

            <?php if( $message_projEq != "" ) { ?>
                
                <p class="notification"><?= $message_projEq ?></p>
            
            <?php } else {?> 

                <div> <!-- bloc correspondant à la liste des projet si une équipe est selectionner -->
    
                    <h5>Vos projet</h5>

                    <select name="equipeProjet" id="equipeProjet">

                        <?php $encadrantProjet = $_POST["equipeProjet"]; ?>
                        
                        <?php foreach($projEquipe as $pe) { ?>
                            <!-- Liste déroulante des projets lié à l'équipe -->
                            <option <?php if ( (isset($encadrantProjet)) && ($encadrantProjet == $pe['idp']) ) { ?>selected="true" <?php }; ?> value="<?= $pe['idp']?>"> 
                                <?= $pe['real_etat'] . " | " . $pe['projet_titre'] . " | " . $pe['annee'] ?> 
                            </option>
                        <?php } ?>

                    </select>
                            
                    <input type="submit" name="submitEquipeProj" value="Valider"/>

                </div>

            <?php }?>
        
        </form>
            
    </div>

    <div class="panneau_details"> <!-- Deuxième bloc permetant l'affichage du titre de la description et des jalons qui compose le projet -->

        <?php if (isset ($titreProjet)) { ?>

            <h2>Votre projet</h2>	
                
            <div>
                <form method="post" action="#">	   

                    <?php foreach($titreProjet as $tp) { ?>
                        <p for=>Détails du projet <?= $tp['projet_titre'] ?></p>
                        <p><?= $tp['resumé'] ?></p>
                    <?php } ?>

                    <label for="choixTable">pour le jalon de rang</label>
                    
                    <select name="jalons" id="jalons">
                        <?php foreach($jalonListe as $tj) { ?>
                            <option value="<?= $tj['rang']?>"><?= $tj['rang']?></option>
                        <?php } ?>
                    </select>
                
                    <input type="submit" name="submitEquipeJalon" value="Selectionner"/>
            
                </form>
            </div>         
        
        <?php } ?>
                    
    </div>

</div>