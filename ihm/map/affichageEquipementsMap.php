<div class="MapEquipments">
    <?php
        $listEquipements = $map->getEquipements();
        if(count($listEquipements) > 0){
            ?>
                <p class="pEquipement">Équipements Présent :</p>
                <p class="divRarete">Commun - Rare</p>
                <ul class="Equipement">
                    <?php
                        foreach($listEquipements as $Equipement){
                            ?>
                                <li id="equipement<?= $Equipement->getId() ?>" style="<?= $Equipement->getClassRarete() ?>">
                                    <a onclick="CallApiAddEquipementInSac(<?= $Equipement->getId() ?>)">
                                        <?= $Equipement->getNom() ?>
                                    </a>
                                </li>
                            <?php
                        }
                    ?>
                </ul>
            <?php
        }
    ?>
</div>