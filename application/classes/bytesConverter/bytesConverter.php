<?php

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 4-7-2016
 * Time: 13:18
 */

namespace application\classes\bytesConverter;

if(!class_exists("BytesConverter")):

    class BytesConverter
    {

        protected $bytes;

        /**
         * ByteConverter constructor.
         * @param $bytes
         */

        public function __construct( $bytes )
        {
           $this->bytes = $bytes;
        }

        public function convert()
        {
            $precision      = 2;
            $base           = log( $this->bytes, 1024 );
            $suffixes       = array(' B', ' Kb', ' Mb', ' Gb', ' Tb');
            $converted      = ( round( pow( 1024, $base - floor( $base ) ), $precision ) ) . $suffixes[floor( $base )];

            return( $converted );
        }
    }

endif;