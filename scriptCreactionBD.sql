
/*********************************************
                                    Enzo EPHREM
                                 Edouard THINOT

   Script SQL d'integration des données


*********************************************/

-- Entitees
/* CREATA TABLE nom -> créé une nouvelle table dans la base de donnée avec différent nom de colonne (ici idEs, nom, prenom, statut). */
CREATE TABLE Enseignant(   idEs INT NOT NULL AUTO_INCREMENT, -- idEs est une clé primaire (donc non null), cette dernière est automatiquement incrémenter avec "AUTO_INCREMENT"
                           nom VARCHAR(50),
                           prenom VARCHAR(50),
                           statut VARCHAR(50),
                     PRIMARY KEY(idEs) -- Permet de définir idEs comme clé primaire
);

INSERT INTO Enseignant (nom, prenom) -- INSERT INTO nomDeLaTable premet comme son nom l'indique d'inserer dans le table des données
/* Réquete SQL simple permetant de selectionner les données pertinante à rajouter */
(SELECT ue_responsable_nom, ue_responsable_prenom
FROM donnees_fournies.instances)
UNION
(SELECT encadrant_nom, encadrant_prenom
FROM donnees_fournies.instances);

/****************************************************************************/

CREATE TABLE UE(  code_apoge VARCHAR(10),
                  ue_libelle VARCHAR(150),
                  sigle VARCHAR(50),
                  niveau VARCHAR(5),
                  nb_projet INTEGER,
            PRIMARY KEY(code_apoge)
);

/* On insert le code apoge et le libelle de l'UE */
INSERT INTO UE (code_apoge, ue_libelle, nb_projet)
SELECT A.code_apoge, A.ue_libelle, COUNT(idp) as nb_projet FROM (SELECT code_apoge, ue_libelle, idp
FROM donnees_fournies.instances
GROUP BY idp
ORDER BY code_apoge) as A GROUP BY A.code_apoge

/****************************************************************************/

CREATE TABLE Semestre(  semestre VARCHAR(10) NOT NULL,
                        annee_semestre INT NOT NULL,
                  PRIMARY KEY(semestre, annee_semestre)
);

INSERT INTO Semestre (semestre, annee_semestre)
SELECT DISTINCT semestre, annee 
FROM donnees_fournies.instances
GROUP BY semestre, annee
ORDER BY annee DESC, semestre;

/*******************************************************************************************************************/

CREATE TABLE Projet( idp BIGINT(4) NOT NULL,
                     projet_titre VARCHAR(80),
                     resumé VARCHAR(50),
                     lien_sujet VARCHAR(50),
                     annee INT,
                     etat VARCHAR(50),
                     PRIMARY KEY(idp)
);

/* On insert les idp + leur titre correspondant */
INSERT INTO Projet (idp, projet_titre, annee)
SELECT DISTINCT idp, projet_titre, annee
FROM donnees_fournies.instances
ORDER BY idp;

/*******************************************************************************************************************/

CREATE TABLE Jalon(  idp BIGINT(4),
                     rang INT,
                     date_limite DATE,
                     es_note BOOLEAN,
                     sur_combien INT,
                     date_report DATE,
               PRIMARY KEY(idp, rang),
               FOREIGN KEY (idp) REFERENCES Projet(idp) -- FOREIGN KEY permet de créé une clé étrangère et de la lié à la table principale  
);

INSERT INTO Jalon (idp, rang, date_limite, es_note, sur_combien)
(SELECT idp, jalon1_num, jalon1_datelimite, TRUE, jalon1_note
FROM donnees_fournies.instances
WHERE jalon1_note IS NOT NULL)
UNION -- Faire des union entre différent requête
(SELECT idp, jalon2_num, jalon2_datelimite, TRUE, jalon2_note
FROM donnees_fournies.instances
WHERE jalon2_note IS  NOT NULL)
UNION 
(SELECT idp, jalon3_num, jalon3_datelimite, TRUE, jalon3_note
FROM donnees_fournies.instances
WHERE jalon3_note IS  NOT NULL)
UNION
(SELECT idp, jalon4_num, jalon4_datelimite, TRUE, jalon4_note
FROM donnees_fournies.instances
WHERE jalon4_num IS NOT NULL and jalon4_note IS  NOT NULL)
UNION
(SELECT idp, jalon1_num, jalon1_datelimite, FALSE, jalon1_note
FROM donnees_fournies.instances
WHERE jalon1_note IS NULL)
UNION 
(SELECT idp, jalon2_num, jalon2_datelimite, FALSE, jalon2_note
FROM donnees_fournies.instances
WHERE jalon2_note IS NULL)
UNION 
(SELECT idp, jalon3_num, jalon3_datelimite, FALSE, jalon3_note
FROM donnees_fournies.instances
WHERE jalon3_note IS NULL)
UNION
(SELECT idp, jalon4_num, jalon4_datelimite, FALSE, jalon4_note
FROM donnees_fournies.instances
WHERE jalon4_num IS NOT NULL and jalon4_note IS NULL)
ORDER BY idp;

