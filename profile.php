<?php
include("navbar.php");
include("./db/connection.php");

// Check if user is logged in
if (empty($_SESSION['unique_id'])) {
    header("LOCATION: login.php");
    exit();
}

$uniqueId = $_SESSION['unique_id'];

// Retrieve user data from MySQL database based on unique_id
$sql = "SELECT * FROM users WHERE unique_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uniqueId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Redirect or handle error if user data not found
    header("LOCATION: login.php");
    exit();
}

$userData = $result->fetch_assoc();

// Define the upload directory
$uploadDirectory = 'images/';

// Process form submission to update user data
if (isset($_POST['updateData'])) {
    // Retrieve updated form data
    $updatedName = $_POST['name'];
    $updatedPhone = $_POST['phone'];
    $updatedDob = $_POST['dob'];
    $updatedGender = $_POST['gender'];

    // Check if a new password is provided
    $updatedPassword = $_POST['password'];
    $passwordConfirmed = $_POST['confirm_password'];

    if (!empty($updatedPassword) && $updatedPassword === $passwordConfirmed) {
        // Hash the new password
        $hashedPassword = password_hash($updatedPassword, PASSWORD_BCRYPT);
        // Update user data in MySQL database with the new password
        $sql = "UPDATE users SET name = ?, phone = ?, dob = ?, gender = ?, password = ? WHERE unique_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $updatedName, $updatedPhone, $updatedDob, $updatedGender, $hashedPassword, $uniqueId);
    } else {
        // Update user data in MySQL database without changing the password
        $sql = "UPDATE users SET name = ?, phone = ?, dob = ?, gender = ? WHERE unique_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $updatedName, $updatedPhone, $updatedDob, $updatedGender, $uniqueId);
    }

    if ($stmt->execute()) {
        // Handle profile picture upload if provided
        if ($_FILES['fileUpload']['size'] > 0) {
            $fileTmpPath = $_FILES['fileUpload']['tmp_name'];
            $fileName = time() . '-' . $_FILES['fileUpload']['name']; // Use a unique name to avoid overwriting
            $uploadPath = $uploadDirectory . $fileName;

            // Move uploaded file to the specified directory
            if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                $profilePictureUrl = $uploadPath;

                // Delete old profile picture if exists and update with new URL
                if (!empty($userData['img']) && file_exists($userData['img'])) {
                    unlink($userData['img']);
                }

                // Update the profile picture URL in MySQL database
                $sql = "UPDATE users SET img = ? WHERE unique_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $profilePictureUrl, $uniqueId);
                $stmt->execute();
            }
        }

        // Redirect to profile page with success message
        $_SESSION['changesSaved'] = true;
        header("LOCATION: profile.php");
        exit();
    } else {
        $errorMessage = "<div class='alert alert-danger text-center mt-4'>Error updating record: " . $stmt->error . "</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .form-control {
            border: none;
            border-bottom: 1px solid #0dcaf0;
        }

        .form-control:focus {
            box-shadow: none;
        }

        .image-data {
            margin-top: 112px;
        }

        .profile_button .btn {
            width: 180px;
            text-transform: uppercase;
            transition: all .3s ease-in-out;
            padding: 10px 0;
        }

        .profile_button .btn:hover {
            letter-spacing: 2px;
            transition: all .3s ease-in-out;
            background: #198754;
            border-color: #198754;
        }
    </style>
</head>

<body>
    <form class="edit_profile_form" method="POST" enctype="multipart/form-data">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-lg-4 left-side">
                    <div class="form-group image-data text-center">
                        <?php
                        // Display the profile picture from the URL stored in user data
                        if (!empty($userData['img'])) {
                            echo '<img src="' . htmlspecialchars($userData['img']) . '" class="img-thumbnail" width="250" alt="Profile Picture">';
                        } else {
                            echo '<img src="default_profile.jpg" class="img-thumbnail" width="250" alt="Default Profile Picture">';
                        }
                        ?>
                    </div>
                    <div class="form-group mt-4">
                        <label for="profile_pic">Update Picture:</label>
                        <input type="file" class="form-control mt-3" id="fileUpload" name="fileUpload" accept="image/*">
                    </div>
                </div>
                <div class="col-lg-8 col-md-6 right-side">
                    <h2 class="mb-4 text-center">Update Profile</h2>
                    <input type="hidden" value="<?php echo htmlspecialchars($uniqueId); ?>" name="userEmail">
                    <div class="form-group">
                        <label for="name">Your Name:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($userData['name']); ?>" required>
                    </div>
                    <div class="form-group mt-4">
                        <label for="email">Email Address:</label>
                        <input style="background: none; cursor:not-allowed" disabled type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                    </div>
                    <div class="form-group mt-4">
                        <label for="phone">Phone Number:</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($userData['phone']); ?>" required>
                    </div>
                    <div class="form-group mt-4">
                        <label for="dob">Date of Birth:</label>
                        <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($userData['dob']); ?>" required>
                    </div>
                    <div class="form-group mt-4">
                        <label for="gender">Gender:</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="male" <?php echo ($userData['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($userData['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo ($userData['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group mt-4">
                        <label for="password">New Password:</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="form-group mt-4">
                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>

                    <?php
                    // Display "Changes Saved" message if changes were successfully saved
                    if (isset($_SESSION['changesSaved']) && $_SESSION['changesSaved']) {
                        echo "<div class='alert alert-success text-center mt-4'>Changes Saved!</div>";
                        // Unset the session variable to prevent showing the message again on page refresh
                        unset($_SESSION['changesSaved']);
                    }
                    ?>

                    <?php echo isset($errorMessage) ? $errorMessage : ''; ?>

                    <div class="profile_button text-center mt-4 mb-2">
                        <button type="submit" class="btn btn-primary" name="updateData">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</body>

</html>