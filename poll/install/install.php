<?php
ob_start();

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

function test_inputA($data)
{
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
}

$step = "";
$step = isset($_GET["step"]) ? test_input($_GET['step']) : "";

$servername = $username = $dbpassword = $dbname = $dbprefix = $basedir = $polldir = "";
$param1 = $param2 = $param3 = $param4 = $param5 = "";
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Install</title>
    <link type="text/css" rel="stylesheet" href="../assets/css/main.css" />
</head>
<body>
    <div id="main" class="container" style="margin-top:-75px;text-align:center;">
        <div class="row 50%">
            <div class="12u 12u$(medium)">
                <header>
                    <h2>PHP Poll Installation</h2>
                </header>
            </div>
        </div>
    </div>
    <?php
    if ($step == "one") {
        ?>
        <div id="main" class="container" style="margin-top:-100px;text-align:center;">
            <div class="row 50%">
                <div class="12u 12u$(medium)">
                    <form action="install.php?step=two" method="post">

                        <header>
                            <h2>MySQL Database</h2>
                        </header>
                        <div class="row">
                            <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                                <label for="servername" style="text-align:left;">
                                    Server Host Name or IP Address
                                    <input type="text" name="servername" required />
                                </label>
                            </div>
                            <div class="4u 1u$">
                                <span></span>
                            </div>

                            <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                                <label for="dbname" style="text-align:left;">
                                    Database Name
                                    <input type="text" name="dbname" required />
                                </label>
                            </div>
                            <div class="4u 1u$">
                                <span></span>
                            </div>

                            <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                                <label for="username" style="text-align:left;">
                                    Database Login
                                    <input type="text" name="username" required />
                                </label>
                            </div>
                            <div class="4u 1u$">
                                <span></span>
                            </div>

                            <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                                <label for="dbpassword" style="text-align:left;">
                                    Database Password
                                    <input type="password" name="dbpassword" required />
                                </label>
                            </div>
                            <div class="4u 1u$">
                                <span></span>
                            </div>

                            <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                                <label for="dbprefix" style="text-align:left;">
                                    Table Prefix
                                    <input type="text" name="dbprefix" value="poll_" required />
                                </label>
                            </div>
                            <div class="4u 1u$">
                                <span></span>
                            </div>

                            <div class="12u 12u$(medium)">
                                <input class="button" type="submit" name="submit" value="Continue" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    } else if ($step == "two") {
        ?>
            <div id="main" class="container" style="text-align:center;">
                <div class="row 50%">
                    <div class="12u 12u$(medium)">
                        <?php

                        $servername = test_input($_POST["servername"]);
                        $dbname = test_input($_POST["dbname"]);
                        $username = test_input($_POST["username"]);
                        $dbpassword = test_input($_POST["dbpassword"]);
                        $dbprefix = test_input($_POST["dbprefix"]);

                        $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);

                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }

                        echo "Creating Database Tables<br /><br />";

                        echo "Creating Admin table...<br />";

                        $sql = "CREATE TABLE IF NOT EXISTS " . $dbprefix . "admin (
                        adminID int(11) NOT NULL AUTO_INCREMENT ,
                        name VARCHAR(255) NOT NULL ,
                        pwd VARCHAR(255) NOT NULL ,
                        PRIMARY KEY (adminID)
                        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

                        if ($conn->query($sql)) {
                            echo "Admin table created successfully<br />";
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        echo "Populating admin table...<br />";

                        $password = password_hash("admin", PASSWORD_DEFAULT);

                        $sql = "INSERT INTO " . $dbprefix . "admin (name,pwd) VALUES ('admin','" . $password . "')";

                        if ($conn->query($sql)) {
                            echo "Admin table populated successfully<br /><br />";
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        echo "Creating settings table...<br />";

                        $sql = "CREATE TABLE IF NOT EXISTS " . $dbprefix . "settings (
                        settingID int(11) NOT NULL AUTO_INCREMENT ,
                        site_title VARCHAR(255) DEFAULT NULL ,
                        domain_name VARCHAR(255) DEFAULT NULL ,
                        PRIMARY KEY (settingID)
                        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

                        if ($conn->query($sql)) {
                            echo "Settings created successfully<br /><br />";
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        echo "Creating Messages table...<br />";

                        $sql = "CREATE TABLE IF NOT EXISTS " . $dbprefix . "messages (
                        messageID int(11) NOT NULL AUTO_INCREMENT ,
                        msg VARCHAR(50) NOT NULL ,
                        message VARCHAR(150) NOT NULL ,
                        PRIMARY KEY (messageID)
                        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

                        if ($conn->query($sql)) {
                            echo "Messages table created successfully<br />";
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        echo "Populating Messages table...<br />";

                        $sql = "INSERT INTO " . $dbprefix . "messages (msg, message) VALUES
                        ('pcr','New Poll has been added!'),
                        ('dlt','The Poll has been Deleted!'),
                        ('pwd','Your User Info. has been changed!'),
                        ('pos','The set active operation was successfully!'),
                        ('pas','Your vote counted!'),
                        ('mus','Messages saved!'),
                        ('sich','Site info saved!'),
                        ('pcrf','Failed to create poll!'),
                        ('error', 'An unknown error has occurred.<br />Please contact support.'),
                        ('cpwds', 'You changed the password successfully!'),
                        ('adad', 'You have successfully added an Admin.'),
                        ('das', 'You have successfully deleted an Admin.'),
                        ('ant', 'Admin name taken.'),
                        ('nadmin', 'You can not change this Admins Info.'),
                        ('pes','The poll was edited successfully!')";

                        if ($conn->query($sql)) {
                            echo "Messages table populated successfully<br /><br />";
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        echo "Creating Poll table...<br />";

                        $sql = "CREATE TABLE IF NOT EXISTS " . $dbprefix . "poll (
                        pollID int(11) NOT NULL AUTO_INCREMENT ,
                        poll_name VARCHAR(50) NOT NULL ,
                        poll_question VARCHAR(255) NOT NULL ,
                        hide_results BIT(1) ,
                        poll_revote BIT(1) ,
                        poll_length int(11) ,
                        start_date date ,
                        poll_lock BIT(1) ,
                        poll_active BIT(1) ,
                        PRIMARY KEY (pollID)
                        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

                        if ($conn->query($sql)) {
                            echo "Poll table created successfully<br />";
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        echo "Populating Poll table...<br />";

                        $sql = "INSERT INTO " . $dbprefix . "poll (poll_name,poll_question,hide_results,poll_revote,poll_length,start_date,poll_lock,poll_active) VALUES ('test Poll','This is a test question',0,1,10,'" . date("Y-m-d") . "',0,1)";

                        if ($conn->query($sql)) {
                            echo "Poll table populated successfully<br /><br />";
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        echo "Creating Poll Choices table...<br />";

                        $sql = "CREATE TABLE IF NOT EXISTS " . $dbprefix . "poll_choices (
                        polID int(11) NOT NULL AUTO_INCREMENT,
                        pollID int(10) ,
                        choiceID  int(10) ,
                        pAnswer VARCHAR(50) ,
                        votes int(10) ,
                        PRIMARY KEY (polID)
                        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

                        if ($conn->query($sql)) {
                            echo "Poll Choices  table created successfully<br /><br />";
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        echo "Populating Poll Choices table...<br />";

                        $sql = "INSERT INTO " . $dbprefix . "poll_choices (pollID,choiceID,pAnswer,votes) VALUES
                        (1,1,'Test 1',38) ,
                        (1,2,'Test 2',142) ,
                        (1,3,'Test 3',77) ,
                        (1,4,'Test 4',150)";

                        if ($conn->query($sql)) {
                            echo "Poll Choices table populated successfully<br /><br />";
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        echo "Creating database tables...Complete!<br /><br /><br />";

                        ?>
                    </div>
                </div>
            </div>
            <div id="main" class="container" style="text-align:center;">
                <div class="row 50%">
                    <div class="12u 12u$(medium)">
                        <form action="install.php?step=three" method="post">
                            <input type="hidden" name="servername" value="<?php echo $servername; ?>" />
                            <input type="hidden" name="dbname" value="<?php echo $dbname; ?>" />
                            <input type="hidden" name="username" value="<?php echo $username; ?>" />
                            <input type="hidden" name="dbpassword" value="<?php echo $dbpassword; ?>" />
                            <input type="hidden" name="dbprefix" value="<?php echo $dbprefix; ?>" />
                            <header>
                                <h3>
                                    <span class="first">
                                        You have successfully installed the MySQL Database<br />
                                        Please click the button below to continue
                                    </span>
                                </h3>
                            </header>
                            <div class="row">
                                <div class="12u 12u$(medium)">
                                    <input class="button" type="submit" name="submit" value="Continue" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php

    } else if ($step == "three") {
        $absPath = "";
        $absPath = $_SERVER['DOCUMENT_ROOT'] . "\\";
        ?>
    <div id="main" class="container" style="text-align:center;">
        <div class="row 50%">
            <div class="12u 12u$(medium)">
                <form action="install.php?step=four" method="post">
                    <input type="hidden" name="servername" value="<?php echo test_input($_POST["servername"]) ?>" />
                    <input type="hidden" name="dbname" value="<?php echo test_input($_POST["dbname"]) ?>" />
                    <input type="hidden" name="username" value="<?php echo test_input($_POST["username"]) ?>" />
                    <input type="hidden" name="dbpassword" value="<?php echo test_input($_POST["dbpassword"]) ?>" />
                    <input type="hidden" name="dbprefix" value="<?php echo test_input($_POST["dbprefix"]) ?>" />
                    <header>
                        <h2>Path Settings</h2>
                    </header>
                    <div class="row">
                        <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                            <label for="basedir" style="text-align:left;">
                                Base Directory
                                <input type="text" name="basedir" value="<?php echo $absPath; ?>" />
                            </label>
                        </div>
                        <div class="4u 1u$">
                            <span></span>
                        </div>

                        <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                            <label for="polldir" style="text-align:left;">
                                PHP Poll Directory
                                <input type="text" name="polldir" value="/poll/" size="40" />
                            </label>
                        </div>
                        <div class="4u 1u$">
                            <span></span>
                        </div>
                        <div class="12u 12u$(medium)">
                            <input class="button" type="submit" name="submit" value="Continue" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
                <?php
    } else if ($step == "four") {

        $file = $fileA = "";

        $servername = test_input($_POST["servername"]);
        $username = test_input($_POST["username"]);
        $dbpassword = test_input($_POST["dbpassword"]);
        $dbname = test_input($_POST["dbname"]);
        $dbprefix = test_input($_POST["dbprefix"]);
        $basedir = test_inputA($_POST["basedir"]);
        $polldir = test_input($_POST["polldir"]);

        $basedir = preg_replace("/([\\\])/", '${1}${1}', $basedir);

        $file = fopen('../includes/globals.php', "r");
        $fileA = fread($file, filesize('../includes/globals.php'));
        fclose($file);

        $file = fopen('../includes/globals.php', "w");

        $fileA = str_replace("{#servername#}", $servername, $fileA);
        $fileA = str_replace("{#username#}", $username, $fileA);
        $fileA = str_replace("{#dbpassword#}", $dbpassword, $fileA);
        $fileA = str_replace("{#dbname#}", $dbname, $fileA);
        $fileA = str_replace("{#dbprefix#}", $dbprefix, $fileA);
        $fileA = str_replace("{#basedir#}", $basedir, $fileA);
        $fileA = str_replace("{#polldir#}", $polldir, $fileA);

        fwrite($file, $fileA);

        fclose($file);
        ?>
    <div id="main" class="container" style="text-align:center;">
        <div class="row 50%">
            <div class="12u 12u$(medium)">
                <form action="install.php?step=five" method="post">
                    <input type="hidden" name="servername" value="<?php echo $servername; ?>" />
                    <input type="hidden" name="dbname" value="<?php echo $dbname; ?>" />
                    <input type="hidden" name="username" value="<?php echo $username; ?>" />
                    <input type="hidden" name="dbpassword" value="<?php echo $dbpassword; ?>" />
                    <input type="hidden" name="dbprefix" value="<?php echo $dbprefix; ?>" />
                    <input type="hidden" name="polldir" value="<?php echo $polldir; ?>" />
                    <header>
                        <h3>
                            <span class="first">
                                You have successfully set the configuration file<br />
                                Please click the button below to continue
                            </span>
                        </h3>
                    </header>
                    <div class="row">
                        <div class="12u 12u$(medium)">
                            <input class="button" type="submit" name="submit" value="Continue" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
                    <?php

    } else if ($step == "five") {

        $servername = test_input($_POST["servername"]);
        $username = test_input($_POST["username"]);
        $dbpassword = test_input($_POST["dbpassword"]);
        $dbname = test_input($_POST["dbname"]);
        $dbprefix = test_input($_POST["dbprefix"]);
        $polldir = test_input($_POST["polldir"]);

        ?>
    <div id="main" class="container" style="margin-top:-100px;">
        <div class="row">
            <div class="12u 12u$(medium)" style="text-align:center;">
                <form action="install.php?step=six" method="post">
                    <input type="hidden" name="servername" value="<?php echo $servername; ?>" />
                    <input type="hidden" name="dbname" value="<?php echo $dbname; ?>" />
                    <input type="hidden" name="username" value="<?php echo $username; ?>" />
                    <input type="hidden" name="dbpassword" value="<?php echo $dbpassword; ?>" />
                    <input type="hidden" name="dbprefix" value="<?php echo $dbprefix; ?>" />
                    <input type="hidden" name="polldir" value="<?php echo $polldir; ?>" />
                    <header>
                        <h2>Other stuff</h2>
                    </header>
                    <div class="row">

                        <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                            <label for="sitetitle" style="text-align:left;">
                                Site title
                                <input type="text" name="sitetitle" required />
                            </label>
                        </div>
                        <div class="4u 1u$">
                            <span></span>
                        </div>

                        <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                            <label for="domainname" style="text-align:left;">
                                Domain name
                                <input type="text" name="domainname" value="<?php echo $_SERVER["SERVER_NAME"]; ?>" />
                            </label>
                        </div>
                        <div class="4u 1u$">
                            <span></span>
                        </div>

                        <div class="12u 12u$(medium)">
                            <input class="button" type="submit" name="submit" value="Continue" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
                        <?php
    } else if ($step == "six") {

        $servername = test_input($_POST["servername"]);
        $username = test_input($_POST["username"]);
        $dbpassword = test_input($_POST["dbpassword"]);
        $dbname = test_input($_POST["dbname"]);
        $dbprefix = test_input($_POST["dbprefix"]);
        $polldir = test_input($_POST["polldir"]);

        $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $param1 = test_input($_POST["sitetitle"]);
        $param2 = test_input($_POST["domainname"]);

        $stmt = $conn->prepare("INSERT INTO " . $dbprefix . "settings (site_title, domain_name) VALUES (?,?)");
        $stmt->bind_param("ss", $param1, $param2);

        if ($stmt->execute()) {

            if ($_SERVER["HTTPS"] == "off") {
                $http = "http";
            } else {
                $http = "https";
            }
            ;

            $httpHost = $_SERVER["HTTP_HOST"];
            $redirect = $http . "://" . $httpHost . $polldir;
            redirect($redirect . "install/install.php?step=done");
            ob_end_flush();

        }
        $conn->close();

    } else if ($step == "done") {
        ?>
    <div id="main" class="container">
        <div class="row">
            <div class="12u 12u$(medium)" style="text-align:center;">
                <span class="first">
                    Success!
                    <br />
                    You have successfully configured PHP Poll!
                    <br />
                    The next step is to change your password.
                    <br />
                    Click on the link below and login to admin.
                    <br />
                    Click on "Password" in the left options menu and change your password.
                    <br />
                    <br />
                    <a class="first" href="../admin/admin_login.php">Login</a>
                </span>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div id="main" class="container" style="margin-top:-75px;">
        <div class="row">
            <div class="12u 12u$(medium)" style="text-align:center;">
                <span class="first">
                    You are about to install PHP Poll.
                    <br />
                    Before you start the installation please read the readme.txt file and follow the instructions!
                    <br />
                    <br />
                    <input class="button" type="button" onclick="parent.location='install.php?step=one'" value="Continue" />
                    <br />
                    <br />
                </span>
            </div>
        </div>
    </div>
<?php } ?>
    <br />
</body>
</html>