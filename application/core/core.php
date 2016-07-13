<?php
/**
 *
 * Core class:
 *
 * - Handles all core functions
 *
 * Requires ( type, attribute, directory ):
 *
 * - A type to handle (class, template, handler).
 * - An attribute(filename) corresponding to the type.
 * - A directory where the filename can be found.
 *
 * Requests:
 *
 * - Types can be requested and will be loaded by their own loader.
 *
 */
namespace application\core;

if(!class_exists( "Core" )):

    class Core
    {
        protected $type;
        protected $attribute;
        protected $directory;

        /**
         * Core constructor.
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
            if ( !in_array($this->type, ['configuration', 'class', 'model', 'service', 'controller', 'xmlController']) ):

                echo 'Error: An invalid $type was passed to Core::request()! Instantiation aborted!';

                return( false );

            else:

                switch( $this->type ):
                    case"configuration":
                        $this->configurationLoader();
                        break;
                    case"class":
                        $this->classLoader();
                        break;
                    case"model":
                        $this->modelLoader();
                        break;
                    case"service":
                        $this->serviceLoader();
                        break;
                    case"controller":
                        $this->controllerLoader();
                        break;
                    case"xmlController":
                        $this->xmlControllerLoader();
                        break;
                endswitch;

            endif;
        }

        /**
         * @return bool
         */

        private function configurationLoader()
        {
            $path = LITENING_ROOT_DIRECTORY . 'application/config/';
            $file = $path . $this->attribute . '.php';

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

            if( !file_exists( $file ) ):
                return( false );
            else:
                require $file;
            endif;

        }

        /**
         * @return bool
         */

        private function modelLoader()
        {
            $path = LITENING_ROOT_DIRECTORY . 'application/model/data/';
            $file = $path . $this->directory . '/' . $this->attribute . '.php';

            if( !file_exists( $file ) ):
                return( false );
            else:
                require $file;
            endif;

        }

        /**
         * @return bool
         */

        private function serviceLoader()
        {
            $path = LITENING_ROOT_DIRECTORY . 'application/model/service/';
            $file = $path . $this->attribute . '.php';

            if ( !file_exists( $file ) ):
                return( false );
            else:
                require $file;
            endif;

        }

        /**
         * @return bool
         */

        private function controllerLoader()
        {
            $path = LITENING_ROOT_DIRECTORY . 'application/controller/';
            $file = $path . $this->attribute . '.php';

            if ( !file_exists( $file ) ):
                return( false );
            else:
                require $file;
            endif;

        }

        /**
         * @return bool
         */

        private function xmlControllerLoader()
        {
            $path = LITENING_ROOT_DIRECTORY . 'application/xmlController/';
            $file = $path . $this->attribute . '.php';

            if ( !file_exists( $file ) ):
                return( false );
            else:
                require $file;
            endif;

        }

    }

endif;

?>