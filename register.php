<?php
session_start();
require 'db.php'; // Your mysqli connection as $conn

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = strtolower(trim($_POST['name']));
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);

    $allowed = ['member', 'executive', 'audit', 'systems', 'procurement', 'director'];

    if (!$name || !$email || !$password || !$role) {
        $error = '❗ Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '❗ Invalid email format.';
    } elseif (!in_array($role, $allowed)) {
        $error = '❗ Invalid role selected.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE name = ? OR email = ?");
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows) {
            $error = '❗ Username or email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name,email,password,role,approved) VALUES (?,?,?,?,0)");
            $stmt->bind_param("ssss", $name, $email, $hashed, $role);
            if ($stmt->execute()) {
                $to = "jaxxitacapital@gmail.com";
                $subject = "🆕 New Registration – SmartSurplus Z-825";
                $message = "New user '$name' registered as '$role'.\nEmail: $email\nPending admin approval.\nApprove at: admin-panel.php";
                $headers = "From: SmartSurplus <no-reply@jaxxita.com>";
                @mail($to, $subject, $message, $headers);
                $success = '✅ Registration successful! Awaiting admin approval.';
            } else {
                $error = '❌ Database error: ' . $stmt->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register | SmartSurplus Z‑825</title>
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #000 url('https://images.unsplash.com/photo-1496307042754-b4aa456c4a2d?auto=format&fit=crop&w=1950&q=80') center/cover fixed;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: #fff;
    }
    .container {
      background: rgba(0,0,0,0.85);
      padding: 40px 30px;
      border-radius: 10px;
      box-shadow: 0 0 25px #00ffffaa;
      width: 90%;
      max-width: 420px;
      text-align: center;
    }
    h1 {
      margin-bottom: 20px;
      color: #00ffff;
      text-shadow: 0 0 10px #00ffff99;
    }
    input, select {
      width: 90%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 6px;
      outline: none;
      font-size: 16px;
      background: rgba(255,255,255,0.08);
      color: #fff;
    }
    button {
      width: 95%;
      background: #00ffff;
      color: #000;
      padding: 12px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      margin-top: 10px;
      font-size: 16px;
    }
    button:hover {
      background: #00cccc;
    }
    .error {
      color: #ff4444;
      font-weight: bold;
      margin-top: 10px;
    }
    .success {
      color: #00ff88;
      font-weight: bold;
      margin-top: 10px;
    }
    .link {
      margin-top: 15px;
      color: #aaa;
      cursor: pointer;
    }
    .link:hover {
      color: #00ffff;
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Register • Z‑825</h1>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <form method="POST">
      <input type="text" name="name" placeholder="Your username" required />
      <input type="email" name="email" placeholder="Your email" required />
      <input type="password" name="password" placeholder="Your password" required />
      <select name="role" required>
        <option value="">Select your role...</option>
        <option value="member">Member</option>
        <option value="executive">Executive</option>
        <option value="audit">Audit</option>
        <option value="systems">Systems</option>
        <option value="procurement">Procurement</option>
        <option value="director">Director</option>
      </select>
      <button type="submit">🚀 Create Account</button>
    </form>
    <div class="link" onclick="location.href='login.php'">← Back to login</div>
  </div>
</body>
</html>
