<!-- 
show the booking details and what amount customer have to pay
-->
<?php 
    session_start();
    require_once('inc/config.php');
    require_once('inc/functions.php');
   
    if(isset($_GET['book_id'])){
        $user_id = htmlentities($_GET['uid']);
        $booking_id = htmlentities($_GET['book_id']);

        date_default_timezone_set("Asia/Colombo");
        $pay_day = Date("Y-m-d H:i:s");

        $query = "CALL get_customer_data('$booking_id')";
        $result_set = mysqli_query($con,$query);
        
        if($result_set){
            //query sucessfull
            if(mysqli_num_rows($result_set) > 0){
                $row = mysqli_fetch_array($result_set);
                    
                $dl_number = $row['dl_number'];
                $reg_number = $row['reg_number'];
                $customer_name = $row['first_name']." ".$row['last_name'];
                $rental_price = $row['amount'];
                $vehicle = $row['make']. " ".$row['model_name'];
                $late_fee = $row['late_fee'];
                $total = $rental_price + $late_fee;
                $book_id = $row['booking_id'];
                $dl_number = $row['dl_number'];
                
                $user_data = array( 
                    "booking_id"=>$book_id,
                    "dl_number"=>$dl_number,
                    "customer_name"=>$customer_name,
                    "reg_number"=>$reg_number,
                    "vehicle"=>$vehicle,
                    "rental_price"=>$rental_price,
                    "late_fee"=>$late_fee,
                    "total"=>$total,
                    "pay_day"=>$pay_day,
                    
                );
                $_SESSION['user_data'] = $user_data;
            }
        }
    }


    
            
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Bill</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="buttons">
    <header>
    <span><a href="admin.php?user_id=<?php echo $user_id; ?>"><button type="back" name = "back_admin_pg">Admin_page</button></a></span>
    </header>
    </div>
    
            <div class='show_bill_data'>
                <form action="print_bill.php?uid=<?php echo $user_id; ?>" method="POST">
                <fieldset>
                    <legend><h2>Bill Details</h2></legend>
                    <p>Booking ID : <?php echo $booking_id;?></p>
                    <p>DL Number : <?php echo $dl_number;?></p>
                    <p>Customer Name : <?php echo $user_data['customer_name']; ?></p>
                    <p>Vehicle Reg Number : <?php echo $user_data['reg_number']; ?></p>
                    <p>Vehicle Name: <?php echo $user_data['vehicle']; ?></p>
                    <p>Rental Price : <?php echo $user_data['rental_price']; ?></p>
                    <p>Late Fee : <?php echo $user_data['late_fee']; ?></p>
                    <p>Total : <?php echo $user_data['total']; ?></p>
                    <p>Payment Data & Time : <?php echo $user_data['pay_day']; ?></p>
                    <button type="submit" name="pay">Pay</button>
    
                </fieldset>
                </form>
            </div>
    
</body>
</html>