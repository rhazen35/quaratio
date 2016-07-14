<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 5-7-2016
 * Time: 23:45
 *
 * IOXMLParser reads a XML file and returns
 *
 */

namespace application\classes\iOXMLParser;

if( !empty( "IOXMLParser" ) ):

    class IOXMLParser
    {

        protected $xml;

        /**
         * IOXMLParser constructor.
         * @param $xml
         */

        public function __construct( $xml )
        {
            $this->xml = $xml;
        }

        /**
         * @return \SimpleXMLElement
         */

        public function fileToSimpleXmlObject()
        {
            // Load the xml file
            $xml = simplexml_load_file( $this->xml );

            return( $xml );

        }

        public function getNode( $path )
        {

            // Get the data with the specified path
            $data = $this->xml->xpath( $path );

            return( $data );

        }

        public function getNodeAttribute( $attribute )
        {
            if( isset( $this->xml[$attribute] ) ):
                return (string) $this->xml[$attribute];
            endif;
        }

    }

endif;