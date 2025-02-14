<?php
// เชื่อมต่อฐานข้อมูล

$host = '127.0.0.1';
$dbname = 'my-vue-synerry';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;port=3306;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "เชื่อมต่อกับฐานข้อมูลสำเร็จ!";
} catch (PDOException $e) {
    die("ไม่สามารถเชื่อมต่อกับฐานข้อมูล: " . $e->getMessage());
}

// รับค่า JSON จาก Vue
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['full_url']) || empty($data['full_url'])) {
    echo json_encode(["error" => "กรุณาป้อน URL"]);
    exit;
}

// กรองและตรวจสอบ URL
$full_url = filter_var($data['full_url'], FILTER_SANITIZE_URL);
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
    echo json_encode(["short_url" => "http://localhost/" . $existing['short_code']]);
    exit;
}

// สร้าง short_code (สุ่ม 6 ตัวอักษร)
function generateShortCode($length = 6) {
    return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
}

$short_code = generateShortCode();

// ตรวจสอบว่า short_code ไม่ซ้ำ
while (true) {
    $stmt = $conn->prepare("SELECT id FROM short_urls WHERE short_code = :short_code");
    $stmt->bindParam(':short_code', $short_code);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        break;
    }
    
    $short_code = generateShortCode(); // ถ้าซ้ำให้สร้างใหม่
}

// บันทึกลงฐานข้อมูล
$stmt = $conn->prepare("INSERT INTO short_urls (full_url, short_code, created_at) VALUES (:full_url, :short_code, NOW())");
$stmt->bindParam(':full_url', $full_url);
$stmt->bindParam(':short_code', $short_code);

if ($stmt->execute()) {
    echo json_encode(["short_url" => "http://localhost/" . $short_code]);
} else {
    echo json_encode(["error" => "เกิดข้อผิดพลาดในการบันทึกข้อมูล"]);
}
?>
