<?php
    $time_start = microtime(true);
    set_time_limit(0);
    /*
    Import des données INPI de base de données dans Elasticsearch
    V1-0 (gère uniquement les noms et classes, et uniquement les nouvelles marques, par leur modifs ou suppression)
    Paramètres cron : Exécution unique php web/api/web/v1/cron/cron_inpi_import_elasticsearch.php
    */

    date_default_timezone_set('GMT');
    require_once __DIR__.'/../config.php';

    require_once __DIR__.'/vendor/autoload.php';


    //Old PHP Style

    //SQL
    $link = new PDO("mysql:dbname=".$mysql_database.";host=".$mysql_host, $mysql_user, $mysql_pass);

    //ElasticSearch Client
    $elasticaClient = new \Elastica\Client(array(
        'host' => $elasticsearch_host,
        'port' => $elasticsearch_port
    ));

    $elasticsearch_index = 'inpi_temp';
    $elasticsearch_type = 'brands';

    $index = $elasticaClient->getIndex($elasticsearch_index);
    $index->create(array(), true);
    $type = $index->getType($elasticsearch_type);

    echo '[Fetching data]'.PHP_EOL;

    $documents = array();


    $sql = "SELECT *
    FROM BrandsINPI
    WHERE 1
    LIMIT 0,100";
    $stmt = $link->prepare($sql);
    $stmt->execute();
    while($row = $stmt->fetch())
    {
        echo '[Adding '.$row['name'].' to ES] ... ';

        $documents[] = new \Elastica\Document($row['id'], array('name' => $row['name']));

        echo '[OK]';

        echo PHP_EOL;

    }

    $type->addDocuments($documents);
    $index->refresh();

    echo '[OK]'.PHP_EOL;


    echo '------------------------------------------------------------'.PHP_EOL;
    echo '------------------------------------------------------------'.PHP_EOL;
    echo '[Checking ES] :'.PHP_EOL;



    // $search = new \Elastica\Search($elasticaClient);

    // $search
    //     ->addIndex($elasticsearch_index)
    //     ->addType($elasticsearch_type);

    // $query = new \Elastica\Query();

    // $query->setQuery(new \Elastica\Query\MatchAll());

    // $search->setQuery($query);

    // $resultSet = $search->search();

    $resultSet = $elasticaClient->getIndex($elasticsearch_index)
        ->getType($elasticsearch_type)
        ->search("facilities");

    print_r($resultSet);

    echo '[Checking OK]'.PHP_EOL;


    $link = null;

    $time_end = microtime(true);
    $time = $time_end - $time_start;

    echo '------------------------------------------------------------'.PHP_EOL;
    echo 'Exec : '.$time.' sec'.PHP_EOL;
    echo '------------------------------------------------------------'.PHP_EOL;
