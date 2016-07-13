<?php

namespace application\model\data\update;

use \application\classes\database;

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 12-6-2016
 * Time: 20:51
 */

if(!class_exists( "DbUpdate" )):

    class DbUpdate
    {
        protected $sql;

        /**
         * DbUpdate constructor.
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

        public function  dbUpdate( $data, $format )

        {
            $mysqli     = (new database\Database(""))->dbConnect();
            $stmt       = $mysqli->prepare( $this->sql );

            if(!empty( $format ) && !empty( $data )):

                $format = implode( '', $format );
                $format = str_replace( '%', '', $format );

                array_unshift( $data, $format );
                call_user_func_array( array( $stmt, 'bind_param' ), (new database\Database(""))->referenceValues( $data ) );

            endif;

            $stmt->execute();
            $stmt->close();

            $mysqli->close();
        }
    }

endif;