<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\Services\EmailChannel;

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['email'], $input['title'], $input['message'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields: email, title, message']);
        exit;
    }

    // Load email configuration
    $emailConfig = include __DIR__ . '/../config/email.php';
    
    // Create email channel
    $emailChannel = new EmailChannel($emailConfig);
    
    // Send email
    $result = $emailChannel->sendEmail(
        $input['email'],
        $input['title'],
        $input['message']
    );

    echo json_encode([
        'success' => $result,
        'message' => $result ? 'Email sent successfully' : 'Failed to send email'
    ]);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>