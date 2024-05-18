<?php
session_start();
include "db/connection.php";

if (isset($_GET['unique_id'])) {
    $id = $_GET["unique_id"];
    $sql = "DELETE FROM `users` WHERE unique_id = $id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        session_destroy();
        header("Location: login.php");
        exit();
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
} else {
    echo "error while deleteing account";
}