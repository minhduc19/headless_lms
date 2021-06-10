<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        
    </head>
    <body>
        <?php 
            echo $this->element('frontend/header');
            echo $this->fetch('content'); 
            echo $this->element('frontend/footer');
        ?>
        <?php
            echo $this->Html->script('plugins/jquery.min');
            echo $this->Html->script('plugins/bootstrap/bootstrap.min');
            echo $this->fetch('script');
        ?>
    </body>

</html>