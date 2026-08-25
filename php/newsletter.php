<?php
header('Content-Type: application/json');

require_once 'config.php';
require_once 'db-connect.php';

function sanitize($input)
{
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

$email = isset($_POST['email']) ? sanitize($_POST['email']) : '';

if (empty($email)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email is required.'
    ]);
    exit;
}

if (!isValidEmail($email)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid email address.'
    ]);
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Check if already subscribed
    $check = $conn->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'This email is already subscribed!'
        ]);
        $check->close();
        exit;
    }
    $check->close();

    // Insert new subscriber
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email, ip_address) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $ip);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => '✅ Subscribed successfully! Thank you.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '❌ Failed to subscribe. Please try again.'
        ]);
    }

    $stmt->close();

} catch (Exception $e) {
    error_log("Newsletter error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => '❌ Something went wrong. Please try again.'
    ]);
}
?>

