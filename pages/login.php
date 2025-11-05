<?php
session_start();
include '../config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT id, username, email, password, role, display_name, profile_image 
            FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["profile_image"] = $user["profile_image"];
            $_SESSION["display_name"] = $user["display_name"];

            header("Location: ../index.php");
            exit;
        } else {
            $message = "Incorrect password!";
        }
    } else {
        $message = "No user found with this email!";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Blog</title>
  <link rel="stylesheet" href="../assets/auth.css">
</head>
<body>
<div class="firefly"></div>
<div class="firefly"></div>
<div class="firefly"></div>
<div class="firefly"></div>
<div class="firefly"></div>
<div class="firefly"></div>
<div class="firefly"></div>
<div class="firefly"></div>
<div class="firefly"></div>
<div class="firefly"></div>

<div class="auth-container">
  <div class="auth-card">
    <h2>Sign in to Your Account</h2>
    <?php if (!empty($message)): ?>
      <p class="error-msg"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <label>Email</label>
      <input type="email" name="email" placeholder="example@domain.com" required>

      <label>Password</label>
      <input type="password" name="password" placeholder="Enter your password" required>

      <button type="submit">Login</button>
    </form>

    <div class="auth-links">
      <a href="register.php">Don’t have an account? <strong>Register</strong></a>
    </div>
  </div>
</div>

</body>
</html>
