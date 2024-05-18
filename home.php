<?php
include("navbar.php");
include("db/connection.php");

if (!isset($_SESSION['unique_id'])) {
  header("location: login.php");
}
?>

<head>
  <link rel="stylesheet" href="style.css">
  <style>
    .sidebar {
      height: 100vh;
      width: 350px;
      background-color: lightgreen;
      color: #fff;
      padding: 20px;
      overflow: auto;
      border-right: 1px solid black;
    }

    .sidebar h2 {
      margin-bottom: 20px;
    }

    .sidebar .form-control {
      width: 260px;
      background: transparent;
      padding: 8px 35px;
      border-radius: 5px;
      text-align: left;
      color: white;
      box-shadow: 0 0 1px 1px #fff;
    }

    .sidebar .form-control:focus {
      outline: none;
    }

    .sidebar .form-control::placeholder {
      color: white;
    }

    .sidebar .search-icon {
      position: absolute;
      top: 91px;
      left: 30px;
    }

    .sidebar .users-content a:hover {
      filter: brightness(.8);
    }

    .sidebar .users-content img {
      width: 55px;
      object-position: center center;
      object-fit: cover;
    }

    .main-page {
      background: #414a4c;
      color: #fff;
    }

    .chat-header {
      padding: 10px 20px;
      border-bottom: 1px solid black;
    }

    .chat-header img {
      width: 45px;
      object-position: center center;
      object-fit: cover;
    }

    .chat-section {
      overflow-y: auto !important;
    }

    .chat-section .chat-history {
      padding: 10px 20px;
    }

    .chat-section .chat-history .chat-messages .sender-chats {
      background: #00A36C;
      border-radius: 15px;
      width: 400px;
      height: auto;
      padding: 10px 15px;
      margin-top: 15px;
    }

    .receiver-messages .receiver-chats {
      background: #343434;
      border-radius: 15px;
      width: 400px;
      height: auto;
      padding: 10px 15px;
      margin-top: 15px;
    }

    .chat_time-history {
      font-size: 10px;
    }

    .chat-footer .input_form .form-control {
      border-radius: 0;
      bottom: 0;
      padding: 20px 50px;
      border: none;
      word-break: break-all;
    }

    .chat-footer .input_form .form-control:focus {
      box-shadow: none;
    }

    .chat-footer .input_form .form-control::placeholder {
      color: #fff;
    }

    .send-btn button {
      background: none;
      border: none;
    }

    .send-btn button i {
      bottom: 22px;
      right: 240px;
      color: #fff;
    }

    .sidebar .content img {
      width: 50px;
      height: 50px;
    }

    .users {
      padding: 0;
    }

    .users .search {
      margin: 5px 0;
    }

    .users .search input {
      top: 45px;
    }

    .users .search button {
      top: 37px;
      right: 15px;
    }

    .sidebar .users-list {
      margin-top: 40px;
    }

    .sidebar .users-list a {
      color: #fff !important;
      text-decoration: none;
      align-items: center;
    }

    .sidebar .users-list .details {
      color: #fff !important;
      margin-top: 15px;
      margin-left: 15px;
    }

    .wrapper {
      max-width: none;
    }
  </style>
</head>

<body>
  <div class="wrapper d-flex">
    <div class="users">
      <aside class="sidebar bg-dark position-relative">
        <div class="content d-flex align-items-center">
          <?php
          $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
          if (mysqli_num_rows($sql) > 0) {
            $row = mysqli_fetch_assoc($sql);
          }
          ?>
          <img src="<?php echo $row['img']; ?>" class="rounded-circle" alt="">
          <div class="details ms-3 mt-3 text-white">
            <span><?php echo $row['name'] ?></span>
            <p><?php echo $row['status']; ?></p>
          </div>
        </div>

        <div class="search">
          <h2>Chats</h2>
          <input type="text" placeholder="Select an user or search to start chat">
          <button><i class="fas fa-search"></i></button>
        </div>
        <div class="users-list text-white"></div>
      </aside>
    </div>

    <main class="">
      <!-- <header class="chat-header bg-dark d-flex align-items-center text-white">

      </header>

      <section class="chat-section">
        <div class="chat-history">
          <div class="chat-messages"></div>
        </div>
      </section> -->
    </main>
  </div>

  <script src="javascript/users.js"></script>
  <script src="javascript/chat.js"></script>

</body>

</html>