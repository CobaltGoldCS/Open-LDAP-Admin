<?php
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
// ERROR: Connection could not be established with host smtp-relay.google.com
// Could be google blocking it? Maybe need to change smtp settings in google itself to allow emailing
// Most suspicious code is probably the $transport definition
// Maybe username and/or password for smtp is wrong? Check config.inc.local.php
$transport = (new Swift_SmtpTransport($SMTP_domain, $SMTP_port))
    ->setUsername($SMTP_username)
    ->setPassword($SMTP_password)
    ->setStreamOptions(array('ssl' => array('allow_self_signed' => false, 'verify_peer' => false)));

$mailer = new Swift_Mailer($transport);

$result = $SMTP_email;

$message = (new Swift_Message())
    ->setSubject($fuil_name." wants to change " .$attribute." to ".$edits)
    ->setFrom($target_email)
    ->setTo($SMTP_email, $SMTP_email_name)
    ->setBody("".$full_name."'s current ".$attribute.": ".$current_attribute);

$sent = $mailer->send($message);

if ($sent == 0) {
    $result = "Failed to send message to IT.";
    header('Location: index.php?page=display&editattributeresult='.$result);
    return;
}
// Cleanup and redirect back to display
$result = "requestedit";
header('Location: index.php?page=display&editattributeresult='.$result);
?>