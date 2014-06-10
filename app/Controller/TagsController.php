<?php
class TagsController extends AppController {
    public $scaffold;
    public $components = array(
        'Paginator',
        'Session',
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

    public function add() {
        if (!empty($this->request->data)) {
            if ($this->Tag->save($this->request->data)) {
                $this->Session->setFlash('Tag created.');
                $this->redirect(array('controller'=>'tags'));
            } else {
                $this->Session->setFlash('Error saving.');
            }
        }
    }

    public function edit($id) {
        $this->Tag->id = $id;
        if (!empty($this->request->data)) {
            if ($this->Tag->save($this->request->data)) {
                $leads = array();
                foreach ($this->request->data['Tag'] as $k=>$lead) {
                    if (substr($k,0,1)=="l" && $lead==true) {
                        $leads[] = substr($k,1);
                    }
                }

                $data = array('Tag'=>array('id'=>$id));
                $data['Lead']['Lead'] = $leads;
                $this->Tag->id = $id;
                if ($this->Tag->save($data)) {
                    $this->Session->setFlash('Tag updated.');
                    $this->redirect(array('action'=>'index'));
                } else {
                    $this->Session->setFlash('Error saving tag.');
                }
            } else {
                $this->Session->setFlash('Failed to save');
            }
        } else {
            $this->request->data = $this->Tag->read();
        }
    }
}
?>
