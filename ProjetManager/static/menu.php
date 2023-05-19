<!--Enzo EPHREM 
Edouard THINOT-->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
		  <img src="./img/navIcon.png" alt="NavIcon" width="40" height="40">
	  </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <?php 
        $url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; //premet de recuperer l'url actuel
        $url_components = parse_url($url); // segment l'url passer en paramètre et renvoie un tableau associatif des différents éléments de l'url 
        if (array_key_exists("query", $url_components)){ // vérifie sur la clé du tableau $url_components existe
          parse_str($url_components['query'], $param); // premet de recuperer la string paramètre de l'url
        }
      ?>
      <div class="navbar-nav">
        <a class="nav-link <?php if ( ($url_components['path'] == "/p1909945/ProjetMVC/index.php") && !(array_key_exists("query", $url_components)) ) { ?>active <?php }; ?>" href="index.php">Acceuil</a>
        <a class="nav-link <?php if ( $param['page'] == "statistiques") { ?> active <?php }; ?>" href="index.php?page=statistiques">Afficher Statistiques</a>
        <a class="nav-link <?php if ( $param['page'] == "tabbord") { ?> active <?php }; ?>" href="index.php?page=tabbord">Tableau de bord</a>
        <a class="nav-link <?php if ( $param['page'] == "rendu") { ?> active <?php }; ?>" href="index.php?page=rendu">Dépôt de jalon</a>
        <a class="nav-link <?php if ( $param['page'] == "projet") { ?> active <?php }; ?>" href="index.php?page=projet">Création de projet</a>
      </div>
    </div>
  </div>
</nav>