/*******************************************************************************************************************/

CREATE TABLE Etudiant(  numero INT,
                        nom VARCHAR(50) NOT NULL,
                        prenom VARCHAR(50) NOT NULL,
               PRIMARY KEY(numero)
);

INSERT INTO Etudiant (numero, nom, prenom)
/* SUBSTRING_INDEX permet ici de séparer une chaine de caractère en deux en utilisant le ; comme séparateur  le -1 sert à récupérer la fin deuxième partie de la chaine sans le début */
(SELECT DISTINCT etudiant1_numetu, SUBSTRING_INDEX(etudiant1_nomprenom, ";", 1), SUBSTRING_INDEX(etudiant1_nomprenom, ";", -1) FROM donnees_fournies.instances 
WHERE etudiant1_numetu IS NOT NULL)
UNION
(SELECT DISTINCT etudiant2_numetu, SUBSTRING_INDEX(etudiant2_nomprenom, ";", 1), SUBSTRING_INDEX(etudiant2_nomprenom, ";", -1) FROM donnees_fournies.instances 
WHERE etudiant2_numetu IS NOT NULL)
UNION
(SELECT DISTINCT etudiant3_numetu, SUBSTRING_INDEX(etudiant3_nomprenom, ";", 1), SUBSTRING_INDEX(etudiant3_nomprenom, ";", -1) FROM donnees_fournies.instances 
WHERE etudiant3_numetu IS NOT NULL)
UNION
(SELECT DISTINCT etudiant4_numetu, SUBSTRING_INDEX(etudiant4_nomprenom, ";", 1), SUBSTRING_INDEX(etudiant4_nomprenom, ";", -1) FROM donnees_fournies.instances 
WHERE etudiant4_numetu IS NOT NULL)
UNION
(SELECT DISTINCT etudiant5_numetu, SUBSTRING_INDEX(etudiant5_nomprenom, ";", 1), SUBSTRING_INDEX(etudiant5_nomprenom, ";", -1) FROM donnees_fournies.instances 
WHERE etudiant5_numetu IS NOT NULL)
UNION
(SELECT DISTINCT etudiant6_numetu, SUBSTRING_INDEX(etudiant6_nomprenom, ";", 1), SUBSTRING_INDEX(etudiant6_nomprenom, ";", -1) FROM donnees_fournies.instances 
WHERE etudiant6_numetu IS NOT NULL)
UNION
(SELECT DISTINCT etudiant7_numetu, SUBSTRING_INDEX(etudiant7_nomprenom, ";", 1), SUBSTRING_INDEX(etudiant7_nomprenom, ";", -1) FROM donnees_fournies.instances 
WHERE etudiant7_numetu IS NOT NULL)
UNION
(SELECT DISTINCT etudiant8_numetu, SUBSTRING_INDEX(etudiant8_nomprenom, ";", 1), SUBSTRING_INDEX(etudiant8_nomprenom, ";", -1) FROM donnees_fournies.instances 
WHERE etudiant8_numetu IS NOT NULL)
ORDER BY etudiant1_numetu;

/*******************************************************************************************************************/

CREATE TABLE Equipe( idEq INT NOT NULL AUTO_INCREMENT,
                     nom_equipe VARCHAR(20),
                     nb_equipier SMALLINT(8),
               PRIMARY KEY(idEq)
);

INSERT INTO Equipe (nom_equipe, nb_equipier)
SELECT nom_equipe,
  ((CASE WHEN etudiant1_numetu IS NULL THEN 0 ELSE 1 END)
+ (CASE WHEN etudiant2_numetu IS NULL THEN 0 ELSE 1 END)
+ (CASE WHEN etudiant3_numetu IS NULL THEN 0 ELSE 1 END)
+ (CASE WHEN etudiant4_numetu IS NULL THEN 0 ELSE 1 END)
+ (CASE WHEN etudiant5_numetu IS NULL THEN 0 ELSE 1 END)
+ (CASE WHEN etudiant6_numetu IS NULL THEN 0 ELSE 1 END)
+ (CASE WHEN etudiant7_numetu IS NULL THEN 0 ELSE 1 END)
+ (CASE WHEN etudiant8_numetu IS NULL THEN 0 ELSE 1 END)) 
  AS sum -- somme calculer grace au case (on compte 0 si il n'y à pas d'etudiant et 1 si il existe)
