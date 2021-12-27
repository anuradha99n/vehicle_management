<?php session_start(); ?>
<?php require_once('inc/config.php'); ?>
<?php require_once('inc/functions.php'); ?>

<?php
    $errors = array(); //save error massages
    $dl_number = '';
    $first_name ='';
    $last_name ='';
    $email ='';
    $phone_number='';
    
    if(isset($_POST['signup'])){

        $dl_number = $_POST['dl_number'];
        $first_name =$_POST['first_name'];
        $last_name =$_POST['last_name'];
        $email =$_POST['email'];
        $phone_number=$_POST['phone_number'];

        //checking required fields
        $req_fields = array('dl_number','first_name','last_name','email','phone_number','password');
        $errors = array_merge($errors, check_req_fields($req_fields));

        //checking max length
        $max_len_fields = array('dl_number' => 15,'first_name'=>50,'last_name'=>100,'email'=>100,'phone_number'=>12,'password'=>40);
        $errors = array_merge($errors,check_max_lengrh($max_len_fields));

        //check password 
        $error = check_password($con, $_POST['password'], $_POST['confirm_password']);
        $errors = array_merge($errors, $error);

        
        //checking email & dl number already exists
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $dl_number = mysqli_real_escape_string($con, $_POST['dl_number']);
        //check email
        $query = "SELECT * FROM customer WHERE email = '{$email}' LIMIT 1";
        $result_set = mysqli_query($con, $query);
        if($result_set){
            if(mysqli_num_rows($result_set) == 1){
                $errors[] = 'Email address already exists';
            }
        }
        //check dl number
        $query = "SELECT * FROM customer WHERE dl_number = '{$dl_number}' LIMIT 1";
        $result_set = mysqli_query($con, $query);
        if($result_set){
            if(mysqli_num_rows($result_set) == 1){
                $errors[] = 'DL number already exists';
            }
        }
        
        if(empty($errors)){
            //no errors.. Adding new records
            //sanitize entries. email , dl number & password already sanitized
            $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
            $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
            $phone_number = mysqli_real_escape_string($con, $_POST['phone_number']);
            $password = mysqli_real_escape_string($con, $_POST['password']);
            $hashed_password = sha1($password); //encript password

            date_default_timezone_set("Asia/Colombo");
            $last_login = Date("Y-m-d H:i:s");
            
            $query = "INSERT INTO customer (";
            $query .= "dl_number,first_name,last_name,email,phone_number,hashed_password,last_login";
            $query .= ") VALUES (";
            $query .= "'{$dl_number}','{$first_name}','{$last_name}','{$email}','{$phone_number}','{$hashed_password}','{$last_login}'";
            $query .= ")";

            $result = mysqli_query($con, $query);
            if($result){
                //query sucessful.. redirecting to login page
                header('Location: login.php');
            }else{
                $errors[] = 'Failed to add new user';
                header('Location: signup.php');
            }
        }
    }    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Signup</title>
        <link rel="stylesheet" href="css/main.css">
    </head>
    <body>
        <main>
        
        
        <div class="signup">
            <form action="signup.php" method="POST">
                <fieldset>
                    <legend><h1>Signup</h1></legend>
                    <?php
                        if(!empty($errors)){
                            //show errors
                            echo '<div class="errors">';
                            echo '<b>There were error(s) on your form.</b><br>';
                            foreach($errors as $error){
                                echo '-' . $error . '<br>';
                            }
                            echo '<div>';
                        }
                    ?>
                    
                    <p>
                        <label for="">DL number</label>
                        <input type="text" name="dl_number" id="dl_number" required value="<?php echo $dl_number; ?>">
                    </p>
                    <p>
                        <label for="">First Name</label>
                        <input type="text" name="first_name" id="first_name" required value="<?php echo $first_name ;?>">
                    </p>
                    <p>
                        <label for="">Last Name</label>
                        <input type="text" name="last_name" id="last_name" required value="<?php echo $last_name ;?>">
                    </p>
                    <p>
                        <label for="">Email</label>
                        <input type="email" name="email" id="email" required value="<?php echo $email ;?>">
                    </p>
                    
                    <p>
                        <label for="">Phone number</label>
                        <input type="text" name="phone_number" id="phone_number" required value="<?php echo $phone_number ;?>">
                    </p>

                    <p>
                    <label for="">Password</label>
                        <input type="password" name="password" id="password" required>
                    </p>
                    <label for="">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm-password" required>
                    </p>
                    

                    <p>
                        <label for="">&nbsp;</label>
                        <button type="submit" name ="signup">Signup</button>
                    </p>
                </fieldset>
            </form>
        </div> <!-- Signup -->
        </main>
    </body>



</html>
<?php mysqli_close($con); ?>