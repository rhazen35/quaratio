<?php
/*
 * Register class:
 *
 * - Registers a new user.
 * - Checks if an email already exists.
 * - Creates a random hash for email verification.
 * 
 */
namespace application\classes\registeruser;

use \application\model\service;

if(!class_exists('RegisterUser')):

    class RegisterUser
    {

        protected $email;
        protected $password;

        /**
         * RegisterUser constructor.
         * @param $email
         * @param $password
         */

        public function __construct( $email, $password )
        {
            $this->email = $email;
            $this->password = $password;
        }

        /**
         * Register a new user
         */

        public function register()
        {
            $emptyspace = '';
            $date       = date('Y-m-d H:i:s');
            $type       = 'verifyEmail';
            $hash       = password_hash( $this->password, PASSWORD_BCRYPT );
            $email_code = $this->randomPassword();

            $sql        = "CALL proc_newUser(?,?,?,?,?,?)";
            $data       = array("id" => $emptyspace, "email" => $this->email, "email_code" => $email_code, "password" => $hash, "type" => $type, "timestamp" => $date);
            $format     = array('isssss');

            $type       = "create";

            ( new service\Service( $type ) )->dbAction( $sql, $data, $format );
            
        }

        /**
         * @return mixed
         */

        public function checkEmailExists()
        {

            $sql        = "SELECT f_checkEmailExists(?)";
            $data       = array("email" => $this->email);
            $format     = array('s');

            $type       = "read";

            $returnData = ( new service\Service( $type ) )->dbAction( $sql, $data, $format );

            if( !empty( $returnData ) ):
                return( $returnData );
            else:
                return( false );
            endif;
        }

        /**
         * @return string
         */

        private function randomPassword()
        {

            $alphabet    = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
            $pass        = array();
            $alphaLength = strlen( $alphabet ) - 1;

            for ($i = 0; $i < 8; $i++) {
                $n = rand( 0, $alphaLength );
                $pass[] = $alphabet[$n];
            }

            return( implode( $pass ) );
        }
    }

endif;

?>