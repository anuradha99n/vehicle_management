<!-- 
create the bill receipt
insert data to 'bill' mysql table

-->
<?php
    session_start();
    require_once('inc/config.php');
    require_once('inc/functions.php');

    $user_id = $_GET['uid'];
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
    <title>Print</title>
</head>
<body>
<div class="buttons">
    <header>
    <span><a href="admin.php?user_id=<?php echo $user_id; ?>"><button type="back" name = "back_admin_pg">Admin_page</button></a></span>
    </header>
    </div>
    

</body>
</html>

<?php
    session_destroy();
?>