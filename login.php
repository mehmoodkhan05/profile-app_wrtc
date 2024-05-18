<?php
include("navbar.php");
include("db/connection.php");

$validlogin = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Retrieve and sanitize email and password
  $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
  $password = mysqli_real_escape_string($conn, $_POST['password'] ?? '');

  if (!empty($email) && !empty($password)) {
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE `email` = '{$email}'");
    if (mysqli_num_rows($sql) > 0) {
      $row = mysqli_fetch_assoc($sql);
      $stored_hashed_password = $row['password'];  // Retrieve hashed password from the database

      // Use password_verify to check if the entered password matches the stored hashed password
      if (password_verify($password, $stored_hashed_password)) {
        $status = "Active now";
        $sql2 = mysqli_query($conn, "UPDATE users SET status = '{$status}' WHERE `unique_id` = {$row['unique_id']}");
        if ($sql2) {
          $_SESSION['unique_id'] = $row['unique_id'];
          header("Location: home.php");
          exit(); // Ensure script execution stops after redirection
        } else {
          $validlogin = "<div class='alert alert-danger text-center'>Something went wrong. Please try again!</div>";
        }
      } else {
        $validlogin = "<div class='alert alert-danger text-center'>Email or Password is Incorrect!</div>";
      }
    } else {
      $validlogin = "<div class='alert alert-danger text-center'>$email - This email does not exist!</div>";
    }
  } else {
    $validlogin = "<div class='alert alert-danger text-center'>All input fields are required!</div>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">

  <style>
    .form-control {
      border: none;
      border-bottom: 1px solid #0dcaf0;
    }

    .form-control:focus {
      box-shadow: none;
      border-bottom: 1px solid #0dcaf0;
    }

    .login_button .btn {
      width: 108px;
      text-transform: uppercase;
      transition: all .3s ease-in-out;
    }

    .login_button .btn:hover {
      letter-spacing: 2px;
      transition: all .3s ease-in-out;
      background: #198754;
      border-color: #198754;
    }

    .form-group i {
      position: absolute;
      right: 15px;
      bottom: 3px;
      color: #0dcaf0;
      cursor: pointer;
      transform: translateY(-50%);
    }

    .form-group i.active::before {
      color: #333;
      content: "\f070";
    }
  </style>
</head>

<body>
  <section class="login_section">
    <div class="container">
      <div class="row justify-content-center">
        <h2 class="mt-5 mb-5 text-center">Login</h2>
        <div class="col-lg-5">
          <form class="login_form" action="" method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="form-group">
              <label for="email">Email:</label>
              <input type="text" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group mt-4 position-relative">
              <label for="password">Password:</label>
              <input type="password" class="form-control" id="password" name="password" required>
              <!-- <i class="fas fa-eye"></i> -->
            </div>
            <div class="text-center mt-4">
              <p>Dont have an account?
                <a href="signup.php" class="text-decoration-none">Sign up</a>
              </p>
            </div>

            <?php echo $validlogin ?>

            <div class="login_button text-center">
              <button type="submit" value="login" name="login" class="btn btn-primary mt-5">Login</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  <script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/login.js"></script>|
</body>

</html>