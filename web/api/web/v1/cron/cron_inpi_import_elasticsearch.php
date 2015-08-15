<?php
    /*
    Import des données INPI de base de données dans Elasticsearch
    V1-0 (gère uniquement les noms et classes, et uniquement les nouvelles marques, par leur modifs ou suppression)
    Paramètres cron : Exécution unique php web/api/web/v1/cron/cron_inpi_import_elasticsearch.php
    */

    date_default_timezone_set('GMT');
    require_once __DIR__.'/../config.php';

    require_once __DIR__.'/vendor/autoload.php';


    //Old PHP Style

    $link = new PDO("mysql:dbname=".$mysql_database.";host=".$mysql_host, $mysql_user, $mysql_pass);


    $sql = "SELECT *
    FROM BrandsINPI
    WHERE 1
    LIMIT 0,100";
    $stmt = $link->prepare($sql);
    $stmt->execute();
    while($row = $stmt->fetch())
    {
        echo '[Fetching '.$row['name'].'...]'.PHP_EOL;
    }

    echo '[OK]'.PHP_EOL;






    $link = null;
