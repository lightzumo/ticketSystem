<?php
//require 'PHP/controlSession.php';
session_start();


require 'PHP/functions.php';
require 'PHP/cred.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>Home</title>
    <link type="text/css" rel="stylesheet" href="./CSS/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <style>
        .ticket:hover {
            background-color: #d9d9d9 !important;
            cursor: pointer;
        }


    </style>
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            let elems = document.querySelectorAll('.sidenav');
            let instances = M.Sidenav.init(elems, {});

            let elems1 = document.querySelectorAll('select');
            let instances1 = M.FormSelect.init(elems1, {});

        });

        function openPost(idTicket) {
            window.location.replace("PHP/post.php?ticket=" + idTicket);
        }

        //when done
        function openPostLang(idTicket, language) {
            window.location.replace("PHP/post.php?ticket=" + idTicket + "&lang="+ language);
        }



    </script>

</head>
<body>
<header>
    <nav>
        <div class=" nav-wrapper grey darken-1">
            <!-- <a href="index.php" class="brand-logo"><img style="height: 58px;margin-left: 5px" src="images/ltamLogo1.png"></a>-->
            <a href="" class="brand-logo"><img style="height: 58px;margin-left: 5px" alt="logo"
                                               src="images/smartix2.png">
                <a href="" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul class="right hide-on-med-and-down">
                    <li class="active"><a href=""><i class="material-icons left">home</i>Home</a></li>
                    <li><a href="./PHP/About"><i class="material-icons left">description</i>About</a></li>

                    <?php

                    if (isset($_SESSION['login'])) {

                        echo "<li><a href=\"PHP/account\"><i class=\"material-icons left\">person</i>Account</a></li>";

                    } else {
                        echo "<li><a href=\"PHP/login\"><i class=\"material-icons left\">person</i>Login</a></li>";
                    }

                    ?>


                </ul>
        </div>
    </nav>
    <ul class="sidenav" id="mobile-demo">
        <li class="active"><a href="">Home</a></li>
        <li><a href="./PHP/About">About</a></li>


        <?php
        if (isset($_SESSION['login'])) {

            echo "<li><a href=\"./PHP/account\">Account</a></li>";

        } else {
            echo "<li><a href=\"./PHP/login\">Login</a></li>";
        }

        ?>

    </ul>
</header>

<?php

//Create ticket
class Ticket
{
    private $description;
    private $title;
    private $idPerson;
    private $typeSub;
    private $value;
    private $PDO;
    private $idOfTicket;

    function __construct($pdo, $id, $titel, $desc, $value1, $sub)
    {
        $this->description = $desc;
        $this->PDO = $pdo;
        $this->title = $titel;
        $this->idPerson = $id;
        $this->value = $value1;
        $this->typeSub = $sub;
    }


    public function set_idOfTicket($id)
    {
        $this->idOfTicket = $id;
    }

    public function display_idOfTicket()
    {
        return $this->idOfTicket;
    }

