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
    '<i class="fa fa-edit fa-fw text-primary"></i>',
    ['controller' => 'dispositions', 'action' => 'add', $letter['id']],
    ['escape' => false]
);
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
        echo '<p class="text-info">';
        echo $disposition['user']['fullname'];
        echo '</p>';//timeline-title
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
        //print_r($disposition);
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
<style>
<?php
if ($isMobile) {
?>
.timeline {
    list-style: none;
    padding: 20px 0 20px;
    position: relative;
}
.timeline:before {
    top: 0;
    bottom: 0;
    position: absolute;
    content:" ";
    width: 3px;
    background-color: #eeeeee;
    left: 50%;
    margin-left: -1.5px;
}
.timeline > li {
    margin-bottom: 20px;
    position: relative;
}
.timeline > li:before, .timeline > li:after {
    content:" ";
    display: table;
}
.timeline > li:after {
    clear: both;
}
.timeline > li:before, .timeline > li:after {
    content:" ";
    display: table;
}
.timeline > li:after {
    clear: both;
}
.timeline > li > .timeline-panel {
    width: 46%;
    float: left;
    border: 1px solid #d4d4d4;
    border-radius: 2px;
    padding: 20px;
    position: relative;
    -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
    box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
}
.timeline > li > .timeline-panel:before {
    position: absolute;
    top: 26px;
    right: -15px;
    display: inline-block;
    border-top: 15px solid transparent;
    border-left: 15px solid #ccc;
    border-right: 0 solid #ccc;
    border-bottom: 15px solid transparent;
    content:" ";
}
.timeline > li > .timeline-panel:after {
    position: absolute;
    top: 27px;
    right: -14px;
    display: inline-block;
    border-top: 14px solid transparent;
    border-left: 14px solid #fff;
    border-right: 0 solid #fff;
    border-bottom: 14px solid transparent;
    content:" ";
}
.timeline > li > .timeline-badge {
    color: #fff;
    width: 50px;
    height: 50px;
    line-height: 50px;
    font-size: 1.4em;
    text-align: center;
    position: absolute;
    top: 16px;
    left: 50%;
    margin-left: -25px;
    background-color: #999999;
    z-index: 100;
    border-top-right-radius: 50%;
    border-top-left-radius: 50%;
    border-bottom-right-radius: 50%;
    border-bottom-left-radius: 50%;
}
.timeline > li.timeline-inverted > .timeline-panel {
    float: right;
}
.timeline > li.timeline-inverted > .timeline-panel:before {
    border-left-width: 0;
    border-right-width: 15px;
    left: -15px;
    right: auto;
}
.timeline > li.timeline-inverted > .timeline-panel:after {
    border-left-width: 0;
    border-right-width: 14px;
    left: -14px;
    right: auto;
}
.timeline-badge.primary {
    background-color: #2e6da4 !important;
}
.timeline-badge.success {
    background-color: #3f903f !important;
}
.timeline-badge.warning {
    background-color: #f0ad4e !important;
}
.timeline-badge.danger {
    background-color: #d9534f !important;
}
.timeline-badge.info {
    background-color: #5bc0de !important;
}
.timeline-title {
    margin-top: 0;
    color: inherit;
}
.timeline-body > p, .timeline-body > ul {
    margin-bottom: 0;
}
.timeline-body > p + p {
    margin-top: 5px;
}
@media (max-width: 767px) {
    ul.timeline:before {
        left: 40px;
    }
    ul.timeline > li > .timeline-panel {
        width: calc(100% - 90px);
        width: -moz-calc(100% - 90px);
        width: -webkit-calc(100% - 90px);
    }
    ul.timeline > li > .timeline-badge {
        left: 15px;
        margin-left: 0;
        top: 16px;
    }
    ul.timeline > li > .timeline-panel {
        float: right;
    }
    ul.timeline > li > .timeline-panel:before {
        border-left-width: 0;
        border-right-width: 15px;
        left: -15px;
        right: auto;
    }
    ul.timeline > li > .timeline-panel:after {
        border-left-width: 0;
        border-right-width: 14px;
        left: -14px;
        right: auto;
    }
}
<?php
}
?>
</style>
