<?php
$btn_login = $this->Form->button('Get Password', array('type' => 'submit', 'class' => 'btn btn-success uppercase', 'div' => false, 'label' => false, 'escape' => false));
?>
<?php echo $this->Form->create('User', array('inputDefaults' => array('label' => false), 'novalidate' => true, 'type' => 'POST')); ?>
<h3 class="form-title"><?php echo __('Login Email'); ?></h3>
<?php echo $this->Session->flash(); ?>
<div class="form-group">
    <label class="control-label visible-ie8 visible-ie9"><?php echo __('Email'); ?></label>
    <?php echo $this->Form->input('email', $options = array('class' => 'form-control', 'label' => false)); ?>
</div>
<div class="form-actions">
    <?php echo $btn_login; ?>        
</div>
<?php echo $this->Form->end(); ?>