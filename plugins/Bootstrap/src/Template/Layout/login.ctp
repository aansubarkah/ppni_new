<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>PPNI - Login</title>

    <?= $this->Html->meta('icon') ?>
<?php
echo $this->Html->script([
    'jquery.min',
    'moment-with-locales.min',
    'bootstrap.min',
    'sb-admin-2.min',
    'metisMenu.min'
]);
echo $this->Html->css([
    'bootstrap/bootstrap.min',
    'metisMenu/metisMenu.min',
    'sb-admin-2.min',
    'morrisjs/morris',
    'font-awesome/css/font-awesome.min'
]);
?>
    <?= $this->fetch('meta') ?>
    </head>
    <body>
        <div class="container">
        <div class="row" style="height: 5px;"></div>
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="row">
<?php
if (isset($isLoginError))
{
?>
<div class="col-md-12">
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo $this->Flash->render(); ?>
    </div>
</div>
<?php
}
?>
                </div>
                <div class="row">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Login</h3>
                    </div>
                    <div class="panel-body">
<?php
echo $this->Form->create(NULL, [
    'url' => [
        'controller' => 'users',
        'action' => 'login'
    ],
    'id' => 'user',
    'data-toggle' => 'validator'
]);

echo '<fieldset>';

echo '<div class="form-group">';
echo $this->Form->text('username', [
    'autofocus' => true,
    'label' => false,
    'class' => 'form-control',
    'placeholder' => 'Username'
]);
echo '</div>';

echo '<div class="form-group">';
echo $this->Form->input('password', [
    'type' => 'password',
    'label' => false,
    'class' => 'form-control',
    'placeholder' => 'Password'
]);
echo '</div>';

echo $this->Form->button('Login', [
    'type' => 'submit',
    'class' => 'btn btn-lg btn-primary btn-block'
]);

echo $this->Form->end();

echo '</fieldset>';
?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </body>
</html>
