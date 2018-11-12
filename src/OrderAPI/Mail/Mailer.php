<?php

namespace OrderAPI\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{

    public static function send_mail($invoice, $recipient, $json)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.mail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'savetheplanet@null.net';
            $mail->Password = getenv('EMAIL_PASSWORD');
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('savetheplanet@null.net', 'OrderAPI');
            $mail->addAddress($recipient);

            $mail->isHTML(!$json);
            $mail->Subject = 'OrderAPI invoice #' . (rand(1, 1000));
            $mail->Body = $invoice;
            $mail->AltBody = 'Invoice cannot be displayed as your email does not support HTML.';

            $mail->send();
        } catch (Exception $e) {
            Flight::halt(400, "could not send invoice to given email address.");
        }
    }
}