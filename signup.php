<?php
include('navbar.php');
include('./db/connection.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $dob = mysqli_real_escape_string($conn, $_POST['dob']);
  $gender = mysqli_real_escape_string($conn, $_POST['gender']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  if (!empty($name) && !empty($email) && !empty($phone) && !empty($dob) && !empty($gender) && !empty($password)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
      if (mysqli_num_rows($sql) > 0) {
        $error = "$email - This email already exist!";
      } else {
        if (isset($_FILES['image'])) {
          $img_name = $_FILES['image']['name'];
          $img_type = $_FILES['image']['type'];
          $tmp_name = $_FILES['image']['tmp_name'];

          $img_explode = explode('.', $img_name);
          $img_ext = end($img_explode);

          $extensions = ["jpeg", "png", "jpg"];

          if (in_array($img_ext, $extensions) === true) {
            $types = ["image/jpeg", "image/jpg", "image/png"];

            if (in_array($img_type, $types) === true) {
              $time = time();
              $new_img_name = "images/" . $time . $img_name;

              if (move_uploaded_file($tmp_name, $new_img_name)) {
                $ran_id = rand(time(), 100000000);
                $status = "Active now";
                $password = password_hash($password, PASSWORD_DEFAULT);
                $insert_query = mysqli_query($conn, "INSERT INTO users (unique_id, name, email, phone, dob, gender, password, img, status)
                                VALUES ({$ran_id}, '{$name}', '{$email}', '{$phone}', '{$dob}', '{$gender}', '{$password}', '{$new_img_name}', '{$status}')");

                if ($insert_query) {
                    header("LOCATION: login.php");
                    exit();
                } else {
                  $error = "<div class='alert alert-danger text-center'>Something went wrong. Please try again!</div>";
                }
              }
            } else {
              $error = "<div class='alert alert-danger text-center'>Please upload an image file - jpeg, png, jpg</div>";
            }
          } else {
            $error = "<div class='alert alert-danger text-center'>Please upload an image file - jpeg, png, jpg</div>";
          }
        }
      }
    } else {
      $error = "<div class='alert alert-danger text-center'>$email is not a valid email!</div>";
    }
  } else {
    $error = "<div class='alert alert-danger text-center'>All input fields are required!</div>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="style.css">

  <style>
    .form-control {
      border: none;
      border-bottom: 1px solid #0dcaf0;
    }

    .form-control:focus {
      box-shadow: none;
    }

    .signup_button .btn {
      width: 128px;
      text-transform: uppercase;
      transition: all .3s ease-in-out;
    }

    .signup_button .btn:hover {
      letter-spacing: 2px;
      transition: all .3s ease-in-out;
      background: #198754;
      border-color: #198754;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <h2 class="mb-4 text-center">Sign Up</h2>
    <div class="row justify-content-center">
      <div class="col-6">
        <form class="signup_form" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label for="username">Your Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="form-group mt-3">
            <label for="email">Email Address:</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="form-group mt-3">
            <label for="phone">Phone Number:</label>
            <input type="text" class="form-control" id="phone" name="phone" required>
          </div>
          <div class="form-group mt-3">
            <label for="dob">Date of Birth:</label>
            <input type="date" class="form-control" id="dob" name="dob" required>
          </div>
          <div class="form-group mt-3">
            <label for="gender">Gender:</label>
            <select class="form-control" id="gender" name="gender" required>
              <option value="select" hidden>Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="form-group mt-3">
            <label for="password">Enter Your Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="form-group mt-4">
            <label for="profile">Upload Picture:</label>
            <input type="file" class="form-control mt-3" id="image" name="image" accept="image/*" required>
          </div>
          <div class="text-center mt-4">
            <p>Already a member?
              <a href="login.php" class="text-decoration-none">login</a>
            </p>
          </div>
          <?php echo $error ?>
          <div class="signup_button text-center mb-2 mt-5">
            <button type="submit" name="submit" action="login.php" class="btn btn-primary">Sign Up</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/signup.js"></script>
</body>

</html>