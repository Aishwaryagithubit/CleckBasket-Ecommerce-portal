<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Send an OTP verification email to the given address.
 * Returns true on success, or an error string on failure.
 */
function sendOtpEmail(string $toEmail, string $toName, string $otp): bool|string {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'cleckbasket@gmail.com';
        $mail->Password   = 'jlbc njew efbg xilc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('cleckbasket@gmail.com', 'CleckBasket');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Your CleckBasket Verification Code';
        $mail->Body    = "
            <div style='font-family:Arial,sans-serif;max-width:480px;margin:0 auto;padding:32px;background:#fff;border-radius:12px;border:1px solid #eee;'>
                <img src='https://cleckbasket.com/assets/images/logo.png' alt='CleckBasket' style='height:40px;margin-bottom:24px;' />
                <h2 style='color:#29170C;margin:0 0 12px;'>Verify your email address</h2>
                <p style='color:#51443E;margin:0 0 24px;line-height:1.6;'>
                    Hello {$toName},<br>
                    Use the code below to verify your CleckBasket account. It expires in 30 minutes.
                </p>
                <div style='background:#FFF8F5;border:2px dashed #572B12;border-radius:8px;padding:20px;text-align:center;margin-bottom:24px;'>
                    <span style='font-size:36px;font-weight:700;letter-spacing:12px;color:#572B12;'>{$otp}</span>
                </div>
                <p style='color:#888;font-size:12px;margin:0;'>If you did not create an account, you can safely ignore this email.</p>
            </div>
        ";
        $mail->AltBody = "Your CleckBasket verification code is: {$otp}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}