FROM donnees_fournies.instances;


/*******************************************************************************************************************/

CREATE TABLE Jalon_avancement(   idp BIGINT(4),
                                 rang INT,
                                 description_avancement VARCHAR(50),
                           PRIMARY KEY(idp, rang),
                           FOREIGN KEY(idp, rang) REFERENCES Jalon(idp, rang)
);

INSERT INTO Jalon_avancement (idp, rang)
SELECT idp, jalon1_num FROM donnees_fournies.instances WHERE jalon1_num IS NOT NULL GROUP BY idp;

/*******************************************************************************************************************/

CREATE TABLE Jalon_rapport(   idp BIGINT(4),
                              rang INT,
                              titre_rapport VARCHAR(20),
                              description_rapport VARCHAR(50),
                        PRIMARY KEY(idp, rang),
                        FOREIGN KEY(idp, rang) REFERENCES Jalon(idp, rang)
);

INSERT INTO Jalon_rapport (idp, rang)
SELECT idp, jalon2_num FROM donnees_fournies.instances WHERE jalon2_num IS NOT NULL GROUP BY idp;

/*******************************************************************************************************************/

CREATE TABLE Jalon_soutenance(   idp BIGINT(4),
                                 rang INT,
                                 titre_soutenence VARCHAR(20),
                                 consigne_soutenance VARCHAR(50),
                                 date_soutenance DATE,
                                 heure_passage TIME,
                                 lieu_soutenance VARCHAR(50),
                           PRIMARY KEY(idp, rang),
                           FOREIGN KEY(idp, rang) REFERENCES Jalon(idp, rang)
);

INSERT INTO Jalon_soutenance (idp, rang)
(SELECT idp, jalon3_num FROM donnees_fournies.instances WHERE jalon3_num IS NOT NULL GROUP BY idp);

/*******************************************************************************************************************/

CREATE TABLE Jalon_code(   idp BIGINT(4),
                           rang INT,
                           code TEXT,
                     PRIMARY KEY(idp, rang),
                     FOREIGN KEY(idp, rang) REFERENCES Jalon(idp, rang)
);

INSERT INTO Jalon_code (idp, rang)
(SELECT idp, jalon4_num FROM donnees_fournies.instances WHERE jalon4_num IS NOT NULL GROUP BY idp);


/*******************************************************************************************************************/

CREATE TABLE Rendu(  idp BIGINT(4),
                     rang INT,
                     idEq INT,
                     rendu_date DATE,
                     rendu_note SMALLINT(6),
                     etat_rendu VARCHAR(50),
               PRIMARY KEY(idp, rang, idEq),
               FOREIGN KEY(idp, rang) REFERENCES Jalon(idp, rang),
               FOREIGN KEY(idEq) REFERENCES Equipe(idEq)
);

INSERT INTO Rendu (idp, rang, idEq, rendu_date, rendu_note)
(SELECT I.idp, I.jalon1_num, E.idEq, I.rendu1_date, I.rendu1_note
FROM donnees_fournies.instances I, Equipe E WHERE I.nom_equipe = E.nom_equipe)
UNION
(SELECT I.idp, I.jalon2_num, E.idEq, I.rendu2_date, I.rendu2_note
FROM donnees_fournies.instances I, Equipe E WHERE I.nom_equipe = E.nom_equipe)
UNION
(SELECT I.idp, I.jalon3_num, E.idEq, I.rendu3_date, I.rendu3_note
FROM donnees_fournies.instances I, Equipe E WHERE I.nom_equipe = E.nom_equipe)
UNION
(SELECT I.idp, I.jalon4_num, E.idEq, I.rendu4_date, I.rendu4_note
FROM donnees_fournies.instances I, Equipe E WHERE I.nom_equipe = E.nom_equipe and I.jalon4_num IS NOT NULL);

/*******************************************************************************************************************/

CREATE TABLE Realisation(  idp BIGINT(4),
                           idEq INT,
                           titre_realisation TEXT,
                           note_finale DECIMAL(4,2),
                           observations VARCHAR(50),
                           url_image VARCHAR(50),
                     PRIMARY KEY(idp, idEq),
                     FOREIGN KEY(idp) REFERENCES Projet(idp),
                     FOREIGN KEY(idEq) REFERENCES Equipe(idEq)

);

