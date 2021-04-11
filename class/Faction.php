
<?php
//cette classe est développé par : équipe Carre

class Faction {

    private $_id;
    private $_nom;
    private $_couleur;
    private $_bdd;

    public function __construct($bdd){
        $this->_bdd = $bdd;
    }

    public function setFactionById($id){

        //select les info personnage
        $req  = "SELECT * FROM `Faction` WHERE id='".$id."'";
        $Result = $this->_bdd->query($req);

        if($tab=$Result->fetch()){
        $this->_id = $tab['id'];
        $this->_nom= $tab['nom'];
        $this->_couleur= $tab['couleur'];
        }
    }

    public function getId(){
        return $this->_id;
    }
    public function getNom(){
        return $this->_nom;
    }
    public function getCouleur(){
        return $this->_couleur;
    }

    public function getAllTypePersonnage(){
        $TypePersos = array();
        $Result = $this->_bdd->query("SELECT * FROM `TypePersonnage` WHERE idFaction = '".$this->_id."'");
        while($tab=$Result->fetch()){
            $TypePerso = new TypePersonnage($this->_bdd);
            $TypePerso->setTypePersonnageById($tab['id']);
            array_push($TypePersos,$TypePerso);
        }
        return $TypePersos ;
    }

}


?>