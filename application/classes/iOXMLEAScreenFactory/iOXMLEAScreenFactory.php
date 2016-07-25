<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 25-Jul-16
 * Time: 21:09
 */

namespace application\classes\iOXMLEAScreenFactory;

use \application\controller;
use application\classes\iOXMLEAModel;
use application\classes\iOXMLModelParser;

( new controller\Controller( "class", "iOXMLEAModel", "iOXMLEAModel" ) )->request();
( new controller\Controller( "class", "iOXMLModelParser", "iOXMLModelParser" ) )->request();

if( !class_exists( "IOXMLEAScreenFactory" ) ):

    class IOXMLEAScreenFactory
    {

        protected $xmlModelId;

        public function __construct( $xmlModel )
        {
            $this->xmlModel = $xmlModel;
        }

        private function extractElementNames( $parsedElements )
        {
            $elementNames = array();

            foreach( $parsedElements as $parsedElement ):

                /**
                 * Get all classes names
                 */
                if( !empty( $parsedElement['name'] ) ):
                    $elementNames[] = $parsedElement['name'];
                endif;

            endforeach;

            return($elementNames);
        }

        private function sortElements( $a, $b )
        {
            return( strnatcmp( $a['order']['cell'], $b['order']['cell'] ) );
        }

        public function extractAndOrderElements()
        {

            $modelData       = ( new iOXMLEAModel\IOXMLEAModel( $this->xmlModel ) )->getModel();
            $parsedElements  = ( new iOXMLModelParser\IOXMLModelParser( './files/'.$modelData['hash'].'.xml' ) )->parseXMLClasses();

            $elementNames = $this->extractElementNames( $parsedElements );
            $orderedElements = array();

            $i = 0;
            foreach( $elementNames as $elementName ):

                $class  = ( isset( $parsedElements[$elementName] ) && $parsedElements[$elementName]['type'] === "uml:Class" ? $parsedElements[$elementName] : "" );
                $fields = ( isset( $class['fields'] ) ? $class['fields'] : false );
                $root   = ( isset( $class['Root'] ) ? $class['Root'] : false );
                $name   = ( isset( $class['name'] ) ? $class['name'] : "" );
                $tags   = ( isset( $class['tags'] ) ? $class['tags'] : false );
                $order  = ( isset( $tags['QR-Volgorde'] ) ? $tags['QR-Volgorde'] : "");
                $documentation = ( isset( $class['documentation'] ) ? $class['documentation'] : "" );
                $idref = ( isset( $class['idref'] ) ? $class['idref'] : "" );
                $operations = ( isset( $class['operations'] ) ? $class['operations'] : "" );

                if( !empty( $order ) ):
                    $orderedElements[$i]['name'] = $name;
                    $orderedElements[$i]['idref'] = $idref;
                    $orderedElements[$i]['order'] = $order;
                    $orderedElements[$i]['root'] = $root;
                    $orderedElements[$i]['documentation'] = $documentation;
                    $orderedElements[$i]['fields'] = $fields;
                    $orderedElements[$i]['operations'] = $operations;
                endif;

                $i++;
            endforeach;

            usort( $orderedElements, array( $this,'sortElements' ) );

            return( $orderedElements );

        }

    }

endif;