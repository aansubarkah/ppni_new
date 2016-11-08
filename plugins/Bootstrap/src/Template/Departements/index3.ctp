<div class="col-lg-12">
    <div class="row">
        <div id="chart-container" class="container-fluid"></div>
        <ul id="ul-data">
<?php
/*foreach ($departements as $departement) {
    echo '<li>';
    echo $departement->name;
    if (isset($departement->children)) {
        echo '<ul>';
        foreach ($departement->children as $child) {
            echo '<li>';
            echo $child->name;
            if (isset($child->children)) {
                echo '<ul>';
                foreach ($child->children as $grandChild) {
                    echo '<li>';
                    echo $grandChild->name;
                    if (isset($grandChild->children)) {
                        echo '<ul>';
                        foreach ($grandChild->children as $grandGrandChild) {
                            echo '<li>';
                            echo $grandGrandChild->name;
                            echo '</li>';
                        }
                        echo '</ul>';
                    }
                    echo '</li>';
                }
                echo '</ul>';
            }
            echo '</li>';
        }
        echo '</ul>';
    }
    echo '</li>';
}*/
?>
        </ul>
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
            //print_r($child);
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
    'direction' : 'l2r'
});
</script>
