<?php

namespace application\model\service;

use \application\core;
use application\classes\database;
use application\model\data\create as dbCreate;
use application\model\data\read as dbRead;
use application\model\data\update as dbUpdate;

(new core\Core( "class", "database", "database" ))->request();
(new core\Core( "model", "create", "create" ))->request();
(new core\Core( "model", "read", "read" ))->request();
(new core\Core( "model", "update", "update" ))->request();

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 9-6-2016
 * Time: 15:25
 */

if(!class_exists( "Service" )):

    class Service
    {
        protected $type;

        /**
         * Service constructor.
         * @param $type
         */
        
        public function __construct( $type )
        {
            $this->type = $type;
        }

        /**
         * @param $sql
         * @param $data
         * @param $format
         * @return bool|\mysqli_result
         */

        public function dbAction( $sql, $data, $format )
        {
            switch($this->type):

                case"create":
                    (new dbCreate\DbCreate( $sql ))->dbInsert( $data, $format );
                    break;
                case"read":
                    $returnData = (new dbRead\DbRead( $sql ))->dbSelect( $data, $format );
                    return($returnData);
                    break;
                case"update":
                    (new dbUpdate\DbUpdate( $sql ))->dbUpdate( $data, $format );
                    break;
                case"delete":

                    break;
                default:

                    break;
            endswitch;
        }

        /**
         * @return bool
         */

        public function checkDbServerConnection()
        {
            $mysqli     = (new database\Database(""))->checkDbConnection();

            if( $mysqli ):

                return( true );
            else:
                return( false );

            endif;
        }

        /**
         * @return bool
         */

        public function checkDbConnection()
        {
            $mysqli     = (new database\Database(""))->dbConnect();

            if( $mysqli ):

                return( true );
            else:
                return( false );

            endif;
        }
    }

endif;
