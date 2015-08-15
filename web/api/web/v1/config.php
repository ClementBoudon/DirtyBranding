<?php
//Configuration spécifique Codeship / Vagrant de dev
$mysql_host = (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV']=='test') ? 'localhost' : ((isset($_ENV['APP_ENV']) && $_ENV['APP_ENV']=='dev') ? 'localhost' : 'localhost');
$mysql_user = isset($_ENV['MYSQL_USER']) ? $_ENV['MYSQL_USER'] : 'root';
$mysql_pass = isset($_ENV['MYSQL_PASSWORD']) ? $_ENV['MYSQL_PASSWORD'] : 'root';
$mysql_database = (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV']=='test') ? 'test' : 'DB_API';

$app['db.options'] = array(
    'driver' => 'pdo_mysql',
    'host' => $mysql_host,
    'dbhost' => $mysql_host,
    'dbname' => $mysql_database,
    'user' => $mysql_user,
    'password' => $mysql_pass
);

/**
 * ElasticSearch Stuff
 */
$elasticsearch_host = 'localhost';
$elasticsearch_port = 9200;

$app['es.options'] = array(
    'host' => $elasticsearch_host,
    'port' => $elasticsearch_port
);

$ftp_inpi_server = '';
$ftp_inpi_user_name = '';
$ftp_inpi_user_pass = '';
