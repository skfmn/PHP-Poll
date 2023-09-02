<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$cookies = $dir = "";
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

//////Can delete after install
$baseDir = str_replace("\\\\", "\\", BASEDIR . POLLDIR . "\\install\\");
if (is_dir($baseDir)) {
    deleteDir($baseDir);
}
///////////////////////

include "../includes/header.php";
?>
<div id="main" class="container">
    <header>
        <h2 style="text-align:center;">PHP Poll Admin</h2>
    </header>
    <div class="row">
        <div class="-3u 3u 12u$(medium)">
            <ul class="alt">
                <li>
                    <a class="button fit" href="admin_create.php">Create Poll</a>
                </li>
                <li>
                    <a class="button fit" href="admin_edit.php">Edit Polls</a>
                </li>
                <li>
                    <a class="button fit" href="admin_settings.php">Manage Settings</a>
                </li>

            </ul>
        </div>
        <div class="3u$ 12u$(medium)">
            <ul class="alt">
                <li>
                    <a class="button fit" href="admin_view.php">View Polls</a>
                </li>
                <li>
                    <a class="button fit" href="admin_manage.php">Manage Admins</a>
                </li>
            </ul>
        </div>
        <div class="-3u 6u$ 12u$(medium)">
            <?php echo file_get_contents("http://www.phpjunction.com/gnews.php?ref=y&ppl=" . $version . ""); ?>
        </div>
    </div>
</div>
<?php include "../includes/footer.php"; ?>