<div class="divSacEquipement">
    <p id='TitleSacocheEquipement'>Equipement</p>
    <ul id="SacEquipement" class="Sac">
        <?php
            $listEquipements = $Joueur1->getPersonnage()->getEquipementNonPorte();
            if(count($listEquipements) > 0){
                foreach($listEquipements as $Equipement){
                    ?>
                        <li id="equipementSac<?= $Equipement->getId() ?>">
                            <a onclick="useEquipement(<?= $Equipement->getId() ?>)">
                                <p><?= $Equipement->getNom() ?> lvl <?= $Equipement->getLvl() ?></p>
                            </a>
                        </li>
                    <?php
                }
            }
        ?>
    </ul>
</div>