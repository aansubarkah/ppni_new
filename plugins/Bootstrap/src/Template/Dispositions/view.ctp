<script>
$(function(){
    moment.locale('id');
    dispositionCreatedDate = moment('<?php echo $this->Time->format($disposition['created'], 'yyyy-MM-dd HH:mm'); ?>').format('D MMMM YYYY HH:mm');
    letterDate = moment('<?php echo $this->Time->format($letter['date'], 'yyyy-MM-dd HH:mm'); ?>').format('D MMMM YYYY');
    letterCreatedDate = moment('<?php echo $this->Time->format($letter['created'], 'yyyy-MM-dd HH:mm'); ?>').format('D MMMM YYYY HH:mm');
    $('#dispositionDateBadge').text(dispositionCreatedDate);
    $('#letterDateBadge').text(letterDate);
    $('#letterCreated').text(letterCreatedDate);
});
</script>
<!-- disposition -->
<blockquote>
    <span class="label label-default" id="dispositionDateBadge"></span>
    <span class="label label-warning">Disposisi</span>
    <h2><?php echo $disposition['content']; ?></h2><br>
<?php
foreach($disposition['evidences'] as $evidence) {
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
        <strong><?php echo $disposition['user']['fullname'];?></strong>
        &nbsp;Kepada:&nbsp;
        <strong><?php echo $disposition['recipient']['fullname']; ?></strong>
    </footer>
</blockquote>

<!-- letter -->
<blockquote>
    <span class="label label-default" id="letterDateBadge"></span>
    <span class="label label-primary">Surat Masuk</span>
    <h2>
<?php
echo $this->Html->link($letter['number'],
    ['controller' => 'letters', 'action' => 'view', $letter['id']],
    ['escape' => false]
);
?>
    </h2>
    <h4><?php echo $letter['content']; ?></h4><br>
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
