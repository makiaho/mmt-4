<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\Routing\Router;
use Cake\Filesystem\Folder;

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
    
    // Image upload functionality works, but there is a problem with permissions on the server. So this commented for now.
//    public function photo()
//    {    
//        if ($this->request->is(['patch', 'post', 'put'])) {
//            
//            $action = $this->request->data['action'];
//            
//            if($action === 'upload'){
//            
//                $imageFile = $this->request->data['image'];
//
//                if($imageFile['size'] === 0){
//                    $this->Flash->error(__('File not found.'));
//                }else{
//
//                    $check = getimagesize($imageFile['tmp_name']);
//
//                    if(!$check){
//                        $this->Flash->error(__('File is not an image.'));
//                    }else{
//
//                        $userId = $this->Auth->user('id');
//
//                        $targetPath = WWW_ROOT . 'img' . DS . 'profile' . DS . 'user_' . $userId . '.png'; 
//
//                        if (move_uploaded_file($imageFile["tmp_name"], $targetPath)) {
//
//                            $this->Flash->success(__('The image has been uploaded.'));
//                        } else {
//
//                            $this->Flash->error(__('The image can not be uploaded. Please, try again.'));
//                        }
//                    }
//                }
//            }else if ($action === 'delete'){
//                
//                $userId = $this->Auth->user('id');
//
//                $path = WWW_ROOT . 'img' . DS . 'profile' . DS . 'user_' . $userId . '.png'; 
//                
//                if(unlink($path)){
//                    $this->Flash->success(__('The image has been deleted.'));
//                }else{
//                    $this->Flash->error(__('The image be deleted. Please, try again.'));
//                }
//                
//            }
//            
//        }
//    }
    
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
        if ($this->request->is('post')) {
            
            $email = $this->request->data['email'];
            
            $checkUser = $this->Users->find()->where(['email' => $email])->toArray();
            
            if(empty($checkUser)){
                $this->Flash->error(__('This email does not belong to any user.'));
            }else{
                
                $user = $checkUser[0];
                
                $key = $string = substr(md5(rand()), 0, 25);
                
                $user->password_key = $key;
                
                if($this->Users->save($user)){
                    
                    $sendMail = new Email();
                
                    $sendMail->from(['mmt@uta.fi' => 'MMT']);
                    $sendMail->to($email);
                    $sendMail->subject('Password reset');
                    $sendMail->emailFormat('html');
                    
                    if($sendMail->send($this->prepareEmail($key))){
                        $this->Flash->success(__('Key for reseting your password has been sent to your email.'));
                        
                        return $this->redirect(['controller' => 'Projects','action' => 'index']);
                    }else{
                        $this->Flash->error(__('An error occured when sending the email, please try again.'));
                    }
          
                }else{
                    $this->Flash->error(__('An error occured when sending the email, please try again.'));
                }
                
                
            }
            
        }
    }
    
    public function resetpassword($key = null)
    {
        $showForm = false;

        if($this->request->is(['patch', 'post', 'put'])){
            
            $showForm = true;
            
            $user = $this->Users->find()->where(['password_key' => $key]);
            
            if($user->first() === null){
                    $this->Flash->error(__('Invalid key.'));
 
            }else{
                
                $user = $user->first();
                
                if ($this->request->data['password'] == $this->request->data['checkPassword']) {
                    
                    if(strlen($this->request->data['password']) < 8){
                        
                        $this->Flash->error(__('The password has to be 8 characters long'));

                    }else{
                        
                        $user->password = $this->request->data['password'];
                        $user->password_key = null;
                    
                        if ($this->Users->save($user)) {
                            $this->Flash->success(__('Your password has been updated.'));
                            
                            $showForm = false;
                            
                            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
                                                 
                        } 
                        else {
                            $this->Flash->error(__('The user could not be saved. Please, try again.'));

                        }                   
                    }
                                  
                }else{
                    $this->Flash->error(__('Passwords are not a match. Try again, please.'));
                    
                    $showForm = true;
                }
                
            }
            
            
            
        }else{       
            if($key == null){
                $this->Flash->error(__('Invalid key.'));
            }else{
                $getUser = $this->Users->find()->where(['password_key' => $key]);

                if($getUser->first() === null){
                    $this->Flash->error(__('Invalid key.'));
                }else{

                    $user = $getUser->first();
                    
                    $showForm = true;
                }
            }
        }
        
        $this->set(compact('showForm','key'));
        
    }
    
    // this allows anyone to go and create users, or reset forgotten password without logging in
    public function beforeFilter(\Cake\Event\Event $event)
    {
        $this->Auth->allow(['signup']);
        $this->Auth->allow(['forgotpassword']);
        $this->Auth->allow(['resetpassword']);
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
                || $this->request->action === 'password' || $this->request->action === 'photo' ) {
            return true;
        }
        
        
        return parent::isAuthorized($user);
    }
    
    public function prepareEmail($key)
    {
        $url = Router::url(['controller' => 'Users','action' => 'resetpassword',$key], true);
        
        $message = '<p>In order to reset your password, visit this link:</p>';
        
        $message .= '<p><a href="'.$url.'">'.$url.'</a></p>';
        
        return $message;
    }
}
