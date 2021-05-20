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
            <link rel="stylesheet" href="css/classement.css">
            <script src="main.js"></script>
        <!-- Informations Générales -->
            <title>Projet Full Stack - Classement</title>
            <meta name='description' content='Projet Full Stack - Classement'>
            <meta name='author' content='La Providence - Amiens'>
            <link rel='shortcut icon' href='favicon.ico'>
        <!-- Intégration Facebook -->
            <meta property='og:title' content='Projet Full Stack - Classement'>
            <meta property='og:description' content='Projet Full Stack - Classement'>
            <meta property='og:image' content='favicon.ico'>
        <!-- Intégration Twitter -->
            <meta name='twitter:title' content='Projet Full Stack - Classement'>
            <meta name='twitter:description' content='Projet Full Stack - Classement'>
            <meta name='twitter:image' content='favicon.ico'>
    </head>
    <body class="bodyAccueil">
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
                    <div class="divClassement">
                        <h1>Classement</h1>
                        <table>
                            <tr>
                                <th>Pseudo<th>
                                <th>Monstre capturé</th>
                                <th>NFT</th>
                                <th>LV</th>
                                <th>XP</th>
                                <th>Dégats</th>
                                <th>Vie</th>
                            </tr>
                            <tr>
                                <td><?= 'Exemple' ?></td>
                                <td><?= 42 ?></td>
                                <td><?= 5 ?></td>
                                <td><?= 21 ?></td>
                                <td><?= 400 ?></td>
                                <td><?= 500 ?></td>
                                <td><?= 600 ?></td>
                            </tr>
                        </table>
                    </div>
                <?php
            }else{
                echo $errorMessage;
            }
            include "ihm/fonction-web/footer.php";
        ?>
    </body>
</html>