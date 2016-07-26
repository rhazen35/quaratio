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
            return( strnatcmp( $a['order']['order'], $b['order']['order'] ) );
        }

        public function extractAndOrderElements()
        {

            $modelData = ( new iOXMLEAModel\IOXMLEAModel( $this->xmlModel ) )->getModel();

            if( !empty( $modelData ) ):
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

            else:

                return( false );

            endif;

        }

        private function extractAndOrderOperations( $operations )
        {

            /**
             * Order the operations
             */
            $operationsArray = array();
            $totalOperations = count( $operations );

            for( $i = 0; $i < $totalOperations; $i++ ):

                $operationName          = $operations['operation'.($i+1)]['name'];
                $operationDocumentation = $operations['operation'.($i+1)]['documentation'];

                $operationsArray[$i]['name'] = $operationName;
                $operationsArray[$i]['documentation'] = $operationDocumentation;

            endfor;

            return( $operationsArray );

        }

        public function createIntro()
        {

            $class   = $this->xmlModel;
            $title   = ( isset( $class['name'] ) ? $class['name'] : "" );
            $intro   = ( isset( $class['documentation'] ) ? $class['documentation'] : "" );

            $element = '<div class="element">';

            /**
             * Order the operations
             */
            if( !empty( $class['operations'] ) ):

                $operations = $class['operations'];
                $orderedOperations = $this->extractAndOrderOperations( $operations );

                $element .= '<div class="elementIntro-title">'. $title .'</div>';
                $element .= '<div class="elementIntro-txt"><p>'. $intro .'<p></div>';

                foreach( $orderedOperations as $orderedOperation ):

                    $element .= '<div class="elementIntro-subTitle">'. $orderedOperation['name'] .'</div>';
                    $element .= '<div class="elementIntro-subIntro"><p>'. $orderedOperation['documentation'] .'</p></div>';

                endforeach;

            endif;

            $element .= '</div>';

            return( $element );

        }

        public function createElement()
        {

            $class   = $this->xmlModel;
            $title   = $class['name'];
            $documentation   = $class['documentation'];

            $element = '<div class="element">';

            $element .= '<div class="element-title">'. $title .'</div>';
            $element .= '<div class="element-documentation"><p>'. $documentation .'</p></div>';

            $element .= '</div>';

            return( $element );
        }

    }

endif;