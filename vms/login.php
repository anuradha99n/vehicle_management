<?php require_once('inc/config.php'); ?>
<?php require_once('inc/functions.php'); ?>
<?php
    //check for form submission
    if(isset($_POST['login'])){
        $errors = array();
        //check if the username & password has been entered 
        if(!isset($_POST['email']) || strlen(trim($_POST['email'])) < 1 ){
            $errors[] = "Username is missing / invalid";
        }
        if(!isset($_POST['password']) || strlen(trim($_POST['password'])) < 1 ){
            $errors[] = "Password is missing / invalid";
        }

        //check errors
        if(empty($errors)){
            $email = mysqli_real_escape_string($con,$_POST['email']);
            $password = mysqli_real_escape_string($con,$_POST['password']);
            $hashed_password = sha1($password);

            //create database query
            $query = "SELECT * FROM customer WHERE email = '{$email}' AND hashed_password = '{$hashed_password}' LIMIT 1";
            $result_set = mysqli_query($con, $query); // execute query

            if($result_set){
                //query sucessfull
                if(mysqli_num_rows($result_set) == 1){
                    //valid user
                    //redirect to users.php
                    $row = mysqli_fetch_assoc($result_set);
                    $dl_number = $row['dl_number'];
                    
                    $errors [] = update_last_login($con,$dl_number,"user"); // update last login details
                    //$errors[] = update_last_login_user($con,$dl_number);
                    header("Location: users.php?dl_number=$dl_number");      

                }else{
                    //username & password invalid
                    $errors[] = 'Invalid username / password';

                }
            }else{
                $errors[] = 'Database query failed';
            }

        }
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Log In</title>
        <link rel="stylesheet" href="css/main.css">
    </head>
    <body>
        <div class="login">
            <form action="login.php" method="POST">
                <fieldset>
                    <legend><h1>Log In</h1></legend>

                    <?php
                        if(isset($errors) && !empty($errors)){
                            //show errors
                            echo '<div class="errors">';
                            echo '<b>There were error(s) on your form.</b><br>';
                            foreach($errors as $error){
                                echo '-' . $error . '<br>';
                            }
                            echo '</div>';
                        }
                    ?>
                    <p>
                        <input type="email" name="email" id="email" required placeholder="Email Address">
                    </p>

                    <p>
                        <input type="password" name="password" id="password" required placeholder="Password">
                    </p>

                    <p>
                        <button type="submit" name ="login">Log In</button>
                    </p>
                    <a href="signup.php">Signup</a>
                </fieldset>
            </form>
            
        </div> <!-- login -->
    </body>



</html>
<?php mysqli_close($con); ?>