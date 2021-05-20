<?php
// Dev by rapidecho

class Personnage extends Entite{
    
    private $_xp;
    private $sacItems=array();
 

    public function __construct($bdd){

        Parent::__construct($bdd);
    }

    /** 
     * 
     * Cette fonction nous permet de modifier l'XP du personnage
     * 
     * Entries:
     * $valeurXp = valeur d'xp
     */
    public function setXp($valeurXp) {

        $req = $this->_bdd->prepare("UPDATE Personnage SET xp = ? WHERE id = ?");
        $req->execute(array($valeurXp, $this->_id));

        $this->_xp = $valeurXp;
    }

    /** 
     * 
     * Cette fonction nous permet de supprimer l'XP du personnage
     * 
     * Entries:
     * Aucune valeur d'entrée
     */
    public function deleteXp() {
        
        $req = $this->_bdd->prepare("DELETE FROM Personnage WHERE id = ?");
        $req->execute(array($this->_id));
    }

    /** 
     * 
     * Cette fonction nous permet d'insérer de l'XP
     * 
     * Entries:
     * $valeurXp = valeur d'xp
     */
    public function addXp($valeurXp) {

        $valeurXp = htmlspecialchars($valeurXp);

        if(!empty($valeurXp)) {
            $req = $this->_bdd->prepare("INSERT INTO Personnage SET id = ?, xp = ?");
            $req->execute(array($this->_id, $valeurXp));
        }
    }

    /** 
     * 
     * Cette fonction nous permet d'afficher l'XP
     * Entries:
     * Aucune valeur nécessaire
     */
    public function getXp() {

        $req = $this->_bdd->prepare("SELECT xp FROM Personnage WHERE id = ?");
        $req->execute(array($this->_id));

        $xp = $req->fetch();
        return $xp;
    }

  
    public function SubitDegatByPersonnage($Personnage){
        $degat = $Personnage->getAttaque();
        //on réduit les déga avec armure si possible
        $degat-=($degat*$this->getDefense())/100;
        $degat = round($degat);
        if($degat<0){
            $degat = 0;
        }

        $this->_vie = $this->_vie - $degat;
        if($this->_vie<0){
            $this->_vie =0;
            //retour en zone 0,0
        }
        $req  = "UPDATE `Entite` SET `vie`='".$this->_vie ."' WHERE `id` = '".$this->_id ."'";
        $Result = $this->_bdd->query($req);

       

        return $this->_vie;
    }

    //todo peut etre factoriser dans la class mère Entite
    public function SubitDegatByMob($Mob)
    {
        //Attente de pull qui marche
        //Si le mob attaquant a plus de O PV, il attaque
        if($Mob->getVie() > 0)
        {
            $MobDegatAttaqueEnvoyer=$Mob->getAttaque();

            //on réduit les déga avec armure si possible
            $MobDegatAttaqueEnvoyer-=($MobDegatAttaqueEnvoyer*$this->getDefense())/100;
            $MobDegatAttaqueEnvoyer = round($MobDegatAttaqueEnvoyer);
            if($MobDegatAttaqueEnvoyer<0){
                $MobDegatAttaqueEnvoyer = 0;
            }

            $vieAvantAttaque = $this->_vie;

            //on va rechercher l'historique
            $req  = "SELECT * FROM `AttaquePersoMob` where idMob = '".$Mob->getId()."' and idPersonnage = '".$this->_id."'" ;
            $Result = $this->_bdd->query($req);
            $tabAttaque['nbCoup']=0;
            $tabAttaque['DegatsDonnes']=$MobDegatAttaqueEnvoyer;
            if($tab=$Result->fetch()){
                $tabAttaque = $tab;
                $tabAttaque['DegatsDonnes']+=$MobDegatAttaqueEnvoyer;
                $tabAttaque['nbCoup']++;

            }else{
                //insertion d'une nouvelle attaque
                $req="INSERT INTO `AttaquePersoMob`(`idMob`, `idPersonnage`, `nbCoup`, `coupFatal`, `DegatsDonnes`, `DegatsReçus`) 
                VALUES (
                    '".$Mob->getId()."','".$this->_id."',0,0,".$tabAttaque['DegatsReçus'].",0
                )";
                $Result = $this->_bdd->query($req);
            }


            $this->_vie = $this->_vie - $MobDegatAttaqueEnvoyer;
            if($this->_vie<0){
                $this->_vie =0;
                //on ne peut pas donner plus de degat que la vie d'un perso
                $tabAttaque['DegatsDonnes'] = $vieAvantAttaque;
                //retour en zone 0,0
            }
            $req  = "UPDATE `Entite` SET `vie`='".$this->_vie ."' WHERE `id` = '".$this->_id ."'";
            $Result = $this->_bdd->query($req);

            //update AttaquePersoMob pour mettre a jour combien le perso a pris de degat 
            $req="UPDATE `AttaquePersoMob` SET 
            `DegatsDonnes`=".$tabAttaque['DegatsDonnes']."
            WHERE idMob = '".$Mob->getId()."' AND idPersonnage ='".$this->_id."' ";
            $Result = $this->_bdd->query($req);
        }

        return $this->_vie;
    }

