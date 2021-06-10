<?php

$password = $this->Form->input('User.password', array('type' => 'password','placeholder' => array('text' => __('Password')), 'class' => 'form-control form-control-solid placeholder-no-fix', 'div' => FALSE, 'label' => FALSE, 'data-validation' => 'required'));
$confirm_password = $this->Form->input('User.confirm_password', array('type' => 'password','placeholder' => array('text' => __('Confirm Password')), 'class' => 'form-control form-control-solid placeholder-no-fix', 'div' => FALSE, 'label' => FALSE, 'data-validation' => 'required'));
?>
<?php
echo $this->Session->flash();
?>
<?php echo $this->Form->create('User', array('inputDefaults' => array('label' => false, 'div' => false), 'novalidate' => true)); ?>
<h3 class="form-title"><?php echo $title_for_layout; ?></h3>
<div class="form-group">
    <label class="control-label visible-ie8 visible-ie9"><?php echo __('New password'); ?></label>
    <?php echo $password; ?>
</div>
<div class="form-group">
    <label class="control-label visible-ie8 visible-ie9"><?php echo __('Confirm Password'); ?></label>
    <?php echo $confirm_password; ?>
</div>
<div class="form-actions">
      <?php
            echo $this->Form->submit(__('Save'), array('class' => 'btn btn-success uppercase', 'div' => FALSE));
            echo $this->Form->button(__('Cancel'), array('class' => 'btn btn-default uppercase', 'div' => FALSE, 'type' => 'button', 'onclick' => 'window.location=\'' . $this->Html->url(array('controller' => $arrParam['controller'], 'action' => 'index')) . '\'; return false'));
       ?>
</div>
<?php echo $this->Form->end(); ?>