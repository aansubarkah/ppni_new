<div class="panel panel-default">
    <div class="panel-heading">
<?php
echo $this->Form->create(null, [
    'url' => ['controller' => 'Users', 'action' => 'index'],
    'type' => 'get'
]);
?>
        <div class="input-group custom-search-form">
            <input name="search" class="form-control" placeholder="Pencarian Nama..." type="text" autocomplete="off">
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
echo $this->Paginator->sort('username', 'Username');
?>
                    </th>
                    <th>
<?php
echo $this->Paginator->sort('fullname', 'Nama');
?>
                    </th>
                    <th>
<?php
echo $this->Paginator->sort('email', 'Email');
?>
                    </th>
                    <th>Grup</th>
                    <th>Unit Kerja</th>
                </tr>
            </thead>
            <tbody>
<?php
// get current page
$numberPage = $this->Paginator->counter('{{page}}');
$sequence = ($numberPage - 1) * $limit;
foreach($users as $user)
{
?>
                <tr>
                    <td>
<?php
    $sequence++;
    echo $sequence;
?>
                    </td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['fullname']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['group']['name']; ?></td>
                    <td>
<?php
    if (count($user['departements']) > 0) {
        echo $user['departements'][0]['name'];
    }

    echo $this->Html->link(
        '<i class="fa fa-undo fa-fw"></i>',
        ['controller' => 'Users', 'action' => 'reset', $user->id],
        ['escape' => false, 'confirm' => 'Ingin Reset Password Pengguna ' . $user->fullname . '? Password akan kembali menjadi angka 1']
    );
    echo $this->Html->link(
        '<i class="fa fa-pencil fa-fw"></i>',
        ['controller' => 'Users', 'action' => 'edit', $user->id],
        ['escape' => false]
    );
    echo $this->Html->link(
        '<i class="fa fa-trash fa-fw"></i>',
        ['controller' => 'Users', 'action' => 'delete', $user->id],
        ['escape' => false, 'confirm' => 'Ingin Menghapus Pengguna ' . $user->fullname . '?']
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
