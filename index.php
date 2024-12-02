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
    <title>Footslot</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Orbitron', sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #fff;
            text-align: center;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        h1 {
            margin-top: 20px;
            font-size: 4rem;
            color: #ff007f;
            text-shadow: 0 0 10px #ff007f, 0 0 20px #ff00bf;
            animation: glow 2s infinite alternate;
        }

        @keyframes glow {
            0% { text-shadow: 0 0 10px #ff007f, 0 0 20px #ff00bf; }
            100% { text-shadow: 0 0 20px #ff007f, 0 0 30px #ff00bf; }
        }

        #slot {
            margin: 50px auto;
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 0 20px #ff007f, inset 0 0 10px rgba(255, 255, 255, 0.3);
        }

        .reel {
            width: 100px;
            height: 100px;
            font-size: 3rem;
            line-height: 100px;
            text-align: center;
            color: #fff;
            border: 3px solid #00e0ff;
            border-radius: 15px;
            background: linear-gradient(45deg, #203a43, #2c5364);
            box-shadow: 0 0 20px #00e0ff, inset 0 0 10px #00e0ff;
        }

        .reel.spin {
            animation: spinEffect 0.5s infinite;
        }

        @keyframes spinEffect {
            0% { transform: rotateX(0); }
            50% { transform: rotateX(180deg); }
            100% { transform: rotateX(360deg); }
        }

        #spin {
            font-size: 1.5rem;
            padding: 15px 50px;
            color: #fff;
            background: linear-gradient(45deg, #ff007f, #00e0ff);
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            text-shadow: 0 0 5px #ff007f;
            box-shadow: 0 0 10px #00e0ff;
        }

        #spin:hover {
            transform: scale(1.2);
            box-shadow: 0 0 30px #ff007f, 0 0 30px #00e0ff;
        }

        #result {
            margin-top: 30px;
            font-size: 2rem;
            color: #00e0ff;
            text-shadow: 0 0 10px #00e0ff, 0 0 20px #00ffff;
        }

        #balance {
            font-size: 1.5rem;
            margin-top: 10px;
            color: #00ffff;
            text-shadow: 0 0 10px #00e0ff, 0 0 20px #00ffff;
        }
        #controls {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
        }

        a {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1.2rem;
            text-decoration: none;
            color: #ff007f;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 20px;
            transition: background 0.3s, color 0.3s;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        a:hover {
            background: #ff007f;
            color: #fff;
            text-shadow: 0 0 10px #fff;
        }
    </style>
</head>
<body>


<p>Your current balance: <span id="balance">$<?php echo number_format($balance, 1); ?></span></p><a href='dashbord.php'>เติมเงิน</a>

   
    <div id="slot">
        <div class="reel" id="reel1">?</div>
        <div class="reel" id="reel2">?</div>
        <div class="reel" id="reel3">?</div>
    </div>
    <div id="controls">
        <button id="decreaseBet">-</button>
        <span id="currentBet">Bet: $1</span>
        <button id="increaseBet">+</button>
    </div>

<div id="result"></div><br>


    <button id="spin">หมุน</button>
    <p id="result"></p>

    

    <!-- เสียงประกอบ -->
    <audio id="spinSound" src="spin.mp3"></audio>
    <audio id="win1Sound" src="win1.mp3"></audio>
    <audio id="win2Sound" src="win2.mp3"></audio>
    <audio id="win3Sound" src="win3.mp3"></audio>
    <audio id="loseSound" src="lose.mp3"></audio>
    <audio id="spinningSound" src="1.mp3"></audio> <!-- เสียงตอนหมุน -->

    <script src="script.js"></script>
</body>
</html> 