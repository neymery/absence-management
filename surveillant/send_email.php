<?php
//Utilisatin de MailHog à la place de phpMailer(rencontrer des problèmes avec de gmail authentication avec phpMailer)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipientEmail = $_POST['email'];
    $subject = $_POST['subject'];
    $content = $_POST['content'];

    $headers = 'From: amina@gmail.com' . "\r\n" .
               'Reply-To: amina@gmail.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

    $success = mail($recipientEmail, $subject, $content, $headers);

    try {
        if ($success) {
            $message = 'success';
        } else {
            $message = 'error';
        }
    } catch (\Throwable $th) {
        echo "errored:$th";
    }

    $redirectUrl = isset($_GET['redirect']) ? $_GET['redirect'] : './view_absences.php';
    header("Location: $redirectUrl?message=$message");
    exit();
}
?>
