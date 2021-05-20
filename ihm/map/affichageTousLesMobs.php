<div class="divAllMobs">
    <?php
        $listMob = $map->getAllMobs();
        if(count($listMob) > 0){
            $Mob = new Mob($mabase);
            // Affichage des Mob Enemis
            $mobContre = $map->getAllMobContre($Joueur1);
            if(count($mobContre) > 0){
                ?>
                    <div class='effect'></div>
                    <div class='divInfoMobs'>
                        <p class='pInfoMobs'>Tu es bloqué, il y a des monstres qui te bloquent le passage...</p>
                    </div>
                <?php
            }
            ?>
                <ul id="ulMob" class="ulMob">
                    <?php
                        foreach($mobContre as $MobID){
                            $Mob->setMobById($MobID);
                            ?>
                                <li id="Mob<?= $Mob->getId() ?>" class="liAdverse">
                                    <a onclick="AttaquerPerso(<?= $Mob->getId() ?>,1, event)">
                                        <?php
                                            $Mob->renderHTML();
                                        ?>
                                    </a>
                                </li>
                            <?php
                        }
                        // Affichage des Mob Capturés
                        foreach($map->getAllMobCapture($Joueur1) as $MobID){
                            $Mob->setMobById($MobID);
                            ?>
                                <li id="Mob<?= $Mob->getId() ?>" class="liCaptured">
                                    <a onclick="SoinMob(<?= $Mob->getId() ?>,1)">
                                        <?php
                                            $Mob->renderHTML();
                                        ?>
                                    </a>
                                </li>
                            <?php
                        }
                    ?>
                </ul>
            <?php
        }
        else{
            $ZoneMobEmpty++;
        }
    ?>
</div>