    public function setPersonnage($xp){
        //un personnage n'a pas de propriete en plus pour le moment
        $this->_xp = $xp;
    }

    //retourne la nouvelle xp 

    //
    //public function addXP($value){
     //   $this->_xp += $value ;
     //   
      //  $req  = "UPDATE `Personnage` SET `xp`='".$this->_xp ."' WHERE `id` = '".$this->_id ."'";
     //   $Result = $this->_bdd->query($req);

        //passage des Lvl suis une loi de racine carre
        //* le double etole ** c'est elevé à la puissance */
     //   $lvl = ceil(($this->_xp/2000)**(0.7));

     //   if($lvl >$this->_lvl ){
     //       $this->_lvl = $lvl;
     //       $req  = "UPDATE `Entite` SET `lvl`='".$this->_lvl."' WHERE `id` = '".$this->_id ."'";
     //       $Result = $this->_bdd->query($req);
      // }

    //    return $this->_xp;
  //  }

    //met a jour la vie de depart et replace le joueur
    public function resurection(){
        $vieMax = round($this->_vieMax - (($this->_vieMax*10)/100));
        $attaque = round($this->_degat - (($this->_degat*15)/100));
        if($vieMax<10){$vieMax=100;}
        $req  = "UPDATE `Entite` SET `degat`='".$attaque."',`vieMax`='".$vieMax."',`vie`='".$vieMax."' WHERE `id` = '".$this->_id ."'";
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
        foreach ($this->getItems() as $value) {
            $valeur+=$value->getValeur();
        }
        foreach ($this->getEquipements() as $value) {
            $valeur+=$value->getValeur();
        }
        return  $valeur;
    }

    //retourne toute la mécanique d'affichage d'un Personnage
    public function renderHTML(){
       
        ?>
        <div class="perso">
            <div class="persoXP"><?php echo $this->_xp?>(xp)</div>
            <?php
                Parent::renderHTML();
            ?>
        </div>

        <?php
    }

    public function getItems(){
        $lists=array();
        foreach ($this->sacItems  as $ItemId) {
            $newItem = new Item($this->_bdd);
            $newItem->setItemByID($ItemId);
            array_push($lists,$newItem);
        }
        return $lists;
    }

    //permet d'attribuer une faction par defaut à un personnage
    public function ChangeFactionById($id){
        $Result = $this->_bdd->query("SELECT * FROM `TypePersonnage` WHERE idFaction = '".$id."'");
        if($tab = $Result->fetch()){
            $TypePersonnage = new TypePersonnage($this->_bdd);
            $TypePersonnage->setTypePersonnageById($tab['id']);
            $this->ChangeTypePersonnage($TypePersonnage);
        }
        
    }

    //permet d'attribuer une faction par defaut à un personnage
    public function ChangeTypePersonnage($TypePersonnage){
       $this->_idTypePersonnage = $TypePersonnage->getId();
       //le changement de Type Personnage ne se fait qu'a la création donc
       //on ne peut pas le save en base tant que le perso n'a pas été cree
    }

    //retourne un objet faction
    public function getFaction(){
        $req = "SELECT * FROM `TypePersonnage` WHERE id = '".$this->_idTypePersonnage."'";
        $Result = $this->_bdd->query($req);
        if($tab = $Result->fetch()){
            $req = "SELECT * FROM `Faction` WHERE id = '".$tab['idFaction']."'";
            $Result2 = $this->_bdd->query($req);
            if($tab2 = $Result2->fetch()){
                $faction = new Faction($this->_bdd);
                return $faction->setFactionById($tab2['id']);
            }
        }
    }

