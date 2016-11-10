<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>
PPNI -
<?php
echo $title;
?>
    </title>

    <?= $this->Html->meta('icon') ?>
    <?php
echo $this->Html->script([
    'jquery.min',
    'moment-with-locales.min',
    'bootstrap.min',
    'sb-admin-2.min',
    'metisMenu.min'
]);
echo $this->Html->css([
    'bootstrap/bootstrap.min',
    'metisMenu/metisMenu.min',
    'sb-admin-2.min',
    'morrisjs/morris',
    'font-awesome/css/font-awesome.min'
]);
    ?>
    <?= $this->fetch('meta') ?>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div id="wrapper">
<!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
<?php
echo $this->Html->link(
    'PPNI Jatim',
    ['controller' => 'Letters', 'action' => 'index'],
    ['class' => 'navbar-brand']
);
?>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <a href="#">
                                <div>
                                    <strong>John Smith</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>John Smith</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>John Smith</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>Read All Messages</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-messages -->
                </li>
                <!-- /.dropdown -->
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-alerts">
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-comment fa-fw"></i> New Comment
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-envelope fa-fw"></i> Message Sent
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>See All Alerts</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-alerts -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
<?php
if (isset($user)) {
    echo '<li>';
    echo $this->Html->link(
        '<i class="fa fa-user fa-fw"></i>&nbsp;User Profile',
        ['controller' => 'users', 'action' => 'profile'],
        ['escape' => false]
    );
    echo '</li>';
    echo '<li class="divider"></li>';
    echo '<li>';
    echo $this->Html->link(
        '<i class="fa fa-sign-out fa-fw"></i>&nbsp;Logout',
        ['controller' => 'users', 'action' => 'logout'],
        ['escape' => false]
    );
    echo '</li>';
} else {
    echo '<li>';
    echo $this->Html->link(
        '<i class="fa fa-sign-in fa-fw"></i>&nbsp;Login',
        ['controller' => 'users', 'action' => 'login'],
        ['escape' => false]
    );
    echo '</li>';
}
?>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                    <li>
<?php
echo $this->Html->link(
    '<i class="fa fa-home fa-fw"></i> Beranda',
    ['controller' => 'letters',
    'action' => 'index'],
    ['escape' => false]
);
?>
                        </li>
                        <li>
<?php
echo $this->Html->link(
    '<i class="fa fa-envelope fa-fw"></i> Surat Masuk',
    ['controller' => 'letters',
    'action' => 'index'],
    ['escape' => false]
);
?>
                        <li>
<?php
echo $this->Html->link(
    '<i class="fa fa-sitemap fa-fw"></i> Organisasi',
    ['controller' => 'departements',
    'action' => 'index'],
    ['escape' => false]
);
?>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <!-- Page Content -->
        <div id="page-wrapper">
            <div style="height: 5px;"></div>
            <!-- Breadcrumbs -->
            <div class="row">
                <div class="col-lg-12">
                    <ol class="breadcrumb">
                        <li>
<?php
echo $this->Html->link('Beranda', [
    'controller' => 'letters',
    'action' => 'index'
]);
?>
</li>
<?php
if (isset($breadcrumbs))
{
    foreach($breadcrumbs as $key=>$value)
    {
        if (intval($key) == intval(count($breadcrumbs) - 1))
        {
            echo '<li class="active">';
            echo $value[1];
        } else {
            echo '<li>';
            echo $this->Html->link($value[1], $value[0]);
        }
        echo '</li>';
    }
}
?>
                        </ol>
                    </div>
                </div>
                <!-- /.breadcrumbs -->
                <!-- alert -->
                <div class="row">
<?php
if (isset($isError))
{
?>
<div class="col-lg-12">
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo $this->Flash->render(); ?>
    </div>
</div>
<?php
}
?>
                </div>
                <!-- /.alert -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
<?php
echo $title;
if (isset($isShowAddButton))
{
    echo $this->Html->link(
        '<i class="fa fa-edit fa-fw"></i>',
        ['controller' => $this->name, 'action' => 'add'],
        ['escape' => false]
    );
}
if (isset($isShowEditButton))
{
    echo $this->Html->link(
        '<i class="fa fa-pencil fa-fw"></i>',
        ['controller' => $this->name, 'action' => 'edit', $controllerObjectId],
        ['escape' => false]
    );
    echo $this->Html->link(
        '<i class="fa fa-trash fa-fw"></i>',
        ['controller' => $this->name, 'action' => 'delete', $controllerObjectId],
        ['escape' => false, 'confirm' => 'Ingin Menghapus?']
    );

}
?>
                        </h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-12">
                        <?= $this->fetch('content') ?>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
    </body>
</html>
