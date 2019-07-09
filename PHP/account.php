<?php
require 'controlSession.php';
//controls if the users is logged

if (!isset($_SESSION['login'])) {

    header("Location:../");
    die();
}
require 'functions.php';
require './cred.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="../CSS/styles.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>


        document.addEventListener('DOMContentLoaded', function () {
            let elems = document.querySelectorAll('.sidenav');
            let instances = M.Sidenav.init(elems, {});

            let elems1 = document.querySelectorAll('select');
            let instances1 = M.FormSelect.init(elems1, {});

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

                <li><a href="login"><i class="material-icons left">person</i>Account</a></li>
            </ul>
        </div>
    </nav>

    <ul class="sidenav" id="mobile-demo">
        <li><a href="../">Home</a></li>
        <li><a href="About">About</a></li>
        <li><a class="active" href="account">Account</a></li>
    </ul>
</header>
<div id="response"></div>
<h3 class="center-align">Account Settings</h3>
<?php
//deletes the users directory if he deletes account
//https://stackoverflow.com/questions/7288029/php-delete-directory-that-is-not-empty/7288055
function rmdir_recursive($dir)
{
    foreach (scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) continue;
        if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
        else unlink("$dir/$file");
    }
    rmdir($dir);
}

//logs the user out
if (isset($_POST['logout'])) {

    header("Location: logout.php");
}

if (isset($_POST['delete'])) {

    //deletes the account and the users directory
    $query = 'CALL sp_deleteUser(:userlogged, @userDeletedMessage)';
    $stmt = $PDO->prepare($query);

    $stmt->bindParam(':userlogged', $_SESSION['idPerson'], PDO::PARAM_INT);
    $stmt->execute();


    $response = '';
    $query = "SELECT @userDeletedMessage";
    $stmt = $PDO->prepare($query);
    $stmt->execute();
    $response = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($response["@userDeletedMessage"] == 'User Deleted.') {
        rmdir_recursive("../UserDirs/" . $_SESSION['user']);
        header("Location: logout.php");
    }


}


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

        if (!isset($_POST['question1']) || $_POST['answer1'] == '' || $_POST['password'] == '' || $_POST['username'] == '') {

            echo "<script>

        window.addEventListener('load', function() {
         document.getElementById('response').innerHTML='Please, fill all the information.';
        });
              </script>";

        } else {

            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $question = $_POST['question1'];
            $answer = $_POST['answer1'];

            //changes the password
            $query = "CALL sp_changePasswordCustomer(:username, :answer, :question, :password, @out) ";
            $stmt = $PDO->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':answer', $answer, PDO::PARAM_STR);
            $stmt->bindParam(':question', $question, PDO::PARAM_INT);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->execute();

            //message for the user
            $query1 = "SELECT @out";
            $stmt = $PDO->prepare($query1);
            $stmt->execute();
            $allData = $stmt->fetchAll(\PDO::FETCH_ASSOC);


            echo "<script>window.addEventListener('load', function() {
                      document.getElementById('response').innerHTML='" . $allData[0]['@out'] . "';
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
            <select name="question1" id="question1">
                <option value="" disabled selected>Select your Security question:</option>
                <?php


                $getQuestions = $PDO->prepare("CALL sp_getQuestions()");
                $getQuestions->execute();
                $questions = $getQuestions->fetchAll();

                foreach ($questions as $question) {
                    echo " <option value='" . $question['idQuestionNr'] . "'>" . $question['dtQuestion'] . "</option>";

                }
                $PDO = null;
                $questions = null;
                $getQuestions = null;
                ?>
            </select>
            <label>Select your Security question:</label>
        </div>


        <div class="row">
            <div class="input-field #26c6da-text">
                <input class="" id="answer1" name="answer1" type="text">
                <label class="#26c6da-text" for="answer1">Answer to question 1</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field #26c6da-text">
                <input class="" id="password" name="password" type="password">
                <label class="#26c6da-text" for="password">New Password</label>
            </div>
        </div>

        <div class="g-recaptcha" data-sitekey=""
             style="margin-bottom: 20px;"></div>

        <button class="btn waves-effect waves-light cyan lighten-1" type="submit" name="submit">Submit
            <i class="material-icons right">send</i>
        </button>

        <button class="btn waves-effect waves-light cyan lighten-1" type="submit" name="logout">Logout
            <i class="material-icons right">send</i>
        </button>
        <button style="" class="btn waves-effect waves-light red darken-4" type="submit" name="delete">Delete Account
            <i class="material-icons right">delete_forever</i>
        </button>


    </form>
</div>

</body>
</html>