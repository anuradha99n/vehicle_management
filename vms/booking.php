<?php session_start(); ?>
<?php require_once ('inc/config.php');?>
<?php require_once ('inc/functions.php');?>
<?php 
    $dl_number = $_GET['dl_number'];
    $errors = array();
    $user_list = ''; //user details
    $search = '';
    $book_dt = '';
    $return_dt = '';
    $v_category = '';
    $vehicle = '';

    if(isset($_POST['book'])){
        $book_dt = mysqli_real_escape_string($con, $_POST['book_dt']);
        $return_dt = mysqli_real_escape_string($con, $_POST['return_dt']);
        $v_category = mysqli_real_escape_string($con, $_POST['v_category']);
        $reg_number = mysqli_real_escape_string($con, $_POST['vehicle']);
        //calculate basic amount
        $amount = cal_amount($con,$book_dt,$return_dt,$v_category);

        $query = "CALL new_book('$book_dt','$return_dt','$amount','$reg_number','$dl_number')";
        $sql = mysqli_query($con, $query);
        
        if($sql){
            //query sucessful. redirect to users.php?dl_number=$dl_number
            header("Location: users.php?dl_number=$dl_number");

        }else{
            $errors[] = 'database query unsucessfull';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Page</title>
    <link rel="stylesheet" href="css/main.css">

</head>
<body>
    <div class="booking">
        <form action="booking.php?dl_number=<?php echo $dl_number ?>" method="post">
            <fieldset>
                <legend><h1>Booking</h1></legend>
                <p>
                    <label for="">Booking Date & Time</label>
                    <input type="datetime-local" name="book_dt" id="book_dt" required value="<?php echo $book_dt; ?>">
                </p>
                <p>
                    <label for="">Return Date & Time</label>
                    <input type="datetime-local" name="return_dt" id="return_dt" required value="<?php echo $return_dt; ?>">
                </p>
                <p>
                    <label for="">Vehicle Category :</label>
                    <select name="v_category" id="v_category">
                        <?php $category_list = get_v_cat($con);
                            echo $category_list ;?>
                    </select>
                </p>
                <p>
                    <label for="vehicle">Vehicle</label>
                    <select name="vehicle" id="vehicle">
                        <option>Select vehicle</option>
                    </select>
                </p>
                <p>
                    <label for="">&nbsp;</label>
                    <button type="submit" name ="book">Book</button>
                </p>

            </fieldset>
            
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#v_category").on("change", function(){
                var v_category = $("#v_category").val();
                var getURL = "inc/get_vehicle.php?v_category=" + v_category;
                $.get(getURL, function(data, status){
                   $("#vehicle").html(data); 
                }); 
            });
        });
    </script>


</body>
</html>