    //retour le type de personnage 
    //retour null si pas de type
    public function getTyePersonnage(){
        if(!is_null($_idTypePersonnage)){
            $TypePersonnage = new TypePersonnage($this->_bdd);
            $TypePersonnage->setTypePersonnageById($_idTypePersonnage);
            return $TypePersonnage;
        }else{
            //ne devrait jamais etre le cas
            return null;
        }
        
    }

   //Retourne un formulaire HTML pourcreer un personnage
    //et permet d'attribuer automatiquement à user
    // retour un objet personnage
    public function CreatNewPersonnage($idUser){
       
        ?>
        <div class = "formCreatio">
           
        <?php 
        //traitement du changement de faction
        if (isset($_POST["idFaction"])){
            $this->ChangeFactionById($_POST["idFaction"]);
            //on mémorise la faction en sessions pour le moment
            $_SESSION['Faction']=$_POST["idFaction"];
        }

       
        $User = new User($this->_bdd);
        $User->setUserById($idUser);
        $factionDuJoueur = $User->getFaction();

       
        //on va vérifier la faction du joueur
        //car un joueur ne peut être que dans une faction.
        

        
        if(is_null($factionDuJoueur)){
            echo "<p> tu Dois choisir  une Faction : </p>";
            $Result = $this->_bdd->query("SELECT * FROM `Faction`");
            ?>
            <form action="" method="post" onchange="this.submit()">
            <select name="idFaction" id="idFaction">
            <option value="">Choisir une Faction</option>
                <?php while($tab=$Result->fetch()){
                    $idFaction = 0;
                    if(isset($_SESSION['Faction'])){
                        $idFaction = $_SESSION['Faction'];
                    }
                    ($tab['id']==$idFaction)?$selected='selected':$selected='';
                    echo '<option value="'.$tab["id"].'" '.$selected.'> '.$tab["nom"].'</option>';
                  
                }
                ?>
            </select>
            </form>
        <?php
        }
        if(isset($_SESSION['Faction'])){
            $faction = new Faction($this->_bdd);
            $faction->setFactionById($_SESSION['Faction']);

            $factionDuJoueur  = $faction;
        } 

        if(!is_null($factionDuJoueur)){
            
            $TypePersos=  $factionDuJoueur->getAllTypePersonnage();
            $TypePerso = $TypePersos[rand(0,count($TypePersos)-1)];
            $imageUrl = $this->generateImage( $factionDuJoueur->getNom().'+'.$TypePerso->getNom()); 
            
            ?>
             <form action="" method="post" onclick="this.submit()">
                <img class="creationImage" src="<?php echo $imageUrl;?>" width="200px" >
            </form>

            <p> tu es dans la Faction : <?=$factionDuJoueur->getNom()?></p>
           

            <form action="" method="post" class="formCreationPersonnage">
                <div>Créez un personnage ou choisissez-en un :</div>
                <input type="text" name="NomPersonnage" required>
                <?php
                //affichage des type de personnage selon la faction
                if(!is_null($factionDuJoueur)){
                    $TypePersos=  $factionDuJoueur->getAllTypePersonnage();
                    ?>
                    <select name="idTypePerso" id="idTypePerso">
                        <?php
                        foreach ($TypePersos as $TypePerso) {
                            echo '<option value="'.$TypePerso->getID().'" '.$selected.'> '.$TypePerso->getNom().'</option>';
                        }
                        ?>
                    </select>
                    <input type="submit" value="Creer" name="createPerso">
                    <input type="hidden" name="image" value="<?php echo $imageUrl;?>">
                    <input type="hidden" name="idFaction" value="<?php echo $factionDuJoueur->getId();?>">
                <?php       
                }else{
                    ?>
                    <div class="ChoixTypePerso">Choisissez une Faction si vous souhaitez creer un perso</div>
                    <?php
                }            
                ?>
            </form>

            <?php
        }
       ?>
        </div><!--fin div formCreatio -->
        <?php
        if (isset($_POST["createPerso"]) && !is_null($factionDuJoueur)){

            $newperso = new Personnage($this->_bdd);
            $newperso = $newperso->CreateEntite($_POST['NomPersonnage'], 100, 10, 0,100,$_POST['image'],$idUser,1,1);
            $idTypePersonnage =$_POST['idTypePerso'];
            if($newperso->getId()){ 
                $req="INSERT INTO `Personnage`(`id`,`xp`,`idTypePersonnage`) VALUES ('".$newperso->getId()."','1','".$idTypePersonnage."')";
                $Result = $this->_bdd->query($req);
                $newperso->setEntiteById($newperso->getId());
                return $newperso;
            }else{
                $this->_bdd->rollback();
                return null;
            }
        }

        return null;
    }

