<?php
//Could not add to the project due time
require "lib/autoload.php";


use Abraham\TwitterOAuth\TwitterOAuth;
// Get everything you need from the dev.twitter.com/apps page

//send the private message
function sendMessageTo($id, $ticket){
	
    //GO generate your own keys at Twitter API website
    $consumer_key = '';
    $consumer_secret = '';
    $oauth_token = '';
    $oauth_token_secret = '';

// Initialize the connection
    $connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);

//data
    $data = [
        'event' => [
            'type' => 'message_create',
            'message_create' => [
                'target' => [
                    'recipient_id' =>"".$id.""
                ],
                'message_data' => [
                    'text' => 'Hello,\n Someone answered to your ticket number: '.$ticket.'! Check the answer here: http://lightroyal.fun'
                ]
            ]
        ]
    ];
$result = $connection->post('direct_messages/events/new', $data, true);
    echo json_encode($result,JSON_PRETTY_PRINT);
    if ($connection->getLastHttpCode() == 200) {
        return "worked";
    } else {
        return $connection->getLastHttpCode();
    }
}

//gets the id of the twitter user
function getIDofUser($username){
    //GO generate your own keys at Twitter API website
    $consumer_key = '';
    $consumer_secret = '';
    $oauth_token = '';
    $oauth_token_secret = '';

    // Initialize the connection
    $connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);

    $dat = ['screen_name'=>$username];
    $result1 = $connection->get('users/show', $dat, true);
    $value = get_object_vars($result1);
    return $value['id'];

}


?>