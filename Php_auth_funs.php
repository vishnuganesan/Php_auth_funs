<?php
$DB_servername = "localhost";
$DB_user = "root";
$DB_password = "";
$DB_table = "test";
// --------------------------------------------DB CONNECTION---------------------------------------
function connection(){
     global $DB_servername;
     global $DB_user;
     global $DB_password;
     global $DB_table;
     $conn = mysqli_connect($DB_servername,$DB_user,$DB_password,$DB_table); 
     if(!$conn) {
          return 0;
     }
     else{
         return $conn;
     }
}

//-----------------------------------------------------SIGNIN----------------------------------------

function signin($username,$password){
    $otp = rand(100000,999999);
    $sql = "INSERT INTO authentication (username,pass,otp) VALUES ('$username','$password','$otp')";
    $db = connection();
    $result = mysqli_query($db,$sql);
    if($result){
        echo "Success";
    }else{
        echo "Username Is Already Used";
    }
     
}

//-----------------------------------------------ACCOUNT ACTIVATE------------------------------------
function activite($otp,$username){
    $sql = "SELECT * FROM authentication WHERE username = '$username'";
    $conn = connection();
    $result1 = mysqli_query($conn,$sql);
    if(!mysqli_num_rows($result1) == 1){
        echo "Not Activation";
    }else{
        $fetch = mysqli_fetch_assoc($result1);
        if($otp == $fetch['otp']){
             echo "Activation";
             $sql1 = "UPDATE authentication set active = 1 where username = '$username'";
             mysqli_query($conn,$sql1);
             newcookies($username);
        }else{
             echo "Wrong OTP";
        
        }
    }
}



function newcookies($username){
    $sql = "SELECT * FROM cookies WHERE cookiename = '$username'";
    $conn = connection();
    $res = mysqli_query($conn,$sql);
    // echo (mysqli_num_rows($res));
    $row = (mysqli_num_rows($res));
    if($row == 0){
        $time = time();
        $DB_expiry = time()+(60*60);
        setcookie("__USER",$username,time()+(60*60),'/');
        $conn = connection();
        $sql = "INSERT INTO cookies (cookiename,starttime,expiry) VALUES ('$username','$time','$DB_expiry')";
        $result = mysqli_query($conn,$sql);
        echo "0 ROWS";
    }
    else{
        $conn = connection();
        $sql = "DELETE FROM cookies WHERE cookiename = '$username'";
        $result = mysqli_query($conn,$sql);
        $time = time();
        $DB_expiry = time()+(60*60);
        setcookie("__USER",$username,time()+(60*60),'/');
        $sql1 = "INSERT INTO cookies (cookiename,starttime,expiry) VALUES ('$username','$time','$DB_expiry')";
        $result1 = mysqli_query($conn,$sql1);
        echo "more ROws";
        // loginactive($username);
    }

//--------------------------------------------------LOGIN------------------------------------

    function login($username,$password){
        $sql = "SELECT * FROM authentication WHERE username = '$username'";
        $conn = connection();
        $result1 = mysqli_query($conn,$sql);
        if (!mysqli_num_rows($result1) == 1) {
            echo "User Not Exist";
        }
        else{
            if(passwordcheck($username,$password)){
                $sql = "SELECT * FROM cookies WHERE cookiename = '$username'";
                $conn = connection();
                newcookies($username);
                $result1 = mysqli_query($conn,$sql);
                // $time =  time()+(60*60);
                $fetch = mysqli_fetch_assoc($result1);
                // echo $fetch['expiry']. '-----------'.time();
                if($fetch['expiry'] > time() && active($username) == 1){
                              return 1;
                              
                }
                else{
                                // logout($username);
                                return 0;
                                
                }
                 
            }else{
                 return 0; 
            }
           
        }   
    }

//--------------------------------------PASSWORD CHECKING------------------------------
    function passwordcheck($username,$password){
        $sql = "SELECT * FROM authentication WHERE username = '$username'";
        $conn = connection();
        $result1 = mysqli_query($conn,$sql);
        $fetch = mysqli_fetch_assoc($result1);
        if($password == $fetch['pass']){
            return 1;
        }
        else{
            return 0;
        }
    }

//-----------------------------------CHECKING ACCOUNT ACTIVATED------------------------

    function active($username){
        $sql = "SELECT * FROM authentication WHERE username = '$username'";
        $conn = connection();
        $result1 = mysqli_query($conn,$sql);
        $fetch = mysqli_fetch_assoc($result1);
        return $fetch['active'];
    
    }

//-----------------------------------------------LOGOUT----------------------------------

    function logout($username){
        $conn = connection();
        $sql1 = "UPDATE authentication set active = 0 where username = '$username'";
        mysqli_query($conn,$sql1);
    }