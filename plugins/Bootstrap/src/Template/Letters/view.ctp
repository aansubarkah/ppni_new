<blockquote>
    <h2><?php echo $letter['content']; ?></h2><br>
<?php
foreach($letter['evidences'] as $evidence) {
    echo $this->Html->link(
        '<i class="fa fa-file fa-fw"></i>&nbsp;' . $evidence['name'],
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
        <strong>
<script>
moment.locale('id');
letterDate = moment('<?php echo $this->Time->format($letter['created'], 'yyyy-MM-dd HH:mm'); ?>').format('D MMMM YYYY HH:mm');
document.write(letterDate);
</script>
        </strong>
        &nbsp;Oleh:&nbsp;
        <strong><?php echo $letter['user']['fullname']; ?></strong>
    </footer>
</blockquote>
<?php
//print_r($letter);