INSERT INTO Realisation (idp, idEq, titre_realisation, note_finale, observations)
SELECT I.idp, E.idEq, I.titre_realisation, I.note_finale, I.observations
FROM donnees_fournies.instances I, Equipe E -- Equipe E premet d'aller chercher dans nos table qu'on vient de créé des données 
WHERE I.nom_equipe = E.nom_equipe;

/*******************************************************************************************************************/
/*                                               Assosations                                                       */
/*******************************************************************************************************************/

CREATE TABLE Inscrits(  code_apoge VARCHAR(10),
                        num_etudiant INT,
                        groupe_td VARCHAR(50),
                        groupe_tp VARCHAR(50),
                  PRIMARY KEY(num_etudiant, code_apoge),
                  FOREIGN KEY(num_etudiant) REFERENCES Etudiant(numero),
                  FOREIGN KEY(code_apoge) REFERENCES UE(code_apoge)
);

INSERT INTO Inscrits (code_apoge, num_etudiant)
(SELECT code_apoge, etudiant1_numetu FROM donnees_fournies.instances WHERE etudiant1_numetu IS NOT NULL)
UNION
(SELECT code_apoge, etudiant2_numetu FROM donnees_fournies.instances WHERE etudiant2_numetu IS NOT NULL)
UNION
(SELECT code_apoge, etudiant3_numetu FROM donnees_fournies.instances WHERE etudiant3_numetu IS NOT NULL)
UNION
(SELECT code_apoge, etudiant4_numetu FROM donnees_fournies.instances WHERE etudiant4_numetu IS NOT NULL)
UNION
(SELECT code_apoge, etudiant5_numetu FROM donnees_fournies.instances WHERE etudiant5_numetu IS NOT NULL)
UNION
(SELECT code_apoge, etudiant6_numetu FROM donnees_fournies.instances WHERE etudiant6_numetu IS NOT NULL)
UNION
(SELECT code_apoge, etudiant7_numetu FROM donnees_fournies.instances WHERE etudiant7_numetu IS NOT NULL)
UNION
(SELECT code_apoge, etudiant8_numetu FROM donnees_fournies.instances WHERE etudiant8_numetu IS NOT NULL)
ORDER BY code_apoge;


/****************************************************************************************/

CREATE TABLE En_equipe( idEq INT,
                        num_etudiant INT,
                        rôle VARCHAR(50),
                  PRIMARY KEY(num_etudiant, IdEq),
                  FOREIGN KEY(num_etudiant) REFERENCES Etudiant(numero),
                  FOREIGN KEY(IdEq) REFERENCES Equipe(IdEq)
);

INSERT INTO En_equipe (idEq, num_etudiant)
(SELECT E.idEq, I.etudiant1_numetu
FROM Equipe E JOIN donnees_fournies.instances I ON E.nom_equipe = I.nom_equipe  -- La jointure permet de récupérer et de les mettre en corrélation avec les données fournies  
WHERE I.etudiant1_numetu IS NOT NULL)
UNION
(SELECT E.idEq, I.etudiant2_numetu
FROM Equipe E JOIN donnees_fournies.instances I ON E.nom_equipe = I.nom_equipe 
WHERE I.etudiant2_numetu IS NOT NULL)
UNION
(SELECT E.idEq, I.etudiant3_numetu
FROM Equipe E JOIN donnees_fournies.instances I ON E.nom_equipe = I.nom_equipe 
WHERE I.etudiant3_numetu IS NOT NULL)
UNION
(SELECT E.idEq, I.etudiant4_numetu
FROM Equipe E JOIN donnees_fournies.instances I ON E.nom_equipe = I.nom_equipe 
WHERE I.etudiant4_numetu IS NOT NULL)
UNION
(SELECT E.idEq, I.etudiant5_numetu
FROM Equipe E JOIN donnees_fournies.instances I ON E.nom_equipe = I.nom_equipe 
WHERE I.etudiant5_numetu IS NOT NULL)
UNION
(SELECT E.idEq, I.etudiant6_numetu
FROM Equipe E JOIN donnees_fournies.instances I ON E.nom_equipe = I.nom_equipe 
WHERE I.etudiant6_numetu IS NOT NULL)
UNION
(SELECT E.idEq, I.etudiant7_numetu
FROM Equipe E JOIN donnees_fournies.instances I ON E.nom_equipe = I.nom_equipe 
WHERE I.etudiant7_numetu IS NOT NULL)
UNION
(SELECT E.idEq, I.etudiant8_numetu
FROM Equipe E JOIN donnees_fournies.instances I ON E.nom_equipe = I.nom_equipe 
WHERE I.etudiant8_numetu IS NOT NULL)
ORDER BY idEq;

