<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 12-Jul-16
 * Time: 20:07
 */

namespace application\xmlController;

use \application\controller;
use application\classes\iOXMLParser;

// Request the xml parser
( new controller\Controller( "class", "iOXMLParser" ,"iOXMLParser" ) )->request();

if(!class_exists("XmlController")):

    class XmlController
    {

        protected $model;

        public function __construct( $model, $type, $path )
        {
            $this->model = $model;
            $this->type  = $type;
            $this->path  = $path;
        }

        public function request()
        {

            switch( $this->type ):

                case"fileToSimpleXmlObject":
                    return( ( new iOXMLParser\iOXMLParser( $this->model ) )->fileToSimpleXmlObject() );
                    break;
                case"getNode":
                    return( ( new iOXMLParser\iOXMLParser( $this->model ) )->getNode( $this->path ) );
                    break;
                case"getNodeAttribute":
                    return( ( new iOXMLParser\iOXMLParser( $this->model ) )->getNodeAttribute( $this->path ) );
                    break;
                default:
                    break;

            endswitch;

        }
    }

endif;