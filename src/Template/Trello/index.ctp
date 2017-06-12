<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Html->link(__('What is Trello Integration?'), ['controller' => 'Trello', 'action' => 'about'],['class' => 'link-highlight']) ?> </li>
    </ul>
</nav>
<div class="workinghours form large-8 medium-16 columns content float: left">
    <?php if($trello == null): ?>
    <a class="trello-link-box" href="<?= $this->Url->build(['controller' => 'Trello', 'action' => 'add']) ?>">
        Add Trello Connection  
    </a>
    <?php else: ?>
    <a class="trello-link-box" href="<?= $this->Url->build(['controller' => 'Trello', 'action' => 'edit', $trello->id]) ?>">
        Edit Trello Connection  
    </a>
    <a class="trello-link-box" href="<?= $this->Url->build(['controller' => 'Trello', 'action' => 'links', $trello->id]) ?>">
        Configure Trello Requirements 
    </a>
    <?php endif; ?>
</div>
