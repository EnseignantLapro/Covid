<?php
// Envoie d'info sur l'arme équipée notamment type et cooldown 
session_start();
include '../session.php';

if($access){
    // Création du ableau de réponse
    $reponse = array();

    $Personnage = $Joueur1->getPersonnage();
    $tab = $Personnage->getArme()->getType();
    //Recupération du type d'arme
    $reponse["arme"] = $tab[3];
    //Recupération du cooldown de l'arme
    /*$reponse["cooldown"] = */
    //Envoie du tableau de réponse
    echo json_encode($reponse);

}
?>
