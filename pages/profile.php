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
  <link rel="stylesheet" href="/php_blog_project/assets/nav.css">
  <link rel="stylesheet" href="/php_blog_project/assets/footer.css">
  <link rel="icon" type="image/x-icon" href="../favicon.ico">

</head>
<body>

<?php include '../components/navbar.php'; ?>

<main class="profile-layout">
  <section class="profile-container">
    <header class="profile-header">
      <span class="pill">Profile</span>
      <h1>Account Preferences</h1>
      <p class="profile-subtitle">Refresh your details to keep your public profile polished.</p>
    </header>

    <?php if ($message): ?>
      <p class="success-msg"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="profile-form">
      <div class="profile-form__grid">
        <div class="profile-form__visual">
          <div class="profile-pic-wrapper">
            <img
              src="<?php echo $user['profile_image']
                ? '../uploads/' . htmlspecialchars($user['profile_image'])
                : '../assets/default-avatar.png'; ?>"
              alt="Profile picture"
              class="profile-pic"
            >
          </div>
          <p class="profile-avatar-hint">This avatar appears beside your posts and comments.</p>

          <div class="profile-details">
            <div class="profile-info-chip">
              <span class="profile-info-label">Username</span>
              <span class="profile-info-value"><?php echo htmlspecialchars($user['username']); ?></span>
            </div>
            <div class="profile-info-chip">
              <span class="profile-info-label">Email</span>
              <span class="profile-info-value"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
          </div>
        </div>

        <div class="profile-form__fields">
          <div class="profile-field">
            <label for="profile_image">Profile picture</label>
            <div class="file-input-wrapper profile-file-input">
              <input type="file" name="profile_image" id="profile_image" accept="image/*">
              <span class="file-input-label">Click or drag an image to upload</span>
              <span class="field-hint">PNG or JPG up to 2&nbsp;MB recommended.</span>
            </div>
          </div>

          <div class="profile-field">
            <label for="display_name">Display name</label>
            <input type="text" name="display_name" id="display_name"
                   value="<?php echo htmlspecialchars($user['display_name']); ?>"
                   required>
          </div>
        </div>
      </div>

      <div class="profile-actions">
        <button type="submit" class="btn-filled">Save changes</button>
      </div>
    </form>
  </section>
</main>

<?php include '../components/footer.php'; ?>

</body>
</html>
