<?php

// connect to the database
$filename = "dbCreds.json";
$file = fopen( $filename, "r" );

if( $file == false ) {
echo ( "Error in opening file" );
exit();
}

$filesize = filesize( $filename );
$filetext = fread( $file, $filesize );
fclose( $file );

$login_arr = json_decode($filetext, true);

$host = $login_arr["host"];
$port = $login_arr["port"];
$db_name = $login_arr["db_name"];
$db_username = $login_arr["db_username"];
$db_password = $login_arr["db_password"];

$conn = pg_connect("host=$host port=$port dbname=$db_name user=$db_username password=$db_password");

 if (!$conn) {
  error_log("Connection failed: " . pg_last_error());
  exit();
}
