<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "./vendor/autoload.php";

session_start();

// Check if these values exist
$full_name = key_exists("displayname", $_GET);
$attribute = isset($_POST["attribute"]);
$edits = isset($_POST["editField"]);
$target_email = key_exists("email", $_GET);
$current_attribute = key_exists("previousVal", $_GET);

if (!$full_name or !$attribute or !$edits or !$target_email or !$current_attribute or !$authenticated) {
    $result = "One or more required values are not defined. Full Name=".$full_name."attribute=".$attribute."edits=".$edits."email=".$target_email."attribute=".$current_attribute;
    header('Location: index.php?page=display&editattributeresult='.$result);
    return;
}

// Get the proper values
$full_name = $_GET["displayname"];
$attribute = $_POST["attribute"];
$edits = $_POST["editField"];
$target_email = $_GET["email"];
$current_attribute = $_GET["previousVal"];

if (strcmp($edits, $current_attribute) == 0)
{
    $result = "Attribute Values are Identical ";
    header('Location: index.php?page=display&editattributeresult='.$result);
    return;
}

// Send Email that says '{dn} is requesting a change to {attribute}: {edits}'
// ERROR: 
$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host = $SMTP_domain;
    $mail->SMTPAuth = true;
    $mail->Username = $SMTP_username;
    $mail->Password = $SMTP_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = $SMTP_port;

    // Recipient
    $mail->setFrom($target_email);
    $mail->addAddress($SMTP_email, $SMTP_email_name);

    // Content
    $mail->Subject = $full_name." wants to change " .$attribute." to ".$edits;
    $mail->Body = "<b>".$full_name."'s</b> current ".$attribute.": <b>".$current_attribute;
    $mail->AltBody = "".$full_name."'s current ".$attribute.": ".$current_attribute;
    
    $mail->send();
    $result = "requestedit";
} catch (Exception $e) {
    $result = "Could not request change: ".$mail->ErrorInfo;
} finally {
    header('Location: index.php?page=display&editattributeresult='.$result);
}
?>