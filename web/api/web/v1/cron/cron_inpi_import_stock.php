<?php
    /*
    Import des fichiers stock et flux dans base de données
    V1-0 (gère uniquement les noms et classes, et uniquement les nouvelles marques, par leur modifs ou suppression)
    Paramètres cron : Exécution unique php web/api/web/v1/cron/cron_inpi_import_stock.php
    */

    /*
    Note Interne : méthode d'export de la BDD :
    mysqldump -u root -proot DB_API BrandsINPI --no-create-info --where="id >=0 AND id <500000" > dump-1.sql
    mysqldump -u root -proot DB_API BrandsINPI --no-create-info --where="id >=500000 AND id <1000000" > dump-2.sql
    mysqldump -u root -proot DB_API BrandsINPI --no-create-info --where="id >=1000000 AND id <1500000" > dump-3.sql
    mysqldump -u root -proot DB_API BrandsINPI --no-create-info --where="id >=1500000 AND id <2000000" > dump-4.sql
    mysqldump -u root -proot DB_API BrandsINPI --no-create-info --where="id >=2000000 AND id <2500000" > dump-5.sql
    mysqldump -u root -proot DB_API BrandsINPI --no-create-info --where="id >=2500000 AND id <3000000" > dump-6.sql

    mysqldump -u root -proot DB_API BrandsClassesINPI --no-create-info --where="id_brand >=0 AND id_brand <500000" > dump-1-C.sql
    mysqldump -u root -proot DB_API BrandsClassesINPI --no-create-info --where="id_brand >=500000 AND id_brand <1000000" > dump-2-C.sql
    mysqldump -u root -proot DB_API BrandsClassesINPI --no-create-info --where="id_brand >=1000000 AND id_brand <1500000" > dump-3-C.sql
    mysqldump -u root -proot DB_API BrandsClassesINPI --no-create-info --where="id_brand >=1500000 AND id_brand <2000000" > dump-4-C.sql
    mysqldump -u root -proot DB_API BrandsClassesINPI --no-create-info --where="id_brand >=2000000 AND id_brand <2500000" > dump-5-C.sql
    mysqldump -u root -proot DB_API BrandsClassesINPI --no-create-info --where="id_brand >=2500000 AND id_brand <3000000" > dump-6-C.sql

    gzip dump-*.sql
    */
    date_default_timezone_set('GMT');
    require_once __DIR__.'/../config.php';

    require_once __DIR__.'/vendor/autoload.php';

    use Prewk\XmlStringStreamer;
    use Prewk\XmlStringStreamer\Parser;
    use Prewk\XmlStringStreamer\Stream;

    //Old PHP Style

    $link = new PDO("mysql:dbname=".$mysql_database.";host=".$mysql_host, $mysql_user, $mysql_pass);

    $xml_inpi_folder  = __DIR__.'/../../../../Ressources/INPI/XML/';

    //listing des fichiers XML dispo
    $list_files = glob($xml_inpi_folder.'*.xml');

    //$pattern = "~^(".$xml_inpi_folder."FR_FRNEWST66_|".$xml_inpi_folder."ST66_)*.xml$~";

    //on parcours chacun des fichiers
    foreach ($list_files as $xml_inpi_absolute_path) {

        echo 'Traitement : '.$xml_inpi_absolute_path.' ...';


        $streamer = Prewk\XmlStringStreamer::createUniqueNodeParser($xml_inpi_absolute_path, array("uniqueNode" => "TradeMark"));


        while ($node = $streamer->getNode()) {

            $TradeMark = simplexml_load_string($node);

            $brand_name = utf8_decode($TradeMark->WordMarkSpecification->MarkVerbalElementText);
            $sql_ins = "INSERT
            INTO BrandsINPI
            (name, dt_caching)
            VALUES
            (?,?)";
            $stmt_ins = $link->prepare($sql_ins);
            $stmt_ins->bindValue(1, $brand_name);
            $stmt_ins->bindValue(2, date('Y-m-d H:i:s'));
            $stmt_ins->execute();

            $id_brand = $link->lastInsertId();

            $ClassDescriptionDetails = $TradeMark->GoodsServicesDetails->GoodsServices->ClassDescriptionDetails->ClassDescription;
            $array_class = array();
            foreach ($ClassDescriptionDetails as $xml_class) {
                $sql_ins = "INSERT
                INTO BrandsClassesINPI
                (id_brand, classe)
                VALUES
                (?,?)";
                $stmt_ins = $link->prepare($sql_ins);
                $stmt_ins->bindValue(1, $id_brand);
                $stmt_ins->bindValue(2, $xml_class->ClassNumber);
                $stmt_ins->execute();
            }
        }

        echo ' [OK]'.PHP_EOL;

    }





    $link = null;
