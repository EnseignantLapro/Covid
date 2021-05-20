<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php
            $NameLocal = "Guide de equipement";
            include "ihm/fonction-web/header.php";
        ?>
        <!-- Style CSS + -->
            <link rel="stylesheet" href="css/credit.css">
    </head>
    <body class="bodyguideequipement">
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
                    <h1>Guide d'équipement</h1>

                    <h2>les differents équipement</h2>
                    <p>il y a plusieurs type d'équipement</p>
                    <p>les objet : </p>
                    <p>en poussiere</p>
                    <p>Tout Moue</p>
                    <p>Cassé</p>
                    <p>Normal</p>
                    <p>Neuf</p>

                    
                <?php
            }else{
                echo $errorMessage;
            }
            include "ihm/fonction-web/footer.php";
        ?>
    </body>
</html>