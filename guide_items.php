<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php
            $NameLocal = "Guide de items";
            include "ihm/fonction-web/header.php";
        ?>
        <!-- Style CSS + -->
            <link rel="stylesheet" href="css/credit.css">
    </head>
    <body class="bodyguideitems">
        <?php
            include "session.php";

            // Vérifie que la Session est Valide avec le bon Mot de Passe.
            if($access === true){
                $access = $Joueur1->DeconnectToi();
            }
            // Vérifie qu'il ne s'est pas déconnecté.
            if($access === true){
                include "ihm/fonction-web/menu.php";
                ?>
                    <h1>Guide d'item</h1>

                    <h1>Guide d'item</h1>

                    <h2>les differents items</h2>
                    <p>il y a plusieurs types d'items</p>
                    <p>les objets : </p>
                    <p>Pourris</p>
                    <p>Cassés</p>
                    <p>Nuls</p>
                    <p>Normaux</p>

                    
                <?php
            }else{
                echo $errorMessage;
            }
            include "ihm/fonction-web/footer.php";
        ?>
    </body>
</html>