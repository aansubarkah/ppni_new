<blockquote>
    <span class="label label-default"><?php echo $profile['username']?></span>
    <h2><?php echo $profile['fullname']; ?></h2><br>
    <footer>
        Email:&nbsp;
        <strong><?php echo $profile['email']; ?></strong>
        &nbsp;Level:&nbsp;
        <strong><?php echo $profile['group']['name']; ?></strong>
    </footer>
</blockquote>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Disposisi&nbsp;
        </h3>
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
echo $this->Paginator->sort('created', 'Tanggal');
?>
                            </th>
                            <th>Kepada</th>
                            <th>Surat Masuk</th>
                            <th>Tanggal</th>
                            <th>Perihal</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
// get current page
$numberPage = $this->Paginator->counter('{{page}}');
$sequence = ($numberPage - 1) * $limit;
foreach($dispositions as $disposition)
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
<?php
    $link = '<script>';
    $link = $link . 'letterDate = moment("';
    $link = $link . $this->Time->format($disposition->created, 'yyyy-MM-dd HH:mm');
    $link = $link . '").format("D MMMM YYYY");';
    $link = $link . 'document.write(letterDate);';
    $link = $link . '</script>';
    echo $this->Html->link(
        $link,
        ['controller' => 'dispositions', 'action' => 'view', $disposition['id']],
        ['escape' => false]
    );
?>
                            </td>
                            <td>
<?php
    echo $disposition['recipient']['fullname'];
?>
                            </td>
                            <td>
<?php
    echo $this->Html->link(
        $disposition['letter']['number'],
        ['controller' => 'Letters', 'action' => 'view', $disposition['letter']['id']],
        ['escape' => false]
    );
?>
                            </td>
                            <td>
    <script>
    letterDate = moment('<?php echo $this->Time->format($disposition['letter']['date'], 'yyyy-MM-dd'); ?>').format('D MMMM YYYY');
    //console.log(letterDate);
    document.write(letterDate);
    </script>
                            </td>
                            <td>
<?php
    echo $disposition['letter']['content'];
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
<!-- /.panel-default>-->
