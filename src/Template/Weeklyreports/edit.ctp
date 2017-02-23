<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $weeklyreport->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $weeklyreport->id)]
            )
        ?></li>
    </ul>
</nav>
<div class="weeklyreports form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($weeklyreport) ?>
    <fieldset>
        <legend><?= __('Edit Weeklyreport') ?></legend>
        <?php
            echo $this->Form->input('title');
            echo $this->Form->input('week', array('style' => 'width: 35%;'));
            echo $this->Form->input('year', array('style' => 'width: 35%;'));
            echo $this->Form->input('meetings');
            echo $this->Form->input('reglink', ['label' => 'Requirements link']);
            echo $this->Form->input('problems', array('label' => 'Challenges, issues, etc.'));
            echo $this->Form->input('additional');
			echo $this->Form->button(__('Submit'));
        ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
