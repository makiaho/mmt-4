<nav class="large-2 medium-4 columns" id="actions-sidebar">    
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Public statistics'), ['controller' => 'Projects', 'action' => 'statistics']) ?> </li>
        <li><?= $this->Html->link(__('About MMT'), ['controller' => 'Projects', 'action' => 'about']) ?> </li>
    </ul>    
</nav>

<div id="faq" class="projects index large-9 medium-18 columns content float: left">
    <h3><?= __('FAQ') ?></h3>
    <ol start="0">
      <li style="list-style-type:none;"><a accesskey="alt+0 "href="#Q0">Video tutorials</a></li>
      <li><a accesskey="alt+1 "href="#Q1">For students of PW and SPM courses in spring 2017</a></li>
      <li><a accesskey="alt+2" href="#Q2">What can I use the Metrics Monitoring Tool (MMT) for?</a></li>
      <li><a accesskey="alt+3" href="#Q3">How do I get started?</a></li>
      <li><a accesskey="alt+4" href="#Q4">I forgot my password. How do I get a new one?</a></li>
      <li><a accesskey="alt+5" href="#Q5">How can I change my password?</a></li>
      <li><a accesskey="alt+6" href="#Q6">As a project manager, how do I do the weekly reporting of my project?</a></li>
      <li><a accesskey="alt+7" href="#Q7">How do I log my daily working time?</a></li>
      <li><a accesskey="alt+8" href="#Q8">Where can I find team's/member's logged working time and the total number of working hours?</a></li>
      <li><a accesskey="alt+9" href="#Q9">How can I view the progress of my project?</a></li>
      <li><a href="#Q10">How can I view the progress of other projects?</a></li>
    </ol>
    
    <h4 id="Q0">Video tutorials</h4>
    <p>
       <ul>
            <li>Developer tutorial shows how to log time and view the progress of the project.</li>
            <li>Manager tutorial shows how to add members and fill in the weekly report form.</li>
       </ul>
        <p>
            <iframe width="560" height="315" src="https://www.youtube.com/embed/RE5sO55sDyk" frameborder="0" allowfullscreen></iframe>
            <iframe width="560" height="315" src="https://www.youtube.com/embed/9Qx3TuLZZc8" frameborder="0" allowfullscreen></iframe>
         </p>
        <p><a href="#">[back to the top]</a></p>
    </p>
    <h4 id="Q1">1. For students of PW and SPM courses in spring 2017</h4>
    <p>
    For developers and managers:
          <ul>
            <li>Unless you created your user account yourself, change the password during your first log in.</li>
            <li>The first weekly report is written for week XX.</li>
            <li>However, remember to log your working hours for weeks XX - XX.</li>
            <li>The working hours that are in the table on Members' page include all working hours, even the ones that are not covered by a weekly report.</li>   
          </ul>
    For managers:
      	<ul>    
            <li>The first week you'll need write a weekly report for is week XX.</li>
            <li>Submit the first weekly report on Monday, XXth January 2017</li>
            <li>For all teams, the phase will be 0 in the first weekly report.</li>
        </ul>
    In the current version of MMT:
        <ul>
            <li>Logging time is limited between the day the project was created and the current day.</li> 
            <li>This means that working hours that are listed in the weekly report change automatically, 
                if a member adds, edits or deletes hours.</li>
            <li>The automatic title in the weekly report is:</br> "Project Name, weekly report".</li>
        
        </ul>

        <a href="#">[back to the top]</a> 
    </p>
	
    <h4 id="Q2">2. What can I use the Metrics Monitoring Tool (MMT) for?</h4>
    <p>
    All project members (incl. clients) can:
        <ul>
            <li>view the progress of your project</li>
            <li>view the progress of other public projects</li>
            <li>leave comments on the weekly reports of your project</li>
            <li>give feedback on MMT</li>
            <ul>
                <li>log in and select your project</li>
                <li>click the "Give feedback" link on left hand side</li>
            </ul>
        </ul>
    As a project manager you can:
      	<ul >
           <li>add members to your project team</li>
	   <li>log your daily working time</li>
	   <li>do the weekly reporting of your project</li> 
        </ul>
        As a developer you can:
          <ul >
             <li>log your daily working time</li>
          </ul>       
    <a href="#">[back to the top]</a> 
    </p>

    <h4 id="Q3">3. How do I get started?</h4>
    <p>
      As a project manager:
      	<ul >
           <li>create a user ID for yourself by signing up in MMT</li>
	   <li>contact your course supervisor, provide him/her with your user ID and ask him/her to create your project in MMT with you as a project manager</li>
	   <li>add your team members as developers to your project - and you are good to go!</li>
        </ul>
      As a developer:
      	<ul >
           <li>create a user ID for yourself by signing up in MMT</li>
	   <li>contact your project manager, provide him/her with your user ID and ask him/her to add you as a developer to your project</li>

        </ul>      
      <a href="#">[back to the top]</a>
    </p>

    <h4 id="Q4">4. I forgot my password. How do I get a new one?</h4>
    <p>
        <ul>
            <li>Contact the MMT administrator Pekka Mäkiaho at pekka.makiaho(at)uta.fi, who will send you a new password.</li>
       </ul>
       <a href="#">[back to the top]</a>
    </p>

    <h4 id="Q5">5. How can I change my password or edit my profile?</h4>
    <p>
        <ul>
            <li>You can change your password and edit your profile by clicking the "Profile" link at the top right corner of MMT.</li> 
            <li>"Change password" link is on the left-side menu of "Edit profile" page.</li> 
            <li>Make the desired changes and click "Submit".</li>
        </ul>    
      <a href="#">[back to the top]</a>
    </p>

    <h4 id="Q6">6. As a project manager, how do I do the weekly reporting of my project?</h4>
    <p>
        A weekly report covers a calendar week. 
        A report should be send on Monday that follows the weekly report week. 
    </p>
    <p>
    You can enter the weekly project reports into MMT as follows:
      	<ul>
           <li>log in and select the project you want to enter a weekly report for</li>
	   <li>click the "Weeklyreports" tab at the top of the screen</li>
	   <li>click the "New Weeklyreport" link on the left hand side</li>
	   <li>weekly report form consists of
                <ul>
                    <li>page 1: enter the basic data</li>
                    <li>page 2: enter the metric data</li>
                    <li>page 3: preview of working hours of your team members</li>
                </ul>
           </li>
	   <li>click "Submit" once you are done, and your weekly report is saved</li>
        </ul> 
      <a href="#">[back to the top]</a>
    </p>

    <h4 id="Q7">7. How do I log my daily working time?</h4>
    <p>
        <!--Working hours can be logged starting from Monday 
        that follows the week that is covered by last weekly report.--> 
        You can log your daily working time in MMT as follows:
      	<ul >
           <li>log in and select the project you want to log time for</li>
	   <li>click the "Log time" tab at the top of the screen</li>
	   <li>click the "Log time" link on the left hand side</li>
	   <li>enter the date, description, duration (hours) and work type for the time to be logged</li>
	   <li>click "Submit" once you are done, and your logged task is saved</li>
         </ul>
     <!--Please note that you can log time late, but only for the week of the last weekly report.
        <ul>
            <li>click the "Reports" tab at the top of the screen</li>
            <li>click the "View" link next to the last weekly report</li>
            <li>click the "Log time late" link on the left hand side menu</li>
        </ul>-->
      <a href="#">[back to the top]</a>
     </p>

    <h4 id="Q8">8. Where can I find team's/member's logged working time and the total number of working hours?</h4>
       <p>
           You can view the total numbers of working hours as follows 
            <ul>
                <li>log in and select your project</li>
                <li>to view the total numbers of working hours per team and per member:</li> 
                    <ul>
                        <li>click the "Members" tab at the top menu</li>
                    </ul>
                <li>to view the total numbers of member's working hours by worktype:</li>
                    <ul>
                        <li>click the "Members" tab at the top menu</li> 
                        <li>click member's name on the list</li>
                    </ul>
            </ul>
        </p>
        <p> 
            You can view the logged working time as follows
            <ul>
                <li>log in and select your project</li>
                <li>to view team's logged tasks:</li> 
                    <ul>
                        <li>click the "Log time" tab at the top menu</li>
                    </ul>
                
                <li>if a member has logged workinghours, you can view his/her logged tasks:
                    <ul>
                        <li>click the "Log time" tab at the top menu,</li>
                        <li>click member's name on the list</li>
                    </ul>
                <li>or alternatively</li>
                    <ul>
                        <li>click the "Members" tab at the top menu</li>
                        <li>click member's name on the list</li> 
                        <li>click the "Logged tasks" link on the left-hand side</li>
                    </ul>
            </ul>    
        <a href="#">[back to the top]</a>
       </p>

    <h4 id="Q9">9. How can I view the progress of my project?</h4>
    <p>
      You can view the progress of your project as follows:
	<ul >
           <li>log in and select the project whose progress you want to view</li>
	   <li>click the "Charts" tab at the top of the screen</li>
	   <li>you now see the progression of the selected project in charts, based on the provided weekly report data</li>
	   <li>you can change the viewing period by amending the min and max weeks and years in the Edit limits section on the left hand side</li>
	</ul> 
      <a href="#">[back to the top]</a>
    </p>

    <h4 id="Q10">10. How can I view the progress of other projects?</h4>
    <p>
      You can view the progress of other projects similarly as your own project in <a accesskey="alt+9" href="#Q9"> How can I view the progress of my project?</a>, 
	if the project is classified as "public" in the project's basic data by the administrator. 
      <br />
      Additionally, you can view the combined statistics of all public projects by clicking the "Public statistics"
      link in the footer.
      <br />
      <br />
      <a href="#">[back to the top]</a>
    </p>
</div>
