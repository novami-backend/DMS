<?php

use CodeIgniter\Email\Email;

function sendEmail(string $to, string $subject, string $message, array $attachments = []): bool
{
    $email = \Config\Services::email();

    $email->setFrom('admin@novamiinfotechs.com', 'Medzus Healthcare Pvt. Ltd.'); //set those values here as well
    $email->setTo($to);
    $email->setSubject($subject);
    $email->setMessage($message);

    foreach ($attachments as $filePath) {
        if (is_file($filePath)) {
            $email->attach($filePath);
        }
    }

    $email->setMailType('html');
    if ($email->send()) {
        return true;
    } else {
        log_message('error', 'Email failed: ' . $email->printDebugger(['headers']));
        return false;
    }
}
