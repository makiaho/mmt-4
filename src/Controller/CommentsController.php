<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;

class CommentsController extends AppController
{
    public function index()
    {
		// this just throws you back
		return $this->redirect($this->referer());
    }
	
	public function add() {
		$comment = $this->Comments->newEntity();
        if ($this->request->is('post')) {
            // get data from the form
            $comment = $this->Comments->patchEntity($comment, $this->request->data);

            if ($this->Comments->save($comment)) {
				// when a comment is added, also add a notification to database; so for that we'll fetch the maximum (= most recent) comment id
				$query = $this->Comments->find();
				$query->select(['max'=>$query->func()->max('id')]);
				$query = $query->toArray();
				
				$maxid = $query[0]->max;
				// also users and weeklyreports id
				$wrid = $this->request->data['weeklyreport_id'];
				$uid  = $this->request->data['user_id'];
				
				// now we need 2 things: project id and with it, member id
				$project_query = \Cake\ORM\TableRegistry::get('Weeklyreports')
								->find()
								->select(['project_id'])
								->where(['id =' => $wrid])
								->toArray();
				$pid = strval($project_query[0]->project_id);
				
				// now the member id
				$member_query = \Cake\ORM\TableRegistry::get('Members')
								->find()
								->select('id')
								->where(['project_id =' => $pid])
								->toArray();
				
				$id_array = array();
				for ($i=0; $i < sizeof($member_query); $i++) {
					$id_array[] = strval( $member_query[$i]->id );
				}

                                $connection = \Cake\Datasource\ConnectionManager::get("default");
                                
				
					for ($i = 0; $i < sizeof($id_array); $i++) {
						
                                                $result = $connection->insert('notifications',[
                                                    'comment_id' => $maxid, 
                                                    'member_id' => $id_array[$i], 
                                                    'weeklyreport_id' => $wrid
                                                        ]);
                                                
						if (!$result) {
							exit;
						}
					}
                 
                //This is for sending the comment to slack
                $slackController = new SlackController();

                $slackData = ['type' => 'comment','details' => [
                    'user_id' => $comment->user_id,
                    'text' => $comment->content,
                    'report_id' => $wrid]]; 
                
                $slackResult = $slackController->sendMessage($pid, $slackData);
                                

                $this->Flash->success(__('The comment has been saved.'));
			
                if($slackResult == 'success'){
                    $this->Flash->success(__('The comment has been sent to slack.'));
                }else if ($slackResult == 'fail'){
                    $this->Flash->error(__('The comment could not be sent to slack.'));
                }
				
				
            } else {
                $this->Flash->error(__('The comment could not be saved. Please, try again.'));
            }
			return $this->redirect($this->referer());
        }
        
		$this->set(compact('comment'));
        $this->set('_serialize', ['comment']);
		return $this->redirect($this->referer());
	}
	
	public function edit($id = null) {
            
            if($this->request->is('post')){
                
                $content = $this->request->data['content'];
            
                $comment = $this->Comments->get($id);
                
                if($content !== null && $content !== ''){
                    $comment->content = $content;
                    $comment->date_modified = Time::now();
                    
                    if ($this->Comments->save($comment)) {
                        $this->Flash->success(__('The comment has been saved.'));
                    }else{
                        $this->Flash->error(__('The comment could not be saved. Please, try again.'));
                    }
                }else{
                    $this->Flash->error(__('The comment can not be empty.'));
                }
                
            }else{
                $this->Flash->error(__('No comment is sent.'));
            }
            
            $this->index();
	}
	
	public function delete($id = null) {	
        $comment = $this->Comments->get($id);
        if ($this->Comments->delete($comment)) {
            $this->Flash->success(__('The comment has been deleted.'));
        } else {
            $this->Flash->error(__('The comment could not be deleted. Please, try again.'));
        }
        return $this->redirect($this->referer());
	}
	
	public function isAuthorized($user) {
		// everyone can leave comments
		if ($this->request->action === 'add') {
			return True;
		}
		// if editing or deleting, you have to be the owner
		if ($this->request->action === 'edit' || $this->request->action === 'delete') {
			$query = $this->Comments
					->find()
					->select(['user_id'])
                    ->where(['id =' => $this->request->pass[0]])
                    ->toArray();

			if ($query[0]->user_id == $this->request->session()->read('Auth.User.id')) {
				return True;
			}
		}
		return parent::isAuthorized($user);
	}
}