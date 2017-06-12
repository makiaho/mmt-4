<nav class="large-2 medium-4 columns" id="actions-sidebar">    
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Public statistics'), ['controller' => 'Projects', 'action' => 'statistics']) ?> </li>
        <li><?= $this->Html->link(__('FAQ'), ['controller' => 'Projects', 'action' => 'faq']) ?> </li>
    </ul>    
</nav>

<div id="faq" class="projects index large-9 medium-18 columns content float: left">
    <h3><?= __('About MMT') ?></h3>

    <h4>Test environment</h4>
    <p>
    Metrics Monitoring Tool has a test environment, 
    <a href="http://mmttest.sis.uta.fi/" target="_blank">mmttest</a>.
    </p>
    <h4>Release notes</h4>
    <p>
        <h5>Version 4.0 (May 2017)</h5>
        <p>Mobile Interface</p>
        <p>Risk metric</p>
        <p>Trello and Slack Integrations</p>
        <p>Comment edit functionality</p>
        <p>Reset forgotten passwords</p>
    </p>
    <h4>Previous versions</h4>
        <p>
            Version 4.0 were implemented by Murat Pojon as a TIETS16 programming project 
            during the spring term of 2017. 
        </p>
        <p>
            Versions 2.0-2.1 and version 3.0 were implemented by Sirkku Seitamäki as a TIETS16 programming project 
            during the fall term of 2016. 
        </p>
        <p>
            Versions 1.1-1.3 were implemented during the spring term of 2016 as a coursework for 
            TIEA4 Project Work course and TIETS19 Software Project Management course. 
            The team consisted of two project managers (Elena Solovieva and Choudhary Shahzad Shabbir) 
            and two developers (Andreas Valjakka and Sirkku Seitamäki). 
        </p>   
        <p>
            Version 1.0 was the product of the fall 2015 Project Work team.  
            Jukka Ala-Fossi and Mykola Andrushchenko were the developers in the project and 
            Katriina Löytty was the manager.
        </p>    

</div>
