<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $metric->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $metric->id)]
            )
        ?></li>
        <?php
            $admin = $this->request->session()->read('is_admin');
            if($admin){
        ?>
            <li><?= $this->Form->postLink(
                        __('Delete admin'),
                    ['action' => 'deleteadmin', $metric->id],
                    ['confirm' => __('Are you sure you want to delete # {0}?', $metric->id)]
                )
            ?></li>
        <?php
            }
        ?> 
    </ul>
</nav>
<div class="metrics form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($metric) ?>
    <fieldset>
        <legend><?= __('Edit Metric') ?></legend>
        <?php
            echo $this->Form->input('value', array('style' => 'width: 30%;'));
			echo $this->Form->button(__('Submit'));
        ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
