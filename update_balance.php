<?php
include 'db.php';
session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$amount = isset($data['amount']) ? (float)$data['amount'] : 0;

if ($amount !== 0) {
    $user_id = $_SESSION['user_id'];

    // อัปเดตยอดเงินในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $stmt->execute([$amount, $user_id]);

    // ดึงยอดเงินที่อัปเดต
    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $balance = $stmt->fetchColumn();

    $_SESSION['balance'] = $balance;

    echo json_encode(["success" => true, "balance" => (float)$balance]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid amount"]);
}
?>
