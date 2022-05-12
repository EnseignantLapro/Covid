<?php
    // TODO MOB ET PERSONNAGE ON TROP DE SIMILITUDE
    //IL FAUT REFACTORISER AVEC DE LhERITAGE

    class Entite {

        protected $_id;
        protected $_nom;
        protected $_vie;
        protected $_vieMax;
        protected $_degat;
        protected $_imageLien;
        protected $_lvl;
        private $sacEquipements=array();
        private $sacEquipe=array();

        protected $_type; //1 = hero 2= mob

        protected $map;

        protected $_bdd;
        //dans le cas d'un perso
        protected $_idTypePersonnage;
        protected $typePersonnage;

        //private $sacItems=array();

        public function __construct($bdd){
            $this->_bdd = $bdd;
        }

        public function removeEquipementByID($EquipementID){
            $idindex = array_search($EquipementID, $this->sacEquipements);
            if($idindex  >= 0){
                unset($this->sacEquipements[ $idindex ]);
                $req="DELETE FROM `EntiteEquipement` WHERE idEntite='".$this->getId()."' AND idEquipement='".$EquipementID."'";
                $this->_bdd->query($req);

                //todo retirer un equipement ne doit pas etre une suppression
                //todo la suppression peut etre déjà faite à la fusion
                $req="DELETE FROM `Equipement` WHERE id='".$EquipementID."'";
                $this->_bdd->query($req);
            }
        }

        //ajoute un lien entre item et la personnage en bdd 
        //et accroche l'item dans la collection itemID dans le sac du perso
        public function addEquipement($newEquipement){
            //on va vérifier ici que l'équipement n'est pas déjà présent de meme niveau sinon fusion.
            //exemple 2 epée lvl 2 = epée lvl 3 avec un boost de fusions dans Efficacite
            //la fusion est récursive un lvl passé lvl 2 peut ausis fusioné avec un lvl3
            $TabIDRemoved = array(); // tableau des id supprimé aprés fusion pour les envoyer au front 
            $newEquipement->fusionEquipement($this,$TabIDRemoved);
            if(count($TabIDRemoved)>0 ){
                foreach ($TabIDRemoved as $idSup) {
                    $this->removeEquipementByID($idSup);
                }
                array_push($this->sacEquipements,$newEquipement->getId());
                return $TabIDRemoved;
            }else{
                $req="INSERT INTO `EntiteEquipement`(`idEntite`, `idEquipement`) VALUES ('".$this->getId()."','".$newEquipement->getId()."')";
                $this->_bdd->query($req);
                array_push($this->sacEquipements,$newEquipement->getId());
                //retourne 0 si ya pas eu de fusion d'équipement
                return 0;
            }
        }

        /* Début Cauet */

        public function getBardeVie(){
            $pourcentage = round(100*$this->_vie/$this->_vieMax);
            ?>
                <div class="EntitePrincipalBarreVie">
                    <div class="attaque" id="attaqueEntiteValeur<?= $this->_id ;?>"> <?= $this->_degat ;?>  </div> 
                    <div class="barreDeVie" id="vieEntite<?= $this->_id ;?>">
                        <div class="vie" id="vieEntiteValeur<?= $this->_id ;?>" style="width:<?= $pourcentage?>%;">
                            ♥️<?= $this->_vie ;?>
                        </div>
                    </div>
                </div>
            <?php
        }

        public function getVieMax(){
            return $this->_vieMax ;
        }

        public function getVie(){
            //on enpeche les boost de perdurer
            if($this->_vie>$this->_vieMax){
                $this->_vie =$this->_vieMax;
                $req  = "UPDATE `Entite` SET `vie`='".$this->_vie ."' WHERE `id` = '".$this->_id ."'";
                $Result = $this->_bdd->query($req);
            }
            return $this->_vie;
        }

        public function getLvl(){
            return $this->_lvl;
        }

        //Equipe l'item au personnage
        //cette methode doit etre appelé que par Equipemement
        public function addEquipeById($EquipementID){
            //la mise a jout en base et fait 
            array_push($this->sacEquipe,$EquipementID);
        }

        //Déséquipe l'item au personnage
        //cette methode doit etre appelé que par Equipemement
        public function removeEquipeBydId($EquipementID){
            //la mise a jout en base et fait dans l'equipement
            $id = array_search($EquipementID, $this->sacEquipe);
            if($id >= 0){
                unset($this->sacEquipe[ $id ]);
            }
        }

        //retourne uniquement les equipiments non porte
        public function getEquipementNonPorte(){
            //compare les 2tableau et retourne ce qui est pas commun
            $tab1 = $this->sacEquipements;
            $tab2 = $this->sacEquipe;
            //attention l'ordre des param est important 
            $tab3 = array_diff($tab1,$tab2);
            //compare les 2tableau et retourne ce qui est commun
            $lists=array();
            foreach($tab3 as $EquipementId){
                $newEquipement = new Equipement($this->_bdd);
                $newEquipement->setEquipementByID($EquipementId);
                array_push($lists,$newEquipement);
            }
            return $lists;
        }

        public function getEquipements(){
            $lists=array();
            foreach($this->sacEquipements as $EquipementId){
                $newEquipement = new Equipement($this->_bdd);
                $newEquipement->setEquipementByID($EquipementId);
                array_push($lists,$newEquipement);
            }
            return $lists;
        }

        //Retour un objet de type arme
        public function getArme(){
            $Arme = null;
            foreach($this->sacEquipe as $EquipementId){
                $EntiteEquipe = new Equipement($this->_bdd);
                $EntiteEquipe->setEquipementByID($EquipementId);
                //Le chiffre 1 et id de la categorie Armure à vérifier en base
                if($EntiteEquipe->getCategorie()['id']==1){
                    $Arme = new Arme($this->_bdd);
                    $Arme->setEquipementByID($EntiteEquipe->getId());
                    return $Arme;
                }
            }
            return $Arme;
        }

        //Retour un objet de type armure 
        public function getArmure(){
            $Armure = null;
            foreach($this->sacEquipe as $EquipementId){
                $EntiteEquipe = new Equipement($this->_bdd);
                $EntiteEquipe->setEquipementByID($EquipementId);
                //Le chiffre 2 et id de la categorie Armure à vérifier en base
                if($EntiteEquipe->getCategorie()['id']==2){
                    $Armure = new Armure($this->_bdd);
                    $Armure->setEquipementByID($EntiteEquipe->getId());
                    return $Armure;
                }
            }
            return $Armure;
        }

        //Retour un objet de type pouvoir
        public function getPouvoir(){
            $Pouvoir = null;
            foreach($this->sacEquipe as $EquipementId){
                $EntiteEquipe = new Equipement($this->_bdd);
                $EntiteEquipe->setEquipementByID($EquipementId);
                //Le chiffre 1 et id de la categorie Pouvoir à vérifier en base
                if($EntiteEquipe->getCategorie()['id']==3){
                    $Pouvoir = new Pouvoir($this->_bdd);
                    $Pouvoir->setEquipementByID($EntiteEquipe->getId());
                    return $Pouvoir;
                }
            }
            return $Pouvoir;
        }

        //Retour un objet de type bouclier
        public function getBouclier(){
            $Pouvoir = null;
            foreach($this->sacEquipe as $EquipementId){
                $EntiteEquipe = new Equipement($this->_bdd);
                $EntiteEquipe->setEquipementByID($EquipementId);
                //Le chiffre 1 et id de la categorie Pouvoir à vérifier en base
                if($EntiteEquipe->getCategorie()['id']==4){
                    $Bouclier = new Bouclier($this->_bdd);
                    $Bouclier->setEquipementByID($EntiteEquipe->getId());
                    return $Bouclier;
                }
            }
        }

        //Fonction pour déséquiper une arme
        public function desequipeArme(){
            $arme = $this->getArme();
            if(!is_null($arme)){
                $arme->desequipeEntite($this);
            }
        }

        //Fonction pour déséquiper une armure
        public function desequipeArmure(){
            $armure = $this->getArmure();
            if(!is_null($armure)){
                $armure->desequipeEntite($this);
            }
        }

        //Fonction pour déséquiper un pourvoir
        public function desequipePouvoir(){
            $pouvoir = $this->getPouvoir();
            if(!is_null($pouvoir)){
                $pouvoir->desequipeEntite($this);
            }
        }

        //Fonction pour déséquiper un bouclier
        public function desequipeBouclier(){
            $bouclier = $this->getBouclier();
            if(!is_null($bouclier)){
                $bouclier->desequipeEntite($this);
            }
        }

        //Fonction Attaque utilise une Arme
        public function getAttaque(){

            //si L'attaquant à une arme cas 1 
            //si il a de la magie cas 2
            //si il a rien cas 3



            //ici on affiche les dégats Maximun du joueur avec Arme
            $arme = $this->getArme();
            $pouvoir = $this->getPouvoir();
            $coef = 1;
            $lvl = 1;
            $forceArme = 0;
            if(!is_null($arme)){
                $coef = $arme->getEfficacite();
                $forceArme = $arme->getForce();
                $lvl = $arme->getLvl();
            }
            if(!is_null($pouvoir)){
                $coef = $pouvoir->getEfficacite();
                $forcePourvoir = $pouvoir->getForce();
                $lvl = $pouvoir->getLvl();
            }
            //application des coef si il y a nu type de personnage
            //1 c'est des perso , 2 c'est des mob
            if ($this->_type == 1){
               $type = $this->getTyePersonnage();
               if(!is_null($arme)){
                $coef = $coef*$type->getCoefAttaque();
               }
               if(!is_null($pouvoir)){
                $coef = $coef*$type->getCoefPouvoir();
                }
               
            }

        
            $val = round(($this->_degat+$forceArme)*$coef);
            return $val;
        }

        public function getCoolDownAttaque(){

            $arme = $this->getArme();
            $pouvoir = $this->getPouvoir();
            $cooldown = -1;
            if(!is_null($arme)){
                $cooldown =$arme->getCoolDown();
            }else if(!is_null($pouvoir)){
                $cooldown = $pouvoir->getCoolDown();
            }else{
                $cooldown = 500;
            }
            return $cooldown;
        }

        //Fonction Sort utilise un Pouvoir
        public function getSort(){
            //ici on affiche les dégats Maximun du joueur avec Arme
            $pouvoir = $this->getPouvoir();
            $coef = 1;
            $lvl = 1;
            $forcePouvoir = 0;
            if(!is_null($pouvoir)){
                $coef = $pouvoir->getEfficacite();
                $forcePourvoir = $pouvoir->getForce();
                $lvl = $pouvoir->getLvl();
            }
            $val = round(($this->_degat+$forcePouvoir)*$coef);
            return $val;
        }

        //Fonction Défense utilise une Armure
        public function getDefense(){
            //cas 
            //ici on affiche les dégats Maximun Absorbé avec une armure
            $armure = $this->getArmure();
            $bouclier = $this->getBouclier();
            $coef = 1;
            $forceArmure = 0;
            if(!is_null($armure)){
                $coef = $armure->getEfficacite();
                $forceArmure = $armure->getForce();
            }
            if(!is_null($bouclier)){
                $coef = $bouclier->getEfficacite();
                $forceBouclier = $bouclier->getForce();
            }
            //alors Todo Je sais pas ... evaluer la valeur d'une armure
            if ($this->_type == 1){
                $type = $this->getTyePersonnage();
                if(!is_null($armure)){
                 $coef = $coef*$type->getCoefDefense();
                }
                if(!is_null($bouclier)){
                 $coef = $coef*$type->getCoefBouclier();
                }
                
            }
            
            $val = $coef * $forceArmure ;

                  

            return round($val,1);//arrondi à 1 chiffre aprés la virgul
        }

        //Fonction Parer utilise un bouclier
        public function getParer(){
            //ici on affiche les dégats Maximun Absorbé avec une armure
            $bouclier = $this->getBouclier();
            $coef = 1;
            $forceBouclier = 0;
            if(!is_null($bouclier)){
                $coef = $bouclier->getEfficacite();
                $forceBouclier = $bouclier->getForce();
            }
            //alors Todo Je sais pas ... evaluer la valeur d'une armure
            $val = $coef * $forceBouclier ;
            return round($val,1);//arrondi à 1 chiffre aprés la virgul
        }

        /* Fin Cauet */

        public function getDegat(){
            //doit retourner des degat que l'entite donne a l'instant t
            return $this->_degat;
        }
        //il n'est possible de booster la vie au dela de vie max
        public function SoinPourcentage($pourcentage){
            $valeur = round(($this->_vieMax*$pourcentage)/100);
            $this->_vie = $valeur+ $this->_vie;
            if($this->_vie>$this->_vieMax){
                $this->_vie = $this->_vieMax;
            }
            $req = "UPDATE `Entite` SET `vie`='".$this->_vie."' WHERE `id` = '".$this->_id."'";
            $Result = $this->_bdd->query($req);
            return $valeur;
        }

        public function SubitDegatByEntite($Entite){
            //nouveauté 2022 JLA on va gerer les dégat selon le type d'équipement
            //get Attaque sera modifier
            $MobDegatAttaqueEnvoyer=$Entite->getAttaque();
            //Mise en place de la defence.
            $MobDegatAttaqueEnvoyer -= round(($MobDegatAttaqueEnvoyer * $this->getDefense())/100,1);
    
            $this->_vie = $this->_vie - $MobDegatAttaqueEnvoyer;
            if($this->_vie<0){
                $this->_vie =0;
                //retour en zone 0,0
            }
            $req = "UPDATE `Entite` SET `vie`='".$this->_vie."' WHERE `id` = '".$this->_id."'";
            $Result = $this->_bdd->query($req);
            return $this->_vie;
        }

        public function getAllMyMobIdByMap($map){
            $listMob=array();
            $req="SELECT `id` FROM `Entite` WHERE `idUser` = '".$this->_id."' AND `idMap` = '".$map->getId()."')";
            $Result = $this->_bdd->query($req);
            while($tab=$Result->fetch()){
                array_push($listMob,$tab);
            }
            return $listMob;
        }

        public function SubitDegatByMob($Mob){
            $MobDegatAttaqueEnvoyer=$Mob->getAttaque();
            //Mise en place de la defence.
            $MobDegatAttaqueEnvoyer -= round(($MobDegatAttaqueEnvoyer * $this->getDefense())/100,1);

            $vieAvantAttaque = $this->_vie;
            //on va rechercher l'historique
            $req = "SELECT * FROM `AttaqueEntiteMob` where idMob = '".$Mob->getId()."' and idEntite = '".$this->_id."'";
            $Result = $this->_bdd->query($req);
            $tabAttaque['nbCoup']=0;
            $tabAttaque['DegatsDonnes']=$MobDegatAttaqueEnvoyer;
            if($tab=$Result->fetch()){
                $tabAttaque = $tab;
                $tabAttaque['DegatsDonnes']+=$MobDegatAttaqueEnvoyer;
                $tabAttaque['nbCoup']++;
            }else{
                //insertion d'une nouvelle attaque
                $req="INSERT INTO `AttaqueEntiteMob`(`idMob`, `idEntite`, `nbCoup`, `coupFatal`, `DegatsDonnes`, `DegatsReçus`) 
                VALUES (
                    '".$Mob->getId()."','".$this->_id."',0,0,".$tabAttaque['DegatsReçus'].",0
                )";
                $Result = $this->_bdd->query($req);
            }

            $this->_vie = $this->_vie - $MobDegatAttaqueEnvoyer;
            if($this->_vie<0){
                $this->_vie=0;
                //on ne peut pas donner plus de degat que la vie d'un perso
                $tabAttaque['DegatsDonnes'] = $vieAvantAttaque;
                //retour en zone 0,0
            }
            $req = "UPDATE `Entite` SET `vie`='".$this->_vie ."' WHERE `id` = '".$this->_id."'";
            $Result = $this->_bdd->query($req);
            //update AttaqueEntiteMob pour mettre a jour combien le perso a pris de degat 
            $req="UPDATE `AttaqueEntiteMob` SET 
            `DegatsDonnes`=".$tabAttaque['DegatsDonnes']." 
            WHERE idMob = '".$Mob->getId()."' AND idEntite ='".$this->_id."'";
            $Result = $this->_bdd->query($req);
            return $this->_vie;
        }

        public function resetCoolDown(){
            $arme = $this->getArme();
            $pouvoir = $this->getPouvoir();
            
            if(!is_null($arme)){
                $cooldown =$arme->resetCoolDown();
            }else if(!is_null($pouvoir)){
                $cooldown = $pouvoir->resetCoolDown();
            }
            
        }

        public function setEntite($id,$nom,$vie,$degat,$vieMax,$image,$type,$lvl){
            $this->_id = $id;
            $this->_nom = $nom;
            $this->_vie = $vie;
            $this->_vieMax = $vieMax;
            $this->_degat = $degat;
            $this->_imageLien = $image;
            $this->_type = $type;
            $this->_lvl = $lvl;

            if($this->_type == 1){
                if(is_null($this->typePersonnage)){
                    $TypePersonnage = new TypePersonnage($this->_bdd);
                    $TypePersonnage->setTypePersonnageByIdPerso($this->_id);
                    $this->typePersonnage = $TypePersonnage;
                    $this->_idTypePersonnage = $TypePersonnage->getId();
                
                }
            }
        }

        //retour le type de personnage 
        //retour null si pas de type
        public function getTyePersonnage(){
            
            if(!is_null($this->_idTypePersonnage) ){
                if(is_null($this->typePersonnage)){
                    $TypePersonnage = new TypePersonnage($this->_bdd);
                    $TypePersonnage->setTypePersonnageByIdPerso($this->_id);
                    $this->typePersonnage = $TypePersonnage;
                
                }
                return $this->typePersonnage;
            }else{
                //ne devrait jamais etre le cas
                return null;
            }
            
        }

        public function getNom(){
            return $this->_nom;
        }

        //met a jour la vie de depart et replace le joueur
        public function resurection(){
            $vieMax = intdiv ($this->_vieMax,2);
            $attaque = intdiv ($this->_vieMax,2);
            if($vieMax<10){$vieMax=10;}
            $req = "UPDATE `Entite` SET `degat`='".$attaque."',`vieMax`='".$vieMax."',`vie`='".$vieMax."' WHERE `id` = '".$this->_id."'";
            $Result = $this->_bdd->query($req);
            $this->_vie=$vieMax;
            $this->_vieMax=$vieMax;
            $this->_degat=$attaque;
            $maporigine = new Map($this->_bdd);
            $maporigine->setMapByID(0);
            $this->changeMap($maporigine);
        }

        //retourne un entier de toutes ses valeurs
        public function getValeur(){
            $valeur = 0;
            foreach($this->getEquipements() as $value){
                $valeur+=$value->getValeur();
            }
            //$valeur = 100;
            return $valeur;
        }

        //retourne toute la mécanique d'affichage d'un Entite
        public function renderHTML(){
            if($this->_vieMax<0 || $this->_vieMax=="0"){
                $this->_vieMax=10;
            }
            $pourcentage = round(100*$this->_vie/$this->_vieMax);
            $arme = $this->getArme();
            $pouvoir = $this->getPouvoir();
            if ($this->_type == 1){
                $type = $this->getTyePersonnage();
            }

            ?>
                <div class="EntiteInfo">
                    <div class="EntiteName">
                        <?= $this->getNom() ?>
                    </div>
                    <div class="EntiteValeur">
                        (<?= $this->getValeur() ?> NFT) LV <?= $this->_lvl ?>
                    </div>
                </div>
                <div>
                    <img class="Entite" src="<?= $this->_imageLien;?>">
                </div>
                
            <?php 
            
            if(!is_null($arme)){
                ?>
                <div class="attaque standard" id="attaqueEntiteValeur<?= $this->_id ;?>"> <?= $this->getAttaque()?>
                    <div class="coef">(*<?php 
                        if(!is_null($type)){
                            echo $type->getCoefAttaque();
                        }else{
                            echo "1";
                        }
                    ?>)</div>
                </div>
                <div id="Arme<?= $arme->getId() ?>" class="Arme standard" onclick="CallApiRemoveEquipementEntite(<?= $arme->getId() ?>)"><?= $arme->getNom() ?> lvl <?= $arme->getLvl() ?></div>
                <?php
            }else if(!is_null($pouvoir)){
                ?>
                <div class="attaque magic" id="attaqueEntiteValeur<?= $this->_id ;?>"> <?= $this->getAttaque()?>
                    <div class="coef">(*<?php 
                        if(!is_null($type)){
                            echo $type->getCoefPouvoir();
                        }else{
                            echo "1";
                        }
                    ?>)</div>
                </div>
                <div id="Arme<?= $pouvoir->getId() ?>" class="Arme magic" onclick="CallApiRemoveEquipementEntite(<?= $pouvoir->getId() ?>)"><?= $pouvoir->getNom() ?> lvl <?= $pouvoir->getLvl() ?></div>
                <?php
            }
            else{
                ?>
                <div class="attaque" id="attaqueEntiteValeur<?= $this->_id ;?>"> <?= $this->getAttaque()?>
                    </div>
                
                <div id="ArmePerso<?= $this->_id ?>" class="Arme"></div>
                <?php
            }
            $armure = $this->getArmure();
            $bouclier = $this->getBouclier();
            if(!is_null($armure)){
                ?>
                <div id ="Armure<?= $armure->getId() ?>" class="ArmureNom standard" onclick="CallApiRemoveEquipementEntite(<?= $armure->getId() ?>)"><?= $armure->getNom() ?> lvl <?= $armure->getLvl() ?>
                    <div class="coef">(*<?php 
                        if(!is_null($type)){
                            echo $type->getCoefDefense();
                        }else{
                            echo "1";
                        }
                    ?>)
                    </div>
                </div>
                <?php
            }else if(!is_null($bouclier)){
                ?>
                <div id ="Armure<?= $bouclier->getId() ?>" class="ArmureNom magic" onclick="CallApiRemoveEquipementEntite(<?= $bouclier->getId() ?>)"><?= $bouclier->getNom() ?> lvl <?= $bouclier->getLvl() ?>
                    <div class="coef">(*<?php 
                        if(!is_null($type)){
                            echo $type->getCoefBouclier();
                        }else{
                            echo "1";
                        }
                    ?>)</div>
                </div>
                <?php
            }else{
                ?>
                    <div id ="ArmurePerso<?= $this->_id ?>" class="ArmureNom"></div>
                <?php
            }
            ?>
                <div class="barreDeVie" id="vieEntite<?= $this->_id ?>">
                    <div class="vie" id="vieEntiteValeur<?= $this->_id ?>" style="width:<?= $pourcentage ?>%;">♥️<?= $this->_vie ?>
                    </div>
                    <div class="armureAll">
                        <div class="armure" id="defenseEntiteValeur<?= $this->_id ?>"
                            <?php
                                if(!is_null($armure)){
                                    ?>
                                        style="width:<?= $this->getDefense() ?>%;"><?= $this->getDefense() ?>
                                    <?php
                                }else{
                                    ?>
                                        >
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div><?php 
                if ($this->_type == 1){
                    $type = $this->getTyePersonnage();
                    echo $type->getNom();
                }
                ?></div>
            <?php
        }

        public function getId(){
            return $this->_id;
        }

        public function getMap(){
            return $this->map;
        }

        public function lvlupAttaque($attaque){
            $this->_degat += $attaque;
            $sql = "UPDATE `Entite` SET `degat`='".$this->_degat."' WHERE `id`='".$this->_id."'";
            $this->_bdd->query($sql);
        }
        public function lvlupVie($viemore){
            $this->_vie += $viemore;
            $sql = "UPDATE `Entite` SET `vie`='".$this->_vie."' WHERE `id`='".$this->_id."'";
            $this->_bdd->query($sql);
        }
        public function lvlupVieMax($viemore){
            $this->_vieMax += $viemore;
            $sql = "UPDATE `Entite` SET `vieMax`='".$this->_vieMax."' WHERE `id`='".$this->_id."'";
            $this->_bdd->query($sql);
        }

        //permet de changer la position du joueur sur la carte
        public function changeMap($NewMap){
            $this->map = $NewMap;
            //on mémorise çà en base
            $sql = "UPDATE `Entite` SET `idMap`='".$NewMap->getId()."' WHERE `id`='".$this->_id."'";
            $this->_bdd->query($sql);
        }

        public function setEntiteById($id){
            $Result = $this->_bdd->query("SELECT * FROM `Entite` WHERE `id`='".$id."'");
            if($tab = $Result->fetch()){
                $this->setEntite($tab["id"],$tab["nom"],$tab["vie"],$tab["degat"],$tab["vieMax"],$tab["lienImage"],$tab["type"],$tab["lvl"]);
                //recherche de sa position
                $map = new map($this->_bdd);
                $map->setMapByID($tab["idMap"]);
                $this->map = $map;

                //select les equipements déjà présent
                $req = "SELECT idEquipement FROM `EntiteEquipement` WHERE idEntite='".$id."'";
                $Result = $this->_bdd->query($req);
                while($tab=$Result->fetch()){
                    array_push($this->sacEquipements,$tab[0]);
                }

                //select les Equipement déjà présent
                $req = "SELECT idEquipement,equipe FROM `EntiteEquipement` WHERE idEntite='".$id."' AND equipe='1'";
                $Result = $this->_bdd->query($req);
                while($tab=$Result->fetch()){
                    if($tab['equipe']==1){
                        array_push($this->sacEquipe,$tab['idEquipement']);
                    }
                }
            }
        }

        public function setEntiteByIdWithoutMap($id){
            $Result = $this->_bdd->query("SELECT * FROM `Entite` WHERE `id`='".$id."'");
            if($tab = $Result->fetch()){
                $this->setEntite($tab["id"],$tab["nom"],$tab["vie"],$tab["degat"],$tab["vieMax"],$tab["lienImage"],$tab["type"],$tab["lvl"]);
            }
        }

        //Retourne un formulaire HTML pourcreer un entite
        //et permet d'attribuer automatiquement à user
        // retour un objet entite
        public function CreateEntite($nom, $vie, $degat, $idMap,$vieMax,$lienImage,$idUser,$type,$lvl){
            $newperso = new Entite($this->_bdd);
            $this->_nom=htmlentities($nom, ENT_QUOTES);
            $this->_lvl = $lvl;
            $this->_imageLien=$lienImage;
            $req="INSERT INTO `Entite`(`nom`, `vie`, `degat`, `idMap`,`vieMax`,`lienImage`,`idUser`,`type`,`lvl`)
            VALUES ('".$this->_nom."','.$vie.','.$degat.','.$idMap.','.$vieMax.','".$this->_imageLien."','".$idUser."','.$type.','.$lvl.')";
            $this->_bdd->beginTransaction();
            $Result = $this->_bdd->query($req);
            $this->_id = $this->_bdd->lastInsertId();
            if($this->_id){
                $newperso->setEntiteById($this->_id);
                $this->_bdd->commit();
                return $newperso;
            }else{
                $this->_bdd->rollback();
                return null;
            }
            return null;
        }

        //Retourne une liste HTML de tous les entites
        //et permet d'attribuer un perso à un user
        // retour un objet entite
        public function getChoixEntite($idUser){
            if(isset($_POST["idEntite"])){
                $this->setEntiteById($_POST["idEntite"]);
                if($this->_vie==0){
                    $this->resurection();
                }
                //si le entite est mort on le place ne origine 0,0
            }
            $Result = $this->_bdd->query("SELECT * FROM `Entite` where idUser='".$idUser."'");
            ?>
                <form action="" method="post" onchange="this.submit()">
                    <select name="idEntite" id="idEntite">
                        <option value="">Choisir un entite</option>
                        <?php
                            while($tab=$Result->fetch()){
                                ($tab['id']==$this->_id)?$selected='selected':$selected='';
                                ?>
                                    <option value="<?= $tab["id"] ?>" <?= $selected ?>>
                                        <?= $tab["nom"] ?>
                                    </option>
                                <?php
                            }
                        ?>
                    </select>
                </form>
            <?php
            return $this;
        }

        public function generateImage($Nom){
            $space = array(" ", ".", "_", "-", "%");
            $onlyconsonants = str_replace($space, "+", $Nom);
            $topic='+personage+'.$onlyconsonants.'+fanart';
            $ofs=mt_rand(0, 100);
            $geturl='https://www.bing.com/images/search?q=' . $topic . '&first=' . $ofs . '&tsc=ImageHoverTitle';
            
            $data=file_get_contents($geturl);
            //echo $data;
            //partialString1 is bigger link.. in it will be a scr for the beginning of the url
            $f1='<div class="img_cont hoff">';
            $pos1=strpos($data, $f1)+strlen($f1);
            $partialString1 = substr($data, $pos1);

            $f1bis='src="';
            $pos1=strpos($partialString1, $f1bis)+strlen($f1bis);
            $partialString1 = substr($partialString1, $pos1);

            

            //PartialString3 ends the url when it sees the "&amp;"
            $f3='"';
            $urlLength=strpos($partialString1, $f3);
            $partialString3 = substr($partialString1, 0, $urlLength);

            
            return $partialString3;
        }
    }
?>