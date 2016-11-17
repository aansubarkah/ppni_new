<div class="panel panel-default">
    <div class="panel-heading">
<?php
echo $this->Form->create(null, [
    'url' => ['controller' => 'Letters', 'action' => 'index'],
    'type' => 'get'
]);
?>
        <div class="input-group custom-search-form">
            <input name="search" class="form-control" placeholder="Pencarian Perihal..." type="text" autocomplete="off">
            <span class="input-group-btn">
                <button class="btn btn-default" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
        <!-- /.custom-search-form -->
<?php
echo $this->Form->end();
?>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <div class="row">
        <table width="100%" class="table table-striped" id="dataTables-letters">
            <thead>
                <tr>
                    <th>#</th>
                    <th>
<?php
echo $this->Paginator->sort('created', 'Tanggal Masuk');
?>
                    </th>
                    <th>
<?php
echo $this->Paginator->sort('date', 'Tanggal');
?>
                    </th>
                    <th>
<?php
echo $this->Paginator->sort('number', 'Nomor');
?>
                    </th>
                    <th>Pengirim</th>
                    <th>
<?php
echo $this->Paginator->sort('content', 'Perihal');
?>
                    </th>
                </tr>
            </thead>
            <tbody>
<?php
// get current page
$numberPage = $this->Paginator->counter('{{page}}');
$sequence = ($numberPage - 1) * $limit;
foreach($letters as $letter)
{
?>
                <tr>
                    <td>
<?php
    $sequence++;
    echo $sequence;
?>
                    </td>
                    <td>

    <script>
    moment.locale('id');
    letterDate = moment('<?php echo $this->Time->format($letter->created, 'yyyy-MM-dd'); ?>').format('D MMMM YYYY');
    document.write(letterDate);
    </script>

                    </td>
                    <td>
    <script>
    letterDate = moment('<?php echo $this->Time->format($letter->date, 'yyyy-MM-dd'); ?>').format('D MMMM YYYY');
    document.write(letterDate);
    </script>
                    </td>
                    <td>
<?php
    echo $this->Html->link(
        $letter->number,
        ['controller' => 'Letters', 'action' => 'view', $letter->id],
        ['escape' => false]
    );
?>
                    </td>
                    <td>
<?php
    echo $letter->has('sender') ? $this->Html->link($letter->sender->name, ['controller' => 'Senders', 'action' => 'view', $letter->sender->id]) : '';
?>
                    </td>
                    <td>
<?php
    echo $this->Html->link(
        $letter->content,
        ['controller' => 'Letters', 'action' => 'view', $letter->id],
        ['escape' => false]

    );
    echo $this->Html->link(
        '<i class="fa fa-pencil fa-fw"></i>',
        ['controller' => 'Letters', 'action' => 'edit', $letter->id],
        ['escape' => false]
    );
    echo $this->Html->link(
        '<i class="fa fa-trash fa-fw"></i>',
        ['controller' => 'Letters', 'action' => 'delete', $letter->id],
        ['escape' => false, 'confirm' => 'Ingin Menghapus Surat No ' . $letter->number . '?']
    );


?>
                    </td>
                </tr>
<?php
}
?>
            </tbody>
        </table>
    </div>
    <div class="row pull-right">
        <div class="col-md-10 col-md-offset-2">
        <ul class="pagination">
<?php
echo $this->Paginator->first('&laquo;',['escape' => false]);
echo $this->Paginator->prev('&lsaquo;',['escape' => false]);
echo $this->Paginator->numbers();
echo $this->Paginator->next('&rsaquo;',['escape' => false]);
echo $this->Paginator->last('&raquo;',['escape' => false]);
?>
        </ul>
        </div>
    </div>
    <!-- /.row -->
    </div>
    <!-- /.panel-body -->
</div>
<!-- /.panel-default>