    public function setPersonnageByIdWithoutMap($id){
        Parent::setEntiteByIdWithoutMap($id);
        $req  = "SELECT * FROM `Personnage` WHERE id='".$id."'";
        $Result = $this->_bdd->query($req);
        if($tab=$Result->fetch()){
            $this->_xp  = $tab['xp'];
            $this->_idTypePersonnage  = $tab['idTypePersonnage'];
        }else{
            return null;
        }
    }


    public function setPersonnageById($id){
        Parent::setEntiteById($id);

        //select les info personnage
        $req  = "SELECT * FROM `Personnage` WHERE id='".$id."'";
        $Result = $this->_bdd->query($req);
        if($tab=$Result->fetch()){
            $this->_xp  = $tab['xp'];
            $this->_idTypePersonnage  = $tab['idTypePersonnage'];
        }else{
            return null;
        }

        //select les items déjà présent
        $req  = "SELECT idItem FROM `PersoSacItems` WHERE idPersonnage='".$id."'";
        $Result = $this->_bdd->query($req);
        while($tab=$Result->fetch()){
            array_push($this->sacItems,$tab[0]);
        }
    }


    public function removeItemByID($id){
        unset($this->sacItems[array_search($id, $this->sacItems)]);
        $req="DELETE FROM `PersoSacItems` WHERE idPersonnage='".$this->getId()."' AND idItem='".$id."'";
        $this->_bdd->query($req);
        $req="DELETE FROM `Item` WHERE id='".$id."'";
        $this->_bdd->query($req);
    }

    
    
   
 

   
    //ajoute un lien entre item et la personnage en bdd 
    //et accroche l'item dans la collection itemID dans le sac du perso
    //au moment ou le personnage prend une Epee, cette derniere fusionne en lvl supérieur 
    //et détruit l'autre . Attention il taut le meme lvl , le meme nom , le meme type
    public function addItem($newItem){

        array_push($this->sacItems,$newItem->getId());
        $req="INSERT INTO `PersoSacItems`(`idPersonnage`, `idItem`) VALUES ('".$this->getId()."','".$newItem->getId()."')";
        $this->_bdd->query($req);
    }

    

    //Retourne une liste HTML de tous les personnages
    //et permet d'attribuer un perso à un user
    // retour un objet personnage
    public function getChoixPersonnage($User){
        if (isset($_POST["idPersonnage"])){
            $this->setPersonnageById($_POST["idPersonnage"]);
            $User->setPersonnage($this);
            if($this->_vie==0){
                $this->resurection();
            }
            //si le personnage est mort on le place ne origine 0,0
        }
        $Result = $this->_bdd->query("SELECT * FROM `Entite` where idUser='".$User->getId()."' AND type=1 ");
        ?>
        <form action="" method="post" onchange="this.submit()">
            <select name="idPersonnage" id="idPersonnage">
            <option value="">Choisir un personnage</option>
                <?php while($tab=$Result->fetch()){
                    ($tab['id']==$this->_id)?$selected='selected':$selected='';
                    echo '<option value="'.$tab["id"].'" '.$selected.'> '.$tab["nom"].'</option>';
                }
                ?>
            </select>
        </form>
        <?php
        return $this;
    }
    //affiche le nombre de personnage humain crée
    public function nbpersonnage(){
        $Result = $this->_bdd->query("SELECT COUNT(*) FROM `Entite` WHERE type=`1`");
        $nbperso = $Result->fetch();
        echo $nbperso;
    }
    //affiche le nom du perso et son lvl 
    public function lvlpersonnage(){
        $Result = $this->_bdd->query("SELECT * FROM `Entite` WHERE type=`1` ");
        $infoperso = $Result->fetch();
        $nom = $infoperso["nom"];
        $lvl = $infoperso["lvl"];
        return $lvl;
        return $nom;

    }

}
?>