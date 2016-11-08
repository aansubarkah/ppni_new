<script>
$(function(){
    moment.locale('id');
    letterDate = moment('<?php echo $this->Time->format($letter['date'], 'yyyy-MM-dd HH:mm'); ?>').format('D MMMM YYYY');
    letterCreatedDate = moment('<?php echo $this->Time->format($letter['created'], 'yyyy-MM-dd HH:mm'); ?>').format('D MMMM YYYY HH:mm');
    $('#letterDateBadge').text(letterDate);
    $('#letterCreated').text(letterCreatedDate);
});
</script>
<blockquote>
    <span class="badge" id="letterDateBadge"></span>
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
    &nbsp;Melalui:&nbsp;
    <strong><?php echo $letter['via']['name']; ?></strong>
    &nbsp;Diterima:&nbsp;
        <strong id="letterCreated"></strong>
        &nbsp;Oleh:&nbsp;
        <strong><?php echo $letter['user']['fullname']; ?></strong>
    </footer>
</blockquote>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Disposisi&nbsp;
<?php
echo $this->Html->link(
    '<i class="fa fa-edit fa-fw"></i>',
    ['controller' => 'dispositions', 'action' => 'add', $letter['id']],
    ['escape' => false]
);
?>
        </h3>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
<?php
//print_r($letter);
if (count($dispositions) > 0)
{
?>
        <ul class="timeline">
<?php
    $index = 0;
    foreach ($dispositions as $disposition) {
        //print_r($disposition);
        if ($index%2 == 0) {
            echo '<li>';
            echo '<div class="timeline-badge info">';
            echo '<i class="fa fa-archive"></i>';
        } else {
            echo '<li class="timeline-inverted">';
            echo '<div class="timeline-badge warning">';
            echo '<i class="fa fa-file-text-o"></i>';
        }
        echo '</div>';
        echo '<div class="timeline-panel">';
        echo '<div class="timeline-heading">';
        echo '<h4 class="timeline-title">';
        echo $disposition['user']['fullname'];
        echo '</h4>';//timeline-title
        echo '<p>';
        echo '<small class="text-muted">';
        echo 'Kepada:&nbsp;';
        echo '<strong>' . $disposition['recipient']['fullname'] . '</strong>';
        echo '&nbsp;Dibuat:&nbsp;';
        echo '<strong>';
?>
        <script>
        date =  moment('<?php echo $this->Time->format($disposition['created'], 'yyyy-MM-dd HH:mm'); ?>').format('D MMMM YYYY HH:mm');
        document.write(date);
        </script>
<?php
        echo '</strong>';
        echo '</small>';
        echo '</p>';
        echo '</div>';//timeline-heading
        echo '<div class="timeline-body">';
        echo '<p>' . $disposition->content . '</p>';
        echo '</div>';//timeline-body
        echo '</div>';//timeline-panel
        echo '</li>';
        $index++;
    }
?>
        </ul>
        <!-- /.timeline -->
<?php
} else {
?>
        <span class="text-center">Belum Ada Disposisi</span>
<?php
}
?>
    </div>
    <!-- /.panel-body -->
</div>
<!-- /.panel-default>-->
