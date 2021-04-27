<?php
    if(rand(0,1)==1){
        ?>
            <div class="letooltip">
                <?php
                    $tooltip = new Tooltip($mabase);
                    echo $tooltip->getTooltipAleatoire();
                ?>
            </div>
        <?php
    }
?>