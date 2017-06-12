<?= $this->Html->script('trello.js') ?>
<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Html->link(__('What is Trello Integration?'), ['controller' => 'Trello', 'action' => 'about'],['class' => 'link-highlight']) ?> </li>
    </ul>
</nav>
<div class="workinghours form large-8 medium-16 columns content float: left">
    <div id="trello-data" data-board-id="<?= $trello->board_id ?>" data-app-key="<?= $trello->app_key ?>" data-token="<?= $trello->token ?>"></div>
    <div id="saved-links">
        <?php foreach ($trello->trellolinks as $link): ?>
            <div class="saved-link" data-req="<?= $link->requirement_type ?>" data-list="<?= $link->list_id ?>"></div>
        <?php endforeach; ?>
    </div>
    <div class="trello-form loading">
        <div class="form-title">
            Trello Requirements Configuration
        </div>
        <div class="form-message">

        </div>
        <form method="post">
            <input type="hidden" name="trello-id" value='<?= $trello->id ?>'>
            
            <div class="trello-req select-list current-select">
                <div class="title">
                    Requirements<span class="select-desc"> - Select a requirement</span>
                </div>
                <?php foreach ($metricTypes as $metric): ?>
                    <div class="selectable req" data-id="<?= $metric->description ?>">
                        <?= $metricNames[$metric->id] ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="trello-lists select-list">
                <div class="title">
                    Trello Lists<span class="select-desc"> - Select a trello list</span>
                </div>
            </div>
            <div class="trello-matches select-list">
                <div class="title">
                    Matches
                </div>
            </div>
            <div class="form-submit top-space wide">
                <input type="submit" value="Save" class="save" />               
            </div>
        </form>
    </div>

</div>
