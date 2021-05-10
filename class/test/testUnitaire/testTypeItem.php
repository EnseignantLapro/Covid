<?php
 echo '<div class="testUnitaire"><p>Test Type Item </p>';

    $Item = new Item($mabase);
    $Item = $Item->createAleatoire();


    echo 'Affichage de l arme ';
    $Item->setEquipementByID($Item->getId());
    echo "<p>le nom est : ".$Item->getNom()." ";
    echo " l'id est : ".$Item->getId()." ";
    echo " la valeur est : ".$Item->getValeur()." ";
    echo " le lvl  est : ".$Item->getLvl()." ";
    echo " l'efficacite est de : ".$Item->getEfficacite()." ";
    echo " la categorie est : ".var_dump($Item->getCategorie())." ";
    $type = $Item->getType();
    echo "  <p>ID de lArme ".$type['id']." / nom : ".$type['nom']." </p>" ;

 echo '</div>';

?>