<?php require_once('config.php'); ?>
<?php

    function get_v_cat($con){
        //return vehicle category list
        $category_list = "";
        $query = "SELECT DISTINCT vc.category_name FROM v_category AS vc INNER JOIN vehicle AS v ON vc.category_name = v.category_name";
        $result_set = mysqli_query($con, $query);
        $category_list .= "<option value=''>Select Category</option>";
        while($result = mysqli_fetch_assoc($result_set)){
            $category_list .= "<option value=\"{$result['category_name']}\">{$result['category_name']}</option>";  
        }
        return $category_list;
    }

    function check_req_fields($req_fields){
        //check all required fields are filled
        $errors = array();
        foreach($req_fields as $field){

            if(empty(trim($_POST[$field]))){
                $error[] = $field . ' is required';
            }
        }
        return $errors;
    }

    function check_max_lengrh($max_len_fields){
        //checking max length of the fields
        $errors = array();
        foreach($max_len_fields as $field => $max_len){
            if(strlen(trim($_POST[$field])) > $max_len){
                $errors[] = $field . ' must be less than ' . $max_len . ' characters';
            }
        }
        return $errors;
    }

    function check_password($con, $password,$confirm_password){
        //confirm password
        $errors = array();
        $password = mysqli_real_escape_string($con, $password);
        $confirm_password = mysqli_real_escape_string($con, $confirm_password);
        if($password != $confirm_password){
            $errors[] = 'Passwords are not same';
        }
        return $errors;
    }

    function cal_amount($con, $bookdt, $returndt,$v_category){
        //calculate the amount for customer requires
        $bd = strtotime($bookdt);
        $rd = strtotime($returndt);
        $datediff = abs($bd - $rd); //days difference
        $ddiff = floor($datediff/(60*60*24)); //days
        $mdiff = floor($datediff/(60*60*24*30)); //months
        
        $query = "SELECT * FROM v_category WHERE category_name = '{$v_category}' LIMIT 1";
        $amounts = mysqli_query($con, $query);
        if($ddiff > 15){
            //if rental days greater than 15 days amount calculate for month
            $row_amount = mysqli_fetch_assoc($amounts);
            $amount = $row_amount['cost_per_month'];
            if($ddiff >30){
                $amount = $amount * $mdiff;
            }
        }else{
            $row_amount = mysqli_fetch_assoc($amounts);
            $amount = $row_amount['cost_per_day'] * $ddiff;
        }
        return $amount;
    }

    function show_is_paid($val){
        if($val == 1){
            return 'Yes';
        }else{
            return 'No';
        }
    }

    function update_late_fee($con,$booking_id){
        //update late fee values
        $errors = array();
        $query = "CALL update_late_fee({$booking_id})";
        $result_set = mysqli_query($con,$query);

        if($result_set){
            //update query sucessfull

        }else{
            //query unsucessfull
            $errors .= "query nusucessfull";
        }
        return $errors;
    }

    function update_all_late_fee($con){
        $query1 = "SELECT * FROM booking";
        $result_set = mysqli_query($con,$query1);
        $bid = array();
        while($result = mysqli_fetch_assoc($result_set)){
            $bid[] = $result['booking_id'];
        }
        foreach($bid as $id){
            update_late_fee($con,$id);
        }
    }

    function update_last_login($con,$username,$acc){
        //update last login deails in customer & employer
        $errors = array();
        date_default_timezone_set("Asia/Colombo");
        $last_login = Date("Y-m-d H:i:s");
        if($acc == 'admin'){
            $query = "CALL update_last_login_admin('{$username}','{$last_login}')";
            $result_set = mysqli_query($con, $query);
            if(!$result_set){
                $errors[] = 'Last Login query unsucessful';
            }
        }else if($acc == 'user'){
            $query = "CALL update_last_login('{$username}','{$last_login}')";
            $result_set = mysqli_query($con, $query);
            if(!$result_set){
                $errors[] = 'Last Login query unsucessful';
            }
        }
        return $errors;
    }

    function customer_data($con,$booking_id){
        //return customer name,dl number,
        //vehicle reg_number,vehicle name,rental price,late fee
        //total,return date & time
        $errors = array();
        $errors = update_late_fee($con,$booking_id);

        date_default_timezone_set("Asia/Colombo");
        $pay_day = Date("Y-m-d H:i:s");

        $query = "CALL get_customer_data('$booking_id')";
        $result_set = mysqli_query($con,$query);

        if(empty($errors) && $result_set){
            //query sucessfull
            if(mysqli_num_rows($result_set) > 0){
                $row = mysqli_fetch_array($result_set);
                    
                    $dl_number = $row['dl_number'];
                    $customer_name = $row['first_name']." ".$row['last_name'];
                    $reg_number = $row['reg_number'];
                    $vehicle = $row['make']. " ".$row['model_name'];
                    $rental_price = $row['amount'];
                    $book_id = $row['booking_id'];
                    $late_fee = $row['late_fee'];
                    $total = $rental_price + $late_fee;

                    $user_data = array( "booking_id"=>$book_id,
                                        "dl_number"=>$dl_number,
                                        "customer_name"=>$customer_name,
                                        "reg_number"=>$reg_number,
                                        "vehicle"=>$vehicle,
                                        "rental_price"=>$rental_price,
                                        "late_fee"=>$late_fee,
                                        "total"=>$total,
                                        "pay_day"=>$pay_day);
                    
                
                return $user_data;
            }
        }
    }

    function update_payment($con,$bill_data,$uid){
        //update bill table
        $bill_date = $bill_data['pay_day'];
        $book_price = $bill_data['rental_price'];
        $total_late_fee = $bill_data['late_fee'];
        $total = $bill_data['total'];
        $booking_id = $bill_data['booking_id'];
        $reg_number = $bill_data['reg_number'];
        $emp_id = $uid;
        
        $query = "CALL update_bill('{$bill_date}',{$book_price},{$total_late_fee},{$total},{$booking_id},{$emp_id},'{$reg_number}')";
        $result_set = mysqli_query($con,$query);
        if($result_set){
            return 1;
        }else{
            return 0;
        } 
        
    }

/*
    function show_username($con,$user_id,$acc){
        //get user/admin full name
        $query = '';
        $first_name = '';
        $last_name = '';
        $full_name = '';
        $result_set = '';
        if($acc == 'admin'){
            $query = "SELECT * FROM employer WHERE user_id='{$user_id}' LIMIT 1";
            $result_set = mysqli_query($con,$query); //excute query
        }elseif($acc == 'user'){
            $query = "SELECT * FROM customer WHERE dl_number='{$user_id}' LIMIT 1";
            $result_set = mysqli_query($con,$query); //excute query
        }
        
        if($result_set){
            //query sucessfull
            return 'query sucess';
            if(mysqli_num_rows($result_set) >0){
                $row = mysqli_fetch_assoc($result_set);
                $first_name = $row['first_name'];
                $last_name = $row['last_name'];
                $full_name = $first_name .+ ' ' .+ $last_name;
                return $full_name;
            }else{
                return 'no data found';
            }
        }else{
            return 'query unsucess';
        }

    }

*/

    
?>