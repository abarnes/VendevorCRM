<?php
class SalesmenController extends AppController {
    public $scaffold;
    public $components = array(
        'Auth' => array(
            'loginRedirect'=>array(
                'controller' => 'leads',
                'action' =>'index'
            ),
            'loginAction' => array(
                'controller' => 'salesmen',
                'action' => 'login'
            ),
            'authError' => 'Did you really think you are allowed to see that?',
            'authenticate' => array(
                'Blowfish',
                'Form' => array(
                    'userModel'=>'Salesman',
                    'fields' => array('username' => 'email')
                )
            )
        )
    );

    public function beforeFilter(){
        $this->Auth->allow('login','add');
    }

    public function login() {
        if (!empty($this->request->data)) {
            if ($this->Auth->login()) {
                $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Session->setFlash('Username or password is incorrect');
            }
        }
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }
}
?>
