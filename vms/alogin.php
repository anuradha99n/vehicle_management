<?php require_once('inc/config.php'); ?>
<?php require_once('inc/functions.php'); ?>
<?php
    //check for form submission
    if(isset($_POST['alogin'])){
        $errors = array();
        //check if the username & password has been entered 
        if(!isset($_POST['user_id']) || strlen(trim($_POST['user_id'])) < 1 ){
            $errors[] = "User ID is missing / invalid";
        }
        if(!isset($_POST['password']) || strlen(trim($_POST['password'])) < 1 ){
            $errors[] = "Password is missing / invalid";
        }

        //check errors
        if(empty($errors)){
            $user_id = $_POST['user_id'];
            $password = mysqli_real_escape_string($con,$_POST['password']);
            $hashed_password = sha1($password);

            //create database query
            $query = "SELECT * FROM employer WHERE user_id = {$user_id} AND hashed_password = '{$hashed_password}' AND is_admin = 1  LIMIT 1";
            $result_set = mysqli_query($con, $query); // execute query

            if($result_set){
                //query sucessfull
                if(mysqli_num_rows($result_set) == 1){
                    //valid user
                    //redirect to admin.php
                    $row = mysqli_fetch_assoc($result_set);
                    $user_id = $row['user_id'];

                    $errors [] = update_last_login($con,$user_id,"admin"); // update last login details
        
                    //header("Location: admin.php?user_id=$user_id");
                    header("Location: admin.php?user_id={$user_id}");
                    
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Page</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="login">
            <form action="alogin.php" method="POST">
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
                            echo '<div>';
                        }
                    ?>
                    <p>
                        <input type="text" name="user_id" id="user_id" required placeholder="User ID">
                    </p>

                    <p>
                        <input type="password" name="password" id="password" required placeholder="Password">
                    </p>

                    <p>
                        <button type="submit" name ="alogin">Log In</button>
                    </p>
                </fieldset>
            </form>
        </div>

</body>
</html>
<?php mysqli_close($con); ?>