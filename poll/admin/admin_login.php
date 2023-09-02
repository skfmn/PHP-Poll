<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$cookies = "";

if (isset($_SESSION["polladminname"])) {
    $cookies = $_SESSION["polladminname"];
}


if ($cookies != "") {

  redirect($redirect . "admin/admin.php");
  ob_end_flush();

}

$username = $pwd = $nPassword = "";

if (isset($_POST["uname"])) {
  $username = test_input($_POST["uname"]);
}
if (isset($_POST["pwd"])) {
  $password = test_input($_POST["pwd"]);
}

if ($username <> "") {

  $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $sql = "SELECT * FROM " . DBPREFIX . "admin WHERE name = '" . $username . "'";

  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['pwd'])) {

      $_SESSION["polladminID"] = $row["adminID"];
      $_SESSION["polladminname"] = $username;

      redirect($redirect . "admin/admin.php");
      ob_end_flush();
    }
  } else {

    redirect($redirect . "admin/admin_login.php");
    ob_end_flush();

  }
  $conn->close();
}
include "../includes/header.php"
?>
<div id="main" class="container" style="text-align:center;">
  <div class="row 50%">
    <div class="12u 12u$(medium)">
      <header>
        <h2>PHP Poll Admin Login</h2>
      </header>
    </div>
    <div class="12u 12u$(medium)">

      <form action="admin_login.php" method="POST">
        <div class="row">
          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="name">Name</label>
            <input type="text" id="uname" name="uname" required />
          </div>
          <div class="4u 1u$">
            <span></span>
          </div>

          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="pwd">Password</label>
            <div class="input-wrapper">
              <input type="password" id="pwd" name="pwd" />
              <br />
              <i id="shpwd" onclick="togglePass('pwd','shpwd')" style="cursor:pointer;" class="fa fa-eye-slash shpwd"></i>
            </div>
          </div>
          <div class="4u 1u$">
            <span></span>
          </div>

          <div class="12u 12u$(medium)">
            <input class="button" type="submit" value="Let me in!" />
          </div>
        </div>

      </form>

    </div>
  </div>
</div>
<?php include "../includes/footer.php"; ?>