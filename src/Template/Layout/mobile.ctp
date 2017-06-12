<?php
$cakeDescription = 'MMT';

$project_role = $this->request->session()->read('selected_project_role');


?>

<!DOCTYPE html>
<html>
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="theme-color" content="#05254d" />
        <title>
            <?= $cakeDescription ?>:
            <?= $this->fetch('title') ?>
        </title>
        <?=
        $this->Html->meta(
                'tktlogo.png', 'webroot/img/tkt.png', ['type' => 'icon']);
        ?>

        <?= $this->Html->css('base.css') ?>
        <?= $this->Html->css('mobile.css') ?>

        <?=
        $this->Html->script(array(
            '//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js',
            '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.7.0/underscore-min.js'
        ))
        ?>

        <?= $this->Html->script('mobile.js') ?>

        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('script') ?>
    </head>
    <body>       
        <header>
            <div class="main-bar">
                <a href="#" class="toggle">
                    <?= $this->Html->image('menu.png'); ?>
                </a>
                <div class="title">
                    MMT - <?= $this->fetch('title') ?>
                </div>
            </div>
            <nav role="navigation">
                <ul>
                    <li>
                        <?= $this->Html->link(__('Home'), ['controller' => 'Mobile', 'action' => 'index']) ?>
                    </li>
                    
                    <?php if(in_array($this->template, ['project','addhour','chart', 'report']) ){ ?>                 
                    <li>
                        <?= $this->Html->link(__('Members'), ['controller' => 'Mobile', 'action' => 'project']) ?>
                    </li>
                    <?php if($project_role === 'manager' || $project_role === 'developer') { ?> 
                    <li>
                        <?= $this->Html->link(__('Add Hours'), ['controller' => 'Mobile', 'action' => 'addhour']) ?>
                    </li>
                    <?php } ?>
                    <li>
                        <?= $this->Html->link(__('Report'), ['controller' => 'Mobile', 'action' => 'report']) ?>
                    </li>
                    <li>
                        <?= $this->Html->link(__('Charts'), ['controller' => 'Mobile', 'action' => 'chart']) ?>
                    </li>
                    <?php } ?>
                    
                    <li>
                        <?= $this->Html->link(__('Main Website'), ['controller' => 'Projects', 'action' => 'index']) ?>
                    </li>
                    
                    <li>
                        <?= $this->Html->link(__('Public Statistics'), ['controller' => 'Mobile', 'action' => 'stat']) ?>
                    </li>
                    
                    <li>
                        <?= $this->Html->link(__('Course Statistics'), 'http://www.uta.fi/sis/tie/pw/statistics.html') ?>
                    </li>
                    
                    <?php if($this->request->session()->read('Auth.User')) { ?>
                    <li>
                        <?= $this->Html->link(__('Logout'), ['controller' => 'Mobile', 'action' => 'logout']) ?>
                    </li>
                    
                    <?php } ?>
                </ul>
            </nav>
        </header>
        <div class="container">
            <?= $this->fetch('content') ?>
        </div>
        <?= $this->Flash->render() ?>
    </body>
</html>

