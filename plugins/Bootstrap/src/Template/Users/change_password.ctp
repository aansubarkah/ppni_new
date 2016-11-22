<?php
echo $this->Form->create($editPassword, [
    'id' => 'user',
    'data-toggle' => 'validator'
]);

echo '<div class="form-group">';
echo $this->Form->password('oldPassword', [
    'label' => false,
    'class' => 'form-control',
    'placeholder' => 'Password Lama',
    'id' => 'oldPassword',
    'required',
    'autocomplete' => 'off',
    'data-error' => 'Password Lama harus diisi'
]);
echo '</div>';

echo '<div class="form-group">';
echo $this->Form->password('newPassword1', [
    'label' => false,
    'class' => 'form-control',
    'placeholder' => 'Password Baru',
    'id' => 'newPassword1',
    'required',
    'autocomplete' => 'off',
    'data-error' => 'Password Baru harus diisi'
]);
echo '</div>';
echo '<div class="form-group">';
echo $this->Form->password('newPassword2', [
    'label' => false,
    'class' => 'form-control',
    'placeholder' => 'Konfirmasi Password Baru',
    'id' => 'newPassword2',
    'required',
    'autocomplete' => 'off',
    'data-error' => 'Password Baru harus diisi',
    'data-match' => '#newPassword1'
]);
echo '</div>';

echo '<div class="form-group pull-right">';
echo $this->Form->button('Reset', [
    'type' => 'reset',
    'class' => 'btn btn-default'
]);
echo '&nbsp;';
echo $this->Form->button('Submit', [
    'type' => 'submit',
    'class' => 'btn btn-primary'
]);
echo $this->Form->end();
echo '</div>';

echo $this->Html->script([
    'validator.min'
]);
?>
<script>
$(function() {
    // simply validating form
    $('#user').validator();
});
</script>
