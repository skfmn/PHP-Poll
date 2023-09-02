<?php
$siteTitle = $domain = "";

$cookieID = "";
if (isset($_SESSION["polladminID"])) {
    $cookieID = $_SESSION["polladminID"];
}

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM " . DBPREFIX . "settings";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();

    $siteTitle = $row["site_title"];
    $domain = $row["domain_name"];

    define('SITETITLE', $siteTitle);
    define('DOMAIN', $domain);

}

$param1 = "1";
$stmt = mysqli_prepare($conn, "SELECT * FROM " . DBPREFIX . "poll WHERE poll_active = ?");
$stmt->bind_param('s', $param1);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    define('ACTIVEPOLL', $row["pollID"]);
}

mysqli_close($conn);

function selectPoll($iPID)
{

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM " . DBPREFIX . "poll";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {

        echo "<select id=\"pollid\" name=\"pollid\">\n";
        while ($row = $result->fetch_assoc()) {
            if ($iPID == $row["pollID"]) {
                echo "<option value=\"" . $row["pollID"] . "\" selected>" . $row["poll_name"] . "</option>\n";
            } else {
                echo "<option value=\"" . $row["pollID"] . "\">" . $row["poll_name"] . "</option>\n";
            }
        }
        echo "</select>\n";

    } else {

        echo "<select id=\"pollid\" name=\"pollid\"><option value=\"0\">No Polls available</option></select>";

    }
    mysqli_close($conn);

}

function totalCount($iPvalue, $iTotal)
{

    $tempcount = "";
    if ($iTotal == 0 or $iPvalue == 0) {
        $tempcount = "1px";
    } else {
        $tempcount = ($iPvalue / $iTotal * 100) . "%";
    }
    return $tempcount;

}

function totalCountC($iPvalue, $iTotal)
{

    $tempcount = "";
    if ($iTotal == 0 or $iPvalue == 0) {
        $tempcount = "0%";
    } else {
        $tempcount = round(($iPvalue / $iTotal * 100), 2) . "%";
    }
    return $tempcount;

}

function msgTrans($sMsg)
{
    $strtmp = "";
    switch ($sMsg) {
        case "pcr":
            $strtmp = "Poll added:";
            break;
        case "dlt":
            $strtmp = "Poll deleted:";
            break;
        case "pwd":
            $strtmp = "Password changed:";
            break;
        case "pos":
            $strtmp = "Set active poll:";
            break;
        case "pas":
            $strtmp = "Vote counted:";
            break;
        case "pes":
            $strtmp = "Poll edited:";
            break;
        case "mus":
            $strtmp = "Message saved:";
            break;
        case "sich":
            $strtmp = "Site info changed:";
            break;
        case "pcrf":
            $strtmp = "Create poll failed:";
            break;
        case "error":
            $strtmp = "Generic error:";
            break;
        case "adad":
            $strtmp = "Admin added:";
            break;
        case "das":
            $strtmp = "Admin deleted:";
            break;
        case "ant":
            $strtmp = "Admin taken:";
            break;
        case "cpwds":
            $strtmp = "Changed Admins Password:";
            break;
        case "nadmin":
            $strtmp = "Change Admin info:";
            break;
        default:
            $strtmp = "If you see this you messed with the code!";
    }

    return $strtmp;
}

function randChrs($num)
{

    $sWord = $rchr = "";
    for ($x = 0; $x <= $num; $x++) {
        $rchr = chr(rand(27, 126));
        $pattern = "/[a-zA-Z0-9 , @$#%]/";

        if (preg_match($pattern, $rchr)) {
            $sWord = $sWord . $rchr;
        }
    }
    return $sWord;
}

function deleteDir($path)
{

    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file) {
            deleteDir(realpath($path) . '/' . $file);
        }

        return rmdir($path);

    } else if (is_file($path) === true) {

        return unlink($path);
    }

    return false;
}

function getMessage($sMsg)
{

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    $strTemp = "";
    $sMsg = test_input($sMsg);
    $sql = "SELECT message FROM " . DBPREFIX . "messages WHERE msg = '" . $sMsg . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $strTemp = $row["message"];
    } else {
        $strTemp = $sMsg;
    }
    $conn->close();

    return $strTemp;

}

