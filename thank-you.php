<?php
// Start session at the very top
session_start();

// Get the name from session
$name = $_SESSION['thank_you_name'] ?? null;

// Check if this is a form submission or direct access
$isFormSubmission = isset($_SESSION['thank_you_name']);

// Clear the session name after we've checked it
unset($_SESSION['thank_you_name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isFormSubmission ? 'Thank You' : 'Contact Us'; ?></title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        .thank-you-page {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.92) url('https://cdn.opr.ae/upload/photo/Villa%20Del%20DIVOS.jpg') center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .thank-you-page::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
        }

        .thank-you-content {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem;
            max-width: 600px;
            width: 90%;
            text-align: center;
            border-radius: 5px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .thank-you-content h2 {
            color: #222;
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
        }

        .thank-you-content p {
            color: #444;
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .close-thank-you {
            background: linear-gradient(135deg, #2e2e2e, #444);
            color: #f0f0f0;
            padding: 1rem 2rem;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }

        .close-thank-you:hover {
            background: linear-gradient(135deg, #1f1f1f, #333);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 768px) {
            .thank-you-content {
                padding: 2rem;
            }

            .thank-you-content h2 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            .thank-you-content {
                padding: 1.5rem;
            }

            .thank-you-content h2 {
                font-size: 1.5rem;
            }

            .thank-you-content p {
                font-size: 1rem;
            }
        }
    </style>
   
</head>
<body>
    <div class="thank-you-page">
        <div class="thank-you-content">
            <?php if ($isFormSubmission): ?>
                <h2>Thank You, <?php echo htmlspecialchars($name); ?>!</h2>
                <p> For contacting us, our expert realtor will connect with you soon. </p>
            <?php else: ?>
                <h2>Contact Us</h2>
                <p>Please use our contact form to get in touch with our team. We'd be happy to assist you with any questions you may have.</p>
            <?php endif; ?>
            <button class="close-thank-you" onclick="window.location.href='/'">Close</button>
        </div>
    </div>
</body>
</html>