<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 25-Jul-16
 * Time: 14:25
 */

namespace application\classes\iOXMLEAModel;

use \application\model\service;

if( !class_exists( "IOXMLEAModel" ) ):

    class IOXMLEAModel
    {
        protected $modelId;

        public function __construct( $modelId )
        {
            $this->modelId = $modelId;
        }

        public function getModel()
        {

            $sql        = "CALL proc_getModel(?)";
            $data       = array("id" => $this->modelId);
            $format     = array('i');

            $type       = "read";
            $database   = "quaratio";

            $returnData = ( new service\Service( $type, $database ) )->dbAction( $sql, $data, $format );

            if( !empty( $returnData ) ):

                foreach( $returnData as $data ):

                    $returnArray = array( 'user_id' => $data['user_id'],
                                          'hash' => $data['hash'],
                                          'date' => $data['date'],
                                          'time' => $data['time']
                                        );

                endforeach;

                return( $returnArray );

            else:

                return( false );

            endif;

        }

        public function getModelIdByHash()
        {

            $sql        = "CALL proc_getModelIdByHash(?)";
            $data       = array("hash" => $this->modelId);
            $format     = array('s');

            $type       = "read";
            $database   = "quaratio";

            $returnData = ( new service\Service( $type, $database ) )->dbAction( $sql, $data, $format );

            if( !empty( $returnData ) ):

                return( $returnData );

            else:

                return( false );

            endif;

        }


    }

endif;