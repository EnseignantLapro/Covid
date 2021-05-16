<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- Compatible / UTF / Viewport-->
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Style CSS / Script -->
            <link rel="stylesheet" href="css/style.css">
            <script src="main.js"></script>
        <!-- Informations Généraux-->
            <title>Projet Full Stack</title>
            <meta name='description' content='Projet Full Stack'>
            <meta name='author' content='La Providence - Amiens'>
            <link rel='shortcut icon' href='favicon.ico'>
        <!-- Intégration Facebook -->
            <meta property='og:title' content='Projet Full Stack'>
            <meta property='og:description' content='Projet Full Stack'>
            <meta property='og:image' content='favicon.ico'>
        <!-- Intégration Twitter -->
            <meta name='twitter:title' content='Projet Full Stack'>
            <meta name='twitter:description' content='Projet Full Stack'>
            <meta name='twitter:image' content='favicon.ico'>
    </head>
    <body class="bodyAccueil">
        <?php
            include "session.php";
            if($access === true){
                $access = $Joueur1->DeconnectToi();
            }
            if($access === true){
                include "ihm/fonction-web/menu.php";
                ?>
                    <div class="divReglement">
                        <div class="divWelcome">
                            <?php
                                if($Joueur1->isAdmin() === true){
                                    ?>
                                        <p>Bienvenue Administrateur <?= $Joueur1->getPrenom() ?>.</p>
                                        <p><a href='admin/'>Accéder au Panel Administrateur.</a></p>
                                    <?php
                                }
                                else{
                                    ?>
                                        <p>Bienvenue Joueur <?= $Joueur1->getPrenom() ?>.</p>
                                    <?php
                                }
                            ?>
                        </div>
                        <?php
                            $PersoChoisie = new Personnage($mabase);
                            $PersoChoisie = $Joueur1->getPersonnage();
                            if(!is_null($PersoChoisie)){
                                $PersoChoisie->getChoixPersonnage($Joueur1);
                            }
                            $PersoCree = new Personnage($mabase);
                            $PersoCree = $PersoCree->CreatNewPersonnage($Joueur1->getId());
                            if(!is_null($PersoCree)){
                                $PersoChoisie = $PersoCree;
                            }
                            if(!is_null($PersoChoisie)){
                                $Joueur1->setPersonnage($PersoChoisie);
                                ?>
                                    <div class="divAction">
                                        <?php

                                        if(!empty($PersoChoisie->getNom())){
                                            ?>
                                                <p><a href="combat.php">Viens combattre avec <?= $PersoChoisie->getNom() ?></a></p>
                                            <?php
                                        }else{
                                            ?>
                                                <p><a href="combat.php">Viens combattre avec <?= $Joueur1->getNomPersonnage() ?></a></p>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                <?php
                            }
                        ?>
                    </div>
                <?php
            }else{
                echo $errorMessage;
            }
            include "ihm/fonction-web/footer.php";
        ?>
    </body>
</html>
