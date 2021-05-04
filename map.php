<?php //Cette Page HTML est modifié par : M. De Almeida
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
            <link rel="stylesheet" href="css/map.css">
            <link rel="stylesheet" href="css/perso.css">
            <link rel="stylesheet" href="css/item.css">
            <link rel="stylesheet" href="css/entite.css">
            <script src="main.js"></script>
        <!-- Informations Générales -->
            <title>Projet Full Stack - Map</title>
            <meta name='description' content='Projet Full Stack - Map'>
            <link rel='shortcut icon' href='favicon.ico'>
            <meta name='author' content='La Providence - Amiens'>
        <!-- Intégration Facebook -->
            <meta property='og:title' content='Projet Full Stack - Map'>
            <meta property='og:description' content='Projet Full Stack - Map'>
            <meta property='og:image' content='favicon.ico'>
        <!-- Intégration Twitter -->
            <meta name='twitter:title' content='Projet Full Stack - Map'>
            <meta name='twitter:description' content='Projet Full Stack - Map'>
            <meta name='twitter:image' content='favicon.ico'>
    </head>
    <body class="bodyMap">
        <?php
            include "session.php";

            // Vérifie que la Session est Valide avec le bon Mot de Passe.
            if($access === true){
                $access = $Joueur1->DeconnectToi();
            }
            // Vérifie qu'il ne s'est pas déconnecté.
            if($access === true){
                include "ihm/fonction-web/menu.php";
                //gestion accès map:
                $Personnage = $Joueur1->getPersonnage();
                if(is_null($Personnage->getId())){
                    ?>
                        <div class="bodyPage">
                            <p>Il faut créer un personnage d'abord.</p>
                            <p><a href="index.php">Retour à l'origine du tout</a></p>
                        </div>
                    <?php
                }else{
                    ?>
                        <div class="bodyPage">
                            <p><a href="index.php">Retour à l'origine du tout</a></p>
                            <?php
                                // Quand on ne génère pas de nouvelle position ou que aucune position
                                // n'est renseignée, on peut appeler un autre personnage.
                                if(!(isset($_GET["position"]) && $_GET["position"]==='Generate')){
                                    ?>
                                        <p>Tu peux appeler un autre personnage.</p>
                                    <?php
                                    $Personnage->getChoixPersonnage($Joueur1);
                                    $Joueur1->setPersonnage($Personnage);
                                }
                                // AFFICHAGE EN-TÊTE PERSONNAGE ET SAC
                                ?>
                                    <div class='entete'>
                                        <div class="avatar">
                                            <?php $Personnage->renderHTML() ?>
                                        </div>
                                        <div class="divSac">
                                            <p id='TitleSacoche'>Sacoche</p>
                                            <!-- Include Items / Equipement-->
                                                <?php
                                                    include "ihm/map/affichageSacItem.php";
                                                    include "ihm/map/affichageSacEquipement.php";
                                                ?>
                                        </div>
                                    </div>
                                <?php
                                // AFFICHAGE d'UN TOOLTIP
                                    include "ihm/map/affichageTooltip.php";
                                // CHARGEMENT  DE LA MAP
                                    include "ihm/map/chargementDeLaMap.php";
                                // HTML  DE LA MAP
                            ?>
                            <div class="lamap">
                                <?= $BousoleDeplacement['nord'] ?>
                                <div class="mapOuest">
                                    <?= $BousoleDeplacement['ouest'] ?>
                                    <div class="mapEst">
                                        <div class="mapCentre">
                                            <?php $Joueur1->getVisitesHTML(6) ?>
                                            <div class="infoMap">
                                                <?= $map->getInfoMap() ?>
                                            </div>
                                            <?php
                                                // AFFICHAGE SI FORGE
                                                    if($map->isForge()){
                                                        include "ihm/map/afficherForge.php.php";
                                                    }
                                                // AFFICHAGE AUTRES JOUEURS PRESENTS
                                                    include "ihm/map/affichageAutrePersos.php";
                                                // AFFICHAGE DES MONSTRES
                                                    include "ihm/map/affichageItemsMap.php";
                                                // AFFICHAGE DES ITEMS DE LA MAP
                                                    include "ihm/map/affichageTousLesMobs.php";
                                                // AFFICHAGE DES EQUIPEMENT DE LA MAP
                                                    include "ihm/map/affichageEquipementsMap.php";
                                            ?>
                                        </div>
                                        <?= $BousoleDeplacement['est'] ?>
                                    </div>
                                </div>
                                <?= $BousoleDeplacement['sud'] ?>
                            </div>
                            <?php $map->getImageCssBack() ?>
                            <div class="basdepage"></div>
                            <div class="divLog">
                                <ul id="log"></ul>
                            </div>
                        </div>
                    <?php
                }
            }else{
                echo $errorMessage;
            }
            include "ihm/fonction-web/footer.php";
        ?>
    </body>
    <?php
        include "ihm/jsDesPages/jsMap.php";
        include "ihm/jsDesPages/jsSac.php";
        include "ihm/jsDesPages/jsAnimation.php";
    ?>
    <script src="Javascript/map.js"></script>
</html>