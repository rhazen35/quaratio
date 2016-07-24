<?php

/**
 *
 * Key validator class:
 *
 * - Validates all get keys.
 * - Array's of keys can be added to the test array.
 * - getArray() returns an array, build of matching keys.
 *
 * This validator only validates keys from the url data array.
 * It matches them with the testArrays specified below.
 * Arrays are setup manually and should contain all keys(variable names).
 *
 * Get array:
 *
 * - Checks if the $_get_data (passed to the constructor) matches the matchSet.
 *   The matchSet is an array of all test arrays.
 * - Returns an associative array of matched keys with their corresponding value (from the $_get_data).
 *
 */

namespace application\classes\keyValidator;

if(!class_exists('KeyValidator')):

    class KeyValidator
    {
        
        private $_get_data;
        private $_matchSet = array();
        private $_testArrays = array(
            'global' => array(

            ),

            'request' => array(
                'requestType',
                'requestAttribute',
                'requestDirectory'
            ),

            'login' => array(
                'email', 
                'password',
                'passwordRepeat', 
                'emailError', 
                'passwordError', 
                'passwordRepeatError', 
                'error', 
                'message'
            ),

            'pagination' => array(
                'limit',
                'pageNumber',
                'totalRows',
            ),

            'systemLogs' => array(
                'systemInfo',
                'systemLogs',
                'sysLogFilterParams',
                'sysLogFilter',
                'sysLogFilter_sort',
                'sysLogFilter_sortType'
            ),

            'excel' => array(
                'excelResponseData'
            ),

            'xmlModel' => array(
                'report',
                'hideValidatorType',
                'hideValidatorAction'
            )

        );

        /**
         * keyValidator constructor.
         * @param $get_data
         * @param $matchSets
         */

        public function __construct( $get_data, $matchSets ) {

            foreach( $matchSets as $matchSet ):

                $this->_matchSet = array_merge( $this->_matchSet, $this->_testArrays[$matchSet] );

            endforeach;
            
            $decode             = json_decode( base64_decode( $get_data ) );
            $this->_get_data    = ( array ) $decode;
        }

        /**
         * @return array
         */

        public function getArray() {

            $returnArray = array();

            foreach( $this->_get_data as $key => $value ):

                if( in_array( $key, $this->_matchSet ) ):

                    $returnArray[$key] = $value;
                
                endif;

            endforeach;

            return( $returnArray );
        }

    }

endif;

?>