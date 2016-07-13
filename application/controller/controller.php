<?php

namespace application\controller;

use \application\config;
use application\classes\keyValidator;

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 10-6-2016
 * Time: 02:17
 */

if(!class_exists( "Controller" )):

    class Controller
    {
        
        protected $type;
        protected $attribute;
        protected $directory;

        /**
         * Controller constructor.
         * @param $type
         * @param $attribute
         * @param $directory
         */

        public function __construct( $type, $attribute, $directory )
        {
            $this->type = $type;
            $this->attribute = $attribute;
            $this->directory = $directory;
        }

        /**
         * @return bool
         */

        public function request()
        {

            if ( !in_array( $this->type, ['template', 'handler', 'class'] ) ):

                echo 'Error: An invalid $type was passed to Controller::request()! Instantiation aborted!';
                return( false );

            else:

                switch( $this->type ):
                    case"template":
                        $this->templateLoader();
                        break;
                    case"handler":
                        $this->handlerLoader();
                        break;
                    case"class":
                        $this->classLoader();
                        break;
                endswitch;

            endif;
        }


        /**
         * @return array
         */

        public function getData()
        {

            ( new Controller( "class", "keyValidator", "keyValidator" ) )->request();

            $allowedKeys = config\allowedKeys();

            if( !empty($_GET['data']) ):

                $data = ( new keyValidator\KeyValidator( $_GET['data'], $allowedKeys ) )->getArray();

                return( $data );

            endif;

        }

        /**
         * @return bool
         */

        private function templateLoader()
        {

            $path = LITENING_ROOT_DIRECTORY.'web/tmp/'.$this->directory.'/';
            $file = $path.$this->attribute.'.phtml';

            if( !file_exists( $file ) ):
                return false;
            else:
                include $file;
            endif;
        }

        /**
         * @return bool
         */

        private function handlerLoader()
        {

            $path = LITENING_ROOT_DIRECTORY.'application/handlers/';
            $file = $path.$this->directory.'/'.$this->attribute.'.php';

            if( !file_exists( $file ) ):
                return false;
            else:
                require $file;
            endif;
        }

        /**
         * @return bool
         */
        
        private function classLoader()
        {

            $path = LITENING_ROOT_DIRECTORY . 'application/classes/';
            $file = $path . $this->attribute . '/' . $this->attribute . '.php';

            if ( !file_exists( $file ) ):
                return false;
            else:
                require $file;
            endif;

        }

    }

endif;