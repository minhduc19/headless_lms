<?php
$email = $this->Form->input('Teacher.email', array('type' => 'text', 'placeholder' => array('text' => __('Email or Username')), 'class' => 'form-control form-control-solid placeholder-no-fix', 'label' => false, 'div' => FALSE));
$password = $this->Form->input('Teacher.password', array('type' => 'password', 'placeholder' => array('text' => __('Password')), 'class' => 'form-control form-control-solid placeholder-no-fix', 'label' => false, 'div' => FALSE));
$btn_login = $this->Form->button('Sign In', array('type' => 'submit', 'class' => 'btn btn-success uppercase center-block', 'div' => false, 'label' => false, 'escape' => false));
?>

<?php echo $this->Form->create('Teacher', array('inputDefaults' => array('label' => false), 'novalidate' => true, 'type' => 'POST')); ?>
<?php echo $this->Session->flash(); ?>

<h3 class="form-title"><?php echo __('Sign in'); ?></h3>
<div class="form-group">
    <label class="control-label visible-ie8 visible-ie9"><?php echo __('Email'); ?></label>
    <?php echo $email; ?>
</div>
<div class="form-group">
    <label class="control-label visible-ie8 visible-ie9"><?php echo __('Password'); ?></label>
    <?php echo $password; ?>
</div>
<div class="form-actions">
    <?php echo $btn_login; ?>
</div>
<a class="btn btn-success google btn-block btn-social btn-google" href="<?php echo $this->Html->url(array('controller' => 'social', 'action' => 'google_oauth', 'teacher' => true)); ?>"> <i class="fa fa-google-plus modal-icons"></i> Signin with Google </a>
<?php echo $this->Form->end(); ?>