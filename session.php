<?php
    //  INCLUDES
        include "class/Entite.php";
        include "class/User.php";
        include "class/CRUD.php";
        include "class/Faction.php";
        include "class/TypePersonnage.php";
        include "class/Personnage.php";
        include "class/Map.php";
        include "class/Forge.php";
        include "class/Mob.php";
        include "class/Tooltip.php";
        include "class/Objet.php";
        include "class/Item.php";
        include "class/Equipement.php";
        include "class/Arme.php";
        include "class/Armure.php";

    //  GESTION DE LA BASE
        $mabase = null;
        $access = null;
        $errorMessage="";
        try{
            $user = "lapro_site";
            $pass = "TDataSource1234";
            //$mabase = new PDO('mysql:host=mysql-lapro.alwaysdata.net;dbname=lapro_virus', $user, $pass);
            $mabase = new PDO('mysql:host=127.0.0.1;dbname=lapro_virus', $user, $pass);
        }catch(Exception $e){
            $errorMessage .= $e->getMessage();
        }
        $Joueur1 = new User($mabase); 

    //  GESTION DES SESSIONS
        if(!is_null($mabase)){
            if (isset($_SESSION["Connected"]) && $_SESSION["Connected"]===true){
                $access = true;
                if(isset($_SESSION["idUser"])){
                    $Joueur1->setUserById($_SESSION["idUser"]);
                }
            }else{
                $access = false;
            // Affichage de formulaire si pas deconnexion
                $access = $Joueur1->ConnectToi();
            }
        }else{
            $errorMessage.= "Le site n'a pas accès à la BDD.";
        }
?>
