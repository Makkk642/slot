<?php
include 'db.php';
$stmt = $conn->prepare("SELECT slip FROM transactions WHERE id = ?");
$stmt->execute([42]); // แทนที่ 1 ด้วย ID ที่ต้องการ
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && $row['slip']) {
    header("Content-Type: image/png"); // หรือ image/jpeg
    echo $row['slip'];
    exit();
} else {
    echo "No file found.";
}
?>
<img src="show_slip.php?id=1" alt="Slip Image">
