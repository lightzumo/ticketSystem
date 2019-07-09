<?php require 'functions.php'; require './cred.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>Create Ticket</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="../CSS/styles.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('textarea').characterCounter();
        });

    </script>

</head>
<body>
<header>
    <nav>
        <div class=" nav-wrapper grey darken-1">
            <a href="../" class="brand-logo"><img style="height: 58px; margin-left: 5px" alt="logo" src="../images/smartix2.png"></a>
            <a href="../" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down">
                <li><a href="../"><i class="material-icons left">home</i>Home</a></li>
                <li><a href="About"><i class="material-icons left">description</i>About</a></li>
                <li><a href="login"><i class="material-icons left">person</i>Login</a></li>
            </ul>
        </div>
    </nav>

    <div class="row">
        <div class="input-field col s12">
            <textarea id="textarea2" class="materialize-textarea" data-length="120"></textarea>
            <label for="textarea2">Textarea</label>
        </div>
    <ul class="sidenav" id="mobile-demo">
        <li><a href="../">Home</a></li>
        <li><a href="About">About</a></li>
        <li><a href="login">Login</a></li>
    </ul>
</header>


</body>
</html>