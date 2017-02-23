<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<div class="metrictypes form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($metrictype) ?>
    <fieldset>
        <legend><?= __('Add Metrictype') ?></legend>
        <?php
            echo $this->Form->input('description');
			echo $this->Form->button(__('Submit'));
        ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
