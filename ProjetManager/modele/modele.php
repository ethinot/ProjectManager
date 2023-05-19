<?php 

/*

Enzo EPHREM 
Edouard THINOT

Structure de données permettant de manipuler une base de données :
- Gestion de la connexion
----> Connexion et déconnexion à la base
- Accès au dictionnaire
----> Liste des tables et statistiques
- Informations (structure et contenu) d'une table
----> Schéma et instances d'une table
- Traitement de requêtes
----> Schéma et instances résultant d'une requête de sélection
*/



////////////////////////////////////////////////////////////////////////
///////    Gestion de la connxeion   ///////////////////////////////////
////////////////////////////////////////////////////////////////////////

/**
 * Fonction de debug on plus l'objet qu'on veut tester en paramètre
 */
function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}


/**
 * Initialise la connexion à la base de données courante (spécifiée selon constante 
	globale SERVEUR, UTILISATEUR, MOTDEPASSE, BDD)			
 */
function open_connection_DB() {
	global $connexion;

	$connexion = mysqli_connect(SERVEUR, UTILISATEUR, MOTDEPASSE, BDD);
	if (mysqli_connect_errno()) {
	    printf("Échec de la connexion : %s\n", mysqli_connect_error());
	    exit();
	}
}

/**
 *  	Ferme la connexion courante
 * */
function close_connection_DB() {
	global $connexion;

	mysqli_close($connexion);
}


////////////////////////////////////////////////////////////////////////
///////   Accès au dictionnaire       ///////////////////////////////////
////////////////////////////////////////////////////////////////////////

/**
 *  Retourne la liste des tables définies dans la base de données courantes
 * */
function get_tables() {
	global $connexion;

	$requete = "SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA LIKE '". BDD ."'";

	$res = mysqli_query($connexion, $requete);
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);
	return $instances;
}


/**
 *  Retourne les statistiques sur la base de données courante
 * */
function get_statistiques($selectedStat, $nb_equipier) {
	global $connexion;

	// récupération des informations sur la table (schema + instance) dependment de l'entree de l'utilisateur
	switch($selectedStat){
		case 0:
			$requete = "SELECT P.projet_titre, U.code_apoge, U.ue_libelle, Es.nom, Es.prenom
			FROM Projet P JOIN Declarer D ON D.idp = P.idp
			JOIN UE U ON D.code_apoge = U.code_apoge
			JOIN Responsable R ON U.code_apoge = R.code_apoge AND P.annee = R.annee_semestre
			JOIN Enseignant Es ON R.idEs = Es.idEs
			AND P.annee =" . strval(date("Y"));
			break;
		case 1:
			$requete = "SELECT U.code_apoge, U.ue_libelle, (CASE P.annee WHEN YEAR(CURDATE()) THEN 'Active' ELSE 'Archiver' END) as real_etat,
				COUNT(D.idp) as nombre_projet
				FROM
					UE U
				JOIN Declarer D ON
					U.code_apoge = D.code_apoge
				JOIN Projet P ON
					D.idp = P.idp
				GROUP BY U.code_apoge, real_etat";
			break;
		case 2:
			$requete = "SELECT D.code_apoge, Eq.nb_equipier as equipe_jusqua
			FROM Equipe Eq JOIN Realisation R ON Eq.idEq = R.idEq 
			JOIN Declarer D ON R.idp = D.idp 
			WHERE Eq.nb_equipier >" . $nb_equipier .
			" GROUP BY D.code_apoge";
			break;
		case 3:
			$requete = "SELECT code_apoge, ue_libelle, niveau, sigle, MAX(nb_projet) as nb_projet FROM UE";
			break;
		case 4:
			$requete = "SELECT R.nom, R.prenom, MAX(nombre_projet) as nombre_projet FROM 
			(SELECT E.nom, E.prenom, COUNT(idp) as nombre_projet FROM 
				Enseignant E JOIN Declarer D ON E.idEs = D.idEs GROUP BY D.idEs) as R";
			break;
		case 5:
			$requete = "
			SELECT Res.code_apoge, U.ue_libelle, P.projet_titre, AP.annee_semestre, AP.semestre, Et.nom, Et.prenom, Res.note_finale
			FROM (
					SELECT R1.code_apoge, R1.idp, R1.idEq, R1.note_finale 
					FROM (
							SELECT D.code_apoge, R.idp, R.idEq, R.note_finale FROM Declarer D
							LEFT OUTER JOIN Realisation R ON D.idp = R.idp
							AND R.note_finale = (SELECT MAX(note_finale) FROM Realisation WHERE idp = R.idp GROUP BY code_apoge)
					) as R1
			JOIN
				(
					SELECT code_apoge, idp, idEq, MAX(note_finale) as note_finale 
					FROM (
							SELECT D.code_apoge, R.idp, R.idEq, R.note_finale FROM Declarer D
							LEFT OUTER JOIN Realisation R ON D.idp = R.idp
							AND R.note_finale = (SELECT MAX(note_finale) FROM Realisation WHERE idp = R.idp GROUP BY code_apoge)
						) as Res GROUP BY code_apoge
				) as R2 
				
			ON R1.code_apoge = R2.code_apoge
			AND R1.note_finale = R2.note_finale
			) as Res
			JOIN En_equipe En ON Res.IdEq = En.idEq JOIN Etudiant Et ON En.num_etudiant = Et.numero
			JOIN Projet P ON Res.idp = P.idp
            JOIN Annee_projet AP ON P.idp = AP.idp
			JOIN UE U ON Res.code_apoge = U.code_apoge
			";
			break;			
		default:
			break;
	}
	$res = mysqli_query($connexion, $requete);

	// extraction des informations sur le schéma à partir du résultat précédent
	$infos_atts = mysqli_fetch_fields($res); 

	// filtrage des information du schéma pour ne garder que le nom de l'attribut
	$schema = array();
	foreach( $infos_atts as $att ){
		array_push( $schema , array( 'nom' => $att->{'name'} ) ); // syntaxe objet permettant de récupérer la propriété 'name' du de l'objet descriptif de l'attribut courant
	}

	// récupération des données (instances) de la table
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	// renvoi d'un tableau contenant les informations sur le schéma (nom d'attribut) et les n-uplets
	return array('schema'=> $schema , 'instances'=> $instances);
}

