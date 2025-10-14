<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

    http_response_code(204);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Read raw input
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

// Accept form-encoded as fallback
if (is_null($input)) {
    $input = $_POST;
}

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'No input received']);
    exit;
}

$snack = isset($input['snack']) ? trim($input['snack']) : null;
$price = isset($input['price']) ? $input['price'] : null;
$cash = isset($input['cash']) ? $input['cash'] : null;
$quantity = isset($input['quantity']) ? $input['quantity'] : null;

// Validate presence
if ($snack === null || $price === null || $cash === null || $quantity === null) {
    echo json_encode(['success' => false, 'message' => 'One or more required fields are missing']);
    exit;
}

// Sanitize and validate numeric values
$price = floatval($price);
$cash = floatval($cash);
$quantity = intval($quantity);

if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1']);
    exit;
}

if ($cash < 0) {
    echo json_encode(['success' => false, 'message' => 'Cash must be non-negative']);
    exit;
}

$total = round($price * $quantity, 2);

if ($cash < $total) {
    echo json_encode(['success' => false, 'message' => "Insufficient funds. Total is ₱" . number_format($total, 2)]);
    exit;
}

$change = round($cash - $total, 2);

$response = [
    'success' => true,
    'message' => "Order placed for {$quantity} x {$snack} (₱" . number_format($price, 2) . " each)",
    'change' => number_format($change, 2, '.', '')
];

echo json_encode($response);

?>
