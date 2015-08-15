<?php
    /*
    Récupération via FTP des denriers fichiers de flux INPI + Import dans base de données
    V1-0 (gère uniquement les noms et classes, et uniquement les nouvelles marques)
    Paramètres cron : 1 3 * * 6 php web/api/web/v1/cron/cron_ftp_inpi_import.php
        => tous les samedi matin à 03:01
    */
    date_default_timezone_set('GMT');
    require_once __DIR__.'/../config.php';

    require_once __DIR__.'/vendor/autoload.php';

    use Prewk\XmlStringStreamer;
    use Prewk\XmlStringStreamer\Parser;
    use Prewk\XmlStringStreamer\Stream;

    //Old PHP Style

    $link = new PDO("mysql:dbname=".$mysql_database.";host=".$mysql_host, $mysql_user, $mysql_pass);

    $time_file = mktime(0,0,0,date("m"),date('d'),date('Y'));

    $today_date = date('Ymd',$time_file);

    $xml_inpi_folder  = __DIR__.'/../../../../Ressources/INPI/XML/';

    //INPI FTP Connexion


    $local_zip_file = $xml_inpi_folder.$today_date.'.zip';

    $remote_zip_file = 'FR_FRST66_'.date('Y-W',$time_file).'.zip';

    $local_xml_filename = 'FR_FRNEWST66_'.date('Y-W',$time_file).'.xml';

    $conn_id = ftp_connect($ftp_inpi_server) or die('FTP DOWN');
    $login_result = ftp_login($conn_id, $ftp_inpi_user_name, $ftp_inpi_user_pass) or die('FTP LOGIN DOWN');
    ftp_pasv ( $conn_id , true );
    ftp_get($conn_id, $local_zip_file, $remote_zip_file, FTP_BINARY);
    ftp_close($conn_id);

    //Unzip it
    $zip = new ZipArchive();
    $zip->open($local_zip_file);
    $zip->extractTo($xml_inpi_folder,array($local_xml_filename));
    $zip->close();


    //listing des fichiers XML dispo
    $list_files = array($xml_inpi_folder.$local_xml_filename);

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

        $sql_ins = "INSERT
            INTO ImportFileLog
            (name, dt_import, ip_office)
            VALUES
            (?,?,?)";
            $stmt_ins = $link->prepare($sql_ins);
            $stmt_ins->bindValue(1, $xml_inpi_absolute_path);
            $stmt_ins->bindValue(2, date('Y-m-d H:i:s'));
            $stmt_ins->bindValue(3, 1);
            $stmt_ins->execute();

        echo ' [OK]'.PHP_EOL;

    }


    $link = null;
