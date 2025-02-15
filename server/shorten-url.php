<?php
header("Content-Type: application/json");

// เชื่อมต่อฐานข้อมูล
$host = '127.0.0.1';
$dbname = 'my-vue-synerry';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;port=3306;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["error" => "ไม่สามารถเชื่อมต่อกับฐานข้อมูล: " . $e->getMessage()]));
}

// ตรวจสอบว่าเป็นคำขอแบบ POST หรือไม่
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "รองรับเฉพาะคำขอแบบ POST"]);
    exit;
}

// รับค่า JSON จาก Vue
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['full_url']) || empty(trim($data['full_url']))) {
    echo json_encode(["error" => "กรุณาป้อน URL"]);
    exit;
}

// กรองและตรวจสอบ URL
$full_url = trim(strip_tags($data['full_url']));
$full_url = filter_var($full_url, FILTER_SANITIZE_URL);

if (!filter_var($full_url, FILTER_VALIDATE_URL)) {
    echo json_encode(["error" => "URL ไม่ถูกต้อง"]);
    exit;
}

// ตรวจสอบว่า URL นี้เคยถูกย่อหรือไม่
$stmt = $conn->prepare("SELECT short_code FROM short_urls WHERE full_url = :full_url");
$stmt->bindParam(':full_url', $full_url);
$stmt->execute();
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    echo json_encode(["short_url" => "http://localhost/synerry/" . $existing['short_code']]);
    exit;
}

// ฟังก์ชันสร้าง Short Code
function generateShortCode($length = 6) {
    return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
}

// ตรวจสอบว่า short_code ไม่ซ้ำ
do {
    $short_code = generateShortCode();
    $stmt = $conn->prepare("SELECT id FROM short_urls WHERE short_code = :short_code");
    $stmt->bindParam(':short_code', $short_code);
    $stmt->execute();
} while ($stmt->rowCount() > 0);

// บันทึกลงฐานข้อมูล
$stmt = $conn->prepare("INSERT INTO short_urls (full_url, short_code, created_at) VALUES (:full_url, :short_code, NOW())");
$stmt->bindParam(':full_url', $full_url);
$stmt->bindParam(':short_code', $short_code);

if ($stmt->execute()) {
    echo json_encode(["short_url" => "http://localhost/synerry/" . $short_code]);
} else {
    echo json_encode(["error" => "เกิดข้อผิดพลาดในการบันทึกข้อมูล"]);
}
?>
