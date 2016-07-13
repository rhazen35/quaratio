<?php

namespace application\model\data\create;

use \application\classes\database;

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 9-6-2016
 * Time: 15:37
 */

if(!class_exists( "DbCreate" )):

    class DbCreate
    {

        protected $sql;

        /**
         * DbCreate constructor.
         * @param $sql
         */

        public function __construct( $sql )
        {
            $this->sql = $sql;
        }

        /**
         * @param $data
         * @param $format
         */

        public function  dbInsert( $data, $format )

        {
            $mysqli     = ( new database\Database("") )->dbConnect();

            $stmt       = $mysqli->prepare( $this->sql );

            if( !empty( $format ) && !empty( $data ) ):

                $format = implode( '', $format );
                $format = str_replace( '%', '', $format );
                
                array_unshift( $data, $format );
                call_user_func_array( array( $stmt, 'bind_param' ), ( new database\Database("") )->referenceValues( $data ) );

            endif;

           $stmt->execute();
           $stmt->close();

           $mysqli->close();
        }
    }

endif;

?>