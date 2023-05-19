<!--Enzo EPHREM 
Edouard THINOT-->
<div class="panneau">
            
    <div> <!-- Premier bloc permetant l'affichage de tous les encadrants & leur projet --> 
        
        <h2>Suivie d'un projet</h2>

        </br>

        <form method="post" action="#">
            
            <?php if( $message_enc != "" ) { ?>
                
                <p class="notification"><?= $message_enc  ?></p>
            
            <?php } else {?>    

                <div> <!-- bloc correspondant à la liste des encadrant -->

                    <h5>Nom encadrants</h5>
                    
                    <select name="encadrantsId" id="encadrantsId" onchange="submit();">
                     
                        <?php $encadrantId = $_POST["encadrantsId"]; ?>
                        
                        <option value="">Selectionnez un encandrants</option>
                        
                        <?php foreach($encadrants as $e) { ?>
                            <!-- Liste déroulante des nom et prenom des enseigants concaténer -->
                            <option <?php if ( (isset($encadrantId)) && ($encadrantId == $e['idEs']) ) { ?>selected="true" <?php }; ?>value="<?= $e['idEs']?>"> 
                                <?= $e['nom'] . " " . $e['prenom'] ?> 
                            </option>
                        <?php } ?>
                    
                    </select>
                        
                </div>

            <?php }?> <!-- ferme le else -->
        
            </br>
            
            <?php if( $message_projEnc != "" ) { ?>
                
                <p class="notification"><?= $message_projEnc ?></p>
            
            <?php } else {?> 

                <div> <!-- bloc correspondant à la liste des projet si un encadrant est selectionner -->
                    
                    <h5>Vos projet</h5>
                        
                    <select name="encadrantProjet" id="encadrantPId">

                        <?php $encadrantProjet = $_POST["encadrantProjet"]; ?>
                        
                        <?php foreach($projEncradrant as $pe) { ?>
                            <!-- Liste déroulante des projets en charge par un encadrant -->
                            <option <?php if ( (isset($encadrantProjet)) && ($encadrantProjet == $pe['idp']) ) { ?>selected="true" <?php }; ?> value="<?= $pe['idp']?>"> 
                                <?= $pe['real_etat'] . " | " . $pe['projet_titre'] . " | " . $pe['annee'] ?> 
                            </option>
                        <?php } ?>

                    </select>
                        
                    <input type="submit" name="submitEncadrantProj" value="Valider"/>

                </div>

            <?php }?> <!-- ferme le else ligne 11 -->

        </form>
    
    </div>

    <div class="panneau_details"> <!-- Deuxième bloc permetant l'affichage du tableau de bord --> 
        
        <?php if (isset ($tabJalon)) { 
            
            if( is_array($tabJalon) ){?>
            
                <h2>Détails d'un projet</h2>

                </br>
                
                <h4>Liste des jalons</h4>

                <table class="table_resultat">
                    <thead>
                        <tr>
                            <?php
                                foreach($tabJalon['schema'] as $att) {
                                    echo '<th>';
                                        echo $att['nom'];
                                    echo '</th>';
                                }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($tabJalon['instances'] as $row){

                                echo '<tr>';
                                foreach($row as $valeur){
                                    echo '<td>' . $valeur . '</td>';
                                }
                                echo '</tr>';
                            }
                        ?>      
                    </tboby>    
                
                </table>

            <?php }else{ ?>

                <p class="notification"><?= $message_nbJalon . 'TOOT' ?></p>	

            <?php }	?>

        <?php } ?>

        </br>
    
        <?php if (isset ($tabEquipe)) { 
            
            if( is_array($tabEquipe) ){?>
            
                <h4>Liste des équipes</h4>

                <table class="table_resultat">
                    <thead>
                        <tr>
                            <?php
                                foreach($tabEquipe['schema'] as $att) {
                                    echo '<th>';
                                        echo $att['nom'];
                                    echo '</th>';
                                }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($tabEquipe['instances'] as $row){

                                echo '<tr>';
                                foreach($row as $valeur){
                                    echo '<td>' . $valeur . '</td>';
                                }
                                echo '</tr>';
                            }
                        ?>      
                    </tboby>    
                
                </table>

            <?php }else{ ?>

                <p class="notification"><?= $message_nbEquipe . 'TOOT' ?></p>	

            <?php }	?>

        <?php } ?>

    </div>

</div>