    function createTicket()
    {
        try {
            //Creates ticket
            $query1 = 'CALL  sp_createTicket(:PersonNr, :title, :descriptionOfTicket , :LanguageOfUser,  :typesub , :value ,  @responseCreateTicketNoAttachment)';
            $stmt = $this->PDO->prepare($query1);
            $stmt->bindParam(':PersonNr', $this->idPerson, PDO::PARAM_INT);
            $stmt->bindParam(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindParam(':descriptionOfTicket', $this->description, PDO::PARAM_STR);
            $stmt->bindParam(':LanguageOfUser', $_SESSION['language'], PDO::PARAM_STR);
            $stmt->bindParam(':typesub', $this->typeSub, PDO::PARAM_INT);
            $stmt->bindParam(':value', $this->value, PDO::PARAM_STR);
            $stmt->execute();


            $stmt = $this->PDO->prepare("SELECT @responseCreateTicketNoAttachment;");
            $stmt->execute();
            $allData = $stmt->fetchAll(\PDO::FETCH_ASSOC);


            //select last ticket
            $query1 = 'SELECT idTicket FROM tblTicket ORDER BY idTicket DESC LIMIT 1';
            $stmt = $this->PDO->prepare($query1);
            $stmt->execute();
            $result = $stmt->fetch();

            $this->set_idOfTicket($result['idTicket']);

            //translation of the ticket
            require 'translateTicket.php';
            $translationFR = translateTicket($_SESSION['language'], "fr", $this->description);
            $translationDE = translateTicket($_SESSION['language'], "de", $this->description);
            $translationEN = translateTicket($_SESSION['language'], "en", $this->description);


            $translationFRTitle = translateTicket($_SESSION['language'], "fr", $this->title);
            $translationDETitle = translateTicket($_SESSION['language'], "de", $this->title);
            $translationENTitle = translateTicket($_SESSION['language'], "en", $this->title);

            if ($_SESSION['language'] == "fr") {

                $query1 = 'CALL sp_TranslateTicket(:idTicket, "en", :description, :title)';
                $stmt = $this->PDO->prepare($query1);
                $stmt->bindParam(':idTicket', $this->idOfTicket, PDO::PARAM_INT);
                $stmt->bindParam(':description', $translationEN, PDO::PARAM_STR);
                $stmt->bindParam(':title', $translationENTitle, PDO::PARAM_STR);
                $stmt->execute();


                $query1 = 'CALL sp_TranslateTicket(:idTicket, "de", :description, :title)';
                $stmt = $this->PDO->prepare($query1);
                $stmt->bindParam(':idTicket', $this->idOfTicket, PDO::PARAM_INT);
                $stmt->bindParam(':description', $translationDE, PDO::PARAM_STR);
                $stmt->bindParam(':title', $translationDETitle, PDO::PARAM_STR);
                $stmt->execute();

            }
            if ($_SESSION['language'] == "de") {

                $query1 = 'CALL sp_TranslateTicket(:idTicket, "en", :description, :title)';
                $stmt = $this->PDO->prepare($query1);
                $stmt->bindParam(':idTicket', $this->idOfTicket, PDO::PARAM_INT);
                $stmt->bindParam(':description', $translationEN, PDO::PARAM_STR);
                $stmt->bindParam(':title', $translationENTitle, PDO::PARAM_STR);
                $stmt->execute();


                $query1 = 'CALL sp_TranslateTicket(:idTicket, "fr", :description, :title)';
                $stmt = $this->PDO->prepare($query1);
                $stmt->bindParam(':idTicket', $this->idOfTicket, PDO::PARAM_INT);
                $stmt->bindParam(':description', $translationFR, PDO::PARAM_STR);
                $stmt->bindParam(':title', $translationFRTitle, PDO::PARAM_STR);
                $stmt->execute();


            }
            if ($_SESSION['language'] == "en") {

                $query1 = 'CALL sp_TranslateTicket(:idTicket, "de", :description, :title)';
                $stmt = $this->PDO->prepare($query1);
                $stmt->bindParam(':idTicket', $this->idOfTicket, PDO::PARAM_INT);
                $stmt->bindParam(':description', $translationDE, PDO::PARAM_STR);
                $stmt->bindParam(':title', $translationDETitle, PDO::PARAM_STR);
                $stmt->execute();


                $query1 = 'CALL sp_TranslateTicket(:idTicket, "fr", :description, :title)';
                $stmt = $this->PDO->prepare($query1);
                $stmt->bindParam(':idTicket', $this->idOfTicket, PDO::PARAM_INT);
                $stmt->bindParam(':description', $translationFR, PDO::PARAM_STR);
                $stmt->bindParam(':title', $translationFRTitle, PDO::PARAM_STR);
                $stmt->execute();
            }


            echo "<script>window.addEventListener('load', function() {
                      document.getElementById('response').innerHTML='" . $allData[0]['@responseCreateTicketNoAttachment'] . "';
                      });</script>";

        } catch (PDOException $Exception) {
            echo $Exception->getMessage(), $Exception->getCode();
            die();
        }
    }
}


