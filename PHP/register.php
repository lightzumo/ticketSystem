<?php
    require 'controlSession.php';
//controls if the users is logged
if (isset($_SESSION['login']))
{
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
    <title>Register</title>

    <script src="https://www.google.com/recaptcha/api.js" async=""></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="../CSS/styles.css" rel="stylesheet">

    <script>


        document.addEventListener('DOMContentLoaded', function () {
            let elems = document.querySelectorAll('.sidenav');
            let instances = M.Sidenav.init(elems, {});

            var elems1 = document.querySelectorAll('select');
            var instances1 = M.FormSelect.init(elems1, {});
        });


    </script>

</head>
<body>
<header>
    <nav>
        <div class=" nav-wrapper grey darken-1">
            <a href="../" class="brand-logo"><img style="height: 58px; margin-left: 5px"
                                                  src="../images/smartix2.png" alt="logo"></a>
            <a href="../" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down">
                <li><a href="../"><i class="material-icons left">home</i>Home</a></li>
                <li><a href="About"><i class="material-icons left">description</i>About</a></li>
                <li><a href="login"><i class="material-icons left">person</i>Login</a></li>
            </ul>
        </div>
    </nav>

    <ul class="sidenav" id="mobile-demo">
        <li><a href="../">Home</a></li>
        <li><a href="About">About</a></li>
        <li><a href="login">Login</a></li>
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


	//note: I used and mysqli in this project in order to show the teacher that I know how to use PDO and mysqli, yo
	//      also  see different methods to send data to the server and the reason is again to show the teacher that I know how to use different methods
        $servername = "YOUR SERVER";
        $dbname = "YOUR DATABASE";
        $username = "YOUR USERNAME";
        $password = "YOUR PASSWORD";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        //checks if the important inputs where filled
        if (!isset($_POST['language']) || !isset($_POST['question1']) || $_POST['answer1'] == '' || $_POST['password'] == '' || $_POST['username'] == '' || $_POST['firstname']
            == '' || $_POST['lastname'] == '') {

            echo "<script>

        window.addEventListener('load', function() {
         document.getElementById('response').innerHTML='Please, fill all the information where an \"*\" is located.';
        });
              </script>";

        } else {


            //Person data (Input)
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $username = htmlentities($_POST['username']); //
            $firstname = htmlentities($_POST['firstname']);//
            $lastname = htmlentities($_POST['lastname']);//
            $address = htmlentities($_POST['address']);
            $name = $firstname . " " . $lastname;
            $language = htmlentities($_POST['language']);
            $question = htmlentities($_POST['question1']);
            $answer = htmlentities($_POST['answer1']);

            //creation of the user
            $query="CALL sp_createUser( ?,?,?,?,?,?,?,@out)";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssis", $name,$address,$password,$username,$language,$question,$answer);
            $stmt->execute();
            $response='';
            $query="SELECT @out";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $stmt->bind_result($response);
            $stmt->fetch();

            $stmt->close();
            $conn->close();
            echo "<script>

                    window.addEventListener('load', function() {
                     document.getElementById('response').innerHTML='".$response."';
                    });
                          </script>";

                //create a directory for the user so that he can upload images in his tickets
                if ($response == "User created!"){
                    mkdir("../UserDirs/"."$username");
                }




        }
    }
}

?>

<div class="row formmid">
    <form id="loginForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="">
        <div class="row">
            <div class="input-field #26c6da-text">
                <input class="" id="username" name="username" type="text">
                <label class="#26c6da-text" for="username">Username *</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field #26c6da-text">
                <input class="" id="password" name="password" type="password">
                <label class="#26c6da-text" for="password">Password *</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field #26c6da-text">
                <input id="firstname" class="" name="firstname" type="text">
                <label class="#26c6da-text" for="firstname">First name *</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field #26c6da-text">
                <input id="lastname" class="" name="lastname" type="text">
                <label class="#26c6da-text" for="lastname">Last name *</label>
            </div>
        </div>



        <div class="row">
            <div class="input-field #26c6da-text">
                <input id="address" class="" name="address" type="text">
                <label class="#26c6da-text" for='address'>Address</label>
            </div>
        </div>


        <div class="row">
            <select id="language" name="language" class="icons">
                <option value="" disabled selected>Choose your language *</option>
                <?php


                $getLanguages = $PDO->prepare('CALL sp_getLanguages()');
                $getLanguages->execute();
                $languages = $getLanguages->fetchAll();
                foreach ($languages as $language) {
                    echo "<option value='" . $language['idLanguage'] . "' data-icon='../images/" . $language['dtName'] . ".png'>" . $language['dtName'] . "</option>";
                }

                ?>

            </select>
            <label>Images in select</label>
        </div>
        <div class="row">
            <select name="question1">
                <option value="" disabled selected>Select your Security question: *</option>

                <?php


                $servername = "mysql.hostinger.com";
                $username = "u124560394_oliwi";
                $password = "lightpw";
                $dbname = "u124560394_tisy2";
                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "CALL sp_getQuestions()";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // output data of each row

                    while($row = $result->fetch_assoc()) {

                        echo "<option value='" . $row['idQuestionNr'] . "'>" . $row['dtQuestion'] . "</option>";
                    }
                } else {
                    echo "0 results";
                }
                $conn->close();

                ?>
            </select>
            <label>Select your Security question: *</label>
        </div>
        <div class="row">
            <div class="input-field #26c6da-text">
                <input id="answer1" class="" name="answer1" type="text">
                <label class="#26c6da-text" for="answer1">Answer to question 1 *</label>
            </div>
        </div>
        <div class="g-recaptcha" data-sitekey="YOUR RECAPTCHA KEY"></div>
        <br>
        <button class="btn waves-effect waves-light cyan lighten-1" type="submit" name="submit">Submit
            <i class="material-icons right">send</i>
        </button>

    </form>
</div>


</body>
</html>