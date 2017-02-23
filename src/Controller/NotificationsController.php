<?php
namespace App\Controller;

use App\Controller\AppController;


class NotificationsController extends AppController
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
				
				$notifications = \Cake\ORM\TableRegistry::get('Notifications');
				$hoo = array('comment_id' => 141, 
					         'member_id' => 50, 
					         'is_read' => false);
				
				$notifs = $notifications->newEntities($hoo);
				

				// now let's link a left comment to all of project's members
				foreach ($notifs as $notif) {
					$this->Notifications->save($notif);
				}

                $this->Flash->success(__('The comment has been saved.'));
				
				
				
            } else {
                $this->Flash->error(__('The comment could not be saved. Please, try again.'));
            }
			return $this->redirect($this->referer());
        }
        
		$this->set(compact('comment'));
        $this->set('_serialize', ['comment']);
		return $this->redirect($this->referer());
	}
	
	public function isAuthorized($user) {
		// everyone can get notifs
		if ($this->request->action === 'add') {
			return True;
		}
		return parent::isAuthorized($user);
	}
}