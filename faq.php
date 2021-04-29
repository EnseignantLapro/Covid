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
            <link rel="stylesheet" href="css/faq.css">
            <script src="main.js"></script>
        <!-- Informations Générales -->
            <title>Projet Full Stack - FAQ</title>
            <meta name='description' content='Projet Full Stack - FAQ'>
            <meta name='author' content='La Providence - Amiens'>
            <link rel='shortcut icon' href='favicon.ico'>
        <!-- Intégration Facebook -->
            <meta property='og:title' content='Projet Full Stack - FAQ'>
            <meta property='og:description' content='Projet Full Stack - FAQ'>
            <meta property='og:image' content='favicon.ico'>
        <!-- Intégration Twitter -->
            <meta name='twitter:title' content='Projet Full Stack - FAQ'>
            <meta name='twitter:description' content='Projet Full Stack - FAQ'>
            <meta name='twitter:image' content='favicon.ico'>
    </head>
    <body class="bodyFAQ">
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
                    <div class="FAQ">
                        <h1>FAQ</h1>
                        <h3>Questions Fréquentes</h3>
                        <h4>Comment se déplacer sur la carte ?</h4>
                        <p>En cliquant.</p>
                        <h4>Comment utiliser un items ?</h4>
                        <p>En cliquant.</p>
                        <h4>Comment créer un nouveau personnage ?</h4>
                        <p>En cliquant.</p>
                    </div>
                <?php
            }else{
                echo $errorMessage;
            }
            include "ihm/fonction-web/footer.php";
        ?>
    </body>
</html>