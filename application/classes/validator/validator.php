<?php

namespace application\classes\validator;

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 22-Jun-16
 * Time: 21:42
 */

if(!class_exists("Validator")):

    class Validator
    {
        protected $type;
        protected $value;

        /**
         * Validator constructor.
         * @param $value
         */
        
        public function __construct( $value )
        {
            $this->value = $value;
        }

        /**
         * @return string
         */

        public function validateIsDigit()
        {

            trim( $this->value );
            $value = htmlspecialchars( $this->value );

            if( ctype_digit( $value ) ):
                return( $value );
            else:
                return( false );
            endif;
        }

        /**
         * @return string
         */

        public function validateIsRealDate()
        {

            trim( $this->value );
            $value = htmlspecialchars( $this->value );

            if (false === strtotime($value)):
                return( false );
            else:

                list($year, $month, $day) = explode('-', $value);

                if (false === checkdate($month, $day, $year)):
                    return( false );
                endif;

            endif;

            return( $value );
        }

        public function validateIsRealTime()
        {

            trim( $this->value );
            $value = htmlspecialchars( $this->value );

            if (strtotime($value)):
                return( $value );
            else:
                 return( false );
            endif;

        }

    }

endif;