<?php
    $listPersos = $map->getAllPersonnages();
    if(count($listPersos)>1){
        ?>
            <div class="left">
                <p class='NoSolo'>Visiblement tu n'es pas seul ici il y a aussi :</p>
                <ul id="ulPersos" class="Persos">
                    <?php
                        $PersoJoeuur = $Joueur1->getPersonnage();
                        foreach($listPersos as  $Perso){
                            if($Perso->getId()!=$PersoJoeuur->getId()){
                                ?>
                                    <li id="Perso<?= $Perso->getId() ?>">
                                        <a onclick="AttaquerPerso(<?= $Perso->getId() ?>,0, event)">
                                            <?php $Perso->renderHTML() ?>
                                        </a>
                                    </li>
                                <?php
                            }
                        }
                    ?>
                </ul>
            </div>
        <?php
    }
?>