////////////////////////////////////////////////////////////////////////
///////    Informations (structure et contenu) d'une table    //////////
////////////////////////////////////////////////////////////////////////

/**
 *  Retourne le détail des infos sur une table
 * */
function get_infos( $typeVue, $nomTable ) {
	global $connexion;

	switch ( $typeVue) {
		case 'schema': return get_infos_schema( $nomTable ); break;
		case 'data': return get_infos_instances( $nomTable ); break;
		default: return null; 
	}
}

/**
 * Retourne le détail sur le schéma de la table
*/
function get_infos_schema( $nomTable ) {
	global $connexion;

	// récupération des informations sur la table (schema + instance)
	$requete = "SELECT * FROM $nomTable";
	$res = mysqli_query($connexion, $requete);

	// construction du schéma qui sera composé du nom de l'attribut et de son type	
	$schema = array( array( 'nom' => 'nom_attribut' ), array( 'nom' => 'type_attribut' ) , array('nom' => 'clé')) ;

	// récupération des valeurs associées au nom et au type des attributs
	$metadonnees = mysqli_fetch_fields($res);

	$infos_att = array();
	foreach( $metadonnees as $att ){
		//var_dump($att);

 		$is_in_pk = ($att->flags & MYSQLI_PRI_KEY_FLAG)?'PK':'';
 		$type = convertir_type($att->{'type'});

		array_push( $infos_att , array( 'nom' => $att->{'name'}, 'type' => $type , 'cle' => $is_in_pk) );	
	}

	return array('schema'=> $schema , 'instances'=> $infos_att);

}

/**
 * Retourne les instances de la table
*/
function get_infos_instances( $nomTable ) {
	global $connexion;

	// récupération des informations sur la table (schema + instance)
	$requete = "SELECT * FROM $nomTable";  
 	$res = mysqli_query($connexion, $requete);  

 	// extraction des informations sur le schéma à partir du résultat précédent
	$infos_atts = mysqli_fetch_fields($res); 

	// filtrage des information du schéma pour ne garder que le nom de l'attribut
	$schema = array();
	foreach( $infos_atts as $att ){
		array_push( $schema , array( 'nom' => $att->{'name'} ) ); // syntaxe objet permettant de récupérer la propriété 'name' du de l'objet descriptif de l'attribut courant
	}

	// récupération des données (instances) de la table
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	// renvoi d'un tableau contenant les informations sur le schéma (nom d'attribut) et les n-uplets
	return array('schema'=> $schema , 'instances'=> $instances);

}


