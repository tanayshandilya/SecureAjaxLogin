<?php

header('Access-Control-Allow-Origin: *');
$reqId = crc32($_SERVER['REMOTE_ADDR']);

if( isset( $_GET['GetEncryptionKey'] ) && $_GET['GetEncryptionKey'] == 1 ){
  if( isset( $_GET['KeyLength'] ) ){
  	$key = hash('sha512',openssl_random_pseudo_bytes($_GET['KeyLength']));
    echo $key;
    file_put_contents('KeyFiles/'.$reqId.'.key',$key);
  }else{
    $key = hash('sha512',openssl_random_pseudo_bytes(512));
    echo $key;
    file_put_contents('KeyFiles/'.$reqId.'.key',$key);
  }
}