<?php
class map{

    private $_id;
    private $_nom;
    private $_imageLien;

    //coordonne de la map
    private $_x;
    private $_y;
    private $_type;

    private $idUserDecouverte;
    Private $listItems=array();
    Private $listPersonnages=array();
    Private $listMobs=array();
    
    Private $_bdd;

    //la position initial est 0
    //les autres sont des hash
    private $_position=0;

    //lien vers les map adjacentes.
    private $mapNord=null;
    private $mapSud=null;
    private $mapEst=null;
    private $mapOuest=null;


    //calcule pitagorien pour avoir une distance au point d'origine
    //la distance determine le niveau
    public function getlvl(){
      $adjacent = $this->_x * $this->_x;
      $opose = $this->_y * $this->_y;
      $hypotenuse = sqrt ( $adjacent+$opose);
    
      return round($hypotenuse);
    }

    public function setMapByID($id){
        
        $req="SELECT * FROM map WHERE id='".$id."' ";

        
        $Result = $this->_bdd->query($req);
        if($tab = $Result->fetch()){ 

            $this->setMap($tab["id"],
                          $tab["nom"],
                          $tab["position"],
                          $tab["mapNord"],
                          $tab["mapSud"],
                          $tab["mapEst"],
                          $tab["mapOuest"],
                          $tab["x"],
                          $tab["y"],
                          $tab["idUserDecouverte"],
                          $tab["lienImage"],
                          $tab["type"]

                        );
        }
        
    }

    public function __construct($bdd){
        $this->_bdd = $bdd;
    }

    public function getImageCssBack(){
        ?>
        <!-- Map.php -- getImageCssBack-->
        <style type="text/css">
        body{

            background-size: cover;
            background-repeat: no-repeat;
            background-image: linear-gradient(rgba(255, 255, 255, 1), rgba(255,255,255, 0.01)), url(<?php echo $this->_imageLien?>);
            
        }
        </style>
        <?php
    }

    public function setMap($id,$nom,$position,$mapNord,$mapSud,$mapEst,$mapOuest,$x,$y,$idUserDecouverte,$image,$type){
        $this->_id = $id;
        $this->_nom = $nom;
        $this->_position = $position;
        $this->_x = $x;
        $this->_y = $y;
        $this->_imageLien = $image;
        $this->idUserDecouverte = $idUserDecouverte;
        //je place les id pour ne pas que l'objet racupère en récurciv toute les maps inclu dans elle meme
        (is_null($mapNord))?$this->mapNord = null:$this->mapNord = $mapNord;
        (is_null($mapSud))?$this->mapSud = null:$this->mapSud = $mapSud;
        (is_null($mapEst))?$this->mapEst = null:$this->mapEst = $mapEst;
        (is_null($mapOuest))?$this->mapOuest = null:$this->mapOuest = $mapOuest;

        //select les items déjà présent
        $this->listItems = array();
        $req  = "SELECT idItem FROM `MapItems` WHERE idMap='".$id."'";
        $Result = $this->_bdd->query($req);
        while($tab=$Result->fetch()){
            array_push($this->listItems,$tab[0]);
        }

        //select les Personnages déjà présent
        $this->listPersonnages = array();
        $req  = "SELECT id FROM `Personnage` WHERE idMap='".$id."'";
        $Result = $this->_bdd->query($req);
        while($tab=$Result->fetch()){
            array_push($this->listPersonnages,$tab[0]);
        }

         //select les Mob déjà présent
         $this->listMobs = array();
         $req  = "SELECT id FROM `Mob` WHERE idMap='".$id."'";
         $Result = $this->_bdd->query($req);
         while($tab=$Result->fetch()){
             array_push($this->listMobs,$tab[0]);
         }


        
        

    }

    //accesseur get set 
    public function getId(){
        return $this->_id;
    }
    public function getNom(){
        return $this->_nom;
    }

    public function getX(){
        return $this->_x;
    }
    public function getY(){
        return $this->_y;
    }

    public function getType(){
        return $this->_type;
    }