function convertir_type( $code ){
	switch( $code ){
		case 1 : return 'BOOL/TINYINT';
		case 2 : return 'SMALLINT';
		case 3 : return 'INTEGER';
		case 4 : return 'FLOAT';
		case 5 : return 'DOUBLE';
		case 7 : return 'TIMESTAMP';
		case 8 : return 'BIGINT/SERIAL';
		case 9 : return 'MEDIUMINT';
		case 10 : return 'DATE';
		case 11 : return 'TIME';
		case 12 : return 'DATETIME';
		case 13 : return 'YEAR';
		case 16 : return 'BIT';
		case 246 : return 'DECIMAL/NUMERIC/FIXED';
		case 252 : return 'BLOB/TEXT';
		case 253 : return 'VARCHAR/VARBINARY';
		case 254 : return 'CHAR/SET/ENUM/BINARY';
		default : return '?';
	}

}

////////////////////////////////////////////////////////////////////////
///////    Traitement de requêtes                             //////////
////////////////////////////////////////////////////////////////////////

/**
 * Retourne le résultat (schéma et instances) de la requ$ete $requete
 * */
function get_nomprenom_enc(){
	
	global $connexion;

	// récupération les noms & prenoms de encadrants
	$requete = "SELECT DISTINCT Enc.idEs, Ens.nom, Ens.prenom 
				FROM Enseignant Ens JOIN Encadre Enc ON Ens.idEs = Enc.idEs 
				ORDER BY Ens.nom, Ens.prenom ";
	/*
	SELECT P.*, R.annee_semestre
	FROM Projet P 
	JOIN Declarer D ON P.idp = D.idp
	JOIN Responsable R ON R.idEs = D.idEs
	WHERE R.idEs = 60
	ORDER BY P.etat, R.annee_semestre DESC, P.projet_titre */
	$res = mysqli_query($connexion, $requete);
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances;
}

/**
 * Retourne un tableau de la requete $requete.
 * Ici on récupère les projet lié à un encadrant passer en paramètre (id_encadrant). 
 */
function get_pojet_enc($id_encadrant){

	global $connexion;

	$requete = "SELECT P.*, (CASE P.annee WHEN YEAR(CURDATE()) THEN 'Actif' ELSE 'Archivé' END) AS real_etat
	FROM Projet P JOIN Encadre Enc ON P.idp = Enc.idp
	WHERE Enc.idEs = $id_encadrant
	ORDER BY real_etat, P.annee DESC, P.projet_titre;";
	$res = mysqli_query($connexion, $requete);
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances;
}

/**
 * Retourn un tableau de la requete $requete
 * Ici on récupère le liste des jalons lié à l'id d'un projet (idp) que l'enseignant encadre
 */
function get_jalon_list($idp, $idEs){
	
	global $connexion;

	$requete = "SELECT rang AS Rang, date_limite AS 'Date limite', es_note AS 'Es noté ?', sur_combien AS 'Sur combien', date_report AS 'Date report', COUNT(E.idEq) AS 'Nombre dépot attendu'
	FROM Jalon J JOIN Encadre E ON J.idp = E.idp
	WHERE J.idp = $idp AND E.idEs = $idEs
	GROUP BY (J.rang)";
	/*SELECT J.rang AS Rang, J.date_limite AS 'Date limite', J.es_note AS 'Es noté ?', J.sur_combien AS 'Sur combien', J.date_report AS 'Date report', COUNT(R.idp) AS 'Nombre rendu déposé',COUNT(E.idEq) AS 'Nombre dépot attendu'
	FROM Jalon J JOIN Encadre E ON J.idp = E.idp
	JOIN (SELECT idp, rang, rendu_date FROM Rendu) R ON R.idp = J.idp AND R.rendu_date <= J.date_limite
	WHERE J.idp = 1 AND E.idEs = 60
	GROUP BY (J.rang)*/

	$res = mysqli_query($connexion, $requete);

	// extraction des informations sur le schéma à partir du résultat précédent
	$infos_atts = mysqli_fetch_fields($res); 

	// filtrage des information du schéma pour ne garder que le nom de l'attribut
	$schema = array();
	foreach( $infos_atts as $att ){
		array_push( $schema , array( 'nom' => $att->{'name'} ) ); // syntaxe objet permettant de récupérer la propriété 'name' du de l'objet descriptif de l'attribut courant
	}

	// récupération des données (instances) de la table
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	// renvoi d'un tableau contenant les informations sur le schéma (nom d'attribut) et les n-uplets
	return array('schema'=> $schema , 'instances'=> $instances);
}
/**
 * Cette fonction prend un paramètre un idp et un identifiant d'enseignant et renvoie un tableau associatif des équipes
 */
