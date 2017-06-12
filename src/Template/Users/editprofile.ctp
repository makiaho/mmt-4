<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Change password'), ['action' => 'password']) ?></li> 
        <!-- Image upload functionality works, but there is a problem with permissions on the server. So this commented for now.-->
        <li><?php /*echo $this->Html->link(__('Upload profile photo'), ['action' => 'photo'])*/ ?></li>
    </ul>
</nav>
<div class="users form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Edit profile') ?></legend>
            <?php
            echo $this->Form->input('email');
            echo $this->Form->input('first_name');
            echo $this->Form->input('last_name');
            echo $this->Form->input('phone');
            echo $this->Form->button(__('Submit'));
            ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
