<?php
try {
     $PDO = new PDO( 'mysql:host=YOUR SERVER;dbname=YOUR DATABASE','YOU USER','YOUR PASSWORD');

}
catch( PDOException $Exception ) {
    echo $Exception->getMessage(), $Exception->getCode();
    die();
}





?>