    //je vais chercher l'objet map au moment ou j'ai besoin
    //sinon les map vont automatiquement chercher leur map adjacente et çà va ramer 
    public function getPosition(){
        return $this->_position;
    }
    public function getMapNord(){
        if(is_null($this->mapNord)){return null; }
        $map = new Map($this->_bdd) ;
        $map->setMapByID($this->mapNord);
        
        return $map;
    }
    public function getMapSud(){
        if(is_null($this->mapSud)){return null;}
        $map = new Map($this->_bdd) ;
        $map->setMapByID($this->mapSud);
        return $map;
    }
    public function getMapEst(){
        if(is_null($this->mapEst)){return null;}
        $map = new Map($this->_bdd) ;
        $map->setMapByID($this->mapEst);
        return $map;
    }
    public function getMapOuest(){
        if(is_null($this->mapOuest)){return null;}
        $map = new Map($this->_bdd) ;
        $map->setMapByID($this->mapOuest);
        return $map;
    }
    public function getItems(){
        $lists=array();
        foreach ($this->listItems  as $ItemId) {
            $newItem = new Item($this->_bdd);
            $newItem->setItemByID($ItemId);
            array_push($lists,$newItem);
        }
        return $lists;
    }

    public function getAllPersonnages(){
        $lists=array();
        foreach ($this->listPersonnages  as $PersoID) {
            $personnage = new Personnage($this->_bdd);
            $personnage->setPersonnageById($PersoID);
            array_push($lists,$personnage);
        }
        return $lists;
    }

    public function getAllMobs(){
        $lists=array();
        foreach ($this->listMobs  as $MobID) {
            $mob = new Mob($this->_bdd);
            $mob->setMobByIdWithMap($MobID);
            array_push($lists,$mob);
        }
        return $lists;
    }



    

    public function getPersonnageDecouvreur(){
        $perso = new User($this->_bdd);
        $perso->setUserById($this->idUserDecouverte);
        return $perso;
    }

    public function setMapNord($NewMap){
        $this->mapNord = $NewMap->getId();
        //update en base
        $req="UPDATE `map` SET `mapNord`='".$NewMap->getId()."'  WHERE `id` = '$this->_id'";
        $Result = $this->_bdd->query($req);
        
    }
    public function setMapSud($NewMap){
        $this->mapSud = $NewMap->getId();
        //update en base
        $req="UPDATE `map` SET `mapSud`='".$NewMap->getId()."'  WHERE `id` = '$this->_id'";
        $Result = $this->_bdd->query($req);
        
    }
    public function setMapEst($NewMap){
        $this->mapEst = $NewMap->getId();
        //update en base
        $req="UPDATE `map` SET `mapEst`='".$NewMap->getId()."'  WHERE `id` = '$this->_id'";
        $Result = $this->_bdd->query($req);
        
    }
    public function setMapOuest($NewMap){
        $this->mapOuest = $NewMap->getId();
        //update en base
        $req="UPDATE `map` SET `mapOuest`='".$NewMap->getId()."'  WHERE `id` = '$this->_id'";
        $Result = $this->_bdd->query($req);
        
    }

    //ajoute un lien entre item et la map en bdd 
    //et accroche l'item dans la collection itemID dans la map
    public function addItem($newItem){
        array_push($this->listItems,$newItem->getId());
        $req="INSERT INTO `MapItems`(`idMap`, `idItem`) VALUES ('".$this->getId()."','".$newItem->getId()."')";
        $this->_bdd->query($req);
    }


    public function removeItemByID($id){
        unset($this->listItems[array_search($id, $this->listItems)]);
        $req="DELETE FROM `MapItems` WHERE idMap='".$this->getId()."' AND idItem='".$id."'";
        $this->_bdd->query($req);
    }


