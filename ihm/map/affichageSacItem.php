<div class="divSacItem">
    <p id='TitleSacocheItems'>Items</p>
    <ul id="SacItem" class="Sac">
        <?php
            $listItems = $Joueur1->getPersonnage()->getItems();
            if(count($listItems) > 0){
                foreach($listItems as $Item){
                    ?>
                        <li id="itemSac<?= $Item->getId() ?>">
                            <a onclick="useItem(<?= $Item->getId() ?>)">
                            <img class='imgItemSac' src='<?= $Item->getLienImage() ?>'/>
                            </a>
                        </li>
                    <?php
                }
            }
        ?>
    </ul>
</div>