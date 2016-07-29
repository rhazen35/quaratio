<?php

namespace application\config;

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 9-6-2016
 * Time: 16:09
 */

function dbCredentials()
{
    $dbArray = array(
        "dbhost" => '127.0.0.1',
        "dbuser" => 'ruben35',
        "dbpass" => 'Ruben1986Hazenbosch35',
        "dbname" => 'litening'
    );

    return( $dbArray );
}

function allowedKeys()
{
    $allowedKeys = array(
        'global',
        'request',
        'login',
        'pagination',
        'systemLogs',
        'excel',
        'xmlModel',
        'project'
    );

    return( $allowedKeys );
}
?>