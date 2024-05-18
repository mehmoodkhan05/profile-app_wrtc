<?php
include("navbar.php");
include('db/connection.php');

if (!isset($_SESSION['unique_id'])) {
  header("location: login.php");
}
?>

<head>
  <link rel="stylesheet" href="style.css">
  <style>
    .wrapper {
      max-width: none;
    }

    .wrapper .chat-area header, .typing-area {
      background: #414a4c;
    }

    .wrapper .chat-area .chat-box {
      background: #414a4c7a;
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <section class="chat-area">
      <header>
        <?php
        $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$user_id}");
        if (mysqli_num_rows($sql) > 0) {
          $row = mysqli_fetch_assoc($sql);
        } else {
          header("location: home.php");
        }
        ?>
        <a href="home.php" class="back-icon text-white"><i class="fas fa-arrow-left"></i></a>
        <img src="<?php echo $row['img']; ?>" alt="">
        <div class="details text-white">
          <span><?php echo $row['name'] ?></span>
          <p class="mb-0"><?php echo $row['status']; ?></p>
        </div>
      </header>
      <div class="chat-box"></div>
      <form action="#" class="typing-area">
        <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
        <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
        <button><i class="fab fa-telegram-plane"></i></button>
      </form>
    </section>
  </div>

  <script src="javascript/chat.js"></script>

</body>

</html>