<?php
echo $this->Form->create($newUser, [
    'id' => 'user',
    'data-toggle' => 'validator'
]);

echo '<div class="form-group">';
echo $this->Form->text('username', [
    'label' => false,
    'class' => 'form-control',
    'placeholder' => 'Username',
    'id' => 'username',
    'required',
    'autocomplete' => 'off',
    'data-error' => 'Username harus diisi, Tanpa Spasi',
    'pattern' => '^\S*$'
]);
echo '</div>';

echo '<div class="form-group">';
echo $this->Form->text('fullname', [
    'label' => false,
    'class' => 'form-control',
    'placeholder' => 'Nama Lengkap',
    'id' => 'fullname',
    'required',
    'autocomplete' => 'off',
    'data-error' => 'Nama Lengkap harus diisi'
]);
echo '</div>';

echo '<div class="form-group">';
echo $this->Form->text('email', [
    'label' => false,
    'class' => 'form-control typeahead',
    'placeholder' => 'Email',
    'autocomplete' => 'off',
    'id' => 'email',
    'required',
    'data-error' => 'Email harus diisi',
    'type' => 'email'
]);
echo '</div>';

echo '<div class="form-group">';
echo $this->Form->select('group_id', $groups, [
    'class' => 'form-control'
]);
echo '</div>';

echo '<div class="form-group">';
echo $this->Form->select('departement_id', $departements, [
    'class' => 'form-control'
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
