<?php
echo $this->Form->create($letter, [
    'id' => 'letter',
    'data-toggle' => 'validator'
]);

echo '<div class="form-group">';
echo $this->Form->text('number', [
    'label' => false,
    'class' => 'form-control',
    'placeholder' => 'Nomor',
    'id' => 'number',
    'required',
    'autocomplete' => 'off',
    'data-error' => 'Nomor harus diisi',
    'value' => $letter['number']
]);
echo '</div>';

echo '<div class="form-group">';
echo $this->Form->text('content', [
    'label' => false,
    'class' => 'form-control',
    'placeholder' => 'Perihal',
    'id' => 'content',
    'required',
    'autocomplete' => 'off',
    'data-error' => 'Perihal harus diisi',
    'value' => $letter['content']
]);
echo '</div>';

echo '<div class="form-group">';
echo $this->Form->text('sender', [
    'label' => false,
    'class' => 'form-control typeahead',
    'placeholder' => 'Pengirim',
    'autocomplete' => 'off',
    'id' => 'sender',
    'required',
    'data-error' => 'Pengirim harus diisi',
    'value' => $letter['sender']['name']
]);
echo $this->Form->hidden('sender_id', [
    'id' => 'sender_id',
    'value' => $letter['sender_id']
]);
echo $this->Form->hidden('sender_name', [
    'id' => 'sender_name',
    'value' => $letter['sender']['name']
]);
echo '</div>';

echo '<div class="form-group">';
echo $this->Form->text('date', [
    'label' => false,
    'class' => 'form-control datepicker',
    'placeholder' => 'Tanggal',
    'default' => date('d/m/Y'),
    'id' => 'date',
    'value' => $letter['date']
]);
echo '</div>';

echo '<div class="form-group">';
echo $this->Form->select('via_id', $viasOptions, [
    'class' => 'form-control',
    'default' => $letter['via_id']
]);
echo '</div>';

echo '<div class="form-group">';
foreach ($letter['evidences'] as $evidence)
{
    echo '<div class="btn-group" id="evidence-' . $evidence['id'] . '">';
    echo '<button type="button" class="btn btn-default">';
    echo '<i class="fa fa-file fa-fw"></i>&nbsp;' . $evidence['name'];
    echo '</button>';
    echo '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    echo '<span class="caret"></span>';
    echo '<span class="sr-only">Toggle Dropdown</span>';
    echo '</button>';
    echo '<ul class="dropdown-menu">';
    echo '<li>';
    echo $this->Html->link(
        '<i class="fa fa-save fa-fw"></i>Unduh',
        '/download/' . $evidence['id'],
        ['escape' => false]
    );
    echo '</li>';
    echo '<li>';
    echo $this->Html->link(
        '<i class="fa fa-trash fa-fw"></i>Hapus',
        '#',
        ['escape' => false, 'class' => 'evidence-delete', 'id' => $evidence['id']]
    );
    echo '</li>';
    echo '</ul>';
    echo '</div>';
    echo '&nbsp;&nbsp;&nbsp;';
}
echo '</div>';
//echo $this->Form->file('evidence');
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

echo $this->Html->css('bootstrap-datepicker3.min');
echo $this->Html->script([
    'bootstrap-datepicker.min',
    'bootstrap-datepicker.id.min',
    'bootstrap3-typeahead.min',
    'validator.min'
]);
echo $this->JqueryFileUpload->loadCss();
echo $this->JqueryFileUpload->loadScripts();
?>
<script>
$(function() {
    $('.datepicker').datepicker({
    format: 'dd-mm-yyyy',
        autoclose: true,
        language: 'id',
        startDate: 0
    });

    // data source for senders
    var sendersData = [];
<?php
foreach($sendersOptions as $key=>$value)
{
?>
    sendersData.push({
        id: "<?php echo $key; ?>",
        name: "<?php echo $value; ?>"
    });
<?php
}
?>
    // typeahead autocomplete for senders
    var $sender = $('.typeahead');
    $sender.typeahead({
        source: sendersData
    });
    $sender.change(function() {
        var current = $sender.typeahead('getActive');
        // if value exist on sendersData
        if(current) {
            // if sendersData as same as text inputed
            if(current.name == $sender.val()) {
                console.log(current.name);
                $('#sender_id').val(current.id);
                $('#sender_name').val(current.name);
            } else {
                $('#sender_id').val(0);
                $('#sender_name').val($sender.val());
                //console.log($sender.val());
            }
        } else {
            $('#sender_id').val(0);
            $('#sender_name').val('');
            //console.log($sender.val());
        }
    });

    // remove evidences
    $('.evidence-delete').click(function(event){
        var id = $(this).attr('id');
        var btnEvidence = '#evidence-' + id;
        var url = '<?php echo $this->Url->build(['controller' => 'evidences', 'action' => 'delete'], true); ?>';
        url = url + '/' + id;

        // ajax call to /evidences/delete/:id
        $.post(
            url,
            0,
            function(data){
                // if success, hide file button
                if(data.result == 1) {
                    $(btnEvidence).hide();
                }
            },
            'json'
        );

        // prevent button to action
        event.preventDefault();
        event.stopPropagation();
    });

    // upload file
    // first hide progress bar
    $('#parent').hide();
    $('#progress').hide();
    $('#files').hide();

    // don't forget to setup max_file_size value on php.ini
    $('#fileupload').fileupload({
        dataType: 'json',
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
    $('#letter').validator();
});
</script>
