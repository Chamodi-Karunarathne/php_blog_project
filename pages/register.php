<?php
include '../config.php';    

$message = "Hello, Please Register Yourself to access the blog!";
$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $display_name = trim($_POST["display_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"]; // use raw value for validation first

    // --- Password Validation ---
    if ($username === "" || strlen(trim($username)) == 0) {
        $errors[] = "Username cannot be empty or contain only spaces.";
    }

    if (preg_match('/\s/', $password)) {
        $errors[] = "Password cannot contain spaces.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Proceed only if there are no validation errors
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, display_name, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $display_name, $email, $hashedPassword);

        if ($stmt->execute()) {
            header("Location: ../index.php");
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = implode("<br>", $errors);     // Join all errors into a single string to display
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | Blog</title>
  <link rel="stylesheet" href="../assets/auth.css">
</head>
<body>
<div class="wave"></div>
<div class="wave"></div>
<div class="wave"></div>
<div class="auth-container">
  <div class="auth-card">
    <h2>Create an Account</h2>
    <?php if (!empty($message)): ?>
      <p class="error-msg"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <label>Username</label>
      <input type="text" name="username" placeholder="Your username" required>

      <label>Display Name</label>
      <input type="text" name="display_name" placeholder="Your display name" required>

      <label>Email</label>
      <input type="email" name="email" placeholder="example@domain.com" required>

      <label>Password</label>
      <input type="password" name="password" placeholder="Create a password" required>

      <button type="submit">Register</button>
    </form>

    <div class="auth-links">
      <a href="login.php">Already have an account? <strong>Login</strong></a>
    </div>
  </div>
</div>

</body>
</html>
