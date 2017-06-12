<?php 

echo $this->Highcharts->includeExtraScripts(); 

$this->assign('title','Charts');

?>

<div class="chart-header">
    <div class="chart-menu"> 
        <a href="#" class="chart-nav prev"><</a>
        <a href="#" class="chart-limit-toggle">Edit Limits</a>
        <a href="#" class="chart-nav next">></a>
    </div>
    <div class="chart-limits">
        <?= $this->Form->create() ?>
        <div>
            <div class="limit-label">Week:</div>
            <?php
                
                echo $this->Form->input('weekmin', array('type' => 'number', 'value' => $this->request->session()->read('chart_limits')['weekmin'])).'&nbsp;&nbsp;&nbsp;';
                echo $this->Form->input('weekmax', array('type' => 'number', 'value' => $this->request->session()->read('chart_limits')['weekmax']));
                
                ?>
        </div>
        <div>
            <div class="limit-label">Year:</div>
              <?php  
                echo $this->Form->input('yearmin', array('type' => 'number', 'value' => $this->request->session()->read('chart_limits')['yearmin'])).'&nbsp;&nbsp;&nbsp;';
                echo $this->Form->input('yearmax', array('type' => 'number', 'value' => $this->request->session()->read('chart_limits')['yearmax']));
                
                ?>
            </div>
                <?php
				echo $this->Form->button(__('Submit'));
			?>
        <?= $this->Form->end() ?>
    </div>
</div>
<div class="chart-gallery">
    <div class="chart selected">
        <div id="phasewrapper">
        	<?php echo $this->Highcharts->render($phaseChart, 'phasechart'); ?>
        </div>
    </div>
    
    <!-- Following two (2) charts are both about requirements, so they share a bigger header -->
    <div class="chart">
        <div id="reqwrapper">
        	<?php echo $this->Highcharts->render($reqChart, 'reqchart'); ?>
        </div>
    </div>

    <div class="chart">
        <div id="reqpercentwrapper">
        	<?php echo $this->Highcharts->render($reqPercentChart, 'reqpercentchart'); ?>
        </div>
    </div>
  

    <div class="chart">
        <div id="commitwrapper">
	        <?php echo $this->Highcharts->render($commitChart, 'commitchart'); ?>
	    </div>
	</div>

    <div class="chart">
        <div id="testcasewrapper">
	        <?php echo $this->Highcharts->render($testcaseChart, 'testcasechart'); ?>
        </div>
    </div>
    
    <div class="chart">
        <div id="hoursperweekwrapper">
            <?php echo $this->Highcharts->render($hoursPerWeekChart, 'hoursperweekchart')?>
        </div>
    </div>

    <div class="chart">
        <div id="hourswrapper">
		<?php echo $this->Highcharts->render($hoursChart, 'hourschart'); ?>
	</div>
    </div>
    
    <div class="chart">
        <div id="risksprobrapper">
            <?php echo $this->Highcharts->render($risksProbChart, 'risksProbChart')?>
        </div>
    </div>

    <div class="chart">
        <div id="risksimpactwrapper">
		<?php echo $this->Highcharts->render($risksImpactChart, 'risksImpactChart'); ?>
	</div>
    </div>

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

