<?php
    require_once 'config.php';

    $vehicle_list = "Select Vehicle";
    if (isset($_GET['v_category'])){
        $v_category = mysqli_real_escape_string($con,$_GET['v_category'] );
        //extract vehicle names by category and availability
        $query = "SELECT * FROM vehicle WHERE (category_name = \"{$v_category}\" AND is_available = 1)
            ORDER BY model_name ASC";

        $result_set = mysqli_query($con, $query);

        while($result = mysqli_fetch_assoc($result_set)){
            $vehicle_list .= "<option value=\"{$result['reg_number']}\">{$result['model_name']}</option>";
        }
        echo $vehicle_list;
    }
    else{
        echo "<option>No vehicle Available</option>";
    }
    
?>