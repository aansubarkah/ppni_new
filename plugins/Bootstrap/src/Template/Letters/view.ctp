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
    <span class="label label-default" id="letterDateBadge"></span>
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
if (isset($isCurrentUserHaveDepartement)) {
    echo $this->Html->link(
        '<i class="fa fa-edit fa-fw text-primary"></i>',
        ['controller' => 'dispositions', 'action' => 'add', $letter['id']],
        ['escape' => false]
    );
}
?>
        </h3>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
<?php
if (count($dispositions) > 0)
{
?>
        <ul class="timeline">
<?php
    $index = 0;
    foreach ($dispositions as $disposition) {
        if ($index%2 == 0) {
            echo '<li>';
            echo '<div class="timeline-badge primary">';
            echo '<i class="fa fa-archive"></i>';
        } else {
            echo '<li class="timeline-inverted">';
            echo '<div class="timeline-badge warning">';
            echo '<i class="fa fa-file-text-o"></i>';
        }
        echo '</div>';
        echo '<div class="timeline-panel">';
        echo '<div class="timeline-heading">';
        echo '<p class="text-info">';
        echo '<span class="text-info">';

        $dispositionLink = '<i class="fa fa-user-md fa-fw"></i>';
        $dispositionLink = $dispositionLink . '&nbsp;&nbsp;';
        $dispositionLink = $dispositionLink . $disposition['user']['fullname'];
        $dispositionLink = $dispositionLink . '&nbsp;&nbsp;';
        $dispositionLink = $dispositionLink . '<i class="fa fa-random fa-fw"></i>';
        $dispositionLink = $dispositionLink . '&nbsp;&nbsp;';
        $dispositionLink = $dispositionLink . $disposition['recipient']['fullname'];
        echo $this->Html->link($dispositionLink,
            ['controller' => 'dispositions', 'action' => 'view', $disposition['id']],
            ['escape' => false]
        );

        if ($currentUserId == $disposition['user']['id']) {
            echo $this->Html->link(
                '<i class="fa fa-pencil fa-fw"></i>',
                ['controller' => 'dispositions', 'action' => 'edit', $disposition['id']],
                ['escape' => false]
            );
            echo $this->Html->link(
                '<i class="fa fa-trash fa-fw"></i>',
                ['controller' => 'dispositions', 'action' => 'delete', $disposition['id']],
                ['escape' => false, 'confirm' => 'Ingin Menghapus?']
            );
        }
        echo '</p>';//timeline-title
        echo '<p>';
        echo '<small class="text-muted">';
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
        echo '<p class="lead">' . $disposition->content . '</p>';
        if (count($disposition->evidences) > 0) {
            echo '<span class="pull-left">';
            foreach ($disposition->evidences as $evidence) {
                echo $this->Html->link(
                    '<i class="fa fa-file fa-fw"></i>&nbsp;' . $evidence['name'] . '&nbsp;(' . $evidence['extension'] . ')',
                    '/download/' . $evidence['id'],
                    ['escape' => false]
                );
                echo '&nbsp;';
            }
            echo '</span>';
        }
        if ($currentUserId == $disposition['recipient_id']) {
            echo '<span class="pull-right"><small>';
            echo $this->Html->link(
                '<i class="fa fa-briefcase fa-fw"></i>&nbsp;' . 'Tindak Lanjuti',
                [
                    'controller' => 'dispositions',
                    'action' => 'add',
                    $letter['id'],
                    $disposition['id']
                ],
                ['escape' => false]
            );
            echo '</small></span>';//span
        }
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
<?php
if ($isMobile) {
    echo $this->Html->css('mobile-timeline');
}
?>
