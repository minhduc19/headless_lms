<!DOCTYPE html>
<html>
    <head>
        <title><?= $item['TeacherScene']['title'];?></title>
        <meta charset="utf-8">
        <meta name="description" content="<?= strip_tags($item['TeacherScene']['content'])?>">
        <meta name="fragment" content="!">
        <meta content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0" name="viewport">
        <link rel="shortcut icon" type="image/png" href="/favicon.ico">
        <meta name="author" content="NEcampus Limited">
        <meta property="og:type" content="website">
        <meta property="og:title" content="<?= $item['TeacherScene']['title'];?>">
        <meta property="og:image" content="<?= $preview_image;?>">
        <meta property="og:description" content="<?= strip_tags($item['TeacherScene']['content']);?>">
        <meta property="og:url" content="https://necampus.com">
        <meta property="fb:app_id" content="874635473067320">
        <meta property="og:site_name" content="NEcampus">
        <link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css">
        
        <style>
            .qrcode {
                margin-top: 50px;
            }
            .install_icon{
                max-width: 200px;
            }
            .btn-download{
                margin-top: 20px;
            }
            .copyright{
                margin-bottom: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container text-center">
            <div class="qrcode">
                <div class="row">Scan QR code from NEcampus to view lesson</div>
                <img src="<?= $share_image?>"/>
            </div>
            <div class="download">
                <div class="row">
                    <?php if(!$is_mobile){?>
                        <div class="col-md-6 col-lg-6">
                            <a class="button" href="https://apps.apple.com/us/app/id1533448409">
                                <img class="install_icon" alt="" src="/img/appstore.png">
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <a class="button" href="http://play.google.com/store/apps/details?id=com.necampus">
                                <img class="install_icon" alt="" src="/img/google-play.png">
                            </a>
                        </div>
                    <?php } else {
                        ?>
                        <div class="col-xs-12 col-md-12 col-lg-12 btn-download">
                            <a class="button" href="https://apps.apple.com/us/app/id1533448409">
                                <img class="install_icon" alt="" src="/img/appstore.png">
                            </a>
                        </div>
                        <div class="col-xs-12 col-md-12 col-lg-12 btn-download">
                            <a class="button" href="http://play.google.com/store/apps/details?id=com.necampus">
                                <img class="install_icon" alt="" src="/img/google-play.png">
                            </a>
                        </div>
                        <div class="col-xs-12 col-md-12 col-lg-12 btn-download">
                            <a class="button" href="necampus://teacher_scene/<?= $item['TeacherScene']['id']; ?>/<?= $user_id; ?>">
                                <button class="btn btn-warning">Deep Link</button>
                            </a>
                        </div>
                    <?php
                    }?>
                </div>
            </div>
            <div class="copyright">
                
            </div>
        </div>
    </body>
</html>
