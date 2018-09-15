<?php

class database{

    protected static $servername = "localhost";
    protected static $username   = "root";
    protected static $password   = "";
    protected static $dbname     = "SecureAjaxLogin";

    public static function connect(){
      $servername = self::$servername;
      $username   = self::$username;
      $password   = self::$password;
      $dbname     = self::$dbname;     
      // Create connection
      $conn = new mysqli($servername, $username, $password, $dbname);
      // Check connection
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }      return $conn;
    } 

}

class CryptoJS
{
    public static function encrypt($pass, $data) {
        // Set a random salt
        $salt = substr(md5(mt_rand(), true), 8);

        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad = $block - (strlen($data) % $block);

        $data = $data . str_repeat(chr($pad), $pad);

        // Setup encryption parameters
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, "", MCRYPT_MODE_CBC, "");

        $key_len =  mcrypt_enc_get_key_size($td);
        $iv_len =  mcrypt_enc_get_iv_size($td);

        $total_len = $key_len + $iv_len;
        $salted = '';
        $dx = '';
        // Salt the key and iv
        while (strlen($salted) < $total_len) {
            $dx = md5($dx.$pass.$salt, true);
            $salted .= $dx;
        }
        $key = substr($salted,0,$key_len);
        $iv = substr($salted,$key_len,$iv_len);

        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data = mcrypt_generic($td, $data);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        //return chunk_split(base64_encode('Salted__' . $salt . $encrypted_data),32,"\r\n");
        return base64_encode('Salted__' . $salt . $encrypted_data);
    }

    public static function decrypt($password, $edata) {
        $data = base64_decode($edata);
        // print "Data: " . $data . "\n";

        $salt = substr($data, 8, 8);
        // print "Salt (Base64): " . base64_encode($salt) . "\n";

        $ct = substr($data, 16);
        // print "Content (Base64): " . base64_encode($ct) . "\n";

        $rounds = 3;
        $data00 = $password.$salt;

        // print "Data00 (Base64): " . base64_encode($data00) . "\n";

        $md5_hash = array();
        $md5_hash[0] = md5($data00, true);

        $result = $md5_hash[0];
        // print "MD5-Hash[0] (Base64): " . base64_encode($result) . "\n";
        for ($i = 1; $i < $rounds; $i++) {
            $md5_hash[$i] = md5($md5_hash[$i - 1].$data00, true);
            $result .= $md5_hash[$i];
            // print "Result (Base64): " . base64_encode($result) . "\n";
        }

        $key = substr($result, 0, 32);
        // print "Key (Base64): " . base64_encode($key) . "\n";
        $iv = substr($result, 32, 16);
        // print "IV (Base64): " . base64_encode($iv) . "\n";

        return openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    }
}

class User
{
    public static function ValidateLogin( array $FormData ){
        if( array_key_exists( 'email', $FormData ) && array_key_exists( 'password', $FormData ) ){
            $conn = database::connect();
            $sql  = "SELECT * FROM `auth`";
            $result = $conn->query($sql);
            if( $result->num_rows > 0 ){ $credentials = [];
                while( $row = $result->fetch_assoc() ){
                    $credentials[ $row['email'] ] = $row['password'];
                    $credentials[ 'username' ] = $row['username'];
                }if( isset( $credentials[ $FormData['email'] ] ) ){
                    if( hash('sha512', $FormData['password'] ) == $credentials[ $FormData['email'] ] ){
                        return [ 
                            'status' => 'success', 
                            'token' => base64_encode( json_encode( [ 
                                'LoginTime' => strtotime('now'), 
                                'TokenTTL' => (strtotime('now')+60), 
                                'TokenData' => [ 
                                    'LoginStatus' => 1, 
                                    'LoginUser' => $credentials['username'], 
                                    'SessionID' => hash('sha256', $_SERVER['REMOTE_ADDR'].strtotime('now') ) 
                                ] 
                            ] ) ) 
                        ];
                    }else{
                        return [ 'status' => 'error', 'detail' => 'Invaid Password' ];
                    }
                }else{
                    return [ 'status' => 'error', 'detail' => 'Invaid Email' ];
                }
            }else{
                return [ 'status' => 'error', 'detail' => 'Database error!' ];
            }
        }else{
            return [ 'status' => 'error', 'detail' => 'Either email or password is missing' ];
        }
    }
}


header('Access-Control-Allow-Origin: *');
$reqId = crc32($_SERVER['REMOTE_ADDR']);

if( file_exists( 'KeyFiles/'.$reqId.'.key' ) ){
    if( isset( $_POST['FormData'] ) ){
        $key = file_get_contents( 'KeyFiles/'.$reqId.'.key' );
        $FormData = json_decode( CryptoJS::decrypt($key,$_POST['FormData'] ), true);
        unlink( 'KeyFiles/'.$reqId.'.key' );
        if ( User::ValidateLogin( $FormData )['status'] == 'success' ) {
            session_start();
            $_SESSION['LoginStatus'] = 1;
            echo json_encode( User::ValidateLogin( $FormData ) );
        }else{
            echo json_encode( User::ValidateLogin( $FormData ) );
        }
    }    
}