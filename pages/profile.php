<?php
session_start();
include '../config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}


$user_id = $_SESSION["user_id"];
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $display_name = trim($_POST["display_name"]);
    $profile_image = null;

    // Handle image upload if provided
    if (!empty($_FILES["profile_image"]["name"])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $profile_image = basename($_FILES["profile_image"]["name"]);
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetDir . $profile_image);
    }

    // Update profile
    $sql = "UPDATE users 
            SET display_name = ?, 
                profile_image = IFNULL(?, profile_image)
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $display_name, $profile_image, $user_id);
    $stmt->execute();
    $stmt->close();

     if ($profile_image) {
        $_SESSION["profile_image"] = $profile_image;
    }

    $message = "Profile updated successfully!";
    
}


// Fetch updated user info
$sql = "SELECT username, email, display_name, profile_image FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include '../components/navbar.php'; ?>

<div class="profile-container">
  <h2>My Profile</h2>

  <?php if ($message): ?>
    <p class="success-msg"><?php echo htmlspecialchars($message); ?></p>
  <?php endif; ?>

  <div class="profile-card">
    <div class="profile-pic-wrapper">
      <img 
        src="<?php echo $user['profile_image'] 
          ? '../uploads/' . htmlspecialchars($user['profile_image']) 
          : '../assets/default-avatar.png'; ?>" 
        alt="Profile Picture" 
        class="profile-pic"
      >
    </div>

    <form method="POST" enctype="multipart/form-data">
        <label>Change Profile Picture:</label><br>
      <input type="file" name="profile_image" accept="image/*"><br><br>
      <label>Display Name:</label><br>
      <input type="text" name="display_name" 
             value="<?php echo htmlspecialchars($user['display_name']); ?>" 
             required><br><br>
      <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

      <button type="submit" class="btn-filled">Save Changes</button>
    </form>
  </div>
</div>

<?php include '../components/footer.php'; ?>

</body>
</html>
