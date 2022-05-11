<?php
    //la classe Arme est une enfant de la classe Equipement
    //est représente la catégorie Arme d'un type Equipement
    // Il est existe donc plusieurs class d'arme 
    //une arme envoi des degat brute suplémentaire

    class Arme extends Equipement{

        public function createArmeAleatoire(){
            //attention la catérogie id arme doit etre = 1
            $req="SELECT * FROM TypeEquipement Where idCategorie = 1 order by rarete ASC";
            $Result = $this->_bdd->query($req);

            $newType=1;//par default une gifle c'est une attaque;
            $rarete=1;
            $newTypeNom='Gifle ';

            while($tab=$Result->fetch()){
                if(rand(0,$tab['chance'])==1){
                $newType = $tab['id'];
                $newTypeNom = $tab['nom'];
                $coef=$tab['rarete'];
                break;
                }
            }

            $getEfficace = $this->getEfficaceAleatoire();

            $newNom = $newTypeNom." ".$getEfficace['adjectif'];
            $efficacite = $getEfficace['id'];

            $newValeur = rand(5,10)*$rarete*$getEfficace['coef'];

            $this->_bdd->beginTransaction();
            $req="INSERT INTO `Equipement`( `type`, `nom`, `valeur`, `efficacite`,`lvl`) VALUES ('".$newType."','".$newNom."','".$newValeur."','".$efficacite."',1)";
            $Result = $this->_bdd->query($req);
            $lastID = $this->_bdd->lastInsertId();
            if($lastID){ 
                $this->setEquipement($lastID,$newType,$newNom,$newValeur,$efficacite,1);
                $this->_bdd->commit();
                return $this;
            }else{
                $this->_bdd->rollback();
                echo "erreur anormal createEquipementAleatoire equipement.php ".$req;
                return null;
            }
        }

        public function getForce(){
        return $val = $this->getLvl()*$this->getValeur();
        }
    }
?>