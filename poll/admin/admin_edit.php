<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$msg = $cookies = "";
$cookies = $_SESSION["polladminname"];
$_SESSION["msg"] = "";

if ($cookies == "") {
    redirect($redirect . "admin/admin_login.php");
    ob_end_flush();
}

if (!isset($_POST["show"])) {
    if (isset($_SESSION["msg"])) {
        $msg = $_SESSION["msg"];
        if ($msg <> "") {
            displayFancyMsg(getMessage($msg));
            $_SESSION["msg"] = "";
        }
    }
}

$submit = "";
$submit = isset($_POST["submit"]) ? test_input($_POST['submit']) : "";

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($submit == "Edit Poll") {

    $pollID = 0;
    $pollLength = 0;
    $pollName = "";
    $pollQuestion = "";
    $blnHideResults = false;
    $blnPollRevote = false;
    $blnActive = false;
    $active = "";
    $startDate = "";

    $pollID = test_input($_POST["pollid"]);
    $pollName = test_input($_POST["pollname"]);
    $pollQuestion = test_input($_POST["pquestion"]);
    $blnHideResults = test_input($_POST["poll_hide"]);
    $blnPollRevote = test_input($_POST["poll_revote"]);
    $pollLength = test_input($_POST["poll_length"]);

    if (isset($_POST["poll_active"])) {
        $active = test_input($_POST["poll_active"]);
    }

    if ($active == "on") {
        $blnActive = true;
    } else {
        $blnActive = false;
    }

    if ($blnHideResults) {
        $blnHideResults = true;
    } else {
        $blnHideResults = false;
    }

    if ($blnPollRevote) {
        $blnPollRevote = true;
    } else {
        $blnPollRevote = false;
    }

    $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "poll WHERE pollID = ?");
    $stmt->bind_param('s', $pollID);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();
        if ($row["poll_length"] == $pollLength) {
            $startDate = $row["start_date"];
        } else {
            $startDate = date("Y-m-d");
        }

        if ($row["poll_active"] <> $blnActive) {

            if ($row["poll_active"] <> 1) {

                $sql = "UPDATE " . DBPREFIX . "poll SET poll_active = 0 WHERE poll_active = 1";
                $conn->query($sql);

            }
        }
    }

    $stmt = $conn->prepare("UPDATE " . DBPREFIX . "poll SET poll_name = ?, poll_question = ?, hide_results = ?, poll_revote = ?, poll_length = ?, start_date = ?, poll_active = ? WHERE pollID = ?");
    $stmt->bind_param('ssssssss', $pollName, $pollQuestion, $blnHideResults, $blnPollRevote, $pollLength, $startDate, $blnActive, $pollID);
    $stmt->execute();
    mysqli_stmt_close($stmt);

    $options = "";
    $options = $_POST["options"];

    foreach ($options as $x => $x_value) {

        $blnConnected = false;
        $postPollAnswer = "";
        $choiceID = 0;

        $postPollAnswer = $x_value;
        $choiceID = $x;

        $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "poll_choices WHERE pollID = ? AND choiceID = ?");
        $stmt->bind_param('ss', $pollID, $choiceID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $blnConnected = true;
        } else {
            $blnConnected = false;
        }
        mysqli_stmt_close($stmt);

        if ($blnConnected == true) {
            if ($postPollAnswer <> "") {

                $stmt = mysqli_prepare($conn, "UPDATE " . DBPREFIX . "poll_choices SET pAnswer = ? WHERE pollID = ? AND choiceID = ?");
                $stmt->bind_param('sss', $postPollAnswer, $pollID, $choiceID);
                if ($stmt->execute()) {
                    $_SESSION["msg"] .= "uds-";
                } else {
                    $_SESSION["msg"] .= "udf-";
                }

            } else {

                $stmt = mysqli_prepare($conn, "DELETE FROM " . DBPREFIX . "poll_choices WHERE pollID = ? AND choiceID = ?");
                $stmt->bind_param('ss', $pollID, $choiceID);
                if ($stmt->execute()) {
                    $_SESSION["msg"] .= "dls1-";
                } else {
                    $_SESSION["msg"] .= "dlf1-";
                }

            }
        } else {

            if ($postPollAnswer <> "") {

                $param1 = 0;
                $stmt = mysqli_prepare($conn, "INSERT INTO " . DBPREFIX . "poll_choices (pollID,choiceID,pAnswer,votes) Values (?,?,?,?)");
                $stmt->bind_param('ssss', $pollID, $choiceID, $postPollAnswer, $param1);
                if ($stmt->execute()) {
                    $_SESSION["msg"] .= "ins-";
                } else {
                    $_SESSION["msg"] .= "inf-";
                }

            } else {

                $stmt = mysqli_prepare($conn, "DELETE FROM " . DBPREFIX . "poll_choices WHERE pollID = ? AND choiceID = ?");
                $stmt->bind_param('ss', $pollID, $choiceID);
                if ($stmt->execute()) {
                    $_SESSION["msg"] .= "dls2-";
                } else {
                    $_SESSION["msg"] .= "dlf2-";
                }

            }
        }
        //Comment out the line below for debugging.
        $_SESSION["msg"] = "pes";
    }

    if (isset($_POST["show"])) {
        if (isset($_SESSION["msg"])) {
            $msg = $_SESSION["msg"];
            if ($msg <> "") {
                displayFancyMsg(getMessage($msg));
                $_SESSION["msg"] = "";
            }
        }
    }

}

