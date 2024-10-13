<!-- 
    Administrative account 
    Create bill, add vehicles, vehicle details, 
-->

<?php session_start(); ?>
<?php require_once('inc/config.php'); ?>
<?php require_once 'inc/functions.php'; ?>
<?php
    $user_id = $_GET['user_id'];
    $errors = array();
    update_all_late_fee($con); // 

    if(!isset($user_id)){
        header('Location: alogin.php');
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <!--buttons for 'add vehicle' 'add admin' 'show customer details & booking details'  
        show all booking details
        text area for search the booking id, redirect to create_bill.php

    -->
    <div class="buttons">
        <header>
            <!--
            <form action="" method="POST">
                <span><button type="add_vehicle" name = "add_vehicle">Add Vehicle</button></span>
                
            </form>
            -->
        </header>
    </div>
    
    <table class="booking_details">
        <tr>
            <th>Book ID</th>
            <th>User ID</th>
            <th>Rental Price</th>
            <th>Late fee</th>
            <th>Total</th>
            <th>Payment</th>
        
        </tr>
        <!--booking data -->
        <?php
            $query = "SELECT * FROM booking ORDER BY is_paid ASC, return_dt ASC";
            
            $result_set = mysqli_query($con,$query);
            if($result_set){
                //query sucessfull
                if(mysqli_num_rows($result_set) > 0){
                    while($result = mysqli_fetch_array($result_set)){
                        $dl_number  = $result['dl_number'];
                        $is_paid = show_is_paid($result['is_paid']);
                        $booking_id = $result['booking_id'];
                        $reg_number = $result['reg_number'];
                        $late_fee = $result['late_fee'];
                        //$late_fee = cal_late_fee($con,$result['booking_id']);
                        
                        $amount = $result['amount'];
                        $total = $amount + $late_fee;
                        ?>
                        <tr>
                            <td><?php echo $booking_id ?></td>
                            <td><?php echo $dl_number ?></td>
                            <td><?php echo $amount ?></td>
                            <td><?php echo $late_fee ?></td>
                            <td><?php echo $total ?></td>
                            <?php
                            if($result['is_paid'] == 0){
                                ?>
                                <td><a href='create_bill.php?book_id=<?php echo $booking_id;?>&uid=<?php echo $user_id?>'><button type="bill" name = "bill">Pay</button></a></td>
                                <?php
                            }else{
                                ?>
                                <td>Paid</td> 
                                <?php
                            }
                            ?>
                            

                        </tr>
                        <?php
                    }

                }else{
                    //no booking data
                    ?>
                        <td>no data</td>
                    <?php
                }

            }else{
                $errors[] = 'Booking details Query unsucessful - admin.php';
                ?>
                        <td>query error</td>
                    <?php
            }
        ?>  
    </table>
    
</body>
</html>
<?php mysqli_close($con); ?>