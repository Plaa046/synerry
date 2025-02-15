<?php
header("Content-Type: application/json");

// เชื่อมต่อฐานข้อมูล
$host = '127.0.0.1';
$dbname = 'my-vue-synerry';
$username = 'root';
$password = '';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// การเชื่อมต่อฐานข้อมูล
try {
    $conn = new PDO("mysql:host=$host;port=3306;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["error" => "ไม่สามารถเชื่อมต่อกับฐานข้อมูล: " . $e->getMessage()]));
}

// หากเป็นคำขอแบบ OPTIONS (preflight) ให้ตอบกลับ 200
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   echo json_encode(["error" => "รองรับเฉพาะคำขอแบบ POST"]);
   exit;
}

// รับค่า JSON จาก Vue
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['full_url']) || empty(trim($data['full_url']))) {
    echo json_encode(["error" => "กรุณาป้อน URL"]);
    exit;
}
error_log("Received Data: " . json_encode($data));

// กรองและตรวจสอบ URL
$full_url = trim(strip_tags($data['full_url']));
$full_url = filter_var($full_url, FILTER_SANITIZE_URL);

if (!filter_var($full_url, FILTER_VALIDATE_URL)) {
    echo json_encode(["error" => "URL ไม่ถูกต้อง"]);
    exit;
}

// ดึง host จาก full_url
$parsedUrl = parse_url($full_url);
$domain = $parsedUrl['scheme'] . "://" . $parsedUrl['host']; 

// ดึง IP และ User-Agent
$user_ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// ตรวจสอบว่า URL นี้เคยถูกย่อหรือไม่
$stmt = $conn->prepare("SELECT id, short_code FROM short_urls WHERE full_url = :full_url");
$stmt->bindParam(':full_url', $full_url);
$stmt->execute();
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    // เมื่อคลิกที่ URL จะบันทึกการคลิก
    $short_id = $existing['id'];
    
    // บันทึกข้อมูลการคลิกในตาราง url_statsnew
    $stmt = $conn->prepare("INSERT INTO url_statsnew (short_id, ip_address, user_agent, clicked_at) 
                           VALUES (:short_id, :ip_address, :user_agent, NOW())");
    $stmt->bindParam(':short_id', $short_id);
    $stmt->bindParam(':ip_address', $user_ip);
    $stmt->bindParam(':user_agent', $user_agent);
    
    if ($stmt->execute()) {
        // เปลี่ยนเส้นทางไปยัง full_url
        header("Location: " . $full_url);
        exit;
    } else {
        echo json_encode(["error" => "เกิดข้อผิดพลาดในการบันทึกข้อมูลการคลิก"]);
    }
} else {
    echo json_encode(["error" => "URL ไม่พบในฐานข้อมูล"]);
}
?>
