<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$cookies = "";
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

if (isset($_POST["chmsg"])) {

    $chmsgs = "";
    $chmsgs = $_POST["messages"];
    $count = count($chmsgs);

    foreach ($chmsgs as $x => $x_value) {

        $param1 = $param2 = "";
        $param1 = trim($x_value);
        $param2 = trim($x);
        $stmt = mysqli_prepare($conn, "UPDATE " . DBPREFIX . "messages SET message = ? WHERE msg = ?");
        $stmt->bind_param('ss', $param1, $param2);

        if ($stmt->execute()) {
            $_SESSION["msg"] = "mus";
        } else {
            $_SESSION["msg"] = "error";
        }
    }

    redirect($redirect . "admin/admin_settings.php");
    ob_end_flush();

}

if (isset($_POST["chmstgs"])) {

    if (isset($_POST["sitename"])) {
        $siteTitle = test_input($_POST["sitename"]);
    }
    if (isset($_POST["domainname"])) {
        $domainname = test_input($_POST["domainname"]);
    }

    $stmt = mysqli_prepare($conn, "UPDATE " . DBPREFIX . "settings SET site_title = ?, domain_name = ?");
    $stmt->bind_param('ss', $siteTitle, $domainname);

    if ($stmt->execute()) {
        $_SESSION["msg"] = "sich";
    } else {
        $_SESSION["msg"] = "error";
    }

    redirect($redirect . "admin/admin_settings.php");
    ob_end_flush();

}

include "../includes/header.php";
?>
<div id="main" class="container">
    <header>
        <h2>Manage Settings</h2>
    </header>
    <div class="row uniform">
        <div class="12u$">
            <h3>Site Settings</h3>
            <form action="admin_settings.php" method="post">
                <input type="hidden" name="chmstgs" value="y" />
                <div class="row">
                    <div class="4u 12u$(medium)">
                        <label for="sitename">Site Name</label>
                        <input type="text" id="sitename" name="sitename" value="<?php echo SITETITLE; ?>" />
                    </div>
                    <div class="4u 12u$(medium)">
                        <label for="domainname">Domain Name</label>
                        <input type="text" id="domainname" name="domainname" value="<?php echo DOMAIN; ?>" />
                    </div>
                    <div class="4u$ 12u$(medium)">
                        <label for="submit">&nbsp;</label>
                        <input class="button fit" type="submit" name="submit" value="Save Settings" style="vertical-align:bottom;" />
                    </div>
                </div>
            </form>
        </div>
        <div class="12u$">
            <h3>Messages</h3>
            <?php
            $sql = "SELECT * FROM " . DBPREFIX . "messages";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                ?>
                <div class="12u$" style="padding-bottom:10px;">
                    <div class="table-wrapper">
                        <form action="admin_settings.php" method="post">
                            <input type="hidden" name="chmsg" value="y" />
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table>
                                                <?php
                                                $counter = 0;
                                                while ($row = $result->fetch_assoc()) {
                                                    $counter++;
                                                    $tempMsg = "";
                                                    $tempMsg = $row["msg"];
                                                    ?>
                                                    <tr>
                                                        <td style="width:30%;">
                                                            <?php echo trim(msgTrans($tempMsg)); ?>
                                                        </td>
                                                        <td style="width:70%;">
                                                            <input type="text" name="messages[<?php echo $tempMsg; ?>]" value="<?php echo trim($row["message"]); ?>" />
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    if ($counter == 8) {
                                                        ?>
                                                    </table>
                                                </td>
                                                <td>
                                                    <table>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </table>
                                        </td>
                                    </tr>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2">
                                                <input type=submit value="Save Messages" class="button fit" />
                                            </td>
                                        </tr>
                                    </tfoot>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <?php
            }
            mysqli_close($conn);
            ?>
        </div>
    </div>
</div>
<?php include "../includes/footer.php" ?>