function showResults()
{
    $pollvoted = "";
    $pollvoted = isset($_SESSION["pollvoted"]) ? test_input($_SESSION['pollvoted']) : "";

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $pollID = ACTIVEPOLL;
    $pollLength = 0;
    $pollName = "";
    $pollQuestion = "";
    $pollOpenUntil = "";
    $startDate = "";
    $endDate = "";
    $blnHideResults = false;
    $blnPollRevote = false;
    $blnActive = false;

    if (isset($_GET["pollname"])) {

        $param1 = test_input($_GET["pollname"]);
        $stmt = mysqli_prepare($conn, "SELECT * FROM " . DBPREFIX . "poll WHERE poll_name = ?");
        $stmt->bind_param('s', $param1);

    } else {

        $param1 = "1";
        $stmt = mysqli_prepare($conn, "SELECT * FROM " . DBPREFIX . "poll WHERE poll_active = ?");
        $stmt->bind_param('s', $param1);

    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $pollLength = $row["poll_length"];
        $pollName = $row["poll_name"];
        $pollQuestion = $row["poll_question"];
        $blnHideResults = $row["hide_results"];
        $blnPollRevote = $row["poll_revote"];
        $startDate = $row["start_date"];
        $blnActive = $row["poll_active"];

    }

    if ($pollLength > 0) {

        $startDate = gmdate("Y-m-d", strtotime($startDate));
        $endDate = gmdate("Y-m-d", strtotime($startDate . "+" . $pollLength . " days"));

        $pollOpenUntil = "(Open Until " . $endDate . ")";

    }

    $sql = "SELECT Sum(votes) as total FROM " . DBPREFIX . "poll_choices WHERE pollID = " . $pollID;
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($result->num_rows > 0) {
        $total = $row["total"];
    }
    if ($blnPollRevote) {
        $revote = "ON";
    } else {
        $revote = "OFF";
    }
    if ($blnHideResults) {
        $hideResults = "ON";
    } else {
        $hideResults = "OFF";
    }
    if ($blnActive) {
        $active = "Active";
    } else {
        $active = "Not Active";
    }

    ?>
    <div id="main" class="container">
        <header style="text-align:center;">
            <h2>
                <?php echo $pollName; ?>
            </h2>
        </header>
        <div class="row">
            <div class="-3u 6u 12u(medium)" style="padding-bottom:10px;">
                <h4>
                    <?php echo $pollQuestion ?><span style="font-size:12px;">
                        <?php echo $pollOpenUntil; ?>
                    </span>
                </h4>
            </div>
            <?php
            $counter = 0;
            $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "poll_choices WHERE pollID = ? ORDER BY choiceID asc");
            $stmt->bind_param('s', $pollID);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $counter = $counter + 1;
                    ?>
                    <div class="-3u 6u 12u(medium)" style="margin-bottom:-5px">
                        <span style="font-size:14px">
                            <?php echo $counter; ?>. <?php echo $row["pAnswer"]; ?>&nbsp;&nbsp;<span class="first" style="font-size:10px">
                                <?php echo $row["votes"]; ?> (<?php echo totalCountC($row["votes"], $total); ?>)
                            </span>
                        </span>
                    </div>
                    <div class="-3u 6u 12u(medium)" style="padding-bottom:10px;">
                        <img src="/poll/images/Image1.jpg" style="height:10px;width:<?php echo totalCount($row["votes"], $total); ?>;border:0px;" />
                    </div>
                    <?php
                }
            }
            ?>
            <div class="-3u 6u 12u(medium)">
                <?php if ($pollvoted == "") { ?>
                    <form action="" method="post">
                        <input type="hidden" name="show" value="back" />
                        <input type="submit" name="submit" value="Back" style="height:35px;margin:0;padding:0 18px 18px 18px;font-size:12px;vertical-align:top;" />
                    </form>

                <?php } else if ($pollvoted <> "" and $blnPollRevote) { ?>
                        <form action="" method="post">
                            <input type="hidden" name="change" value="yes" />
                            <input type="submit" name="submit" value="Change Vote" style="height:35px;margin:0;padding:0 18px 18px 18px;font-size:12px;vertical-align:top;" />
                        </form>
                        <?php } ?>
            </div>
            <div class="-3u 3u 12u(medium)" style="padding-bottom:10px;">
                <span style="font-size:16px;font-weight:bold;">
                    Powered By <a href="http://phpjunction.com" target="_blank">PHP Poll</a>
                </span>
            </div>
            <div class="3u 12u(medium)" style="padding-bottom:10px;">
                <span style="font-size:16px;font-weight:bold;">
                    There are <?php echo $total; ?> votes!
                </span>
            </div>
        </div>
    </div>
    <?php
    mysqli_close($conn);
}

