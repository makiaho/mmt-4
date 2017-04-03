<?php if (empty($this->request->session()->read('Auth.User'))){ 
    
    $this->assign('title','Login');
    
    ?>
    <div class="login-form">
        <?= $this->Form->create() ?>
        <?= $this->Form->input('email') ?>
        <?= $this->Form->input('password') ?>
        <?= $this->Form->button('Login') ?>
        <?= $this->Form->end() ?>
    </div>
<?php } else { 
    
    $this->assign('title','My Projects');
    
    ?>
<div class="link-item-list">
    <?php foreach ($myProjects as $item): ?>
        <a class="item" href="<?= $this->Url->build(['controller' => 'Mobile', 'action' => 'project', $item->id]) ?>">
            <h2>
                <?= $item->project_name ?>
            </h2>
            <div>
                <?= $item->description ?>
            </div>
        </a>
    <?php endforeach; ?>
</div>
<?php } ?>


