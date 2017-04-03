<?php
namespace App\Controller;

use App\Controller\AppController;


class UsersController extends AppController
{
    private static $PASS_MIN_LENGTH = 8;

    public function index()
    {
        $this->set('users', $this->paginate($this->Users));
        $this->set('_serialize', ['users']);
    }
    
    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect(
                    ['controller' => 'Projects', 'action' => 'index']
                );
            }
            $this->Flash->error('Your username or password is incorrect.');
        }
    }
    
    public function logout()
    {
        // remove all session data
        $this->request->session()->delete('selected_project');
        $this->request->session()->delete('selected_project_role');
        $this->request->session()->delete('selected_project_memberid');
        $this->request->session()->delete('current_weeklyreport');
        $this->request->session()->delete('current_metrics');
        $this->request->session()->delete('current_weeklyhours');
        $this->request->session()->delete('project_list');
        $this->request->session()->delete('project_memberof_list');
        $this->request->session()->delete('is_admin');
        $this->request->session()->delete('is_supervisor');
        
        $this->Flash->success('You are now logged out.');
        return $this->redirect($this->Auth->logout());
    }
    
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Members']
        ]);
        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {           
            $user = $this->Users->patchEntity($user, $this->request->data);   
            if ($this->Users->save($user)){
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }   
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }
    
    public function signup()
    { 
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            
            // when adding a new user, make the role always "user", as in normal user
            $this->request->data['role'] = "user";
            
            /* 
             * CHANGE THE VALUE HERE
             * Check if the user is human
             */ 
            if ($this->request->data['checkIfHuman'] == 5) {
                $user = $this->Users->patchEntity($user, $this->request->data);   
                if ($this->Users->save($user)){
                    $this->Flash->success(__('Your account has been saved.'));
                    return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
                } else {
                    $this->Flash->error(__('The user could not be saved. Please, try again.'));
                }                
            }
            else {
                    $this->Flash->error(__('Check the sum.'));
            }  
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }
    
    public function editprofile()
    {
        $user = $this->Users->get($this->Auth->user('id'), [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The profile has been updated.'));
                return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }
        public function password()
    {
        $user = $this->Users->get($this->Auth->user('id'), [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->request->data['password'] == $this->request->data['checkPassword']) {
                if ($this->Users->save($user)) {
                    $this->Flash->success(__('The profile has been updated.'));
                    return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
                } 
                else {
                    $this->Flash->error(__('The user could not be saved. Please, try again.'));
                }
            }
            else {
                $this->Flash->error(__('Passwords are not a match. Try again, please.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }
    
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
    
    public function forgotpassword()
    {
        
    }
    
    // this allows anyone to go and create users, or reset forgotten password without logging in
    public function beforeFilter(\Cake\Event\Event $event)
    {
        $this->Auth->allow(['signup']);
        $this->Auth->allow(['forgotpassword']);
    }
    
    public function isAuthorized($user)
    {
        // Admin can access every action
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        if ($this->request->action === 'add' || $this->request->action === 'edit'
            || $this->request->action === 'delete' || $this->request->action === 'index') 
        {
            return False;
        }
        
        // All registered users can edit their own profile and logout
        if ($this->request->action === 'logout' || $this->request->action === 'editprofile' 
                || $this->request->action === 'password' ) {
            return true;
        }
        
        
        return parent::isAuthorized($user);
    }
}
