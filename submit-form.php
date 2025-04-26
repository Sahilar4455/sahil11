<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Google Sheet Webhook (your actual Google Apps Script URL)
$google_sheet_webhook = 'https://script.google.com/macros/s/AKfycbwInnAW2u3IbpXQ678b33udMIIvka0nB1rxv1zQ6dMTgw1odazrQ1MrvKIA-CyeurwD/exec';

// Get and sanitize inputs
$f_name = trim($_POST['f_name'] ?? '');
$l_name = trim($_POST['l_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$country_code = trim($_POST['country_code'] ?? '');
$full_phone = trim($_POST['full_phone'] ?? '');

// Validation
$errors = [];
if (empty($f_name)) $errors[] = 'First name is required';
if (empty($l_name)) $errors[] = 'Last name is required';
if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}
if (empty($phone)) $errors[] = 'Phone number is required';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Prepare payload
$data = [
    'f_name' => $f_name,
    'l_name' => $l_name,
    'email' => $email,
    'phone' => $phone,
    'country_code' => $country_code,
    'full_phone' => $full_phone
];

// Send data to Google Sheet
$ch = curl_init($google_sheet_webhook);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Response
echo json_encode(['success' => true]);
?>
