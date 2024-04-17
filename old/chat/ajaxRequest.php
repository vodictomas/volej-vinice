
<?php
 
 header("Expires: ".date("D, j M Y H:i:s",(time()-3600)-60*10)." GMT");              // Date in the past
 header("Last-Modified: ".date("D, j M Y H:i:s",(time()+3600))." GMT");              // Date in the future
 header("Cache-Control: no-cache, must-revalidate");            // HTTP/1.1
 header("Pragma: no-cache");                                    // HTTP/1.0
 header("Content-Type: text/html");
 header("Content-Encoding: UTF-8");

 include "kecani.php";
 
?>
