<div class="divSacEquipement">
    <p class="pTitleSacEquipement">Equipement</p>
    <ul id="SacEquipement" class="ulSac">
        <?php
            $listEquipements = $Joueur1->getPersonnage()->getEquipementNonPorte();
            if(count($listEquipements) > 0){
                foreach($listEquipements as $Equipement){
                    ?>
                        <li id="equipementSac<?= $Equipement->getId() ?>">
                            <a onclick="useEquipement(<?= $Equipement->getId() ?>)">
                                <img class='imgEquipementSac' src='<?= $Equipement->getLienImage() ?>'/>
                                <span class='spanEquipementSac'>
                                    <?= $Equipement->getNom() ?> lvl <?= $Equipement->getLvl() ?>
                                </span>
                            </a>
                        </li>
                    <?php
                }
            }
        ?>
    </ul>
</div>