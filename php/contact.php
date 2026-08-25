<?php
header('Content-Type: application/json');

require_once 'config.php';
require_once 'db-connect.php';

// ========================
// SANITIZE INPUT
// ========================
function sanitize($input)
{
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// ========================
// VALIDATE EMAIL
// ========================
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// ========================
// GET CLIENT IP
// ========================
function getClientIP()
{
    $ip = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ip = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ip = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ip = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ip = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ip = getenv('REMOTE_ADDR');
    else
        $ip = 'UNKNOWN';
    return $ip;
}

// ========================
// RATE LIMITING
// ========================
function checkRateLimit($ip)
{
    $limit = 5;
    $timeWindow = 300;

    if (!isset($_SESSION['rate_limit_' . $ip])) {
        $_SESSION['rate_limit_' . $ip] = [
            'count' => 1,
            'first_request' => time()
        ];
        return true;
    }

    $data = $_SESSION['rate_limit_' . $ip];
    $timeElapsed = time() - $data['first_request'];

    if ($timeElapsed > $timeWindow) {
        $_SESSION['rate_limit_' . $ip] = [
            'count' => 1,
            'first_request' => time()
        ];
        return true;
    }

    if ($data['count'] >= $limit) {
        return false;
    }

    $data['count']++;
    $_SESSION['rate_limit_' . $ip] = $data;
    return true;
}

// ========================
// PROCESS FORM
// ========================
$ip = getClientIP();

// Rate limiting
if (!checkRateLimit($ip)) {
    echo json_encode([
        'success' => false,
        'message' => 'Too many messages. Please wait a few minutes.'
    ]);
    exit;
}

// Get form data
$name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
$subject = isset($_POST['subject']) ? sanitize($_POST['subject']) : 'New Contact Message';
$message = isset($_POST['message']) ? sanitize($_POST['message']) : '';

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required.';
}

if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!isValidEmail($email)) {
    $errors[] = 'Please enter a valid email address.';
}

if (empty($message)) {
    $errors[] = 'Message is required.';
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode(' ', $errors)
    ]);
    exit;
}

// ========================
// SAVE TO DATABASE
// ========================
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $subject, $message, $ip);
    $stmt->execute();
    $stmt->close();
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
}

// ========================
// SEND EMAIL
// ========================
$to = ADMIN_EMAIL;
$email_subject = "📩 New Contact from $name";

$email_message = "
<!DOCTYPE html>
<html>
<head><style>
body { font-family: Arial, sans-serif; }
.container { max-width: 600px; margin: 0 auto; padding: 20px; }
.header { background: #4f46e5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
.content { background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; }
.field { margin-bottom: 15px; }
.label { font-weight: bold; color: #555; }
.value { margin-top: 4px; color: #222; }
.footer { text-align: center; padding: 20px; color: #888; font-size: 12px; }
</style></head>
<body>
<div class='container'>
    <div class='header'><h2>📬 New Contact Message</h2></div>
    <div class='content'>
        <div class='field'><div class='label'>👤 Name</div><div class='value'>$name</div></div>
        <div class='field'><div class='label'>📧 Email</div><div class='value'>$email</div></div>
        <div class='field'><div class='label'>📌 Subject</div><div class='value'>$subject</div></div>
        <div class='field'><div class='label'>💬 Message</div><div class='value'>$message</div></div>
        <div class='field'><div class='label'>🌐 IP</div><div class='value'>$ip</div></div>
    </div>
    <div class='footer'><p>Sent from your portfolio website - Shreya.G</p></div>
</div>
</body>
</html>
";

$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: " . SITE_NAME . " <noreply@" . $_SERVER['HTTP_HOST'] . ">\r\n";
$headers .= "Reply-To: $email\r\n";

$mail_sent = mail($to, $email_subject, $email_message, $headers);

// ========================
// RESPONSE
// ========================
if ($mail_sent) {
    echo json_encode([
        'success' => true,
        'message' => '✅ Message sent successfully! I\'ll get back to you soon.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => '❌ Failed to send message. Please try again later.'
    ]);
}
?>

