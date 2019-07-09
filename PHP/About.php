<?php
require 'controlSession.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>About</title>
    <link type="text/css" rel="stylesheet" href="./CSS/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>


    </style>
    <script>


        document.addEventListener('DOMContentLoaded', function() {
            let elems = document.querySelectorAll('.sidenav');
            let instances = M.Sidenav.init(elems, {});
        });

    </script>

</head>
<body>
<header>
    <nav>
        <div class=" nav-wrapper grey darken-1">
            <a href="../" class="brand-logo"><img  alt="logo" style="height: 58px;margin-left: 5px" src="../images/smartix2.png"></a>
            <a href="../" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down">
                <li><a href="../"><i class="material-icons left">home</i>Home</a></li>



                <li class="active"><a href="About"><i class="material-icons left">description</i>About</a></li>

                <?php

                if (isset($_SESSION['login'])){
                    echo "<li><a href=\"account\"><i class=\"material-icons left\">person</i>Account</a></li>";
                }else{
                    echo "<li><a href=\"login\"><i class=\"material-icons left\">person</i>Login</a></li>";
                }

                ?>


            </ul>
        </div>
    </nav>

    <ul class="sidenav" id="mobile-demo">
        <li><a href="../">Home</a></li>
        <li class="active"><a href="About">About</a></li>
        <?php

        if (isset($_SESSION['login'])){
            echo " <li><a href=\"account\">Account</a></li>";
        }else{
            echo " <li><a href=\"login\">Login</a></li>";
        }

        ?>

    </ul>
</header>


<h1 class="center-align">Welcome to the SmarTix</h1>

<p class="flow-text center-align">Welcome to the SmarTix, this application is a so called Ticket System Aplication.<br>
    This application was developped by Wilson Silva, student of the BTSi during his school Project in SERSS and CREBA.</p>

<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2584.7022819728713!2d6.1236107160771045!3d49.622217179369656!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47954f27e3a5d0ab%3A0x1a3ff4801fa9a3a5!2sSchool+Des+Arts+Et+M%C3%A9tiers!5e0!3m2!1sen!2slu!4v1552418606994"
        width="600" height="450" style="display: block; border:0;margin-left: auto;margin-right: auto; margin-top: 30px;"  allowfullscreen=""></iframe>



</body>
</html>