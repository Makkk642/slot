<?php
include 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $password, $email]);
        $success_message = "สมัครสมาชิกเรียบร้อย!!<a href='login.php'>  เข้าสู่ระบบเลย</a>";
    } catch (Exception $e) {
        $error_message = "คุณอาจมีบัญชีอยู่แล้ว: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        .register-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 300px;
        }
        .register-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #4a90e2;
        }
        .register-container input[type="text"], 
        .register-container input[type="password"], 
        .register-container input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .register-container button {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }
        .register-container button:hover {
            background-color: #357ab8;
        }
        .success-message {
            color: green;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="email" name="email" placeholder="Email" required>
            <button type="submit">Register</button> <br> <br>
            มีบัญชีแล้วหรอ <a href='login.php'>เข้าสู่ระบบ</a>
        </form>
    </div>
</body>
</html>
