<?php
namespace application\model\data\read;

use \application\classes\database;

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 09-Jun-16
 * Time: 22:37
 */

if(!class_exists( "DbRead" )):

    class DbRead
    {
        protected $sql;

        /**
         * DbRead constructor.
         * @param $sql
         */

        public function __construct( $sql )
        {
            $this->sql = $sql;
        }

        /**
         * @param $data
         * @param $format
         * @return bool|\mysqli_result
         */

        public function  dbSelect( $data, $format )
        {
            $mysqli     = (new database\Database(""))->dbConnect();
            $stmt       = $mysqli->prepare( $this->sql );

            if(!empty($format) && !empty($data)):

                $format = implode( '', $format );
                $format = str_replace( '%', '', $format );

                array_unshift( $data, $format );
                call_user_func_array( array( $stmt, 'bind_param' ), ( new database\Database(""))->referenceValues( $data ) );

            endif;

            if($stmt->execute()):

                $result  = $stmt->get_result();
                $results = array();
                
                while ($row = $result->fetch_array()):
                    $results[] = $row;
                endwhile;

                $stmt->close();
                $mysqli->close();

                return( $results );

            else:

                return( false );

            endif;

        }

    }

endif;