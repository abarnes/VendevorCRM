<?php
class CommentsController extends AppController {
	var $name = 'Comments';
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
	
	public function add($lead_id) {
		if (!empty($this->request->data)) {
            $this->request->data['Comment']['lead_id'] = $lead_id;
            $this->request->data['Comment']['salesman_id'] = $this->Auth->user('id');
			if ($this->Comment->save($this->request->data)) {
				$this->autoRender = false;
				header("HTTP/1.0 200 OK");
				echo (string)$this->Comment->id;
				exit();
			}
		}
	}
	
	public function delete($id) {
			if ($this->Comment->delete($id)) {
                $this->autoRender = false;
                header("HTTP/1.0 200 OK");
                exit();
			} else {
                $this->autoRender = false;
                header("HTTP/1.0 404 Not Found");
                exit();
			}
	}
	
}
?>