if (isset($_GET["delete"])) {

    $pollID = test_input($_GET["pid"]);

    $stmt = mysqli_prepare($conn, "DELETE FROM " . DBPREFIX . "poll WHERE pollID = ?");
    $stmt->bind_param('s', $pollID);
    $stmt->execute();

    $stmt = mysqli_prepare($conn, "DELETE FROM " . DBPREFIX . "poll_choices WHERE pollID = ?");
    $stmt->bind_param('s', $pollID);
    $stmt->execute();

    $_SESSION["msg"] = "dlt";
    redirect($redirect . "admin/admin_edit.php");
    ob_end_flush();

}
mysqli_close($conn);

include "../includes/header.php"
    ?>
<div id="main" class="container">
    <header>
        <h2>Edit a poll</h2>
    </header>

    <form action="admin_edit.php" method="post">
        <input type="hidden" name="show" value="yes" />
        <div class="row">
            <div class="-4u 4u$ 12u$(medium)" style="padding-bottom:10px;">
                <div class="select-wrapper">
                    <?php
                    if (isset($_POST["show"])) {
                        selectPoll(test_input($_POST["pollid"]));
                    } else {
                        selectPoll(0);
                    }
                    ?>
                </div>
            </div>
            <div class="-4u 4u$ 12u$(medium)">
                <input class="button fit" type="submit" value="Select a Poll to Edit" />
            </div>
        </div>
    </form>