function get_equipe_list($idp, $idEs){

	global $connexion;

	$requete = "SELECT Eq.nom_equipe FROM Encadre E JOIN Equipe Eq ON E.idEq = Eq.idEq WHERE E.idp = $idp AND E.idEs = $idEs"; // affiche que le noms des équipes
	
	$res = mysqli_query($connexion, $requete);

	// extraction des informations sur le schéma à partir du résultat précédent
	$infos_atts = mysqli_fetch_fields($res); 

	// filtrage des information du schéma pour ne garder que le nom de l'attribut
	$schema = array();
	foreach( $infos_atts as $att ){
		array_push( $schema , array( 'nom' => $att->{'name'} ) ); // syntaxe objet permettant de récupérer la propriété 'name' du de l'objet descriptif de l'attribut courant
	}

	// récupération des données (instances) de la table
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	// renvoi d'un tableau contenant les informations sur le schéma (nom d'attribut) et les n-uplets
	return array('schema'=> $schema , 'instances'=> $instances);
}



/**
 * Retourn un tableau de la requete $requete
 * Ici on récupère le liste des jalons lié à l'id d'un projet (idp) que l'enseignant encadre
 */
function get_total_jalon_expected($listJalon){
	
	global $connexion;

	foreach ($listJalon['Rang'] as $rj){
		
	}

	$requete = "SELECT COUNT(idEq) AS nombre_rendu_total
	FROM Rendu 
	WHERE idp = $idp && rang = $rang;"; //recupère le nombre d'équipe donc de rendu total par jalon
	$res = mysqli_query($connexion, $requete);
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances;
}

/**
 * Fonction non utilisé permetant de récuperer le total de rendu déposer pour un idp et un rand 
 */
function get_total_rendu_deposit($idp, $rang){
	global $connexion;

	$requete = "SELECT COUNT(rendu_date) AS nombre_jalon_rendu
	FROM Rendu
	WHERE rendu_date IS NOT NULL && idp = $idp && rang = $rang;"; //recupère le nombre de jalon rendu 
	$res = mysqli_query($connexion, $requete);
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances;
}

/**
 * Retourne les noms des équipes trié par odre alphabétique
 */
function get_nom_equipe(){
	global $connexion;

	// récupération les noms des equipe trié par ordre alphabétique
	// SUBSTRING_INDEX permet de récupèrer que le nombre associer au nom d'une équipe
	// CAST(... AS ...) permet de convertir, ici en INT
	$requete = 'SELECT idEq, nom_equipe
				FROM Equipe
				ORDER BY CAST(SUBSTRING_INDEX(nom_equipe, "_", -1) AS unsigned)';
	
	$res = mysqli_query($connexion, $requete);
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances;
}

/**
 * Prend en paramètre un id d'équipe et renvoie les projets lié a cette équipe
 */
function get_pojet_eq($idEq){
	
	global $connexion;

	$requete = "SELECT P.*, (CASE P.annee WHEN YEAR(CURDATE()) THEN 'Actif' ELSE 'Archivé' END) AS real_etat
	FROM Encadre E JOIN Projet P ON E.idp = P.idp
	WHERE E.idEq = $idEq
	ORDER BY real_etat, P.annee DESC, P.projet_titre;";
	
	$res = mysqli_query($connexion, $requete);
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances;
}

/**
 * Prend en paramètre un idp et renvoie son titre et son résumé
 */ 
function get_projet_title($idp){

	global $connexion;

	$requete = "SELECT projet_titre, resumé 
	FROM Projet
	WHERE idp = $idp;";
	
	$res = mysqli_query($connexion, $requete);
	
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances;
}

/**
 * Prend en paramètre un idp et renvoie les jalons lié au projet
 */
function get_projet_jalons($idp){

	global $connexion;

	$requete = "SELECT idp, rang
	FROM Jalon
	WHERE idp = $idp";

	$res = mysqli_query($connexion, $requete);
	
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances;
}

/**
 * Retourne le nom est prenom des respossable d'UE
 * */
function get_nomprenom_resp(){
	
	global $connexion;

	// récupération les noms & prenoms de responsable
	$requete = "SELECT DISTINCT Res.idEs, Ens.nom, Ens.prenom 
				FROM Enseignant Ens JOIN Responsable Res ON Ens.idEs = Res.idEs 
				ORDER BY Ens.nom, Ens.prenom ";

	$res = mysqli_query($connexion, $requete);
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances;
}


