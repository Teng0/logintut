<?php

function clean($string){

    return htmlentities($string);
}
function redirect($location){

    return header("location: {$location}");
}

function set_message($message){

    if (!empty($message)){
        $_SESSION["message"]=$message;
    }else{
        $message="";
    }
};
function display_message(){
    if (!isset($_SESSION["message"])){
        echo $_SESSION["message"];
        unset($_SESSION["message"]);
    }
}
function token_generator(){
    $token= $_SESSION["token"]= md5(uniqid(mt_rand(),true));
    return $token;

}

function validation_errors($error_message){
   $error_message=<<<DELIMITER
                <div class="alert alert-danger" role="alert">$error_message</div>
DELIMITER;
            return $error_message;


}

function send_email($email,$subject,$msg,$headers){

 return mail($email,$subject,$msg,$headers);
 
}

function email_exists($email){
    $sql= "SELECT * FROM `users` WHERE email='$email'";
    $result = query($sql);

    if (row_count($result)>0) {
        return true;
    }else{
        return false;
    }
}
function username_exists($username){
     $sql= "SELECT * FROM `users` WHERE username='$username'";
    $result = query($sql);
    if (row_count($result)>0) {
        return true;
    }else{
        return false;
    }
}
/*               VALIDATION FUNCTIONS         */

function validare_user_registration(){



    if ($_SERVER["REQUEST_METHOD"]== "POST"){
        $min =3;
        $maximum =20;
        $errors=[];

        $first_name = clean($_POST["first_name"]);
        $last_name = clean($_POST["last_name"]);
        $username = clean($_POST["user_name"]);
        $email = clean($_POST["email"]);
        $password = clean($_POST["password"]);
        $confirm_password = clean($_POST["confirm_password"]);

        if (strlen($first_name)<3){
            $errors[]= "Your name caanot be less then {$min}";
        }
        if (strlen($first_name)>$maximum){
            $errors[]= "Your name caanot be Max then {$maximum}";
        }
         if (username_exists($username)) {
           $errors[]=  "Sorry that username already is registered";
        }

        if (strlen($username)<3){

            $errors[]= "Your Username caanot be less then {$min}";
        }
        if (strlen($username)>$maximum){
            $errors[]= "Your Username caanot be Max then {$maximum}";
        }

        if (email_exists($email)){
            $errors[]= "Sorry that email already is registered";
        }
        if (strlen($last_name)<3){
            $errors[]= "Your Lastname caanot be less then {$min}";
        }
        if (strlen($last_name)>$maximum){
            $errors[]= "Your Lastname caanot be less then {$maximum}";
        }
        if (empty($first_name)){
            $errors[]="First name must not be empty";
        }
        if ($password!==$confirm_password) {

           $errors[]="Your passwords do not Mutch";
        }
        if (!empty($errors)){
            foreach ($errors as $error){
             echo validation_errors($error);
            }
        }else{
            if(register_user($first_name,$last_name,$username,$email,$password)){
                set_message("<p class='bg-success text-center'>Please Check your email or spam folder for activation link</p>");
/*                echo $_SESSION["message"];*/
                redirect("index.php");
            }else{
                 set_message("<p class='bg-danger text-center'>Sorry we caanot active your accaount</p>");
/*                echo $_SESSION["message"];*/
                redirect("index.php");
            }
        }

    }// POST REQUEST

}


function register_user($first_name,$last_name,$username,$email,$password){

    $first_name =escape($first_name);
    $last_name =escape($last_name);
    $username =escape($username);
    $email =escape($email);
    $password =escape($password);




    if (email_exists($email)) {
        return false;
    }
    elseif(username_exists($username)) {
        return false;
    }else{
          $password =md5($password);
          $validation = md5($username.microtime());

            $sql ="INSERT INTO users(first_name,last_name,username,password,validation_code,active,email)";
            $sql.=" VALUES('$first_name','$last_name','$username','$password','$validation',0,'$email')";
            $result= query($sql);
            confirm($result);

            $subject ="activation account";
            $msg = "Please click link bleow to activation 

               http://localhost/logintut/activate.php?email=$email&code=$validation
            ";
            $headers="From: localhost";

            send_email($email,$subject,$msg,$headers);

           return true;
    }

}



function activate_user(){
    if ($_SERVER["REQUEST_METHOD"]=="GET") {
         
         if (isset($_GET['email'])) {
            $email = clean($_GET['email']);
            $activation = clean($_GET['code']);

            $sql ="SELECT id FROM users where email ='".escape($_GET['email'])."' AND validation_code= '".escape($_GET['code'])."'";
            $result = query($sql);
            confirm($result);
            if (row_count($result)>0){
                $sql2 = "UPDATE users SET active=1, validation_code=0 WHERE email='".escape($email)."' AND validation_code='".$activation."'";
                $result2 = query($sql2);
                confirm($result2);
                set_message("<p class='bg-danger'>Your account has been activated please login</p>") ;
                redirect("login.php");
            }else {
                 set_message("<p class='bg-danger'>sory you accaount could not be activated</p>") ;
                redirect("login.php");
            }
            
         }else {

         }
    }
}




function validare_user_login(){

        $min =3;
        $maximum =20;


    if ($_SERVER["REQUEST_METHOD"]== "POST"){
        
        $email = clean($_POST["email"]);
        $password = clean($_POST["password"]);
        $remember = isset($_POST["remember"]);


        if (empty($email)) {
            $errors[] = "Eemail Filed Cannot Be empty";
            echo  validation_errors();;
        }
        if (empty($password)) {
            $errors[] = "Password Filed Cannot Be empty";
            echo  validation_errors();;
        }
        else{

            if(login_user($email,$password,$remember)){
                redirect("admin.php");
            }else{
                echo validation_errors("YOur crediials ar not correct");
            }
        }

    }
}





function login_user($email,$password,$remember){

    $sql ="SELECT password , id FROM users WHERE email='".escape($email)."' AND active=1";

    $result = query($sql);

    if (row_count($result)>0) {

        $row = fetch_array($result);
        $db_password = $row["password"];

        if(md5($password)===$db_password){

            if ($remember=="on"){
                setcookie("email",$email,time()+86400);
            }

            $_SESSION['email']=$email;
            return true;

        }else{
            return false;
        }

        return true;
    }else {
        return false;
    }

}


function logged_in(){

    if(isset($_SESSION["email"]) || isset($_COOKIE["email"])){
        return true;
    }else {
        return false;
    }
}