if (isset($_POST['submit'])) {
    $description = htmlentities($_POST['description']);
    $title = htmlentities($_POST['title']);
    $subOption = 0;

    //Controls if the user wants a subscription or not
    $sub = htmlentities($_POST['subscription']);
    if (isset($_POST['subscriptionOption'])) {
        $subOption = $_POST['subscriptionOption'];

        //get the id of the twitter user
        if ($subOption == 2) {
            require 'PHP/twitterSub.php';
            $sub = getIDofUser($sub);
        }
    }


//controls if there is an image
    if ($_FILES['files']['size'][0] == NULL) {

        $ticket = new Ticket($PDO, $_SESSION['idPerson'], $title, $description, $sub, $subOption);
        $ticket->createTicket();

    } else {
        //https://www.codexworld.com/upload-multiple-images-store-in-database-php-mysql/
        // File upload configuration
        $targetDir = "UserDirs/" . $_SESSION['user'] . "/";
        // $targetDir = "PHP/uploads/";
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

        $statusMsg = $errorMsg = $errorUpload = $errorUploadType = '';
        $insertValuesSQL = array();
        if (!empty(array_filter($_FILES['files']['name']))) {
            foreach ($_FILES['files']['name'] as $key => $val) {
                // File upload path
                $fileName = basename($_FILES['files']['name'][$key]);
                $targetFilePath = $targetDir . $fileName;

                // Check whether file type is valid
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                if (in_array($fileType, $allowTypes)) {
                    // Upload file to server
                    if (move_uploaded_file($_FILES["files"]["tmp_name"][$key], $targetFilePath)) {
                        // Image db insert sql

                        $temp = "UserDirs/" . $_SESSION['user'] . "/" . $fileName;
                        array_push($insertValuesSQL, $temp);
                        //   $insertValuesSQL .= "('".$fileName."', NOW()),";
                    } else {
                        $errorUpload .= $_FILES['files']['name'][$key] . ', ';
                    }
                } else {
                    $errorUploadType .= $_FILES['files']['name'][$key] . ', ';
                }
            }

            $ticket = new Ticket($PDO, $_SESSION['idPerson'], $title, $description, $sub, $subOption);
            $ticket->createTicket();
            $idOfTicket = $ticket->display_idOfTicket();

            if (!empty($insertValuesSQL)) {

                // Insert image file name into database
                foreach ($insertValuesSQL as $file) {
                    try {
                        //Creates ticket
                        $query1 = 'CALL sp_addAttachment(:ticket, NULL,:file, @errorAttachment)';
                        $stmt = $PDO->prepare($query1);

                        $stmt->bindParam(':ticket', $idOfTicket, PDO::PARAM_STR);
                        $stmt->bindParam(':file', $file, PDO::PARAM_STR);
                        $stmt->execute();


                        $stmt = $PDO->prepare("SELECT @errorAttachment;");
                        $stmt->execute();
                        $allData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                        if ($allData[0]['@errorAttachment'] != null) {

                            echo "<script>window.addEventListener('load', function() {
                      document.getElementById('response').innerHTML='" . $allData[0]['@errorAttachment'] . "';
                      });</script>";

                        }


                    } catch (PDOException $Exception) {
                        echo $Exception->getMessage(), $Exception->getCode();
                        die();
                    }
                }
            }

        }

    }
}
//controls who is logged, a normal user or a technician
if (isset($_SESSION['login'])) {

    //if technician is logged in he can see all tickets
    if (isset($_SESSION['idTechnician'])) {


        echo "<h3 class=\"center-align\">All Tickets</h3>";
        echo "<form id=\"languageForm\" method=\"post\" action='" . $_SERVER['PHP_SELF'] . "' class='row formmid' enctype=\"multipart/form-data\"><div class=\"row\">
            <select name=\"Language\" id=\"Language\"  onchange=\"this.form.submit();\">
                <option value=\"\" disabled selected>Select the language of the ticket:</option>
                <option value=\"en\">English</option>
                <option value=\"fr\">Français</option>
                <option value=\"de\">Deutsch</option>
            </select>
            <label>Select the language of the ticket:</label>
    </div></form>
    
    <script>
    function onSelectChange(){
        console.log('here');
 document.getElementById('language').submit();
}
</script>";
        // $query1 = 'SELECT idTicket, dtTitle, dtStatus, dtCreationTime FROM tblTicket GROUP BY idTicket ORDER BY dtStatus, dtCreationTime DESC';
        $query1 = 'CALL sp_showAllTickets(:language)';

        $stmt = $PDO->prepare($query1);
        if (isset($_POST['Language'])) {

            $languageTest123 = $_POST['Language'];
            $stmt->bindParam(':language', $languageTest123, PDO::PARAM_STR);;
        } else {
            $languageTest123 = $_SESSION['language'];
            $stmt->bindParam(':language', $languageTest123, PDO::PARAM_STR);
        }

        $stmt->execute();

        $allData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($allData != null) {
            echo "<table class=\"center-align centered striped\"><thead>
          <tr>
              <th>Ticket ID</th>
              <th>Title</th>
              <th>Creation Time</th>
              <th>Status</th>
          </tr>
        </thead><tbody>";
            foreach ($allData as $tickets => $info) {

                echo "<tr class='ticket' onclick='openPostLang(\"" . $info['idTicket'] . "\", \"" .$info['fiLanguage']."\")'>";
                echo "<td>" . $info['idTicket'] . "</td>";
                echo "<td>" . $info['dtTitle'] . "</td>";
                echo "<td>" . $info['dtCreationTime'] . "</td>";
                echo "<td>" . $info['dtStatus'] . "</td>";
                echo "</tr>";
            }
            echo "</tbody>
      </table>";
        } else {
            echo "<p class=\"center-align\">No Tickets found.</p>";
        }
    } else {
        //If costumers is logged in

        echo "<h3 class=\"center-align\">Create a Ticket</h3>
<form id=\"loginForm\" method=\"post\" action='" . $_SERVER['PHP_SELF'] . "' class='row formmid' enctype=\"multipart/form-data\">
<div class=\"row\" style=\"margin-bot: 0\">
        <div class=\"input-field col s6\">
                <input class=\"\" id=\"title\" name=\"title\" type=\"text\">
                <label class=\"#26c6da-text\" for=\"title\">Title</label>

        </div>
        </div>
          <div class=\"row\">
        <div class=\"input-field col s12\">
          <textarea id=\"textarea1\" name='description' data-length=\"255\" class=\"materialize-textarea\"></textarea>
          <label for=\"textarea1\">Description</label>
        </div>
      </div>
      
      
    <div class=\"file-field input-field\">
      <div class=\"btn waves-effect waves-light cyan lighten-1\">
        <span>File</span>
        <input name='files[]' type=\"file\" multiple>
      </div>
      <div class=\"file-path-wrapper\">
        <input class=\"file-path validate \"  type=\"text\" placeholder=\"Upload one or more files\">
      </div>
    </div>
    
    <div class=\"row\">
            <select name=\"subscriptionOption\" id=\"subscriptionOption\">
                <option value=\"\" disabled selected>Select your Subscription (not required):</option>
                <option value=\"1\">Email</option>
                <option value=\"2\">Twitter (Enter twitter name eg: @JohnDoe)</option>
            </select>
            <label>Select your Subscription (not required):</label>
    </div>
                <div class=\"input-field row\">
                <input class=\"\" id=\"subscription\" name=\"subscription\" type=\"text\">
                <label class=\"#26c6da-text\" for=\"subscription\">Subscription</label>

        </div>
        </div>
    
          <button class=\"btn waves-effect waves-light cyan lighten-1\" type=\"submit\" name=\"submit\">Submit
            <i class=\"material-icons right\">send</i>
        </button>
        </form>
        <div id='response'></div>
";


        echo "<h3 class=\"center-align\">My Tickets</h3>";
        echo "<form id=\"languageForm\" method=\"post\" action='" . $_SERVER['PHP_SELF'] . "' class='row formmid' enctype=\"multipart/form-data\"><div class=\"row\">
            <select name=\"Language\" id=\"Language\"  onchange=\"this.form.submit();\">
                <option value=\"\" disabled selected>Select the language of the ticket:</option>
                <option value=\"en\">English</option>
                <option value=\"fr\">Français</option>
                <option value=\"de\">Deutsch</option>
            </select>
            <label>Select the language of the ticket:</label>
    </div></form>
    
    <script>
    function onSelectChange(){
        console.log('here');
 document.getElementById('language').submit();
}
</script>";
//shows the users tickets
       function showTicketsUser($language, $PDO) {

        $query1 = "CALL sp_getTicketOfUser(:userlogged, :language)" ;
        $stmt = $PDO->prepare($query1);

        $stmt->bindParam(':userlogged', $_SESSION['idPerson'], PDO::PARAM_INT);
           $stmt->bindParam(':language', $language, PDO::PARAM_STR);
        $stmt->execute();

        $allData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($allData != null) {
            echo "<table class=\"center-align centered striped\"><thead>
          <tr>
              <th>Ticket ID</th>
              <th>Title</th>
                <th>Creation</th>
              <th>Status</th>
          </tr>
        </thead><tbody>";
            foreach ($allData as $tickets => $info) {
                echo "<tr class='ticket' onclick='openPostLang(\"" . $info['idTicket'] . "\", \"" .$info['fiLanguage']."\")'>";
                echo "<td>" . $info['idTicket'] . "</td>";
                echo "<td>" . $info['dtTitle'] . "</td>";
                echo "<td>" . $info['dtCreationTime'] . "</td>";
                echo "<td>" . $info['dtStatus'] . "</td>";

                echo "</tr>";
            }
            echo "</tbody>
      </table>";
        } else {
            echo "<p class=\"center-align\">No Tickets found.</p>";
        }
    }

        if (isset($_POST['Language'])) {
            showTicketsUser($_POST['Language'], $PDO);
        } else {
            showTicketsUser($_SESSION['language'], $PDO);
        }
    }

} else {
    echo "<div id='response'>Please login to create a ticket!</div>";
}


?>
<script>


</script>
</body>
</html>