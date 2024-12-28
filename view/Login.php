<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<?php
include "../Model/conx.php";
session_start();
 if($_SERVER["REQUEST_METHOD"] === "POST"){
    $erors = "";
    foreach($_POST as $key=>$value){
        $$key = $value;
    }
    if(empty($usern)||empty($pass)){
        $erors = 'There is an empty fild';
    }
    else{
        $selected = Login($usern,$pass);
        if( !$selected ){
           $erors = 'User name or password are not exists';
       }
    }
    if(empty($erors)){
        $_SESSION['username'] = $usern;
        header('location:Profile.php');
    }
   else{
    
        setcookie("errors",$erors,time()+24*60*60);
        $_COOKIe["errors"] = $erors;
        header("location:Login.php") ;
   }
 }
?>
<body>
    <form action="" method='POST'>
       < 
     username   <input type="text" name='usern'  ><br>
     password <input type="password" name='pass' ><br>
     <input type="submit" value="Log in"> 
     <?php if(isset($_COOKIE['errors'])): ?>
        <div>
            <p><?php echo $_COOKIE['errors'] ?></p>
        </div>
        <?php setcookie("errors", "", time() + 24*60*60); ?>
        <?php endif ?>
    </form>
</body>
</html>