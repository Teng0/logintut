<?php
$con = mysqli_connect("localhost","root","","logintutdb");

function row_count($result){

   /* global $con;*/
    return mysqli_num_rows($result);
}

function escape($sting){

    global $con;
    return mysqli_real_escape_string($con,$sting);
}

function query($query){

    global $con;

    return mysqli_query($con,$query);

}

function fetch_array($result){

   /* global $con;*/
    return mysqli_fetch_array($result);
}

/**
 * @param $result
 */
function confirm($result){
    global $con;
    if (!$result){
        die("QUERY FAILED". mysqli_error($con));
    }


}