/**
 * Retourne le nom et prenom du responsable en fonction d'un id 
 * */
function get_nomprenom_resp_byid($id){
	
	global $connexion;

	// récupération les noms & prenoms de responsable
	$requete = "SELECT DISTINCT Ens.nom, Ens.prenom 
				FROM Enseignant Ens JOIN Responsable Res ON Ens.idEs = Res.idEs
				WHERE Ens.idEs = " . $id . " 
				ORDER BY Ens.nom, Ens.prenom ";

	$res = mysqli_query($connexion, $requete);
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances;
}


/**
 * Retourne le nom et le prenom des enseignant 
 * */
function get_nomprenom_ens(){
	
	global $connexion;

	// récupération les noms & prenoms de responsable
	$requete = "SELECT DISTINCT Ens.idEs, Ens.nom, Ens.prenom 
				FROM Enseignant Ens
				ORDER BY Ens.nom, Ens.prenom ";

	$res = mysqli_query($connexion, $requete);
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances;
}

/**
 * Retourne le UE
 * */
function get_ue(){
	
	global $connexion;

	// récupération les noms & prenoms de responsable
	$requete = "SELECT DISTINCT * 
				FROM UE U
				ORDER BY U.code_apoge";

	$res = mysqli_query($connexion, $requete);
	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances;
}

/**
 * Compte le nombre de projet et le retourne
 */
function get_project_count(){

	global $connexion;

	$requete = "SELECT COUNT(P.idp) as nbr_idp FROM Projet P";

	$res = mysqli_query($connexion, $requete);

	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances[0]['nbr_idp'] + 1;
}


/**
 * Compte le nombre d'enseignant
 */
function get_idEs(){

	global $connexion;

	$requete = "SELECT COUNT(E.idEs) as nbr_idEs FROM Enseignant E";

	$res = mysqli_query($connexion, $requete);

	$instances = mysqli_fetch_all($res, MYSQLI_ASSOC);

	return $instances[0]['nbr_idEs'] + 1;
}

/**
 * Ajoute un projet a la base de donnée
 */
function add_to_Project($array){

	global $connexion;

	$into = "Projet (idp, projet_titre, annee";
	$what = " VALUES (";
	$idp= get_project_count();

	$what = $what . $idp .',"' . $_POST['input_nom_projet'] . '","' . date('Y');

	if (isset($array['input_res'])){
		$into = $into . ", resumé";
		$what = $what . '","' . $array['input_res'];
	}

	if (isset($array['input_link'])){
		$into = $into . ", lien_sujet";
		$what = $what . '","' . $array['input_link'];
	}

	$what = $what . '")';
	$into = $into . ')';

	$requete = "INSERT INTO " . $into . $what;

	$res = mysqli_query($connexion, $requete);

	return $idp;
}

/**
 * Ajoute un jalon avancement à la base de donnée
 */
function add_to_JalonAv($array, $idp, $rang){

	global $connexion;

	$into = "Jalon (idp, rang, ";
	$what = ' VALUES ('. $idp . ',' . $rang;

	if (isset($array['dateav'])){
		$into = $into . "date_limite";
		$what = $what . ',"' . $array['dateav'] . '"';
	}

	if (isset($array['noteav'])){
		if ($array['noteav'] > 0){
			$into = $into . ", es_note, sur_combien";
			$what = $what . ', true ,' . $array['noteav'];
		}else{
			$into = $into . ", es_note, sur_combien";
			$what = $what . ', false , NULL';
		}
	}

	$what = $what . ')';
	$into = $into . ')';

	$requete = "INSERT INTO " . $into . $what;
	$res = mysqli_query($connexion, $requete);

}

/**
 * Ajoute un jalon question à la base de donnée
 */
function add_to_JalonQues($array, $idp, $rang){

	global $connexion;

	$into = "Jalon (idp, rang, ";
	$what = ' VALUES ('. $idp . ',' . $rang;


	if (isset($array['dateques'])){
		$into = $into . "date_limite";
		$what = $what . ',"' . $array['dateques'] . '"';
	}

	if (isset($array['noteques'])){
		if ($array['noteques'] > 0){
			$into = $into . ", es_note, sur_combien";
			$what = $what . ', true ,' . $array['noteques'];
		}else{
			$into = $into . ", es_note, sur_combien";
			$what = $what . ', false , NULL';
		}
	}

	$what = $what . ')';
	$into = $into . ')';

	$requete = "INSERT INTO " . $into . $what;
	$res = mysqli_query($connexion, $requete);
}

