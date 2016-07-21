<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 21-Jul-16
 * Time: 23:14
 */

namespace application\classes\iOXMLPrimitiveTypes;

if( !class_exists( "IOXMLPrimitiveTypes" ) ):

    class IOXMLPrimitiveTypes
    {

        protected $dataType;
        protected $value;

        public function __construct( $dataType, $value )
        {

            switch( $this->dataType ):

                case"String":
                    return(  is_string( $value ) );
                    break;

            endswitch;

        }

    }

endif;