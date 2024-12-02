<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$balance = $_SESSION['balance'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $slip = null;

    // ตรวจสอบการอัปโหลดไฟล์
    if (isset($_FILES['slip']) && $_FILES['slip']['error'] === UPLOAD_ERR_OK) {
        // อ่านไฟล์และแปลงเป็น Binary Data
        $slip = file_get_contents($_FILES['slip']['tmp_name']);
    }

    // บันทึกข้อมูลลงในฐานข้อมูล
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, amount, slip) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $user_id);
    $stmt->bindParam(2, $amount);
    $stmt->bindParam(3, $slip, PDO::PARAM_LOB);
    $stmt->execute();

    echo "<p style='color: #00e0ff;'>Transaction recorded successfully!</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Orbitron', sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #fff;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        h2 {
            margin-top: 20px;
            font-size: 3rem;
            color: #ff007f;
            text-shadow: 0 0 10px #ff007f, 0 0 20px #ff00bf;
        }

        form {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 20px;
            margin: 30px auto;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 0 20px #ff007f, inset 0 0 10px rgba(255, 255, 255, 0.2);
        }

        form h3 {
            color: #00ffff;
            font-size: 1.5rem;
            text-shadow: 0 0 10px #00e0ff;
        }

        form p {
            font-size: 1.2rem;
            margin: 10px 0;
        }

        input, button, a {
            font-size: 1rem;
            padding: 10px 15px;
            margin: 10px 0;
            display: block;
            width: calc(100% - 20px);
            margin-left: auto;
            margin-right: auto;
        }

        input[type="file"], input[type="number"] {
            color: #fff;
            background: linear-gradient(135deg, #0f2027, #203a43);
            border: 2px solid #00e0ff;
            border-radius: 10px;
            text-align: center;
        }

        button {
            color: #fff;
            background: linear-gradient(45deg, #ff007f, #00e0ff);
            border: none;
            border-radius: 30px;
            cursor: pointer;
            text-shadow: 0 0 5px #ff007f;
            box-shadow: 0 0 10px #00e0ff;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        button:hover {
            transform: scale(1.1);
            box-shadow: 0 0 20px #ff007f, 0 0 20px #00e0ff;
        }

        a {
            text-decoration: none;
            color: #00ffff;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 10px 20px;
            transition: background 0.3s, color 0.3s;
            display: inline-block;
        }

        a:hover {
            background: #ff007f;
            color: #fff;
            text-shadow: 0 0 10px #fff;
        }

        #copy-btn {
            margin-left: 10px;
            padding: 5px 10px;
            cursor: pointer;
            border: 1px solid #007BFF;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
        }

        #copy-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>Your current balance: <span>$<?php echo number_format($balance, 2); ?></span></p>

    <form method="post" enctype="multipart/form-data">
        <h3>บัญชีสำหรับโอนเงิน:</h3>
        <p>
            กสิกร<br>
            <span id="account-number">1263733319</span>
            <button type="button" id="copy-btn">คัดลอก</button>
        </p>
        <p>ชานนท์ ตรีมงคล</p>

        <label for="amount">จำนวนเงิน:</label>
        <input type="number" name="amount" id="amount" step="0.01" required>

        <label for="slip">อัปโหลดสลิป:</label>
        <input type="file" name="slip" id="slip" accept="image/*">

        <button type="submit">ส่งข้อมูล</button>
        <a href="index.php">เล่นเกมต่อ</a>
    </form>

    <script>
        document.getElementById("copy-btn").addEventListener("click", function () {
            const accountNumber = document.getElementById("account-number").innerText;

            // สร้าง Temporary Element เพื่อคัดลอกข้อความ
            const tempInput = document.createElement("input");
            tempInput.value = accountNumber;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);

            // แจ้งให้ผู้ใช้ทราบว่าได้คัดลอกแล้ว
            alert("Copied to clipboard: " + accountNumber);
        });
    </script>
</body>
</html>
