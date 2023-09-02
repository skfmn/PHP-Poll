<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$msg = $cookies = "";
$cookies = $_SESSION["polladminname"];

if ($cookies == "") {

    redirect($redirect . "admin/admin_login.php");
    ob_end_flush();

}

if (isset($_SESSION["msg"])) {
    $msg = $_SESSION["msg"];
    if ($msg <> "") {
        displayFancyMsg(getMessage($msg));
        $_SESSION["msg"] = "";
    }
}

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST["create"])) {

    $pollID = 0;
    $pollLength = 0;
    $pollName = "";
    $pollQuestion = "";
    $blnHideResults = false;
    $blnPollRevote = false;
    $blnActive = false;
    $active = "";
    $startDate = "";

    $pollName = test_input($_POST["pollname"]);
    $pollQuestion = test_input($_POST["pquestion"]);
    $blnHideResults = test_input($_POST["poll_hide"]);
    $blnPollRevote = test_input($_POST["poll_revote"]);
    $active = isset($_POST["poll_active"]) ? test_input($_POST['poll_active']) : "";
    $pollLength = isset($_POST["poll_length"]) ? test_input($_POST['poll_length']) : "";

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

    $param1 = 0;
    $startDate = date("Y-m-d");
    $stmt = mysqli_prepare($conn, "INSERT INTO " . DBPREFIX . "poll (poll_name,poll_question,hide_results,poll_revote,poll_length,start_date,poll_lock,poll_active) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param('ssssssss', $pollName, $pollQuestion, $blnHideResults, $blnPollRevote, $pollLength, $startDate, $param1, $blnActive);

    if ($stmt->execute()) {

        $pollID = mysqli_insert_id($conn);

        $sql = "SELECT * FROM " . DBPREFIX . "poll WHERE poll_active = 1";

        if ($result = mysqli_query($conn, $sql)) {

            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            if ($row["poll_active"] == $blnActive) {

                $sql = "UPDATE " . DBPREFIX . "poll SET poll_active = 0 WHERE poll_active = 1";
                mysqli_query($conn, $sql);

                $sql = "UPDATE " . DBPREFIX . "poll SET poll_active = 1 WHERE pollID = " . $pollID;
                mysqli_query($conn, $sql);

            }

        }
        mysqli_free_result($result);

        $options = "";
        $options = $_POST["options"];

        foreach ($options as $x => $x_value) {

            $postPollAnswer = $x_value;
            $choiceID = $x;
            $param1 = 0;

            $stmt = mysqli_prepare($conn, "INSERT INTO " . DBPREFIX . "poll_choices (pollID,choiceID,pAnswer,votes) VALUES (?,?,?,?)");
            $stmt->bind_param('ssss', $pollID, $choiceID, $postPollAnswer, $param1);
            mysqli_stmt_execute($stmt);

        }
        $_SESSION["msg"] = "pcr";
    } else {
        $_SESSION["msg"] = "pcrf";
    }

    redirect($redirect . "admin/admin_create.php");
    ob_end_flush();

}
mysqli_close($conn);

include "../includes/header.php";
?>
<div id="main" class="container">
    <header>
        <h2>Create a poll</h2>
    </header>
    <form action="admin_create.php" method="post" name="reply">
        <input type="hidden" name="create" value="yes" />
        <div class="row">
            <div class="6u 12u$(medium)">

                <label for="pollname" style="margin-bottom:-3px;">Poll Name:</label>
                <input type="text" id="pollname" name="pollname" size="50" required />
                <br />

                <label for="pquestion" style="margin-bottom:-3px;">Poll Question:</label>
                <input type="text" id="pquestion" name="pquestion" size="50" required />
                <br />

                <h4 style="margin-bottom:-5px;">Poll Answers</h4>
                <label for="options-0" style="margin-bottom:-3px;">Answer 1</label>
                <input type="text" name="options[1]" id="options-1" value="" tabindex="1" size="20" required />
                <br />
                <label for="options-1" style="margin-bottom:-3px;">Answer 2</label>
                <input type="text" name="options[2]" id="options-2" value="" tabindex="2" size="20" required />
                <br />
                <span id="pollMoreOptions"></span>
                <a class="button fit nohijack" href="javascript:addPollOption(); void(0);">Add an Answer</a>

            </div>
            <div class="6u$ 12u$(medium)">

                <h4>Show Results</h4>
                <input type="radio" id="poll_hide1" name="poll_hide" value="1" checked="checked" />
                <label for="poll_hide1">Show the poll's results to anyone.</label>
                <input type="radio" id="poll_hide2" name="poll_hide" value="0" />
                <label for="poll_hide2">Only show the results after someone has voted.</label>

                <h4>Allow posters to change vote:</h4>
                <input type="radio" id="poll_revotey" name="poll_revote" value="1" checked="checked" />
                <label for="poll_revotey">Yes</label>
                <input type="radio" id="poll_revoten" name="poll_revote" value="0" />
                <label for="poll_revoten">No</label>

                <h4>Length of poll in days</h4>
                <input type="text" id="poll_length" name="poll_length" style="width:100px;" />
                <label for="poll_length">leave blank for no close date.</label>

                <input type="checkbox" id="poll_active" name="poll_active" />
                <label for="poll_active">Set This poll as active:</label>

                <input class="button fit" type="submit" name="submit" value="Create Poll" />

            </div>
        </div>
    </form>
</div>
<?php include "../includes/footer.php"; ?>