/****************************************************************************************/

CREATE TABLE Responsable(  idEs INT,
                           code_apoge VARCHAR(10),
                           semestre VARCHAR(10),
                           annee_semestre INT,
                     PRIMARY KEY(idEs, code_apoge, semestre, annee_semestre),
                     FOREIGN KEY(idEs) REFERENCES Enseignant(idEs),
                     FOREIGN KEY(code_apoge) REFERENCES UE(code_apoge),
                     FOREIGN KEY(semestre, annee_semestre) REFERENCES Semestre(semestre, annee_semestre)

);

INSERT INTO Responsable(idEs, code_apoge, semestre, annee_semestre)
SELECT Ens.idEs, I.code_apoge, I.semestre, I.annee
FROM donnees_fournies.instances I,  Enseignant Ens 
WHERE Ens.nom = I.ue_responsable_nom AND Ens.prenom = I.ue_responsable_prenom 
GROUP BY code_apoge, annee, semestre;

/*******************************************************************************************************************/

CREATE TABLE Encadre( idEs INT,
                      idp BIGINT(4),
                      idEq INT,   
                  PRIMARY KEY(idEs, idp, idEq),
                  FOREIGN KEY(idEs) REFERENCES Enseignant(idEs),
                  FOREIGN KEY(idp) REFERENCES Projet(idp),
                  FOREIGN KEY(idEq) REFERENCES Equipe(idEq)
);

INSERT INTO Encadre(idEs, idp, idEq)
SELECT DISTINCT En.idEs, P.idp, Eq.idEq
FROM Enseignant En JOIN donnees_fournies.instances I ON I.encadrant_nom = E.nom 
AND I.encadrant_prenom = E.prenom JOIN Projet P ON I.idp = P.idp
JOIN Equipe Eq ON Eq.nom_equipe = I.nom_equipe
ORDER BY E.idEs, P.idp, Eq.IdEq ;

/*******************************************************************************************************************/

CREATE TABLE Declarer(  code_apoge VARCHAR(10),
                        idEs INT,
                        idp BIGINT(4),
                    PRIMARY KEY(code_apoge, idEs, idp)
                    FOREIGN KEY(idEs) REFERENCES Enseignant(idEs),
                    FOREIGN KEY(code_apoge) REFERENCES UE(code_apoge),
                    FOREIGN KEY(idp) REFERENCES Projet(idp)

);

INSERT INTO Declarer(code_apoge, idEs, idp)
SELECT I.code_apoge, E.idEs, I.idp FROM
donnees_fournies.instances I JOIN
Enseignant E ON I.ue_responsable_nom = E.nom 
AND I.ue_responsable_prenom = E.prenom GROUP BY idp
ORDER bY I.idp; 

/*******************************************************************************************************************/

CREATE TABLE Annee_projet(  idp BIGINT(4),
                            semestre VARCHAR(10),
                            annee_semestre INT,
                        PRIMARY KEY(idp, semestre, annee_semestre),
                        FOREIGN KEY(idp) REFERENCES Projet(idp),
                        FOREIGN KEY(semestre, annee_semestre) REFERENCES Semestre(semestre, annee_semestre)
);

INSERT INTO Annee_projet(idp, semestre, annee_semestre)
SELECT idp, semestre, annee
FROM donnees_fournies.instances
GROUP BY idp, annee, semestre
ORDER BY idp, annee, semestre

/*******************************************************************************************************************/


CREATE TABLE Equipe_peda( code_apoge VARCHAR(10),
                          semestre VARCHAR(10), 
                          annee_semestre INT,
                          idResp INT,
                          idEs INT,
                      PRIMARY KEY(code_apoge, semestre, annee_semestre, idResp, idEs),
                      FOREIGN KEY(code_apoge) REFERENCES UE(code_apoge),
                      FOREIGN KEY(semestre, annee_semestre) REFERENCES Semestre(semestre, annee_semestre),
                      FOREIGN KEY(idEs) REFERENCES Enseignant(idEs)
);

INSERT INTO Equipe_peda(code_apoge, semestre, annee_semestre, idResp, idEs)
SELECT DISTINCT R.code_apoge, R.semestre, R.annee_semestre, R.idEs, E.idEs
FROM Responsable R JOIN Declarer D ON R.code_apoge = D.code_apoge 
JOIN Encadre E ON D.idp = E.idp  
ORDER BY R.idEs, E.idEs


/*******************************************************************************************************************/
/*******************************************************FIN*********************************************************/
/*******************************************************************************************************************/