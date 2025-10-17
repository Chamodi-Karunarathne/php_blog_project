<?php
include '../config.php';    

$message = "Hello, Please Register Yourself to access the blog!";
$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
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
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            $message = "Registration successful!";
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
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="../assets/style.css"> 
</head>
<body>
    <h2>Register</h2>
    <p style="color: red;"><?php echo $message; ?></p>

    <form method="POST" action="">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
