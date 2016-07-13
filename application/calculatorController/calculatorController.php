<?php

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 05-Jul-16
 * Time: 14:45
 */

namespace application\calculatorController;

if( !class_exists( "CalculatorController" ) ):

    class CalculatorController
    {

        protected $type;

        public function __construct( $type )
        {
            $this->type = $type;
        }

        public function request()
        {

            switch( $this->type ):

                case"excel":
                        
                    break;

            endswitch;

        }

    }

endif;