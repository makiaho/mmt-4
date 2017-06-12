<?php echo $this->Highcharts->includeExtraScripts(); ?>

<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Edit limits') ?></li> 
        <?= $this->Form->create() ?>
        <fieldset>
            <?php
                echo $this->Form->input('weekmin', array('type' => 'number', 'value' => $this->request->session()->read('chart_limits')['weekmin']));
                echo $this->Form->input('weekmax', array('type' => 'number', 'value' => $this->request->session()->read('chart_limits')['weekmax']));
                echo $this->Form->input('yearmin', array('type' => 'number', 'value' => $this->request->session()->read('chart_limits')['yearmin']));
                echo $this->Form->input('yearmax', array('type' => 'number', 'value' => $this->request->session()->read('chart_limits')['yearmax']));
				echo $this->Form->button(__('Submit'));
			?>
        </fieldset>
        <?= $this->Form->end() ?>
    </ul>
</nav>
<div class="metrics index large-9 medium-8 columns content float: left">

	<!-- 12.3.2016: code cleanup for displaying the charts properly
	     Requirement ID: 7 (Andy)
	-->
    <div class="chart">
        <h4>Phase Chart</h4>
        <div id="phasewrapper">
        	<?php echo $this->Highcharts->render($phaseChart, 'phasechart'); ?>
        </div>
    </div>
    
    <!-- Following two (2) charts are both about requirements, so they share a bigger header -->
    <div class="chart">
        <h4>Requirement Charts</h4>
        <!--<h5>Amounts in numbers</h5>-->
        <div id="reqwrapper">
        	<?php echo $this->Highcharts->render($reqChart, 'reqchart'); ?>
        </div>
    </div>
    </br>
    <div class="chart">
        <!--<h5>Amounts in percentages</h5>-->
        <div id="reqpercentwrapper">
        	<?php echo $this->Highcharts->render($reqPercentChart, 'reqpercentchart'); ?>
        </div>
    </div>
  

    <div class="chart">
        <h4>Commit Chart</h4>
        <div id="commitwrapper">
	        <?php echo $this->Highcharts->render($commitChart, 'commitchart'); ?>
	    </div>
	</div>

    <div class="chart">
        <h4>Test Case Chart</h4>
        <div id="testcasewrapper">
	        <?php echo $this->Highcharts->render($testcaseChart, 'testcasechart'); ?>
        </div>
    </div>
    <!--
    <div class="chart">
        <h4>Weeklyhour Chart</h4>
        <h5>This will be removed soon.</h5>
        <div id="weeklyhourwrapper">
	        <?php echo $this->Highcharts->render($weeklyhourChart, 'weeklyhourchart'); ?>
		</div>
    </div>-->
    
    <div class="chart">
        <h4>Working hours</h4>
        <div id="hoursperweekwrapper">
            <?php echo $this->Highcharts->render($hoursPerWeekChart, 'hoursperweekchart')?>
        </div>
    </div>
    </br>
    <div class="chart">
        <div id="hourswrapper">
		<?php echo $this->Highcharts->render($hoursChart, 'hourschart'); ?>
	</div>
    </div>
    
    <div class="chart">
        <h4>Risks</h4>
        <div id="risksprobrapper">
            <?php echo $this->Highcharts->render($risksProbChart, 'risksProbChart')?>
        </div>
    </div>
    </br>
    <div class="chart">
        <div id="risksimpactwrapper">
		<?php echo $this->Highcharts->render($risksImpactChart, 'risksImpactChart'); ?>
	</div>
    </div>
    </br>
    <div class="chart">
        <div id="riskscombinedwrapper">
		<?php echo $this->Highcharts->render($risksCombinedChart, 'risksCombinedChart'); ?>
	</div>
    </div>
    
    <?php 
    // The chart for derived metrics is visible only to admins
    $admin = $this->request->session()->read('is_admin');
    if ($admin) { ?>  
        <div class="chart">
            <h4>Derived Metrics Chart</h4>
            <div id="derivedwrapper">
                <?php echo $this->Highcharts->render($derivedChart, 'testcasechart'); ?>
            </div>
        </div>
    <?php } ?>
	
</div>
