<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<div class="weeklyreports form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($weeklyreport) ?>
    <fieldset>
        <legend><?= __('Add Weeklyreport, Page 1/4') ?></legend>
        <?php
            $current_weeklyreport = $this->request->session()->read('current_weeklyreport');
            use Cake\I18n\Time;
            
            if(!is_null($current_weeklyreport)){
                echo $this->Form->input('title', array('value' => $current_weeklyreport['title']));
                echo $this->Form->input('week', array('value' => $current_weeklyreport['week'], 'style' => 'width: 35%;'));
                echo $this->Form->input('year', array('value' => $current_weeklyreport['year'], 'style' => 'width: 35%;'));
                echo $this->Form->input('meetings', array('value' => $current_weeklyreport['meetings']));
                echo $this->Form->input('reglink', array('value' => $current_weeklyreport['reglink'], 'label' => 'Requirements link' ));
                echo $this->Form->input('problems', array('value' => $current_weeklyreport['problems'], 'label' => 'Challenges, issues, etc.'));
                echo $this->Form->input('additional', array('value' => $current_weeklyreport['additional'], 'label' => 'Additional information'));
            }
            else{
                $now = Time::now();
                $nowWeek = date('W');
                $reportWeek = $now->weekOfYear -1;
                $currProj = $this->request->session()->read('selected_project')['project_name'];

                echo $this->Form->input('title', array('value' => $currProj.', weekly report') );
                // the week and year for the last weeklyreport of the year are not automatically filled out
                if ($nowWeek == 01) {
                    echo $this->Form->input('week', array('style' => 'width: 35%;'));
                    echo $this->Form->input('year', array('style' => 'width: 35%;'));
                }
                else {
                    echo $this->Form->input('week', array('value' => $reportWeek, 'style' => 'width: 35%;'));
                    echo $this->Form->input('year', array('value' => $now->year, 'style' => 'width: 35%;'));
                }
                echo $this->Form->input('meetings');
                echo $this->Form->input('reglink', array('label' => 'Requirements link'));
                echo $this->Form->input('problems', array('label' => 'Challenges, issues, etc.'));
                echo $this->Form->input('additional', array('label' => 'Additional information'));
            }
			echo $this->Form->button(__('Next page'));
        ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