/**
 * Ajoute un jalon rapport à la base de donnée
 */
function add_to_JalonRap($array, $idp, $rang){

	global $connexion;

	$into = "Jalon (idp, rang, ";
	$what = ' VALUES ('. $idp . ',' . $rang;


	if (isset($array['daterap'])){
		$into = $into . "date_limite";
		$what = $what . ',"' . $array['daterap'] . '"';
	}

	if (isset($array['noterap'])){
		if ($array['noterap'] > 0){
			$into = $into . ", es_note, sur_combien";
			$what = $what . ', true ,' . $array['noterap'];
		}else{
			$into = $into . ", es_note, sur_combien";
			$what = $what . ', false , NULL';
		}
	}

	$what = $what . ')';
	$into = $into . ')';

	$requete = "INSERT INTO " . $into . $what;
	$res = mysqli_query($connexion, $requete);
}

/**
 * Ajoute un jalon code à la base de donnée
 */
function add_to_JalonCod($array, $idp, $rang){

	global $connexion;

	$into = "Jalon (idp, rang, ";
	$what = ' VALUES ('. $idp . ',' . $rang;



	if (isset($array['datecod'])){
		$into = $into . "date_limite";
		$what = $what . ',"' . $array['datecod'] . '"';
	}

	if (isset($array['notecod'])){
		if ($array['notecod'] > 0){
			$into = $into . ", es_note, sur_combien";
			$what = $what . ', true ,"' . $array['notecod'];
		}else{
			$into = $into . ", es_note, sur_combien";
			$what = $what . ', false , NULL';
		}
	}

	$what = $what . ')';
	$into = $into . ')';

	$requete = "INSERT INTO " . $into . $what;
	$res = mysqli_query($connexion, $requete);
}


/**
 * Ajoute un jalon soutenance à la base de donnée
 */
function add_to_JalonSout($array, $idp, $rang){

	global $connexion;

	$into = "Jalon (idp, rang, ";
	$what = ' VALUES ('. $idp . ',' . $rang;



	if (isset($array['datesout'])){
		$into = $into . "date_limite";
		$what = $what . ',"' . $array['datesout'] . '"';
	}

	if (isset($array['notesout'])){
		if ($array['notesout'] > 0){
			$into = $into . ", es_note, sur_combien";
			$what = $what . ', true ,' . $array['notesout'];
		}else{
			$into = $into . ", es_note, sur_combien";
			$what = $what . ', false , NULL';
		}
	}

	$what = $what . ')';
	$into = $into . ')';


	$requete = "INSERT INTO " . $into . $what;
	$res = mysqli_query($connexion, $requete);
}

/**
 * Met à jour Declarer et Responsable dans le base de donnée
 */
function add_to_Declarer($array, $idp){

	global $connexion;

	if (isset($array["input_id"])){
		$idEs = $array["input_id"];
	}else {
		$idEs = get_idEs();
		$requete = "INSERT INTO Enseignant (idEs, nom, prenom) VALUES (" . $idEs . ',"' . $array['input_nom'] . '","' . $array['input_prenom'] . '")';
		print($requete);
		$res = mysqli_query($connexion, $requete);
	}

	$requete = 'INSERT INTO Declarer (code_apoge, idp, idEs) VALUES ("' . $array['input_ue'] . '",' . $idp . ',' . $idEs . ')';
	print($requete);
	$res = mysqli_query($connexion, $requete);

	$requete = 'INSERT INTO Responsable (code_apoge, idEs, annee_semestre, semestre) VALUES ("' . $array['input_ue'] . '",' . $idEs . ',' . 2022 . ', "Printemps")';
	print($requete);
	$res = mysqli_query($connexion, $requete);

}

/**
 * Fonction qui permet de créé un projet
 */
function create_project($array){

	$rang = 1;

	$idp = add_to_Project($array);

	if (isset($array['av'])){
		add_to_JalonAv($array, $idp, $rang);
		$rang = $rang + 1;
	}
	if (isset($array['ques'])){
		add_to_JalonQues($array, $idp, $rang);
		$rang = $rang + 1;
	}
	if (isset($array['rap'])){
		add_to_JalonRap($array, $idp, $rang);
		$rang = $rang + 1;
	}
	if (isset($array['cod'])){
		add_to_JalonCod($array, $idp, $rang);
		$rang = $rang + 1;
	}
	if (isset($array['sout'])){
		add_to_JalonSout($array, $idp, $rang);
		$rang = $rang + 1;
	}

	add_to_Declarer($array, $idp);
}

?>