function showPoll()
{

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $pollID = ACTIVEPOLL;
    $pollLength = 0;
    $total = 0;
    $pollName = "";
    $pollQuestion = "";
    $pollOpenUntil = "";
    $startDate = "";
    $endDate = "";
    $blnHideResults = false;
    $blnPollRevote = false;
    $blnActive = false;

    $sql = "SELECT * FROM " . DBPREFIX . "poll WHERE poll_active = 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $pollLength = $row["poll_length"];
        $pollName = $row["poll_name"];
        $pollQuestion = $row["poll_question"];
        $blnHideResults = $row["hide_results"];
        $blnPollRevote = $row["poll_revote"];
        $startDate = $row["start_date"];
        $blnActive = $row["poll_active"];
    }

    if ($pollLength > 0) {

        $endDate = gmdate("Y-m-d", strtotime($startDate . "+" . $pollLength . " days"));
        $pollOpenUntil = "(Open Until " . $endDate . ")";

    }

    $sql = "SELECT Sum(votes) as total FROM " . DBPREFIX . "poll_choices WHERE pollID = " . $pollID;
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if ($result->num_rows > 0) {
        $total = $row["total"];
    }
    ?>
    <div id="main" class="container">
        <header style="text-align:center;">
            <h2>
                <?php echo $pollName; ?>
            </h2>
        </header>
        <h4 style="text-align:center;">
            <?php echo $pollQuestion; ?><br />
            <span style="font-size:12px;">
                <?php echo $pollOpenUntil; ?>
            </span>
        </h4>
        <script type="text/javascript">
            function validateVote() {
                var radios = document.getElementsByName('choiceid');

                for (var i = 0; i < radios.length; i++) {
                    if (radios[i].checked) {
                        return document.pollvote.submit();
                    }
                };

                alert('Please select an answer!');
                return false;
            }
        </script>
        <form action="" method="post" name="pollvote" id="pollvote" onsubmit="return validateVote();">
            <input type="hidden" name="castvote" value="Yes" />
            <div class="row">
                <?php
                $counter = 0;

                $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "poll_choices WHERE pollID = ? ORDER BY choiceID asc");
                $stmt->bind_param('s', $pollID);
                $stmt->execute();

                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $counter = $counter + 1;
                        ?>
                        <div class="-3u 6u 12u(medium)" style="padding-bottom:10px;">
                            <input type="radio" name="choiceid" id="choiceid<?php echo $counter; ?>" value="<?php echo $row["choiceID"]; ?>" required />
                            <label for="choiceid<?php echo $counter; ?>">
                                <?php echo $counter; ?>. <?php echo $row["pAnswer"]; ?>
                            </label>
                        </div>
                        <?php
                    }
                }

                ?>
                <div class="-3u 6u 12u(medium)" style="padding-bottom:10px;">
                    <a class="button" onclick="document.pollvote.submit()">Vote</a>
                </div>
                </form>
                <?php
                if (!$blnHideResults) {
                    ?>
                    <div class="3u 12u(medium)" style="padding-bottom:10px;">
                        <form action="" method="post">
                            <input type="hidden" name="show" value="results" />
                            <input type="submit" name="submit" value="Show results" style="height:35px;margin:0;padding:0 18px 18px 18px;font-size:12px;vertical-align:top;" />
                        </form>
                    </div>
                    <?php
                }
                ?>
            </div>

        <div class="row">
            <div class="-3u 3u 12u(medium)" style="padding-bottom:10px;">
                <span style="font-size:16px;font-weight:bold;">
                    Powered By <a href="http://phpjunction.com" target="_blank">PHP Poll</a>
                </span>
            </div>
            <div class="3u 12u(medium)" style="padding-bottom:10px;">
                <span style="font-size:16px;font-weight:bold;">
                    There are <?php echo $total ?> votes!
                </span>
            </div>
        </div>
    </div>
    <?php
    mysqli_close($conn);
}

function displayFancyMsg($sText)
{
    ?>
    <div style="display:none">
        <a id="textmsg" href="#displaymsg">Message</a>
        <div id="displaymsg" style="background-color:#fff;text-align:left;width:300px;">
            <div class="left_menu_block">
                <div class="left_menu_top">
                    <h2>Message</h2>
                </div>
                <div class="left_menu_center" align="center" style="background-color:#fff; padding-left:0px;">
                    <span style="color:#444;">
                        <?php echo $sText; ?>
                    </span>
                </div>
                <div class="left_menu_bottom"></div>
            </div>
        </div>
    </div>
    <?php
}

function redirect($location)
{
    if ($location) {

        header('Location: ' . $location);
        exit;

    }
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function getNumtxt($sNum)
{

    $strTemp = "";

    switch ($sNum) {
        case 1:
            $strTemp = "11.png";
            break;
        case 2:
            $strTemp = "12.png";
            break;
        case 3:
            $strTemp = "13.png";
            break;
        case 4:
            $strTemp = "14.png";
            break;
        case 5:
            $strTemp = "15.png";
            break;
        case 6:
            $strTemp = "16.png";
            break;
        case 7:
            $strTemp = "17.png";
            break;
        case 8:
            $strTemp = "18.png";
            break;
        case 9:
            $strTemp = "19.png";
            break;
        default:
            $strTemp = "20.png";
    }

    return $strTemp;

}

?>