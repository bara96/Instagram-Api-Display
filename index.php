<?php

require_once ("InstagramApi.php");

$clientId = "5890618884346526";     //app client id
$clientSecret = "4fc46b8fff6f310d41094d5bdf0ecccc";     //app client secret
$redirectUrl = "https://localhost/ig-api/auth.php";     //redirect url, can also be this page

$igApi = new InstagramApi($clientId, $clientSecret, $redirectUrl);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-center mt-5">
            <a class="btn btn-danger mr-4" href="<?= $igApi->getInstagramLoginUrl() ?>" role="button">Log in with Instagram</a>
            <a class="btn btn-secondary" href="logout.php" role="button">Logout</a>
    </div>

    <?php if($igApi->isLoggedIn()) : ?>
        <div class="row mt-5">
            <div class="offset-6 col-6 mb-5">
                <h3>Logged In.</h3>
            </div>
        <?php
        $mediaArray = $igApi->getUserMedia();
        /** @var $media \InstagramApiModels\IgMedia */
        foreach ($mediaArray as $media) : ?>
        <div class="col-4"><p><?= $media->getId() ?></p></div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
