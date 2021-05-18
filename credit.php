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
            <link rel="stylesheet" href="css/credit.css">
            <script src="main.js"></script>
        <!-- Informations Générales -->
            <title>Projet Full Stack - Crédit</title>
            <meta name='description' content='Projet Full Stack - Crédit'>
            <meta name='author' content='La Providence - Amiens'>
            <link rel='shortcut icon' href='favicon.ico'>
        <!-- Intégration Facebook -->
            <meta property='og:title' content='Projet Full Stack - Crédit'>
            <meta property='og:description' content='Projet Full Stack - Crédit'>
            <meta property='og:image' content='favicon.ico'>
        <!-- Intégration Twitter -->
            <meta name='twitter:title' content='Projet Full Stack - Crédit'>
            <meta name='twitter:description' content='Projet Full Stack - Crédit'>
            <meta name='twitter:image' content='favicon.ico'>
    </head>
    <body class="bodyCredit">
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
                    <h2 class="h2SN1">Les Acteur du Projet</h2>
                    <div class="divSN1">
                        <h3>Les BTS SN1</h3>
                        <p>Léa Bernard</p>
                        <p>Thomas Berthier</p>
                        <p>Louis Boucher</p>
                        <p>Alexandre Caré</p>
                        <p>Jeremy Caruelle</p>
                        <p>Clément Caudron</p>
                        <p>Clément Cauet</p>
                        <p>Baptiste Colson</p>
                        <p>Nicolas De almeida</p>
                        <p>Mathis Dechir</p>
                        <p>Gaëtan Deneufgermain</p>
                        <p>Maël Drelon</p>
                        <p>Kylian Duval</p>
                        <p>Gregory Febvin</p>
                        <p>Lucas Ghyselen</p>
                        <p>Julien Laridant</p>
                        <p>Romain Lienard</p>
                        <p>Julien Lussiez</p>
                        <p>Nicolas Marrocchi</p>
                        <p>Vincent Martel</p>
                        <p>Yann Martin</p>
                        <p>Valentin Pesant</p>
                        <p>Florian Rabasté</p>
                        <p>Evans Varnier</p>
                        <p>Maxence Vollet</p>
                    </div>
                <?php
            }else{
                echo $errorMessage;
            }
            include "ihm/fonction-web/footer.php";
        ?>
    </body>
</html>