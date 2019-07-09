<?php
require 'controlSession.php';
require 'functions.php';
require './cred.php';

//controls if the users is logged
if (!isset($_SESSION['login'])) {
    header("Location: ../");
    die();
}

if (!isset($_SESSION['idTechnician'])) {
    if (isset($_SESSION['idCustomer'])) {
        $currentUser = $_SESSION['idPerson'];

        if (!isset($_SESSION['ticket'])) {
            $_SESSION['ticket'] = $_GET['ticket'];
        } elseif (!empty($_GET['ticket'])) {
            $_SESSION['ticket'] = $_GET['ticket'];
        }
        if (!isset($_SESSION['ticketLang'])) {
            $_SESSION['ticketLang'] = $_GET['lang'];
        } elseif (!empty($_GET['lang'])) {
            $_SESSION['ticketLang'] = $_GET['lang'];
        }

        $query1 = "SELECT fiPerson FROM tblTicket WHERE idTicket= ?";
        $stmt = $PDO->prepare($query1);
        $stmt->execute([$_SESSION['ticket']]);

        $allData = $stmt->fetch();

        if ($allData == null) {
            header("Location: ../");
            die();
        }

        $query1 = "SELECT fiPerson FROM tblTicket WHERE fiPerson= ? AND idTicket = ?";
        $stmt = $PDO->prepare($query1);
        $stmt->execute([$_SESSION['idPerson'], $_SESSION['ticket']]);

        $allData = $stmt->fetch();

        if ($allData == null) {
            header("Location: ../");
            die();
        }


    }
} else {
    $currentUser = $_SESSION['idPerson'];

    if (!isset($_SESSION['ticket'])) {
        $_SESSION['ticket'] = $_GET['ticket'];
    } elseif (!empty($_GET['ticket'])) {
        $_SESSION['ticket'] = $_GET['ticket'];
    }

    if (!isset($_SESSION['ticketLang'])) {
        $_SESSION['ticketLang'] = $_GET['lang'];
    } elseif (!empty($_GET['lang'])) {
        $_SESSION['ticketLang'] = $_GET['lang'];
    }
    $query1 = "SELECT fiPerson FROM tblTicket WHERE idTicket= ?";
    $stmt = $PDO->prepare($query1);
    $stmt->execute([$_SESSION['ticket']]);

    $allData = $stmt->fetch();

    if ($allData == null) {
        header("Location: ../");
        die();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>Post</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link href="../CSS/styles.css" rel="stylesheet">
    <script src="../JS/login.js"></script>
    <style>

        .post {
            margin: 0 auto;
            margin-top: 20px;
            width: 80%;
            border: 1px solid grey;
            border-radius: 25px;
            padding: 10px;
        }

        .open {
            font-weight: bold;
        }

        .title {
            font-weight: bold;
            text-decoration: underline;
        }

        .description {

            margin-top: 10px;
        }
    </style>

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
                <li><a href="account"><i class="material-icons left">person</i>Account</a></li>
            </ul>
        </div>
    </nav>

    <ul class="sidenav" id="mobile-demo">
        <li><a href="../">Home</a></li>
        <li><a href="About">About</a></li>
        <li><a href="account">Account</a></li>
    </ul>
</header>
<?php
if (isset($_POST['submit'])) {
    $text = htmlentities($_POST['description']);
    $currentUser = $_SESSION['idPerson'];
    $idTicket = $_SESSION['ticket'];
    if (!isset($_SESSION['language'])) {
        $lang = "en";
    } else {
        $lang = $_SESSION['language'];
    }


    $query1 = "CALL sp_createPost(:TicketNr, :PersonNr, :langOfUser,:answer, @out)";

    //$query1 = 'INSERT INTO tblPost (fiTicket, fiPerson) VALUES (:TicketNr, :PersonNr)';
    $stmt = $PDO->prepare($query1);
    $languageTest = $_SESSION['ticketLang'];
    $stmt->bindParam(':TicketNr', $idTicket, PDO::PARAM_STR);
    $stmt->bindParam(':PersonNr', $currentUser, PDO::PARAM_INT);
    $stmt->bindParam(':langOfUser', $languageTest, PDO::PARAM_STR);
    $stmt->bindParam(':answer', $text, PDO::PARAM_STR);
    $stmt->execute();

    $query1 = "SELECT @out";
    $stmt = $PDO->prepare($query1);
    $stmt->execute();
    $allData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    if ($allData[0]['@out'] === NULL) {

        //select last ticket
        $query1 = 'SELECT idPost FROM tblPost ORDER BY idPost DESC LIMIT 1';
        $stmt = $PDO->prepare($query1);
        $stmt->execute();
        $result = $stmt->fetch();

        $idlastPost = $result['idPost'];

        require "translatePost.php";

        $translationFR = translateTicket($_SESSION['ticketLang'], "fr", $text);
        $translationDE = translateTicket($_SESSION['ticketLang'], "de", $text);
        $translationEN = translateTicket($_SESSION['ticketLang'], "en", $text);


        if ($_SESSION['ticketLang'] == "fr") {

            $query1 = 'CALL sp_TranslatePost(:idPost, "en", :description)';
            $stmt = $PDO->prepare($query1);
            $stmt->bindParam(':idPost', $idlastPost, PDO::PARAM_INT);
            $stmt->bindParam(':description', $translationEN, PDO::PARAM_STR);
            $stmt->execute();


            $query1 = 'CALL sp_TranslatePost(:idPost, "de", :description)';
            $stmt = $PDO->prepare($query1);
            $stmt->bindParam(':idPost', $idlastPost, PDO::PARAM_INT);
            $stmt->bindParam(':description', $translationDE, PDO::PARAM_STR);
            $stmt->execute();

        }
        if ($_SESSION['ticketLang'] == "de") {

            $query1 = 'CALL sp_TranslatePost(:idPost, "en", :description)';
            $stmt = $PDO->prepare($query1);
            $stmt->bindParam(':idPost', $idlastPost, PDO::PARAM_INT);
            $stmt->bindParam(':description', $translationEN, PDO::PARAM_STR);
            $stmt->execute();


            $query1 = 'CALL sp_TranslatePost(:idPost, "fr", :description)';
            $stmt = $PDO->prepare($query1);
            $stmt->bindParam(':idPost', $idlastPost, PDO::PARAM_INT);
            $stmt->bindParam(':description', $translationFR, PDO::PARAM_STR);
            $stmt->execute();


        }
        if ($_SESSION['ticketLang'] == "en") {

            $query1 = 'CALL sp_TranslatePost(:idPost, "de", :description)';
            $stmt = $PDO->prepare($query1);
            $stmt->bindParam(':idPost', $idlastPost, PDO::PARAM_INT);
            $stmt->bindParam(':description', $translationDE, PDO::PARAM_STR);
            $stmt->execute();

            $query1 = 'CALL sp_TranslatePost(:idPost, "fr", :description)';
            $stmt = $PDO->prepare($query1);
            $stmt->bindParam(':idPost', $idlastPost, PDO::PARAM_INT);
            $stmt->bindParam(':description', $translationFR, PDO::PARAM_STR);
            $stmt->execute();
        }


        if ($_SESSION['TicketOwner'] != $currentUser) {
            //select the latest status of the ticket

            $query1 = 'CALL sp_ChangeTicketStatus("Ongoing", :TicketNr1, @TicketStatusMessage)';
            $stmt = $PDO->prepare($query1);
            $stmt->bindParam(':TicketNr1', $idTicket, PDO::PARAM_INT);
            $stmt->execute();

            $query1 = 'CALL sp_CheckSubscription(:TicketNr1, @SubscriptionMessage)';
            $stmt = $PDO->prepare($query1);
            $stmt->bindParam(':TicketNr1', $idTicket, PDO::PARAM_INT);
            $stmt->execute();


            $query1 = 'SELECT @SubscriptionMessage';
            $stmt = $PDO->prepare($query1);
            $stmt->execute();
            $allData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if ($allData[0]['@SubscriptionMessage'] != 'No Subscription') {

                $message = $allData[0]['@SubscriptionMessage'];


                if (strpos($message, '@') !== false) {
                    require 'sendMail.php';
                    $response = mailPear($message, $idTicket);
                    if ($response != 'Alert message successfully sent!') {
                        echo "<script>

                    window.addEventListener('load', function() {
                     document.getElementById('response').innerHTML='" . $response . "';
                    });
                          </script>";

                    }
                } else {
                    require 'twitterSubPost.php';
                    $response = sendMessageTo($message, $idTicket);
                    if ($response != 'worked') {
                        echo "<script>

                    window.addEventListener('load', function() {
                     document.getElementById('response').innerHTML='" . $response . "';
                    });
                          </script>";
                    }
                }
            }
        }
    } else {
        echo "<script>

                    window.addEventListener('load', function() {
                     document.getElementById('response').innerHTML='" . $allData[0]['@out'] . "';
                    });
                          </script>";
    }

}


// show


$idTicket = $_SESSION['ticket'];
$languageOfTicket = $_SESSION['ticketLang'];
//check if he has images
$query2 = "CALL sp_searchTicketWithAttachments(:ticketSearch,:language )";
//without images
$query1 = 'CALL sp_searchTicketNoAttachments(:ticketSearch, :language)';
$stmt = $PDO->prepare($query2);
$stmt->bindParam(':ticketSearch', $idTicket, PDO::PARAM_STR);
$stmt->bindParam(':language', $languageOfTicket, PDO::PARAM_STR);
$stmt->execute();

$allData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

if ($allData != null) {


    $_SESSION['TicketOwner'] = $allData[0]['fiPerson'];
    echo "<div  class=' post'>
                  <div class='open'>Ticket opend by " . $allData[0]['dtUsername'] . " on the: " . $allData[0]['dtCreationTime'] . "</div>
                  <div class='open'>Ticket Nr: " . $allData[0]['idTicket'] . "</div><br>";
    echo "<div class='title' id='title'>Title: " . $allData[0]['dtTitle'] . "</div>";
    echo "<div class='description' id='description'> " . $allData[0]['dtDescription'] . "</div><br>";
    echo "Attachements: ";
    foreach ($allData as $info) {
        echo "<a target='_blank' href='../" . $info['dtURL'] . "'>" . $info['dtURL'] . "</a><br>";
    }


    echo "</div>";


    $query1 = "CALL sp_searchPost(:currentTicket, :language)";

    $stmt = $PDO->prepare($query1);

    $stmt->bindParam(':currentTicket', $idTicket, PDO::PARAM_STR);
    $stmt->bindParam(':language', $languageOfTicket, PDO::PARAM_STR);
    $stmt->execute();

    $allData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    if ($allData != null) {
        foreach ($allData as $tickets => $info) {
            echo "<div  class=' post'>
                  <div class='open'> " . $info['dtUsername'] . "</div><br>";
            echo "<div class='description' id='description'>" . $info['dtText'] . "</div><br></div>";

        }
    } else {
        echo '';
    }
} else {

    $idTicket = $_SESSION['ticket'];
    $query1 = 'CALL sp_searchTicketNoAttachments(:ticketSearch, :language)';
    $stmt = $PDO->prepare($query1);

    $stmt->bindParam(':ticketSearch', $idTicket, PDO::PARAM_STR);
    $stmt->bindParam(':language', $languageOfTicket, PDO::PARAM_STR);
    $stmt->execute();

    $allData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    if ($allData != null) {


        foreach ($allData as $tickets => $info) {

            $_SESSION['TicketOwner'] = $info['fiPerson'];
            echo "<div  class=' post'>
                  <div class='open'>Ticket opend by " . $info['dtUsername'] . " on the: " . $info['dtCreationTime'] . "</div>
                  <div class='open'>Ticket Nr: " . $info['idTicket'] . "</div><br>";
            echo "<div class='title' id='title'>Title: " . $info['dtTitle'] . "</div><br>";
            echo "<div class='description' id='description'> " . $info['dtDescription'] . "</div><br></div>";
        }

        $query1 = "CALL sp_searchPost(:currentTicket, :language)";
        $stmt = $PDO->prepare($query1);

        $stmt->bindParam(':currentTicket', $idTicket, PDO::PARAM_STR);
        $stmt->bindParam(':language', $languageOfTicket, PDO::PARAM_STR);
        $stmt->execute();

        $allData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if ($allData != null) {
            foreach ($allData as $tickets => $info) {
                echo "<div  class=' post'><div class='open'>" . $info['dtUsername'] . "</div><br>";
                echo "<div class='description' id='description'> " . $info['dtText'] . "</div><br></div>";

            }
        } else {
            echo '';
        }
    }
}

$query1 = "CALL  sp_getStatusOfTicket (:TicketNr)";

//$query1 = 'INSERT INTO tblPost (fiTicket, fiPerson) VALUES (:TicketNr, :PersonNr)';
$stmt = $PDO->prepare($query1);
$stmt->bindParam(':TicketNr', $idTicket, PDO::PARAM_STR);
$stmt->execute();
$allData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

if ($allData[0]['dtStatus'] != "Closed") {

    echo "
<form id=\"loginForm\" method=\"post\" action='" . $_SERVER['PHP_SELF'] . "' class='row formmid' enctype=\"multipart/form-data\">
          <div class=\"row\">
        <div class=\"input-field col s12\">
          <textarea id=\"textarea1\" name='description' data-length=\"255\" class=\"materialize-textarea\"></textarea>
          <label for=\"textarea1\">Answer!</label>
        </div>
      </div>

          <button class=\"btn waves-effect waves-light cyan lighten-1\" type=\"submit\" name=\"submit\">Submit
            <i class=\"material-icons right\">send</i>
        </button>
                </form>
        

";
}

if ($_SESSION['TicketOwner'] != $_SESSION['idPerson']) {


    echo " <form id=\"loginForm\" method=\"post\" action='" . $_SERVER['PHP_SELF'] . "' class='row formmid' enctype=\"multipart/form-data\">
    <div class=\" col s6\">
            <select name=\"statusOption\" id=\"statusOption\">
                <option value=\"\" disabled selected>Change Ticket Status</option>
                <option value=\"Open\">Open</option>
                <option value=\"Ongoing\">Ongoing</option>
                <option value=\"Closed\">Closed</option>
            </select>
            <label>Change Ticket Status</label>
    </div>
    <button class=\"btn waves-effect waves-light cyan lighten-1\" type=\"submit\" name=\"ChangeStatus\">Change Status
            <i class=\"material-icons right\">send</i>
    </button>
            </form>";


} else {


    echo "<form id=\"loginForm\" method=\"post\" action='" . $_SERVER['PHP_SELF'] . "' class='row formmid' enctype=\"multipart/form-data\">
<button class=\"btn waves-effect waves-light cyan lighten-1\" type=\"closeTicket\" name=\"closeTicket\">Close Ticket
            <i class=\"material-icons right\">send</i>
        </button>
                </form>";
}

if (isset($_POST['closeTicket'])) {

    $query1 = 'CALL sp_ChangeTicketStatus("Closed", :TicketNr1, @TicketStatusMessage)';
    $stmt = $PDO->prepare($query1);
    $stmt->bindParam(':TicketNr1', $idTicket, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: ../");
}

if (isset($_POST['ChangeStatus'])) {

    if (isset($_POST['statusOption'])) {
        $subOption = $_POST['statusOption'];

        $query1 = 'CALL sp_ChangeTicketStatus(:status, :TicketNr1, @TicketStatusMessage)';
        $stmt = $PDO->prepare($query1);
        $stmt->bindParam(':status', $subOption, PDO::PARAM_STR);
        $stmt->bindParam(':TicketNr1', $idTicket, PDO::PARAM_INT);
        $stmt->execute();

        $query1 = 'SELECT @TicketStatusMessage';
        $stmt = $PDO->prepare($query1);
        $stmt->execute();
        $response = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $response = $response[0]['@TicketStatusMessage'];
        echo "<script>

                    window.addEventListener('load', function() {
                     document.getElementById('response').innerHTML='" . $response . "';
                    });
                          </script>";


    }

}

?>
<div id="response"></div>

</body>
</html>