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
            <link rel="stylesheet" href="../css/admin.css">
            <link rel="stylesheet" href="../css/style.css">
            <link rel="stylesheet" href="../css/index.css">
            <script src="main.js"></script>
        <!-- Informations Générales -->
            <title>Projet Full Stack - Statistique</title>
            <meta name='description' content='Projet Full Stack - Panel Admin'>
            <meta name='author' content='La Providence - Amiens'>
            <link rel='shortcut icon' href='favicon.ico'>
        <!-- Intégration Facebook -->
            <meta property='og:title' content='Projet Full Stack - Panel Admin'>
            <meta property='og:description' content='Projet Full Stack - Panel Admin'>
            <meta property='og:image' content='favicon.ico'>
        <!-- Intégration Twitter -->
            <meta name='twitter:title' content='Projet Full Stack - Panel Admin'>
            <meta name='twitter:description' content='Projet Full Stack - Panel Admin'>
            <meta name='twitter:image' content='favicon.ico'>
    </head>
    <body class="admin-panel">
        <?php
            include "../session.php";

            // Vérifie que la Session est Valide avec le bon Mot de Passe.
            if($access === true){
                $access = $Joueur1->DeconnectToi();
            }
            // Vérifie qu'il ne s'est pas déconnecté.
            if($access === true){
                if($Joueur1->isAdmin() == true){
                    ?>
                        <div class='Div1 BG_Blanc'>
                            <h1 class='TITRE'>Statistique du Jeu</h2>
                        </div>
                        <div class='Div1 BG_Cyan'>
                            <h3 class='TC'>Statistiques Utilisateurs :</h3>
                            <p class='TC'>Texte.</p>
                        </div>
                        <div class='Div1 BG_Rouge'>
                            <h3 class='TC'>Statistiques Personnage :</h3>
                            <p class='TC'>Texte.</p>
                        </div>
                        <div class='Div1 BG_Bleu'>
                            <h3 class='TC'>Statistiques Monstre :</h3>
                            <p class='TC'>Texte.</p>
                        </div>
                        <div class='Div1 BG_Jaune'>
                            <h3 class='TC'>Statistiques Map :</h3>
                            <p class='TC'>Texte.</p>
                        </div>
                        <div class='Div1 BG_Vert'>
                            <h3 class='TC'>Statistiques d'Objet :</h3>
                            <p class='TC'>Texte.</p>
                        </div>
                    <?php
                }else{
                    include "non_acces.php";
                }
            }else{
                echo $errorMessage;
            }
        ?>
    </body>
</html>