    //il faut lui donner la map adjacente
    //String cardinalite: lui dire si elle est par rapport à elle au sud , nord , est ou ouest ($cardinalite)
    //int id du user qui as decouvert cette map en premier
    public function Create($map,$cardinalite,$idUserDecouverte){

       
        if(intval($map->getId())>=0){
            $map->setMapByID($map->getId());
        }else{
            //la map n'existe pas
            return null;
        }

        $mapSud = 'NULL' ;
        $mapNord= 'NULL';
        $mapOuest = 'NULL';
        $mapEst = 'NULL';

        //IMPORTANT IL Faut vérifier que la zone qu'on découvre n'existe pas déjà par un autre chemin 
  
        $newx = $map->_x;
        $newy = $map->_y;

       

        switch ($cardinalite) {
            case "sud":
                $mapSud = "'".$map->getId()."'";
                //on vérifie si la map n'existe pas déjà a cette cardinalité
                
                if(!is_null($map->getMapNord())){
                    return $map->getMapNord();
                }
                $newy++;
                break;
            case "nord":
                $mapNord = "'".$map->getId()."'";
                if(!is_null($map->getMapSud())){
                    return $map->getMapSud();
                }
                
                $newy--;
                break;
            case "est":
                $mapEst = "'".$map->getId()."'";
                if(!is_null($map->getMapOuest())){
                    return $map->getMapOuest();
                }
                $newx--;
                break;
            case "ouest":
                $mapOuest = "'".$map->getId()."'";
                
                if(!is_null($map->getMapEst())){
                    return $map->getMapEst();
                }
                $newx++;
                break;
             default:
             
                return null;

        }

        $mapExistante = $map->trouveMapAdjacente($map,$cardinalite);
        if(is_object($mapExistante)){
        
            switch ($cardinalite) {
                case "nord":
                    $req="UPDATE `map` SET `mapSud`='".$mapExistante->getId()."' WHERE `id` = '".$map->getId()."'";
                    $map->setMapSud($mapExistante);

                    $req="UPDATE `map` SET `mapNord`='".$map->getId()."' WHERE `id` = '".$mapExistante->getId()."'";
                    $mapExistante->setMapNord($map);
                    
                    break;
                case "sud":
                    $req="UPDATE `map` SET `mapNord`='".$mapExistante->getId()."' WHERE `id` = '".$map->getId()."'";
                    $map->setMapNord($mapExistante);

                    $req="UPDATE `map` SET `mapSud`='".$map->getId()."' WHERE `id` = '".$mapExistante->getId()."'";
                    $mapExistante->setMapSud($map);
                    break;
                case "est":
                    $req="UPDATE `map` SET `mapOuest`='".$mapExistante->getId()."' WHERE `id` = '".$map->getId()."'";
                    $map->setMapOuest($mapExistante);

                    $req="UPDATE `map` SET `mapEst`='".$map->getId()."' WHERE `id` = '".$mapExistante->getId()."'";
                    $mapExistante->setMapEst($map);

                    break;
                case "ouest":
                    $req="UPDATE `map` SET `mapEst`='".$mapExistante->getId()."' WHERE `id` = '".$map->getId()."'";
                    $map->setMapEst($mapExistante);

                    $req="UPDATE `map` SET `mapOuest`='".$map->getId()."' WHERE `id` = '".$mapExistante->getId()."'";
                    $mapExistante->setMapOuest($map);


                    break;
            }
            $Result = $map->_bdd->query($req);
            return $mapExistante;
        }

        
        $position = $this->generatePosition();
        $Generate = $this->generateMap();
        $nom = $Generate[2];
        $typeId=$Generate[0];
        $type=$Generate[1];

        //insertion en base
        //la position doit etre unique
        $imgLink = $this->getAleatoireImage($type);
        
        $req="INSERT INTO `map`( `nom`, `position`, `mapNord`, `mapSud`, `mapEst`, `mapOuest`, `x`, `y`,`idUserDecouverte`,`lienImage`) 
                VALUES 
              ('".$nom."','".$position."',".$mapNord.",".$mapSud.",".$mapEst.",".$mapOuest.",".$newx.",".$newy.",".$idUserDecouverte.",'".$imgLink."')";
        
        $Result = $this->_bdd->query($req);

        $req="select id from map where position='".$position."'";
        $Result = $this->_bdd->query($req);
        if($tab = $Result->fetch()){ 
            $newmap = new map($this->_bdd);
            $newmap->setMapByID($tab["id"]);

            //on met à jour l'ancienne map avec les coordonnée de la nouvelle
            switch ($cardinalite) {
                case "sud":
                    $map->setMapNord($newmap);
                    break;
                case "nord":
                    $map->setMapSud($newmap);
                    break;
                case "ouest":
                    $map->setMapEst($newmap);
                    break;
                case "est":
                    $map->setMapOuest($newmap);
                    break;
            }

            //chargement d'un Item aléatoire
            if(rand(0,3)>1){
                $item1 = new Item($this->_bdd);
                $nbItem = rand(0,3);
                
                for($i=0;$i<$nbItem;$i++){
                    $newmap->addItem($item1->createItemAleatoire()); 
                }
            }

            //chargement d'un Mob aléatoire à la création
            if(rand(0,3)>1){
                $Mob1 = new Mob($this->_bdd);
                $nbMob = rand(0,2);
                for($i=0;$i<$nbMob;$i++){
                    //il faut passer la map($this) au créateur de mob
                    $Mob1 = $Mob1->CreateMobAleatoire($newmap);
                    if(!is_null($Mob1)){
                        array_push($newmap->listMobs,$Mob1->getId());
                    }
                    
                }
            }
            
                


            return $newmap;
        }
        return null;
    }

