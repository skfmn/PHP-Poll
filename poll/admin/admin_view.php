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

$pollID = 0;
if (isset($_POST["pollid"])) {
    $pollID = test_input($_POST["pollid"]);
}

include "../includes/header.php";
?>
<div id="main" class="container">
    <header style="text-align:center;">
        <h2>View a poll</h2>
    </header>

    <form action="admin_view.php" method="post">
        <input type="hidden" name="view" value="yes" />
        <div class="row">
            <div class="-4u 4u$ 12u$(medium)" style="padding-bottom:10px;">
                <div class="select-wrapper">
                    <?php
                    if (isset($_POST["view"])) {
                        selectPoll(test_input($_POST["pollid"]));
                    } else {
                        selectPoll(0);
                    }
                    ?>
                </div>
            </div>
            <div class="-4u 4u$ 12u$(medium)" style="padding-bottom:10px;">
                <input class="button fit" type="submit" value="Select a Poll to View" />
            </div>
        </div>
    </form>
    <?php

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if ($pollID <> 0) {

        $pollLength = 0;
        $pollName = "";
        $pollQuestion = "";
        $pollOpenUntil = "";
        $pollOpen = "";
        $startDate = date("1970-01-01");
        $endDate = date("1970-01-01");
        $blnHideResults = 0;
        $blnPollRevote = 0;
        $blnActive = 0;
        $revote = "";
        $hideResults = "";
        $active = "";
        $total = 0;

        $sql = "SELECT * FROM " . DBPREFIX . "poll WHERE pollID = " . $pollID;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $pollLength = trim($row["poll_length"]);
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
        <div class="row">
            <div class="-4u 4u$ 12u$(medium)" style="padding-bottom:10px;">
                <h2 style="text-align:center;">
                    <?php echo $pollName; ?>
                </h2>
            </div>
            <div class="-4u 4u$ 12u$(medium)" style="padding-bottom:10px;">
                <div class="row">
                    <div class="6u$ 12u$(medium)">
                        This Poll is <span class="first" style="color:#FF0000;">
                            <?php echo $active; ?>
                        </span>
                    </div>
                    <div class="6u$ 12u$(medium)">
                        Re-vote is <span style="color:#FF0000;">
                            <?php echo $revote; ?>
                        </span>
                    </div>
                    <div class="6u$ 12u$(medium)">
                        Hide Results is <span style="color:#FF0000;">
                            <?php echo $hideResults; ?>
                        </span>
                    </div>
                    <div class="6u$ 12u$(medium)">
                        <?php echo $pollOpenUntil; ?>
                    </div>

                </div>
            </div>
            <div class="-4u 4u$ 12u$(medium)" style="padding-bottom:10px;">
                <h4>
                    <?php echo $pollQuestion; ?>
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
                    <div class="-4u 4u$ 12u$(medium)" style="margin-bottom:-5px">
                        <span class="first" style="font-size:14px">
                            <?php echo $counter; ?>. <?php echo $row["pAnswer"]; ?>&nbsp;&nbsp;<span class="first" style="font-size:10px">
                                <?php echo $row["votes"]; ?> (<?php echo totalCountC($row["votes"], $total); ?>)
                            </span>
                        </span>
                    </div>
                    <div class="-4u 4u$ 12u$(medium)" style="padding-bottom:10px;">
                        <img src="/poll/images/Image1.jpg" style="height:10px;width:<?php echo totalCount($row["votes"], $total); ?>;border:0px;" />
                    </div>
                    <?php
                }
            }
            ?>
            <div class="-4u 4u$ 12u$(medium)">
                <span class="first" style="font-size:16px">
                    There Are <?php echo $total; ?> Votes!
                </span>
            </div>
            <div class="-4u 4u$ 12u$(medium)" style="margin-top:20px;">
                <form action="admin_edit.php" method="post">
                    <input type="hidden" name="show" value="yes" />
                    <input type="hidden" name="pollid" value="<?php echo $pollID; ?>" />
                    <input class="button" type="submit" name="submit" value="Edit this poll" />
                </form>
            </div>

        </div>
    <?php } ?>
</div>
<?php
mysqli_close($conn);
include "../includes/footer.php";
?>