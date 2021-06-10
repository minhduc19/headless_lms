<section class="banner">
    <div class="background-banner"></div>
</section>
<section class="content-page">
    <div class="container">
        <div class="row">
            <?php echo $this->element('frontend/guide/header');?>
            <div class="wrap-guide tab-content">
                <?php
                    echo $this->element('frontend/guide/gioi-thieu');
                    echo $this->element('frontend/guide/vu-khi');
                    echo $this->element('frontend/guide/trang-bi');
                    echo $this->element('frontend/guide/bo-tro');
                    echo $this->element('frontend/guide/che-do');
                ?>
            </div>
        </div>
    </div>
</section>
