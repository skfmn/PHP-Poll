<?php
session_start();
ob_start();
include 'includes/globals.php';
include 'includes/functions.php';

if (isset($_SESSION["msg"])) {
    $msg = $_SESSION["msg"];
    if ($msg <> "") {
        displayFancyMsg(getMessage($msg));
        $_SESSION["msg"] = "";
    }
}

$show = "";
if (isset($_POST["show"])) {
    $show = $_POST["show"];
}

$choiceID = 0;
$total = 0;

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST["castvote"])) {

    $choiceID = test_input($_POST["choiceid"]);
    $pollID = ACTIVEPOLL;
    $stmt = mysqli_prepare($conn, "SELECT Sum(votes) as total FROM " . DBPREFIX . "poll_choices WHERE choiceID = ? AND pollID = ?");
    $stmt->bind_param('ss', $choiceID, $pollID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total = $row["total"];
    }

    $total = $total + 1;

    $stmt = $conn->prepare("UPDATE " . DBPREFIX . "poll_choices SET votes = ? WHERE choiceID = ? AND pollID = ?");
    $stmt->bind_param('sss', $total, $choiceID, $pollID);
    $stmt->execute();
    mysqli_stmt_close($stmt);

    $_SESSION["pollvoted"] = $choiceID;
    $blnVoted = True;
}

if (isset($_POST["change"])) {

    $blnPollRevote = false;
    $choiceID = $_SESSION["pollvoted"];

    $pollID = ACTIVEPOLL;
    $stmt = mysqli_prepare($conn, "SELECT * FROM " . DBPREFIX . "poll WHERE pollID = ?");
    $stmt->bind_param('s', $pollID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $blnPollRevote = $row["poll_revote"];

    }

    if ($blnPollRevote) {

        $stmt = mysqli_prepare($conn, "UPDATE " . DBPREFIX . "poll_choices SET votes = (votes-1) WHERE pollID = ? AND choiceID = ?");
        $stmt->bind_param("ss", $pollID, $choiceID);
        $stmt->execute();

        $_SESSION["pollvoted"] = "";
        $blnVoted = False;
    }

}

$choiceID = 0;
$startDate = "";

$sql = "SELECT * FROM " . DBPREFIX . "poll WHERE poll_active = 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $startDate = $row["start_date"];
    $pollLength = $row["poll_length"];

}
mysqli_close($conn);

$endDate = gmdate("Y-m-d", strtotime($startDate . "+" . $pollLength . " days"));

if ($show == "results") {

    showResults();

} else {

    if ($blnVoted) {

        showResults();

    } else {

        showPoll();

    }
}
?>