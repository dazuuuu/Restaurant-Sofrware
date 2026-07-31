<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class MailService {
    private static function config(): array {
        return require APP_ROOT . '/apps/config/mail.php';
    }

    private static function send(string $toEmail, string $toName, string $subject, string $bodyHtml): bool {
        $config = self::config();
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPSecure = $config['encryption'];
            $mail->Port = $config['port'];

            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $bodyHtml;
            $mail->AltBody = strip_tags($bodyHtml);

            $mail->send();
            return true;
        } catch (PHPMailerException $e) {
            error_log('MailService send failed: ' . $mail->ErrorInfo);
            return false;
        }
    }

    public static function sendOtp(string $toEmail, string $toName, string $otp): bool {
        $subject = 'Your Restaurant POS password reset code';
        $body = '
            <p>Hi ' . htmlspecialchars($toName, ENT_QUOTES) . ',</p>
            <p>Use the code below to reset your Restaurant POS admin password. This code expires in 10 minutes.</p>
            <p style="font-size:28px;font-weight:bold;letter-spacing:4px;">' . htmlspecialchars($otp, ENT_QUOTES) . '</p>
            <p>If you did not request this, you can safely ignore this email.</p>
        ';
        return self::send($toEmail, $toName, $subject, $body);
    }

    public static function sendResetConfirmation(string $toEmail, string $toName): bool {
        $subject = 'Your Restaurant POS password was changed';
        $body = '
            <p>Hi ' . htmlspecialchars($toName, ENT_QUOTES) . ',</p>
            <p>This confirms your Restaurant POS admin password was just reset. If you did not make this change, contact your system administrator immediately.</p>
        ';
        return self::send($toEmail, $toName, $subject, $body);
    }
}
