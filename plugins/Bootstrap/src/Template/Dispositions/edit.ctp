<blockquote>
    <span class="label label-default" id="letterDateBadge"></span>
    <span class="label label-primary">Surat Masuk</span>
    <h2><?php echo $letter['content']; ?></h2><br>
<?php
foreach($letter['evidences'] as $evidence) {
    echo $this->Html->link(
        '<i class="fa fa-file fa-fw"></i>&nbsp;' . $evidence['name'] . '&nbsp;(' . $evidence['extension'] . ')',
        '/download/' . $evidence['id'],
        ['escape' => false]
    );
    echo '&nbsp;';
}
?>
    <footer>
        Pengirim:&nbsp;
<?php
echo $this->Html->link(
    $letter['sender']['name'],
    ['controller' => 'senders', 'action' => 'view', $letter['sender']['id']]
);
?>
        &nbsp;Nomor:&nbsp;
<?php
echo $this->Html->link(
    $letter['number'],
    ['controller' => 'letters', 'action' => 'view', $letter['id']]
);
?>
    </footer>
</blockquote>
<?php
echo $this->Form->create($disposition, [
    'id' => 'disposition',
    'data-toggle' => 'validator'
]);

echo '<div class="form-group">';

echo $this->Form->text('recipient', [
    'label' => false,
    'class' => 'form-control',
    'placeholder' => $recipientDepartement[0]['name'],
    'id' => 'sender',
    'required',
    'data-error' => 'Kepada harus diisi',
    'value' => $recipientDepartement[0]['name'],
    'disabled'
]);
echo $this->Form->hidden('recipients', [
    'id' => 'sender',
    'value' => $recipientDepartement[0]['id']
]);

echo '</div>';

echo '<div class="form-group">';
echo $this->Form->textarea('content', [
    'label' => false,
    'class' => 'form-control',
    'placeholder' => 'Isi Disposisi',
    'id' => 'content',
    'required',
    'autocomplete' => 'off',
    'data-error' => 'Isi disposisi harus diisi',
    'value' => $disposition['content']
]);
echo '</div>';
?>
<div class="form-group">
    <span class="btn btn-default fileinput-button">
    <i class="glyphicon glyphicon-plus"></i>
    <span>Unggah Berkas</span>
    <!-- The file input field used as target for the file upload widget -->
    <input id="fileupload" type="file" name="files[]">
    </span>
</div>

<div class="form-group">
    <div id="progress" class="progress form-group">
        <div class="progress-bar progress-bar-success"></div>
    </div>
    <!-- The container for the uploaded files -->
    <div id="files" class="files"></div>
</div>

<?php
echo $this->Form->hidden('evidence_id', [
    'id' => 'evidence_id',
    'value' => 0
]);

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

echo $this->Html->css([
    'bootstrap-tagsinput-typeahead',
    'bootstrap-tagsinput'
]);
echo $this->Html->script([
    'typeahead.bundle.min',
    'bloodhound.min',
    'bootstrap-tagsinput.min',
    'validator.min'
]);
echo $this->JqueryFileUpload->loadCss();
echo $this->JqueryFileUpload->loadScripts();
?>
<script>
$(function() {
    // upload file
    // first hide progress bar
    $('#parent').hide();
    $('#progress').hide();
    $('#files').hide();

    // don't forget to setup max_file_size value on php.ini
    $('#fileupload').fileupload({
        dataType: 'json',
        formData: {filename: 'Disposisi'},
        url: '/evidences/upload',
        add: function (e, data) {
            $('#progress').show();
            $('#files').show();
            $('#files').empty();
            data.context = $('<p/>').text('Mengunggah...').appendTo('#files');
            data.submit();
        },
        done: function (e, data) {
            $('#progress').hide();
            $('#files').empty();

            // get evidence_id from server
            $('#evidence_id').val(data.result.evidence.id);

            $.each(data.result.result.files, function (index, file) {
                var textToDisplay = file.name + ' berhasil diunggah.';
                $('<p/>').text(textToDisplay).appendTo('#files');
            });
        },
        progressall: function (e, data) {
            //$('#progress').show();
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disable', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');

    // simply validating form
    $('#disposition').validator();

    moment.locale('id');
    letterDate = moment('<?php echo $this->Time->format($letter['date'], 'yyyy-MM-dd HH:mm'); ?>').format('D MMMM YYYY');
    $('#letterDateBadge').text(letterDate);
});
</script>
<style>
.bootstrap-tagsinput{
    width: 100%;
}
</style>
