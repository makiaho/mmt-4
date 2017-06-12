<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Html->link(__('What is Trello Integration?'), ['controller' => 'Trello', 'action' => 'about'],['class' => 'link-highlight']) ?> </li>
    </ul>
</nav>
<div class="workinghours form large-8 medium-16 columns content float: left">
    <div class="trello-form">
        <div class="form-title">
            Trello Configuration
        </div>
        <form method="post">
            <div class="form-item">
                <div class="form-label">
                    Board Id:
                </div>
                <div class="description">
                    <input type="text" name="board_id" value="<?= $trello->board_id ?>">
                    <div class="info-text">
                        This is the Id of your board.<br/> In a trello board link like <b>trello.com/b/XXXXXXX/board-name</b>, id is <b>XXXXXXX</b>
                    </div>
                </div>
            </div>
            <div class="form-item">
                <div class="form-label">
                    App Key:
                </div>
                <div class="description">
                    <input type="text" name="app_key" value="<?= $trello->app_key ?>">
                    <div class="info-text">
                        This is your application key. You can get it from <a href="https://trello.com/app-key" target="_blank">this link</a>.
                    </div>
                </div>
            </div>
            <div class="form-item">
                <div class="form-label">
                    Token:
                </div>
                <div class="description">
                    <input type="text" name="token" value="<?= $trello->token ?>">
                    <div class="info-text">
                        This is your authentication token. To get it, go to <a href="https://trello.com/app-key" target="_blank">this page</a>, and click the <u>Token</u> link. You need to be logged on to Trello. 
                    </div>
                </div>
            </div>
            <div class="form-submit left-space">
                <input type="submit" value="Save" class="save" />               
                <a class="delete" href="<?= $this->Url->build(['action' => 'delete',$trello->id]) ?>" onclick="return confirm('Are you sure you wnat to delete # <?= $trello->id ?> ?')">Delete</a>
            </div>
        </form>
    </div>
    
</div>