</div>
<?php

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST["show"])) {

    $pollID = $checked = "";
    $pollID = test_input($_POST["pollid"]);

    $stmt = mysqli_prepare($conn, "SELECT " . DBPREFIX . "poll.*, " . DBPREFIX . "poll_choices.* FROM " . DBPREFIX . "poll INNER JOIN " . DBPREFIX . "poll_choices ON " . DBPREFIX . "poll.pollID  = " . DBPREFIX . "poll_choices.pollID WHERE " . DBPREFIX . "poll.pollID = ? ORDER BY choiceID asc");
    $stmt->bind_param('s', $pollID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $pollName = "";
        $pollQuestion = "";
        $startDate = "";
        $blnHideResults = 0;
        $blnPollRevote = 0;
        $blnActive = 0;
        $pollLength = 0;

        $pollName = $row["poll_name"];
        $pollQuestion = $row["poll_question"];
        $startDate = $row["start_date"];
        $blnHideResults = $row["hide_results"];
        $blnPollRevote = $row["poll_revote"];
        $blnActive = $row["poll_active"];
        $pollLength = $row["poll_length"];
        ?>
        <div id="main" class="container">
            <h2 style="text-align:center;">
                <?php echo $pollName; ?>
            </h2>
            <form action="admin_edit.php" method="post" name="reply">
                <input type="hidden" name="show" value="yes" />
                <input type="hidden" name="pollid" value="<?php echo $pollID; ?>" />
                <div class="row">
                    <div class="6u 12u$(medium)">

                        <label for="pollname" style="margin-bottom:-3px;">Poll Name</label>
                        <input type="text" id="pollname" name="pollname" value="<?php echo $pollName; ?>" />

                        <label for="pquestion" style="margin-bottom:-3px;">Poll Question:</label>
                        <input type="text" id="pquestion" name="pquestion" value="<?php echo $pollQuestion; ?>" />

                        <h4 style="margin-bottom:-5px;">
                            Poll Answers&nbsp;<span style="color:#FF0000;font-size:12px">To delete an answer, leave it blank.</span>
                        </h4>
                        <br />
                        <label for="options-1" style="margin-bottom:-3px;">Answer 1</label>
                        <input type="text" name="options[<?php echo $row["choiceID"]; ?>]" id="options-1" value="<?php echo $row["pAnswer"]; ?>" tabindex="1" size="20" />
                        <br />
                        <?php
                        $counter = 1;
                        while ($row = $result->fetch_assoc()) {

                            $counter = $counter + 1;
                            ?>
                            <label for="options-<?php echo $counter; ?>" style="margin-bottom:-3px;">
                                Answer <?php echo $counter; ?>
                            </label>
                            <input type="text" name="options[<?php echo $row["choiceID"]; ?>]" id="options-<?php echo $counter; ?>" value="<?php echo $row["pAnswer"]; ?>" tabindex="<?php echo $counter; ?>" size="20" />
                            <br />
                            <?php
                        }
                        ?>
                        <span id="pollMoreOptions"></span>
                        <a class="button fit nohijack" href="javascript:addPollOption(); void(0);">Add an Answer</a>
                        <?php
    }
    ?>

                </div>
                <div class="6u 12u$(medium)">

                    <h4>Show Results</h4>
                <?php if ($blnHideResults == "1") { ?>
                    <input type="radio" id="poll_hide1" name="poll_hide" value="0" />
                    <label for="poll_hide1">Show the poll's results to anyone.</label>
                    <input type="radio" id="poll_hide2" name="poll_hide" value="1" checked="checked" />
                    <label for="poll_hide2">Only show the results after someone has voted.</label>
                <?php } else { ?>
                    <input type="radio" id="poll_hide1" name="poll_hide" value="0" checked="checked" />
                    <label for="poll_hide1">Show the poll's results to anyone.</label>
                    <input type="radio" id="poll_hide2" name="poll_hide" value="1" />
                    <label for="poll_hide2">Only show the results after someone has voted.</label>
                <?php } ?>

                    <h4>Allow posters to change vote:</h4>
                <?php if ($blnPollRevote == "1") { ?>
                    <input type="radio" id="poll_revote1" name="poll_revote" value="1" checked="checked" />
                    <label for="poll_revote1">Yes</label>
                    <input type="radio" id="poll_revote2" name="poll_revote" value="0" />
                    <label for="poll_revote2">No</label>
                <?php } else { ?>
                    <input type="radio" id="poll_revote1" name="poll_revote" value="1" />
                    <label for="poll_revote1">Yes</label>
                    <input type="radio" id="poll_revote2" name="poll_revote" value="0" checked="checked" />
                    <label for="poll_revote2">No</label>
                <?php } ?>

                    <h4>Length of poll in days</h4>
                    <input type="text" id="poll_length" name="poll_length" style="width:75px;" value="<?php echo $pollLength; ?>" />
                <label for="poll_length">
                    Entering a new number will reset the poll start date<br />
                    Leave blank for no close date.
                </label>

                <?php if ($blnActive) {
                    $checked = " checked";
                } ?>
                <input type="checkbox" id="poll_active" name="poll_active" <?php echo $checked; ?> />
                <label for="poll_active">Set This poll as active:</label>

                <input type="submit" class="button fit" name="submit" value="Edit Poll" />
                <a class="button fit" onclick="return confirmSubmit('Are you SURE you want to delete this Poll?','admin_edit.php?delete=yes&pid=<?php echo $pollID; ?>')" style="cursor:pointer;">Delete</a>

                </div>

            </div>
        </form>
    </div>
<?php
}
mysqli_close($conn);

include "../includes/footer.php";
?>