<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <?php
            $admin = $this->request->session()->read('is_admin');
            if($admin){
        ?>
            <li><?= $this->Form->postLink(
                    __('Delete'),
                    ['action' => 'delete', $weeklyhour->id],
                    ['confirm' => __('Are you sure you want to delete # {0}?', $weeklyhour->id)]
                )
            ?></li>
        <?php
            }
        ?> 
    </ul>
</nav>
<div class="weeklyhours form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($weeklyhour) ?>
    <fieldset>
        <legend><?= __('Edit Weeklyhour') ?></legend>
        <?php
             /* Req 10: changing the ID's of entities to their textual names 
             * updated: WorkingHours.addev.ctp, WorkingHoursController.php, User.php, 
             * Weeklyhours.edit.ctp, WeeklyHoursController.php*/
            //echo $this->Form->input('member_id', ['options' => $members, 'label' => 'Member Name']);
            echo $this->Form->input('duration', array('style' => 'width: 33%;'));
			echo $this->Form->button(__('Submit'));
        ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
