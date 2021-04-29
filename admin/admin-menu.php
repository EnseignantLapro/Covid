<nav class="nav-Menu">
    <ul class="ul-Menu">                
        <li><a href="../index.php">Accueil</a></li>
        <li><a href="../reglement.php">Règlement</a></li>
        <li><a href="../combat.php">Combat</a></li>
        <li><a href="../map.php">Map</a></li>
        <li><a href="../classement.php">Classement</a></li>
        <?php
            if($Joueur1->isAdmin() == true){
                ?>
                    <li><a href="statistique.php">Statistique</a></li>
                    <li><a href="index.php">Administration</a>
                        <ul class="ul-SousMenu">
                            <li><a href="admin-map.php">Gestion Map</a></li>
                            <li><a href="admin-mobs.php">Gestion Monstre</a></li>
                            <li><a href="admin-map.php">Gestion Objet</a></li>
                            <li><a href="admin-map.php">Gestion Personnage</a></li>
                            <li><a href="admin-map.php">Gestion Utilisateur</a></li>
                        </ul>
                    </li>
                <?php
            }
        ?>
    </ul>
</nav>