<?php
session_start();
//cette api retourne aprés usage d'une equipement l'atttaque la vie et la vie max 
//elle retourn 0 si çà c'est pas bien passé
include "../session.php"; 
$reponse[0]=0; //sera le nouveau id
$reponse[1]=0; //sera l'attaque
$reponse[5]=0; //sera le nom de la nouvel arme
$reponse[6]=0; //sera l'ancienne Arme id
$reponse[4]='';
$reponse[7]=0;// permet de savoir si c'est utilisation d'une arme ou d'un bouclier ect çà retourne la catégorie de Equipement
$reponse[8]=0;//sera le coeficient de defence
if($access){
    if(isset($_GET["idEquipement"])){

        //on doit toujours vérifier en bdd la posibilité de l'appel de API
        //iici on va utiliser un equipement pour un personnage.
        $message ='';
        $reponse[0]=0;
        $reponse[1]=0;
        $Perso = $Joueur1->getPersonnage();
        if($Perso->getVie()==0){
            $Perso->resurection();
            $reponse[1]="ton perso est mort";
        }
 
        //une fois que j'ai mes objet je vérifie que le perso possède bien equipement
        foreach ($Perso->getEquipements()  as $equipement) {
            if($_GET["idEquipement"]==$equipement->getId()){

                

                //selon l'id du type on fait un truc différent
                $type = $equipement->getCategorie();
                switch ($type['id']) {
                    case 1: //1 représente les armes dans la table categorie
                        //il faut changer d'arme
                        //on retire donc l'arme en cours ( equipe = 0 dans la table entite equipement)
                        if(!is_null($Perso->getArme())){
                            $reponse[6]=$Perso->getArme()->getId();
                            $Perso->desequipeArme();
                        }
                        if(!is_null($Perso->getPouvoir())){
                            $reponse[6]=$Perso->getPouvoir()->getId();
                            $Perso->desequipePouvoir();
                        }
                        //$equipement->desequipeEntite($Perso);
                        
                        
                        $equipement->equipeEntite($Perso);
                        $reponse[5] = $equipement->getNom()." lvl".$equipement->getLvl();
                        $message.= 's\'équipe de '.$equipement->getNom();
                        $reponse[7] =1;//1 est la categorie l'arme
                        break;
                    case 2: //1 représente les Armures dans la table categorie
                        //il faut changer d'ararmureme
                        //on retire donc l'armure en cours ( equipe = 0 dans la table entite equipement)
                        if(!is_null($Perso->getArmure())){
                            $reponse[6]=$Perso->getArmure()->getId();
                            $Perso->desequipeArmure();
                        }
                        if(!is_null($Perso->getBouclier())){
                            $reponse[6]=$Perso->getBouclier()->getId();
                            $Perso->desequipeBouclier();
                        }
                        
                       
                        $equipement->equipeEntite($Perso);
                        $reponse[5] = $equipement->getNom()." lvl".$equipement->getLvl();
                        $message.= 's\'équipe de '.$equipement->getNom();
                        $reponse[7] =2;//2 est la categorie l'Armure;
                        
                        break;
                    case 3: //3 représente les AttaqueMagic dans la table categorie
                        //il faut changer d'ararmureme
                        //on retire donc l'armure en cours ( equipe = 0 dans la table entite equipement)
                        if(!is_null($Perso->getArme())){
                            $reponse[6]=$Perso->getArme()->getId();
                            $Perso->desequipeArme();
                        }
                        if(!is_null($Perso->getPouvoir())){
                            $reponse[6]=$Perso->getPouvoir()->getId();
                            $Perso->desequipePouvoir();
                        }
                        $equipement->equipeEntite($Perso);
                        $reponse[5] = $equipement->getNom()." lvl".$equipement->getLvl();
                        $message.= 's\'équipe de '.$equipement->getNom();
                        $reponse[7] =3;//3 est la categorie du pouvoir magic;
                        
                        break;

                    case 4: //4 représente les bouclierMagic dans la table categorie
                        //il faut changer d'ararmureme
                        //on retire donc l'armure en cours ( equipe = 0 dans la table entite equipement)
                        if(!is_null($Perso->getArmure())){
                            $reponse[6]=$Perso->getArmure()->getId();
                            $Perso->desequipeArmure();
                        }
                        if(!is_null($Perso->getBouclier())){
                            $reponse[6]=$Perso->getBouclier()->getId();
                            $Perso->desequipeBouclier();
                        }
                        $equipement->equipeEntite($Perso);
                        $reponse[5] = $equipement->getNom()." lvl".$equipement->getLvl();
                        $message.= 's\'équipe de '.$equipement->getNom();
                        $reponse[7] =4;//4 est la categorie du bouclier magic;
                        
                        break;
                
                    default:
                        //on retire l'equipement du perso pour le transformer un statsuplementaire
                        $Perso->removeEquipementById($equipement->getId());
                        $val=$equipement->getValeur();
                        $attaque=round($val/10);
                        $viemore=round($val/5);
                        $message = $Perso->getNom()." à utilisé un Equipement pour booster ses stats ";
                        $Perso->lvlupAttaque($attaque);
                        $Perso->lvlupVie($viemore);
                        $Perso->lvlupVieMax($viemore);
                        break;
                }

                
                $reponse[8]= $Perso->getDefense();
                $reponse[4]=$message;
                $reponse[3]=$Perso->getVieMax();
                $reponse[2]=$Perso->getVie();
                $reponse[1]=$Perso->getAttaque();
                $reponse[0]=$Perso->getId();
                break;
            }
        }  
    }
}
echo json_encode($reponse);
?>