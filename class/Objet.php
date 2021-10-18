<?php
    class Objet{
        //valeurs
        protected $_id;
        protected $_type;
        protected $_nom;
        protected $_valeur;
        protected $_efficacite;
        protected $_lvl;
        protected $_bdd;
        //fonctions
        public function getEfficacite(){
            $req="SELECT * FROM Efficacite where id = '".$this->_efficacite."'";
            $Result = $this->_bdd->query($req);
            if($tab=$Result->fetch()){
                return $tab['coef'];
            }
                return 0;
        }
        public function getLvl(){
            return $this->_lvl;
        }
        public function getNom(){
            return $this->_nom;
        }
        public function getId(){
            return $this->_id;
        }
        public function getValeur(){
            return $this->_valeur;
        }
        protected function getEfficaceAleatoire(){
            $req="SELECT * FROM Efficacite ORDER BY ordre ASC";
            $Result = $this->_bdd->query($req);
            $found = false;
            while($tab=$Result->fetch()){
                if(rand(0,$tab['chance'])==1){
                    $tabretour  = $tab;
                    $found = true;
                }
            }
            if($found){
                return $tabretour;
            }
            //si on trouve rien dans la base ( ce qui est pas normal
            //on envoi une efficacité bidon)
            $tab['id'] = 1;
            $tab['coef'] = 0.1;
            $tab['ordre'] = 1;
            $tab['adjectif']="nul";
            return $tab;
        }
    }
?>