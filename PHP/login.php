<?php
require 'controlSession.php';
//controls if the users is logged
if (isset($_SESSION['login'])) {
    header("Location: ../");
    die();
}

require 'functions.php';
require './cred.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>Login</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link href="../CSS/styles.css" rel="stylesheet">
    <script src="../JS/login.js"></script>


    <script>


        document.addEventListener('DOMContentLoaded', function () {
            let elems = document.querySelectorAll('.sidenav');
            let instances = M.Sidenav.init(elems, {});
        });

    </script>

</head>
<body>
<header>
    <nav>
        <div class=" nav-wrapper grey darken-1">
            <a href="../" class="brand-logo"><img alt="logo" style="height: 58px; margin-left: 5px"
                                                  src="../images/smartix2.png"></a>
            <a href="../" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down">
                <li><a href="../"><i class="material-icons left">home</i>Home</a></li>
                <li><a href="About"><i class="material-icons left">description</i>About</a></li>

                <?php
                if (isset($_SESSION['login'])) {

                    echo "<li class=\"active\"><a href=\"account\"><i class=\"material-icons left\">person</i>Account</a></li>";

                } else {
                    echo "<li class=\"active\"><a href=\"login\"><i class=\"material-icons left\">person</i>Login</a></li>";
                }

                ?>

            </ul>
        </div>
    </nav>

    <ul class="sidenav" id="mobile-demo">
        <li><a href="../">Home</a></li>
        <li><a href="About">About</a></li>

        <?php
        if (isset($_SESSION['login'])) {

            echo "<li class=\"active\"><a href=\"account\">Account</a></li>";

        } else {
            echo "<li class=\"active\"><a href=\"login\">Login</a></li>";
        }

        ?>

    </ul>
</header>
<div id="response"></div>
<?php


if (isset($_POST['submit'])) {

//Check if the captcha was correct
    $decode = reCaptcha($_POST['g-recaptcha-response']);
    if ($decode['success'] != true) {

        //message for the user
        echo "<script>

        window.addEventListener('load', function() {
         document.getElementById('response').innerHTML='Please verify if you\'re not a robot!';
        });
              </script>";

    } else {

        //User input
        $password = $_POST['password'];
        $username = $_POST['username'];

        //Query to get the password

/*        $query= "CALL sp_selectPassword(:username, @password, @idperson)";
        $stmt = $PDO->prepare($query1);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        $stmt->execute();


        $query= "SELECT @password , @person;";
        $stmt = $PDO->prepare($query1);
        $stmt->execute();
        $allData = $stmt->fetch();

        var_dump($allData);*/

       $query1 = 'SELECT dtPassword, idPerson FROM tblPerson WHERE dtUsername = :username';
        $stmt = $PDO->prepare($query1);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        $stmt->execute();

        $allData = $stmt->fetch();

        //Verify the login
        if ($allData != null && password_verify($password, $allData["dtPassword"])) {

            $_SESSION['login'] = true;
            $_SESSION['user'] = $username;
            $_SESSION['idPerson'] = $allData["idPerson"];
           // $_SESSION['language'] = $allData["fiLanguage"];
            //Controls if the user is a Customer or a technician
            $query1 = 'SELECT idCustomer, fiLanguage FROM tblCustomer WHERE fiPerson = ?';
            $stmt = $PDO->prepare($query1);
            $stmt->execute([$_SESSION['idPerson']]);

            $allData = $stmt->fetch();

            if ($allData != null) {
                $_SESSION['idCustomer'] = $allData["idCustomer"];
                $_SESSION['language'] =   $allData["fiLanguage"];
            } else {
                $_SESSION['idTechnician'] = $_SESSION['idPerson'];
                $_SESSION['language'] =   "en";
            }


            header("Location:../index.php");

        } else {
            echo "<script>window.addEventListener('load', function() {
         document.getElementById('response').innerHTML='Username or Password are wrong!';
        });</script>";

        }
    }
}


?>


<div class="row formmid">
    <form id="loginForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="">
        <div class="row">
            <div class="input-field #26c6da-text">
                <input class="" id="username" name="username" type="text">
                <label class="#26c6da-text" for="username">Username</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field #26c6da-text">
                <input class="" id="password" name="password" type="password">
                <label class="#26c6da-text" for="password">Password</label>
            </div>
        </div>
        <div class="g-recaptcha" data-sitekey=""
             style="margin-bottom: 20px;"></div>
        <button class="btn waves-effect waves-light cyan lighten-1" type="submit" name="submit">Submit
            <i class="material-icons right">send</i>
        </button>
        <button id="forgotPassword" class="btn waves-effect waves-light cyan lighten-1" type="submit" name="action">
            Forgot password
        </button>

        <button id="register" class="btn right waves-effect waves-light cyan lighten-1" type="submit" name="action">
            Register
        </button>
    </form>
</div>

</body>
</html>