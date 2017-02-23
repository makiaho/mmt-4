<?php
namespace App\Controller;

use App\Controller\AppController;

class NotesController extends AppController 
{
    public function index() {
        
        $this->paginate = [
            'order' => ['note_read' => 'asc', 'created_on' => 'desc']
        ];

        $this->set('notes', $this->paginate($this->Notes));
        $this->set('_serialize', ['notes']);

    }
    
    public function view($id = null) {
        
        $note = $this->Notes->get($id);
        
        $note->note_read = 1;
        // mark note as read in database
        if ($this->Notes->save($note)) {
            
        }

        $this->set('note', $note);
        $this->set('_serialize', ['note']);        
    }
    
    public function add() {

        $note = $this->Notes->newEntity();
        
        if ($this->request->is('post')) { 
        
            $note = $this->Notes->patchEntity($note, $this->request->data);

            $note['project_role'] = $this->request->session()->read('selected_project_role');

            $note['email'] = $this->request->session()->read('Auth.User.email');
            
            $project_id = $this->request->session()->read('selected_project')['id'];
            
            if ($this->Notes->save($note)) {
                    $this->Flash->success(__('The feedback has been saved.'));
                    return $this->redirect(
                            ['controller' => 'Projects', 'action' => 'view', $project_id]);
                } else {
                    $this->Flash->error(__('The feedback could not be saved. Please, try again.'));
                }
        }
        $this->set(compact('note'));
        $this->set('_serialize', ['note']);

    }
    
    public function delete($id = null) {
        $note = $this->Notes->get($id);
        if ($this->Notes->delete($note)) {
            $this->Flash->success(__('The feedback has been deleted.'));
        } else {
            $this->Flash->error(__('The feedback could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);       
    }

	
    public function isAuthorized($user) {
        
        // admins can do anything
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
        $project_role = $this->request->session()->read('selected_project_role');
        
        // others can only add new notes
        if ($this->request->action === 'add'){    

            if($project_role == "manager" || $project_role == "supervisor" || $project_role == "developer" || $project_role == "client") {
                return True;
            }
            else {
                return False;
            }
        }    
        else {
            return False;
        }
        return parent::isAuthorized($user);
    }
}

