<?php

require_once 'models/agenda.php';
require_once 'models/activite.php';
require_once 'models/categorie.php';
require_once 'models/utilisateur.php';
require_once 'models/commentaire.php';
require_once 'models/aimecomm.php';

class Controller_Activite
{

  public static function doTree($comm,$niveau,$activite)
  {
    $aime=Aimecomm::get_by_comm($comm->idComm());
    for($i=0;$i<count($aime);$i++)
    {
      $UtilAime[$i]=Utilisateur::get_by_id($aime[$i]->idUtilisateur());
    }

    $commentateur=Utilisateur::get_by_id($comm->idUtilisateur());
    include 'views/commentaire.php';
    $childs=Commentaire::get_childs($comm->idComm());
    $niveau ++;
    for($i=0;$i<count($childs);$i++)
    {
      self::doTree($childs[$i],$niveau,$activite);
    }

  }

	public static function show($id) {
        if(isset($_SESSION['user'])) {
          $activite=Activite::get_by_id($id);
					include 'views/activite.php';
          $commentaires=Commentaire::get_by_activite($id);
          for($j=0;$j<count($commentaires);$j++){
            self::doTree($commentaires[$j],0,$activite);
          }
          include 'views/endActivite.php';
  			}
				else {
					$_SESSION['message']['type'] = 'error';
					$_SESSION['message']['text'] = "You aren't connected";
					include 'views/connexion.php';
				}

  }

  public static function doCommentaire($idActivite,$idParent)
  {
		switch ($_SERVER['REQUEST_METHOD']) {
			case 'GET' :
				break;

			case 'POST' :
				if(isset($_SESSION['user'])) {
          $c= new Commentaire(1,$idParent,$_SESSION['idUser'],$idActivite,'',$_POST['contenu']);
          $c->add();
					self::show($idActivite);
				}
				else {
					$_SESSION['message']['type'] = 'error';
					$_SESSION['message']['text'] = "You aren't connected";
					include 'views/connexion.php';
				}
				break;
		}
  }

}