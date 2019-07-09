<?php
function reCaptcha($post) {
    // Ma clé privée
    $secret = "YOUR RECAPTCHA KEY"; //don't forget to change in all the pages that use the recaptcha
    // Paramètre renvoyé par le recaptcha
    $response = $post;
    // On récupère l'IP de l'utilisateur
    $remoteip = $_SERVER['REMOTE_ADDR'];

    $api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
        . $secret
        . "&response=" . $response
        . "&remoteip=" . $remoteip;

    return json_decode(file_get_contents($api_url), true);
}


?>