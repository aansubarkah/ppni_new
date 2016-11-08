<div class="col-lg-12">
    <div id="tree-simple"></div>
</div>
<?php
echo $this->Html->script(['raphael', 'Treant', 'jquery.orgchart']);
echo $this->Html->css(['Treant', 'jquery.orgchart']);
?>
<script>
$(function(){
    var simple_chart_config = {
        container: '#tree-simple',
        connectors: {
            type: 'step'
        }
    };

    var chart_config = [simple_chart_config];
<?php
foreach ($departements as $departement) {
?>
    var tree<?php echo $departement['id']; ?> = {
<?php
    if ($departement['parent_id'] != 0) {
?>
        parent: tree<?php echo $departement['parent_id']; ?>,
<?php
    }
?>
        text: {
            name: "<?php echo $departement['name']; ?>"
        }
    };
    chart_config.push(tree<?php echo $departement['id']; ?>);
<?php
}
?>
    var my_chart = new Treant(chart_config);
});
</script>