    //cardinalite = la d'ou l'on vient
    public function loadMap($position,$Cardinalite,$Joueur1){
        if(isset($position) && isset($Cardinalite) ){
            if($position==="Generate"){
                //la cardinalité permet de lui dire d'ou on vient
                $map= new map($this->_bdd);
                $map = $map->Create($Joueur1->getPersonnage()->getMap(),$_GET["cardinalite"],$Joueur1->getId());
                if(!is_null($map)){
                    echo "<p>tu viens de découvrir une nouvelle  position : ". $map->getNom()." </p>";
                    //puis on déplace le joueur
                    $Joueur1->getPersonnage()->ChangeMap($map);
                    return $map;
                    
                }else{
                    return $this;
                }
                

            }else if ($position>=0) {
                //récupération de la map est atttribution au combatant
                $this->setMapByPosition($position);
                echo "<p>tu es ici => <b>". $this->getNom()."</b> découvert par ".$this->getPersonnageDecouvreur()->getPrenom()." et ses Personnages</p>";
                $Joueur1->getPersonnage()->ChangeMap($this);
                
                 //chargement des Items
                if(rand(0,2)>1){
                    $itemEnplus = new Item($this->_bdd);
                    $nbItem = rand(0,2);
                    
                    for($i=0;$i<$nbItem;$i++){
                        $this->addItem($itemEnplus->createItemAleatoire()); 
                    }
                }

                
            }else{
                echo "Tu es en terre  Incconu revient vite là ou tu été";
            }

        }else{
            echo "Tu es en terre  Incconu revient vite là ou tu étais";
        }
        return $this;
       
      
        
    }
    
    

    //retourn un string 
    //hash aléatoire pour une nouvelle position
    public function generatePosition(){
        return hash('ripemd160', $this->_nom.rand(0,100000000).rand(0,100000000));
    }

    
    public function setMapByPosition($position){
        $Result = $this->_bdd->query("SELECT id FROM `map` WHERE `position`='".$position."' ");
        if($tab = $Result->fetch()){ 
            $this->setMapByID($tab["id"]);
        }
    }

    

    //retourne les liens HTML des 4 map adjacente
    public function getMapAdjacenteLienHTML(){
        ?>
        <div class="MapAdjacente">
        <?php 
    
            $Mnord = $this->getMapNord();
            $Msud = $this->getMapSud();
            $Mest = $this->getMapEst();
            $Mouest = $this->getMapOuest();
            echo '<div class="cardinalite">';
            echo '<div class="nord">';
            if(!is_null($Mnord)){
                ?>
                Nord : <div class="MapAdjacenteNord"><a href="map.php?position=<?php echo $Mnord->getPosition();?>&cardinalite=sud"><?php echo $Mnord->getNom()?></a></div>
                <?php
            }else{
                ?>
                Nord : <div class="MapAdjacenteNord"><a href="map.php?position=Generate&cardinalite=sud">Decouvre cette Région Inconnue</a></div>
                <?php
            }
            echo '</div>';
            echo '<div class="est">';
            if(!is_null($Mest)){
                ?>
                Est : <div class="MapAdjacenteEst"><a href="map.php?position=<?php echo $Mest->getPosition()?>&cardinalite=ouest"><?php echo $Mest->getNom()?></a></div>
                <?php
            }else{
                ?>
                Est : <div class="MapAdjacenteEst"><a href="map.php?position=Generate&cardinalite=ouest">Decouvre cette Région Inconnue</a></div>
                <?php
            }
            echo '</div>';
            echo '<div class="ouest">';
            if(!is_null($Mouest)){
                ?>
                Ouest : <div class="MapAdjacenteOuest"><a href="map.php?position=<?php echo $Mouest->getPosition()?>&cardinalite=est"><?php echo $Mouest->getNom()?></a></div>
                <?php
            }else{
                ?>
                Ouest : <div class="MapAdjacenteOuest"><a href="map.php?position=Generate&cardinalite=est">Decouvre cette Région Inconnue</a></div>
                <?php
            }
            echo '</div>';
            echo '<div class="sud">';
            if(!is_null($Msud)){
                ?>
                Sud : <div class="MapAdjacenteSud"><a href="map.php?position=<?php echo $Msud->getPosition();?>&cardinalite=nord"><?php echo $Msud->getNom()?></a></div>
                <?php
            }else{
                ?>
                Sud : <div class="MapAdjacenteSud"><a href="map.php?position=Generate&cardinalite=nord">Decouvre cette Région Inconnue</a></div>
                <?php
            }
            echo '</div></div>';
        
        ?>
       
        </div>

        <?php

    }

