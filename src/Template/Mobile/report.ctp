<?php 
$this->assign('title','Report');

?>

<?php if ($report != null): ?>

<div class="report-box">
    <div class="title">General</div>
    <div class="report-item">
        <div class="item-label"><?= __('Title') ?></div>
        <div class="item-desc"><?= h($report->title) ?></div>
    </div>
    <div class="report-item">
        <div class="item-label"><?= __('Week') ?></div>
        <div class="item-desc"><?= h($report->week) ?></div>
    </div>
    <div class="report-item">
        <div class="item-label"><?= __('Year') ?></div>
        <div class="item-desc"><?= h($report->year) ?></div>
    </div>
    <div class="report-item">
        <div class="item-label"><?= __('Meetings') ?></div>
        <div class="item-desc"><?= h($report->meetings) ?></div>
    </div>
    <div class="report-item">
        <div class="item-label"><?= __('Requirements link') ?></div>
        <div class="item-desc"><?= h($report->reglink) ?></div>
    </div>
    <div class="report-item">
        <div class="item-label"><?= __('Challenges, issues, etc.') ?></div>
        <div class="item-desc"><?= h($report->problems) ?></div>
    </div>
    <div class="report-item">
        <div class="item-label"><?= __('Additional') ?></div>
        <div class="item-desc"><?= h($report->additional) ?></div>
    </div>
    <div class="report-item">
        <div class="item-label"><?= __('Created on') ?></div>
        <div class="item-desc"><?= h($report->created_on->format('d.m.Y')) ?></div>
    </div>
    <div class="report-item">
        <div class="item-label"><?= __('Updated on') ?></div>
        <div class="item-desc"><?php 
		if ( $report->updated_on != NULL ) {
			echo h($report->updated_on->format('d.m.Y'));
		} ?></div>
    </div>
    
   
     
      
    
    
</div>
<div class="report-box">
    <div class="title">Working Hours</div>
<?php foreach ($members as $member): ?>
    <div class="report-item">
        <div class="item-label"><?= $member->name ?></div>
        <div class="item-desc">
            <?= $member->role ?>
            <?php
   
    if($member->hours == 0){
        echo "";
    }else{
        echo ' - '.$member->hours.' '.($member->hours > 1 ? 'hours' : 'hour');
    }
            
    ?>
        </div>

    
    </div>
<?php endforeach; ?>
</div>
<div class="report-box">
    <div class="title">Metrics</div>
    <?php foreach ($report->metrics as $metrics): ?>
        <div class="report-item">
            <div class="item-label"><?= h($metrics->metric_description) ?></div>
            <div class="item-desc"><?= h($metrics->value) ?></div>
        </div>
    <?php endforeach; ?>
</div>
<div class="report-box">
    <div class="title">Risks</div>
    <?php foreach ($risks as $risk): ?>
        <div class="report-item">
            <div class="item-label"><?= h($risk->description) ?></div>
            <div class="item-desc"><?= 'Impact: '.h($risk->impact).', Probability: '.h($risk->probability) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<?php else: ?>

<h3>This project doesn't have any reports!</h3>

<?php endif; ?>

