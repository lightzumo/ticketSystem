<?php
require_once "Mail.php";
function mailPear($email, $ticket) {
    $to = $email;
    $body = "Hello,\n Someone answered to your ticket number: ".$ticket."! Check the answer here: http://lightroyal.fun";
    $headers =
        array (
            'From' => "YOUR USERNAME <YOUR EMAIL>",
            'To' => $to,
            'Subject' => "Ticket subscription"
        );
    $smtp = Mail::factory('smtp',
        array (
            'host' => "YOUR SMTP HOST",
            'port' => 587,
            'auth' => true,
            'username' => "YOUR EMAIL",
            'password' => "YOUR PASSWORD"
        )
    );

    $mail = $smtp->send($to, $headers, $body);

    if (PEAR::isError($mail)) {
        return "<p>" . $mail->getMessage() . "</p>";
    } else {
        return "Alert message successfully sent!";
    }
}
