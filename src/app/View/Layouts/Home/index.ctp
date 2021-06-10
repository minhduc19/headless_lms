<div class="body-iweb">
    <?php echo $this->element('home/dang_ki') ?>
    <?php echo $this->element('home/tinh_nang') ?>
    <?php echo $this->element('home/quang_cao') ?>
    <?php echo $this->element('home/thu_vien_mau_website', array('best_sel' => $best_sel, 'skin_cate' => $skin_cate)) ?>
    <?php echo $this->element('home/tinh_nang_2') ?>
    <?php echo $this->element('home/comment_customer') ?>
</div>
<script>
    $(function () {
        if (/#register/.test(location.hash)) {
            $('.dang-ki').click();
        }
    });
</script>