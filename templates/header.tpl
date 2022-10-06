<!DOCTYPE html>
<html lang="{$lang}">
<head>
    <title>{$msg_title}</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="LDAP Tool Box" />
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap-theme.min.css" />
    <link rel="stylesheet" type="text/css" href="vendor/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="vendor/datatables/datatables.min.css" />
    <link rel="stylesheet" type="text/css" href="vendor/chosen/chosen.min.css"/>
    <link rel="stylesheet" type="text/css" href="vendor/select2/css/select2.min.css"/>
    <link rel="stylesheet" type="text/css" href="vendor/select2/css/select2-bootstrap4.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/service-desk.css" />
{if $custom_css}
    <link rel="stylesheet" type="text/css" href="{$custom_css}" />
{/if}
    <link href="images/favicon.ico" rel="icon" type="image/x-icon" />
    <link href="images/favicon.ico" rel="shortcut icon" />
{if $background_image}
     <style>
       html, body {
         background: url({$background_image}) no-repeat center fixed;
         background-size: cover;
       }
  </style>
{/if}

</head>
<body>

<div class="container">
