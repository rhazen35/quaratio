<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 01-Jul-16
 * Time: 12:45
 */

namespace api\classes\RequestHandler;

use application\classes\database;
use application\config;

require($_SERVER['DOCUMENT_ROOT']."/quaratio/application/config/config.php");
require($_SERVER['DOCUMENT_ROOT']."/quaratio/application/classes/database/Database.php");

if( !class_exists("RequestHandler") ):

    class requestHandler
    {
        protected $type;

        public function __construct( $type )
        {
            $this->type = $type;
        }


        public function handle( $dataArray )
        {
            switch( $this->type ):
                case"post":
                    $id = "";
                    $file = $dataArray['file'];
                    $excel = $dataArray['excel'];
                    $mysqli = ( new database\Database() )->dbConnect();
                    $stmt = $mysqli->prepare("INSERT INTO test VALUES(?,?,?)");
                    $stmt->bind_param("iss", $id, $file, $excel);
                    $stmt->execute();
                    $stmt->close();
                    $mysqli->close();
                    break;
            endswitch;
        }
    }

endif;

