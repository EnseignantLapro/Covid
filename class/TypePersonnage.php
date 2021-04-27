
<?php
//cette classe est développé par : équipe Carre

class TypePersonnage  extends CRUD{

    private $_id;
    private $_coefAttaque;
    private $_coefBouclier;
    private $_coefDefense;
    private $_distance;
    private $_idFaction;
    private $_lienImage;
    private $_nom;
    private $_bdd;

    public function __construct($bdd){
        $this->_bdd = $bdd;
    }


    public function setTypePersonnageById($id){

         //select les info personnage
         $req  = "SELECT * FROM `TypePersonnage` WHERE id='".$id."'";
         $Result = $this->_bdd->query($req);

         if($tab=$Result->fetch()){
            $this->_id = $tab['id'];
            $this->_coefAttaque= $tab['coefAttaque'];
            $this->_coefBouclier= $tab['coefBouclier'];
            $this->_coefDefense= $tab['coefDefense'];
            $this->_distance= $tab['distance'];
            $this->_idFaction= $tab['idFaction'];
            $this->_lienImage= $tab['lienImage'];
            $this->_nom= $tab['nom'];
         }

    }
    

    public function getId(){
        return $this->_id;
    }
    public function getNom(){
        return $this->_nom;
    }
    public function getCoefBouclier(){
        return $this->_coefBouclier;
    }
    public function getCoefDefense(){
        return $this->_coefDefense;
    }
    public function getCoefAttaque(){
        return $this->_coefAttaque;
    }
    public function getDistance(){
        return $this->_distance;
    }
    public function getLienImage(){
        return $this->_lienImage;
    }
    public function getFaction(){
        $faction = new Faction($this->_bdd);
        $faction->setFactionById($this->_idFaction);
        return $faction;
    }

}


?>