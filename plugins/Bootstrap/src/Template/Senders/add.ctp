<?php
echo $this->Form->create($letter, [
    'id' => 'sender',
    'data-toggle' => 'validator'
]);

echo '<div class="form-group">';
echo $this->Form->text('name', [
    'label' => false,
    'class' => 'form-control',
    'placeholder' => 'Nama',
    'id' => 'name',
    'required',
    'autocomplete' => 'off',
    'data-error' => 'Nama harus diisi'
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
    $('#sender').validator();
});
</script>
