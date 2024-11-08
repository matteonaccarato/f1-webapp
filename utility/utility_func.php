<?php
/**
 * This function retrieves the correct user information based on the provided ID.
 * ID: null => the user to select is the one stored in $_SESSION
 * ID: otherwise => querying the DB is required in order to collect the specified user information
 * @param $id:
 * @return array
 */
function select_user($id) : array {
    unset($_SESSION['redirection']);
    if($id == null) {
        $_SESSION['redirection'] = "/f1-webapp/views/private/users/show_profile.php";
        $id = $_SESSION["id"];
    }

    $conn = DB::connect("\utility\utility_func.php");
    $element = DB::get_record_by_field($conn,
        "SELECT * FROM Users WHERE id = ?;",
        ["i"],
        [$id],
        "\utility\utility_func.php",
        "/f1-webapp/views/private/users/show_profile.php")[0];
    if (!$conn->close()) {
        error("500", "conn_close()", "\utility\utility_func.php", "/f1-webapp/views/private/users/all.php");
        exit;
    }
    
    return $element;
}

/**
 * @throws Exception|\Exception
 */
function generate_random_string($length): string {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $random_string = "";
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[random_int(0, $characters_length - 1)];
    }
    return $random_string;
}


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once ('PHPMailer/src/Exception.php');
require_once ('PHPMailer/src/PHPMailer.php');
require_once ('PHPMailer/src/SMTP.php');
/**
 * Send email method
 * @param array $recipients : recipients email address (e.g. ["pippo@topolino.com", "pluto@topolino.com"] OR [["email" => "a@a.c"], ["email" => "b@b.c"]])
 * @param string $subject : email subject
 * @param string $body : email body (it can be HTML)
 * @param array $bcc : optional parameter to specify BCC (e.g. ["pippo@topolino.com", "pluto@topolino.com"] OR [["email" => "a@a.c"], ["email" => "b@b.c"]])
 * @return void
 * @throws Exception
 *
 * Service provided by https://github.com/PHPMailer/PHPMailer
 */
function send_mail(array $recipients, string $subject, string $body, array $bcc = []): void {
    $ini = parse_ini_file("config/keys.ini");

    // 'true' needed to set exceptions
    $mail = new PHPMailer(true);
    $mail->isSMTP(); // "Send messages using SMTP"
    $mail->Host = $ini["smtp_host"];
    $mail->SMTPAuth = true; // Whether to use SMTP authentication
    $mail->Username = $ini["g_email"]; // GMAIL email
    $mail->Password = $ini["g_app_password"]; // GMAIL APP Password
    $mail->SMTPSecure = $ini["smtp_secure"];
    $mail->Port = $ini["smtp_port"];

    // I send email from $ini["g_email"] to addresses stored in $recipients.
    $mail->setFrom($ini["g_email"], $ini["g_name"]?? null);
    foreach ($recipients as $recipient) {
        $mail->addAddress($recipient["email"]?? $recipient);
    }
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;

    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // BCC implemented in order to respect privacy
    foreach ($bcc as $recipient) {
        $mail->addBCC($recipient["email"]?? $recipient);
    }

    $mail->send();
}