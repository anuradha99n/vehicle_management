<?php session_start(); ?>
<?php require_once 'inc/config.php'; ?>
<?php require_once 'inc/functions.php'; ?>
<?php
    $dl_number = $_GET['dl_number'];
    //update_late_fee($)

    $user_list = '';
    //getting the list of users
    $query = "SELECT * FROM booking WHERE dl_number = '$dl_number' AND is_paid = 0 LIMIT 1";
    $result_set = mysqli_query($con, $query);
    if($result_set){
        if(mysqli_num_rows($result_set) == 1){
            //show last unpaid booking details
            $user_list .= "<table class=\"bookinglist\">";
            $user_list .=   "<tr>";
            $user_list .=       "<th>Booking Date & Time</th>";
            $user_list .=       "<th>Return  Date & Time</th>";
            $user_list .=       "<th>Amount</th>";
            $user_list .=       "<th>Late Fee</th>";
            $user_list .=       "<th>Total</th>";
            $user_list .=       "<th>Vehicle Reg Number</th>";
            $user_list .=       "<th>Is Paid</th>";
            $user_list .=   "</tr>";
            
            $result = mysqli_fetch_assoc($result_set);
            $is_paid = show_is_paid($result['is_paid']);
            $late_fee = $result['late_fee'];
            //$late_fee = cal_late_fee($con, $result['booking_id']); //caluclate late fee
            $amount = $result['amount'];
            $total = $amount + $late_fee;

            $user_list .= "<tr>";
            $user_list .=   "<td>{$result['book_dt']}</td>";
            $user_list .=   "<td>{$result['return_dt']}</td>";
            $user_list .=   "<td>{$amount}</td>";
            $user_list .=   "<td>{$late_fee}</td>"; // late fee
            $user_list .=   "<td>{$total}</td>"; // total
            $user_list .=   "<td>{$result['reg_number']}</td>";
            $user_list .=   "<td>{$is_paid}</td>";
            $user_list .= "</tr>";
            $user_list .= "</table>";

        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>user</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php
        $query = "SELECT * FROM customer WHERE dl_number='{$dl_number}' LIMIT 1";
        $result_set = mysqli_query($con,$query);
        $r = mysqli_fetch_array($result_set);
        $name = $r['first_name']." ".$r['last_name'];
    ?>
    <h3><?php echo $name; ?></h3>
    
    <div class="show_booking">
        <h2>Last booking Details</h2>
        <?php
            if(empty($user_list)){
            ?>
                <span><a href="booking.php?dl_number=<?php echo $dl_number;?>">New booking</a></span>
            <?php    
            }else{
                echo $user_list;
            }
            
            
        ?>
    </div>
</body>
</html>