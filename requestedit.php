<?php

if (!isset($_POST["attribute"]) or !isset($_POST["dn"]) or !isset($_POST["editField"]) or !$authenticated) {
    $result = "Invalid State";
    header('Location: index.php?page=display&editattributeresult='.$result);
    return;
}

$dn = $_POST["dn"];
$attribute = $_POST["attribute"];
$edits = $_POST["editField"];

// Send Email that says '{dn} is requesting a change to {attribute}: {edits}'

$result = "requestedit";
header('Location: index.php?page=display&editattributeresult='.$result);