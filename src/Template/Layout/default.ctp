<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = 'MMT';

//debug prints
//print_r($this->request->session()->read('selected_project_role'));
?>

<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta(
			'tktlogo.png',
			'webroot/img/tkt.png',
			['type' => 'icon'] ); ?>

    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('cake.css') ?>

    <?= $this->Html->script(array(
        '//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.7.0/underscore-min.js'
    )) ?>
    
    <?= $this->Html->script('main.js') ?>
    
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body <?php if(\Cake\Core\Configure::read('debug')){echo 'class="test-layout"';} ?> >
<?php
	$admin = $this->request->session()->read('is_admin');
	$supervisor = ( $this->request->session()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
	
	// also determine supervisor role by skimming member table. If role supervisor is even once, you'll be set as a supervisor
	$supervisorquery = \Cake\ORM\TableRegistry::get('Members')->find()
						->select(['project_role'])
						->where(['user_id =' => $this->request->session()->read('Auth.User.id'), 'project_role =' => 'supervisor'])
						->first();
	if ( !empty($supervisorquery) ) {
		// set role as a supervisor; the consequence is that 
		// a) comment notifications are visible right after login and b) public statistics links are directly accessible
		$this->request->session()->write('selected_project_role', 'supervisor');
		$supervisor = 1;
	}
        
?>
<div id="area51">
	<!-- This area is meant for notifications about new messages -->
	<?php
	/* Check notifications table; 
	 * if user's member ID is linked to any comment of current project, show a link
	 * admins don't get any notifications
	 */
	// execute only if a project is chosen OR if you are a supervisor
	if ( $this->request->session()->read('selected_project')['id'] || $supervisor ) {
		$userid = $this->request->session()->read('Auth.User.id');
		$projid = $this->request->session()->read('selected_project')['id'];
		
		// non-supervisor users only get notifications from their own project after choosing it
		if ( !($supervisor) ) {
			$memid = Cake\ORM\TableRegistry::get('Members')->find()
						->select(['id'])
						->where(['user_id =' => $userid, 'project_id =' => $projid])
						->toArray();

		// else (as SV) you get informed about every new comment of every project
		} else {
			$memid = Cake\ORM\TableRegistry::get('Members')->find()
						->select(['id'])
						->where(['user_id =' => $userid])
						->toArray();
		}
		
		

		// proceed only if ID's were found
		if ( sizeof($memid) > 0) {
			// now try to find current member's id from notifications
			$notifquery = array();
			
			//non-supervisors get only one member id, so one query
			if ( !($supervisor) ) {
			$notifquery = Cake\ORM\TableRegistry::get('Notifications')->find()
						->select(['comment_id', 'weeklyreport_id'])
						->distinct(['weeklyreport_id'])
						->where(['member_id =' => $memid[0]->id])
						->toArray();
			
			// supervisors need looping to get notifications with all their member ID's
			} else {
				$notifquery = Cake\ORM\TableRegistry::get('Notifications')->find()
							->select(['comment_id', 'weeklyreport_id'])
							->distinct(['weeklyreport_id']);
				
				// this part fetches all the rows by putting OR condition between all member ID's
				for ($i=0; $i<sizeof($memid); $i++) {
					$notifquery = $notifquery->orWhere(['member_id =' => $memid[$i]->id]);
				}
				$notifquery = $notifquery->toArray();
			}

			// if there are any notifications, tell user
			if ( $amount = sizeof($notifquery) > 0 ) { ?>
				<div id="notificationarea">
					<!-- this button displays a letter icon -->
					<button><span id="label"><?= $this->Html->image('letter.png'); ?></span></button>
					<div class="n-content"><div id="cominfo">
					<?= "Unread comments in:" ?><br/>
					<ul>
						<?php
						
						foreach($notifquery as $notif) {
							// fetch reports' week numbers
							$week = Cake\ORM\TableRegistry::get('Weeklyreports')->find()
										->select(['week', 'project_id'])
										->where(['id =' => $notif->weeklyreport_id])
										->toArray();
							$weekno = $week[0]->week;
							// supervisors also need project's name
							if ( $supervisor ) {
								$projname = \Cake\ORM\TableRegistry::get('Projects')->find()
											->select(['project_name'])
											->where(['id =' => $week[0]->project_id])
											->toArray();
								$projname = $projname[0]->project_name;
							}
							if ( $supervisor ) {
								echo "<li>" . $this->Html->link($projname. " report, week " . $weekno, ['controller'=>'Weeklyreports', 'action'=>'view', $notif->weeklyreport_id]) . "</li>";
							} else {
								echo "<li>" . $this->Html->link("Report, week " . $weekno, ['controller'=>'Weeklyreports', 'action'=>'view', $notif->weeklyreport_id]) . "</li>";
							}
						}

				// close a million tags
				echo "</ul></div></div></div>";
			}
		}
	}
	?>
	
	<div class="dropdown">
		<button title="Click to open">
			<?php
				// different label text depending on if logged in or not
				if ( empty($this->request->session()->read('Auth.User')) ) {
					echo '<span id="label">Log in</span>';
				} else {
					echo '<span id="label">Options</span>';
				}
			?>
			<?= $this->Html->image('arrow.png'); ?>
		</button>
		<div class="dd-content">
			<?= 
                            //$this->Html->image('icon.png');
                            $this->Custom->profileImage($this->request->session()->read('Auth.User')['id']);
                        ?>
			<div id="userinfo">
				<div class="info">Logged in as</div>
				<?php
				/* Displays user information to every page
				   Requirement ID: 23
				   - Andy
				*/
				// checks if logged in
				if ( empty($this->request->session()->read('Auth.User')) ){
					print_r('Currently not logged in');
				?>
				<ul>
                                    <li class="buttonIn"><?= $this->Html->link(__('Log in'), ['controller' => 'Users', 'action' => 'login']) ?></li>					
                                    <li class="buttonTop"><?= $this->Html->link(__('Sign up'), ['controller' => 'Users', 'action' => 'signup']) ?></li>
				</ul>
                                <br>
                                <?= $this->Html->link(__('Forgot Password'), ['controller' => 'Users', 'action' => 'forgotpassword']) ?>
				<?php

				} else {
					// prints user's full name
					print_r( ($this->request->session()->read('Auth.User.first_name')).' '.($this->request->session()->read('Auth.User.last_name')) );
					// checks if user has accessed any projects
					if ($this->request->session()->check('selected_project') ) {

						// fetch the name of current project
						$selected_project = $this->request->session()->read('selected_project');
						$name = $selected_project['project_name'];

						echo "<div class=\"info\">On project</div>";

						// the text part
						if ($selected_project)
							print_r($name);
						else
							print_r('none');
						/* FIX 10.3.2016: now being a non-member is also shown
						   Requirement ID: 23
						   - Andy
						*/
						// display current role. Non-member status is also displayed
						echo "<div class=\"info\">Project role</div>";
						if (($this->request->session()->read('selected_project_role')) != 'notmember' ) {
							print_r(($this->request->session()->read('selected_project_role')) );
						} else {
							print_r('not a member');
						}
					}
				?>
				<ul>
                                    <li class="buttonTop"><?= $this->Html->link(__('Change password'), ['controller' => 'Users', 'action' => 'password']) ?></li>
                                    <li class="buttonTop"><?= $this->Html->link(__('Edit profile'), ['controller' => 'Users', 'action' => 'editprofile']) ?></li>
                                    <li class="buttonOut"><?= $this->Html->link(__('Log out'), ['controller' => 'Users', 'action' => 'logout']) ?></li>
                                </ul>
				<?php } ?>
			</div>
		</div>
	</div>
	<!-- this non-breaking space is empty content that makes the page render correctly -->
	&nbsp;
	<!--<div id="topimg">
		<?= $this->Html->image('ylapalkki.jpg'); ?>
	</div>-->
	<div id="topimg">
		<?= $this->Html->image('pitkalogo1.png'); ?>
		<div class="logo">METRICS</div>
		<div class="logo">MONITORING</div>
		<div class="logo">TOOL</div>
	</div>
	<!-- Left side (displays current location)-->
	<nav id="left-title">
		<ul>
			<li class="title-area">
				<h1><a href=""><?= $this->fetch('title') ?></a></h1>
			</li>
		</ul>
	</nav> 
	
	<!-- top navigation bar with every other button -->
	<nav id="navtop" role="navigation" data-topbar>
	    	<ul>
                    <li class="navbutton"><?= $this->Html->link(__('Home'), ['controller' => 'Projects', 'action' => 'index']) ?></li>	
	            <?php // Link to public statistics (only for admins and supervisors)
                    if ($admin || $supervisor) { ?>
                        <li class="navbutton"><?= $this->Html->link(__('Statistics'), ['controller' => 'Projects', 'action' => 'statistics']) ?></li>
                    <?php }
			// logged in with a project selected
			if( $this->request->session()->check('selected_project') ) { ?>
                            <li class="navbutton"><?= $this->Html->link(__('Project'), ['controller' => 'Projects', 'action' => 'view', $this->request->session()->read('selected_project')['id']]) ?></li>
                            <?php // if not a member, particular links are not shown 
                            if ( $this->request->session()->read('selected_project_role') != 'notmember' ) { ?>
                                <li class="navbutton"><?= $this->Html->link(__('Members'), ['controller' => 'Members', 'action' => 'index']) ?></li>
                                <li class="navbutton"><?= $this->Html->link(__('Reports'), ['controller' => 'Weeklyreports', 'action' => 'index']) ?></li>
                                <li class="navbutton"><?= $this->Html->link(__('Log time'), ['controller' => 'Workinghours', 'action' => 'index']) ?></li>
                                <li class="navbutton"><?= $this->Html->link(__('Risks'), ['controller' => 'Risks', 'action' => 'index']) ?></li>
                            <?php } ?>	
                            <?php if( in_array($this->request->session()->read('selected_project_role'),['manager','admin','supervisor'])): ?>
                                <li class="navbutton"><?= $this->Html->link(__('Slack'), ['controller' => 'Slack', 'action' => 'index']) ?></li>
                                <li class="navbutton"><?= $this->Html->link(__('Trello'), ['controller' => 'Trello', 'action' => 'index']) ?></li>
                            <?php endif; ?>
                            <li class="navbutton"><?= $this->Html->link(__('Charts'), ['controller' => 'Charts', 'action' => 'index']) ?></li>
                            <?php  
                        } ?>
	        </ul> <!-- end -->
		<div class="clearer"></div>
	</nav>

    <?= $this->Flash->render() ?>
    <section class="container clearfix">
        <?= $this->fetch('content') ?>
    </section>

</div>
<!--<hr/>-->
<footer>
	<!-- "reverse background" for footer -->
	<div id="area52">
		<div class="footerblock">
			<div id="logoblock">
                                <?= $this->Html->image('ylapalkki.jpg'); ?>
				<!--<?= $this->Html->image('pitkalogo1.png'); ?>
				<div class="footerlogo">METRICS</div>
				<div class="footerlogo">MONITORING</div>
				<div class="footerlogo">TOOL</div>-->
			</div>
		</div>
		<div class="footerblock">
			<h6>PUBLIC PAGES</h6>
			<ul>
                            <li><?= $this->Html->link(__('Home'), ['controller' => 'Projects', 'action' => 'index']) ?></li>      
                            <li><?= $this->Html->link(__('About MMT'), ['controller' => 'Projects', 'action' => 'about']) ?> </li>
                            <li><?= $this->Html->link(__('Public statistics'), ['controller' => 'Projects', 'action' => 'statistics']) ?> </li>
                            <li><?= $this->Html->link(__('FAQ'), ['controller' => 'Projects', 'action' => 'faq']) ?> </li>                   
			</ul>
		</div>
		<div class="footerblock">
			<h6>OTHER RESOURCES</h6>
			<ul>
                            <li><a href="http://www.uta.fi/sis/tie/pw/statistics.html" target="_blank">Project Work course - Statistics</a></li>
                            <li><a href="http://www.uta.fi/sis/yhteystiedot/henkilokunta/pekkamakiaho.html" target="_blank">Supervisor's web site</a></li>
                            <li><?= $this->Html->link(__('Mobile Website'), ['controller' => 'Mobile', 'action' => 'index']) ?> </li>                   
			</ul>


		</div>
	</div>
</footer>
</body>
</html>
