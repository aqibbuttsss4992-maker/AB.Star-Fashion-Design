<?php
session_start();

// Set your admin username & password
$adminUser = "admin";
$adminPass = "1234"; // change this to a strong password!

// -------------------------
// Handle Login
// -------------------------
if(isset($_POST['login'])){
    $user = $_POST['username'];
    $pass = $_POST['password'];

    if($user === $adminUser && $pass === $adminPass){
        $_SESSION['admin'] = true;
        $loginMsg = "Logged in successfully!";
    } else {
        $loginMsg = "Invalid username or password!";
    }
}

// -------------------------
// Handle Logout
// -------------------------
if(isset($_GET['logout'])){
    session_destroy();
    header("Location: index.php");
    exit;
}

// -------------------------
// Handle Admin Upload
// -------------------------
if(isset($_POST['upload']) && isset($_SESSION['admin'])){
    $desc = $_POST['description'];
    $file = $_FILES['photo']['name'];
    $target = "uploads/".basename($file);

    if(move_uploaded_file($_FILES['photo']['tmp_name'], $target)){
        $data = $file."||".$desc.PHP_EOL;
        file_put_contents("photos.txt", $data, FILE_APPEND);
        $msg = "Photo uploaded successfully!";
    } else {
        $msg = "Failed to upload photo!";
    }
}

// -------------------------
// Handle Orders
// -------------------------
if(isset($_POST['order'])){
    $photo = $_POST['photo'];
    $to = "abstat4992@gmail.com"; // your Gmail
    $subject = "New Order Request";
    $message = "Someone wants to order: $photo";
    $headers = "From: orders@yourwebsite.com";

    if(mail($to, $subject, $message, $headers)){
        $orderMsg = "Order sent successfully!";
    } else {
        $orderMsg = "Failed to send order.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>AB.Star Fashion & Design</title>
<style>
body { font-family: Arial,sans-serif; margin:0; background:#f5f5f5;}
header { background:#111; color:#fff; padding:20px; text-align:center;}
.logo { max-width:180px; margin-bottom:10px; width:50%; height:auto; }
section { padding:20px; max-width:1100px; margin:auto; }
h2 { text-align:center; margin-bottom:15px;}
.gallery { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:15px;}
.gallery div { text-align:center; }
.gallery img { width:100%; border-radius:10px; object-fit:cover; height:200px; transition: all 0.3s ease; }
.gallery img:hover { transform:scale(1.05); }
.buttons { display:flex; justify-content:center; gap:15px; flex-wrap:wrap; margin-top:20px; }
.btn { padding:12px 20px; border-radius:30px; color:#fff; text-decoration:none; font-size:16px; cursor:pointer; }
.call { background:#28a745; }
.whatsapp { background:#25D366; }
.email { background:#007bff; }
.orderBtn { background:#FF5733; border:none; }
footer { background:#111; color:#fff; text-align:center; padding:15px; margin-top:30px; }
form { margin-top:10px; }
input[type=text], input[type=file], input[type=password] { padding:8px; margin:5px 0; width:90%; border-radius:5px; border:1px solid #ccc; }
input[type=submit] { padding:10px 15px; border:none; border-radius:5px; background:#111; color:#fff; cursor:pointer; }
input[type=submit]:hover { opacity:0.8; }
.msg { text-align:center; color:green; margin-bottom:15px; }
.error { text-align:center; color:red; margin-bottom:15px; }
</style>
</head>
<body>

<header>
  <img src="logo.jpg" alt="AB.Star Fashion & Design Logo" class="logo" />
  <h1>AB.Star Fashion & Design</h1>
  <p>Premium Clothing • Modern Style • Trusted Quality</p>
</header>

<?php if(!isset($_SESSION['admin'])): ?>
<section>
  <h2>Admin Login</h2>
  <?php if(isset($loginMsg)) echo "<p class='error'>$loginMsg</p>"; ?>
  <form method="post">
    Username: <input type="text" name="username" placeholder="Admin Username" required><br>
    Password: <input type="password" name="password" placeholder="Password" required><br>
    <input type="submit" name="login" value="Login">
  </form>
</section>
<?php else: ?>
<section>
  <h2>Admin Upload <a href="index.php?logout=true" style="float:right; color:red; text-decoration:none;">Logout</a></h2>
  <?php if(isset($msg)) echo "<p class='msg'>$msg</p>"; ?>
  <form method="post" enctype="multipart/form-data">
    Photo: <input type="file" name="photo" required><br>
    Description: <input type="text" name="description" placeholder="Enter photo description" required><br>
    <input type="submit" name="upload" value="Upload Photo">
  </form>
</section>
<?php endif; ?>

<section>
  <h2>Gallery</h2>
  <?php if(isset($orderMsg)) echo "<p class='msg'>$orderMsg</p>"; ?>
  <div class="gallery">
  <?php
    if(file_exists("photos.txt")){
        $photos = file("photos.txt", FILE_IGNORE_NEW_LINES);
        foreach($photos as $p){
            list($file, $desc) = explode("||", $p);
            echo "<div>";
            echo "<img src='uploads/$file' alt='$desc'>";
            echo "<p>$desc</p>";
            echo "<form method='post'>
                    <input type='hidden' name='photo' value='$desc'>
                    <input type='submit' name='order' class='btn orderBtn' value='Order Now'>
                  </form>";
            echo "</div>";
        }
    } else {
        echo "<p style='text-align:center;'>No photos uploaded yet.</p>";
    }
  ?>
  </div>
</section>

<section>
  <h2>Contact Us</h2>
  <div class="buttons">
    <a class="btn call" href="tel:3184096106">Call Now</a>
    <a class="btn whatsapp" href="https://wa.me/923184096106" target="_blank">WhatsApp</a>
    <a class="btn email" href="mailto:abstat4992@gmail.com">Email Us</a>
  </div>
</section>

<footer>
  <p>Email: abstat4992@gmail.com | Phone: 3184096106</p>
  <p>© 2025 All Rights Reserved</p>
</footer>

</body>
</html>
