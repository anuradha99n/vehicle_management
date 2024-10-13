<?php
require_once('config.php');
require_once('functions.php');
session_start();
$user_id = 1;
    $user_data = array();
    foreach($_SESSION['user_data'] as $key=>$value){
        $user_data[$key] = $value;
    }

    $r = update_payment($con,$user_data,$user_id);
    if($r == 1){
        echo '<h3>Payment Sucessfull</h3>';
    }else{
        echo '<h3>Payment Failed</h3>';
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    


</body>
</html>
<?php
    session_destroy();
?>