<div class="col-lg-12">
    <div class="row">
        <div id="chart-container" class="container-fluid"></div>
    </div>
</div>
<?php
echo $this->Html->script(['jquery.orgchart']);
echo $this->Html->css(['jquery.orgchart']);
?>
<script>
var children = [];
<?php
foreach ($departements as $departement) {
?>
    var datum = {
    'name': "<?php echo $departement['name']; ?>",
    'title': "<?php echo $departement['name']; ?>",
<?php
    if (isset($departement->children)) {
?>
    'children': [
<?php
        foreach ($departement->children as $child) {
?>
                    {
                        'name': "<?php echo $child->name; ?>",
                        'title': "<?php echo $child->name; ?>",
<?php
            if (count($child->children) > 0) {
?>
               'children': [
<?php
                foreach ($child->children as $grandChild) {
?>
                    {
                        'name': "<?php echo $grandChild->name; ?>",
                        'title': "<?php echo $grandChild->name; ?>",
<?php
                    if (count($grandChild->children) > 0) {
?>
                        'children': [
<?php
                        foreach ($grandChild->children as $greatGrandChild) {
?>
                            {
                                'name': "<?php echo $greatGrandChild->name; ?>",
                                'title': "<?php echo $greatGrandChild->name; ?>"
                            },
<?php
                        }//great grand child
?>
                        ]
<?php
                    }
?>
                    },
<?php
                }//foreach grand child
?>
                ]
<?php
            }//if grand child isset
?>
                },
<?php
        }//child
?>
    ]
<?php
    }//children
?>
    };
    children.push(datum);
<?php
}
?>
var datasource = {
    'name': "PPNI Jawa Timur",
    'title': "PPNI Jawa Timur",
    'children': children
};

$('#ul-data').hide();
$('#chart-container').orgchart({
    'data': datasource,
    'direction' : 'l2r',
    'nodeContent': 'title'
});
</script>
