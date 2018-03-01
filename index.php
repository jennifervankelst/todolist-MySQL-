<?php
 
try {
    //je me connecte a MySQL
    $bdd = new PDO('mysql:host=localhost;dbname=todoliste;charset=utf8', 'root', 'user');
}   

catch(Exception $erreur) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur: ' .$erreur->getMessage());

}




/*FORMULAIRE*/
  /*Sanitisation*/
  $options = array(
    'tache' => FILTER_SANITIZE_STRING,
    'tacheligne' => FILTER_SANITIZE_STRING
  );
  $result = filter_input_array(INPUT_POST, $options);
  /*fin Sanitisation*/
  //Requête POST:
  //vérification des valeurs après la Sanitisation
  if($result != null && $result != FALSE && $_SERVER['REQUEST_METHOD']=='POST')
  {

    if(isset($_POST["submit"])){

      $tache=$_POST["tache"];
      insertmysql($tache, "false");
    }

    if(isset($_POST["ajouter"]) || isset($_POST["Retour"])) {

      $tache_ligne = $_POST["tacheligne"];
      // print_r($tache_ligne);
      for($i = 0; $i < sizeof($tache_ligne); $i++){
        updatemysql($tache_ligne[$i]);
        // enregistreJSON($tache_ligne);

      } 
    }
    if(isset($_POST["Supprimer"])){
      
      $tache_ligne = $_POST["tacheligne"];
      // print_r($tache_ligne);
      for($i = 0; $i < sizeof($tache_ligne); $i++){
        deletemysql($tache_ligne[$i]);
      }
    }
    /*nom de la tache contenu dans le "TextBox"*/
    /*$tache=$_POST["tache"];
    /utilisation de la fonction ecrireJSON/
    /ecrireJSON($tache, false);*/
  }

 
  function affichemysql($archive="false")
  {
    global $bdd;
    $reponse = $bdd->query('SELECT * FROM `tasks`');
    
    while ($ligne=$reponse->fetch())
    {
       
    if($ligne['archive'] == $archive)
      {
        $i = $ligne['id']; 


        $txt = '<div class="draggable">';
        $txt .= '<label class="';
        $txt .= $archive=="true"?"tache_archive":"tache_non_archive";
        $txt .= '" for="">';
        /*début : balise <input>*/
        $txt .= '<input type="checkbox" name="tacheligne[]" value="';
        /*$i représente le numero de la ligne*/
        $txt .= $i.'" ';
        /*si la valeur $archive est vraie ajouter l'attribut "checked" */
        //$txt .= $archive=="true"?"checked":"";
        $txt .= ">";
        //$ligne['archive'] = true;
        /*fin : balise <input>*/
        /*balise fermante <label>*/
        $txt .= $ligne['tache'].'</label>';
        $txt .= "<br/>";
        $txt .= '</div>';
        echo $txt;
      }
    }
  }

  function insertmysql($tache, $archive)
  {
    
    global $bdd;
  
    $req = $bdd->prepare("INSERT INTO tasks(tache, archive) VALUES(:tache, :archive)");

    $req->execute(array(
        "tache" => $tache,
        "archive" => $archive
  ));
    
  }
  function updatemysql($id)
  {
    global $bdd;
    $reponse = $bdd->prepare("SELECT * FROM tasks WHERE id = :id LIMIT 0,1"); 
    $reponse->execute(array(
        "id" => $id,
        ));

    $ligne = $reponse->fetch();
    $archive = $ligne["archive"] == "true" ? "false": "true";

    $req = $bdd->prepare("UPDATE tasks SET archive = :archive WHERE id = :id");
    $req->execute(array(
        "id" => $id,
        "archive" => $archive
        ));
  }
  function deletemysql($id)
  {
    global $bdd;
    $reponse = $bdd->prepare("DELETE FROM tasks WHERE id = :id"); 
    $reponse->execute(array(
        "id" => $id,
        ));

  }

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <link href="https://fonts.googleapis.com/css?family=Indie+Flower" rel="stylesheet"> 
    <title>To-do list</title>
  </head>
  <body>
    <h1>TO DO LIST</h1>  
      <fieldset class="afaire">
        <form action="index.php" method="POST">
          <h5>
            A FAIRE
          </h5>
          <div class="dropper">
            <div class="essai">
          
              <?php affichemysql("false"); ?>

            </div>
          </div>
          <input class="button" type="submit" name="ajouter" value="Fini">
        </form>
      </fieldset>
      <fieldset class="archive">
        <form method="POST" action="index.php">
          <h5>
            ARCHIVE
          </h5>
            <span class="barre">
              <div class="dropper">
                <div class="essai">

                  <?php affichemysql("true"); ?>

                </div>
              </div>
            </span>
          <input class= "button" type="submit" name="Retour" value="Retour">
          <input class= "button" type="submit" name="Supprimer" value="Supprimer"> 
        </form>
      </fieldset>
      <form method="POST" action="index.php">
        <fieldset class="task">
          <label for="tache">Ajouter une tâche</label>
          <p><span>Liste des tâches a effectuer</span></p>
          <input type="text" name="tache" value="">
          <input class="button" type="submit" name="submit" value="Valider">
        </fieldset>
      </form>
  </body>
</html>