    //retourn un tableau d'objet de 4 map 
    public function getMapAdjacente(){  
        $tabMapAdjacent = array();
        array_push($tabMapAdjacent, $this->mapNord);
        array_push($tabMapAdjacent, $this->mapSud);
        array_push($tabMapAdjacent, $this->mapEst);
        array_push($tabMapAdjacent, $this->mapOuest);
    }


    //Permet de générer un nom de map
    public function generateMap(){
        $nom ="";
        $req="Select * from TypeMap";
        $Result = $this->_bdd->query($req);
        $typemap=array();
        while($tab=$Result->fetch()){
            array_push($typemap,$tab);
        }

        $choixAleatoire= array_rand($typemap, 1);
        
        $type =  $typemap[$choixAleatoire];
        $nom = $type['nomFr'];
        
        $Adjectif ="";
        switch (rand(0,10)){
            case 0:
                $Adjectif ="Poisseux";
            break;
            case 1:
                $Adjectif ="Luxuriant";
            break;
            case 2:
                $Adjectif ="Pas belle";
            break;
            case 3:
                $Adjectif ="Enchantée";
            break;
            case 4:
                $Adjectif ="de la mort";
            break;
            case 5:
                $Adjectif ="des nains";
            break;
            case 6:
                $Adjectif ="Du pauvre";
            break;
            case 7:
                $Adjectif ="des loups";
            break;
            case 8:
                $Adjectif ="Lumineux";
            break;
            case 9:
                $Adjectif ="Sombre";
            break;
            default:
                $Adjectif ="Noir";
        }

        $Consone ="";
        for($i=0;$i<=rand(1,3);$i++){
            switch (rand(0,19)){
                case 0:
                    $Consone .="bien";
                break;
                case 1:
                    $Consone .="dra";
                break;
                case 2:
                    $Consone .="bel";
                break;
                case 3:
                    $Consone .="con";
                break;
                case 4:
                    $Consone .="car";
                break;
                case 5:
                    $Consone .="den";
                break;
                case 6:
                    $Consone .="feu";
                break;
                case 7:
                    $Consone .="for";
                break;
                case 8:
                    $Consone .="ga";
                break;
                case 9:
                    $Consone .="lon";
                break;
                case 10:
                    $Consone .="la";
                break;
                case 11:
                    $Consone .="len";
                break;
                case 12:
                    $Consone .="mon";
                break;
                case 13:
                    $Consone .="ma";
                break;
                case 14:
                    $Consone .="ni";
                break;
                case 15:
                    $Consone .="nar";
                break;
                case 16:
                    $Consone .="pon";
                break;
                case 17:
                    $Consone .="pen";
                break;
                case 18:
                    $Consone .="ri";
                break;
                case 19:
                    $Consone .="or";
                break;
                default:
                $Consone .=" ";
            }
        }

        //la premiere case et le type en anglais pour une recherche d'image
        $tab[0]=$type['id'];
        $tab[1]=$type['nom'];
        $tab[2]=$nom ." ". $Adjectif." ".$Consone;
        return $tab;
    }


    //fonction de recherche récursive de map adjacent
    //retourne une map si elle se trouve 
    //
    public function trouveMapAdjacente($map,$cardinalite){

        $x=$map->getX();
        $y=$map->getY();

        switch ($cardinalite) {
            case "nord":
                $y--;
                break;
            case "sud":
                $y++;
                break;
            case "est":
                $x--;
                break;
            case "ouest":
                $x++;
                break;
        }

        $req="select id from map where x='".$x."' AND y='".$y."'";
        $Result = $this->_bdd->query($req);
        if($tab = $Result->fetch()){ 
            $newmap = new Map($this->_bdd);
            $newmap->setMapByID($tab['id']);
            return $newmap;
        }
       

        
        return null;
    }

    public function getAleatoireImage($typeName){
        $typeName2 = str_replace(' ',',',$typeName);
        $url = "https://source.unsplash.com/random/1280×720?".$typeName2;
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch); 
        $result = stristr($result, 'https://',false);
        $result = stristr($result, '"',true);
        return  $result;  
    }

}
