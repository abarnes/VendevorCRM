<?php
class EmailCampaignsController extends AppController {
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

    public function index() {
        $this->EmailCampaign->Behaviors->attach('Containable');
        $this->Paginator->settings = array(
            'limit' => 30,
            'contain'=>array('Salesman'=>array('fields'=>array('Salesman.name','Salesman.id'))),
            'order' => array(
                'EmailCampaign.created' => 'desc'
            )
        );
        $email_campaigns = $this->Paginator->paginate('EmailCampaign');
        $this->set(compact('email_campaigns'));
    }

    public function add() {
        if (!empty($this->request->data)) {
            if ($this->EmailCampaign->save($this->request->data)) {
                App::uses('HttpSocket', 'Network/Http');
                $HttpSocket = new HttpSocket();

                $result = $HttpSocket->post(
                    'https://api.mailgun.net/v2/vendevor.com/campaigns',
                    array('name'=>$this->request->data['EmailCampaign']['name'],'id'=>$this->EmailCampaign->id),
                    array('auth'=>array('method'=>'Basic','user'=>'api','pass'=>'key-9cnjtdwy8d-a8i7qj1mtai4jct4931j5'))
                );

                if ($result->isOk()) {
                    $this->Session->setFlash('Email campaign created');
                    $this->redirect(array('action'=>'index'));
                    //exit(json_decode($result->body));
                } else {
                    $this->Session->setFlash('Error: '.$result->reasonPhrase);
                }
            } else {
                $this->Session->setFlash('Error: failed to save.');
            }
        }
    }

    public function edit($id) {
        $this->EmailCampaign->id = $id;
        if (!empty($this->request->data)) {
            if ($this->EmailCampaign->save($this->request->data)) {
                App::uses('HttpSocket', 'Network/Http');
                $HttpSocket = new HttpSocket();

                $result = $HttpSocket->put(
                    'https://api.mailgun.net/v2/vendevor.com/campaigns/'.$id,
                    array('name'=>$this->request->data['EmailCampaign']['name']),
                    array('auth'=>array('method'=>'Basic','user'=>'api','pass'=>'key-9cnjtdwy8d-a8i7qj1mtai4jct4931j5'))
                );

                if ($result->isOk()) {
                    $this->Session->setFlash('Email campaign updated');
                    $this->redirect(array('action'=>'index'));
                } else {
                    $this->Session->setFlash('Error: '.$result->reasonPhrase);
                }
            } else {
                $this->Session->setFlash('Error: failed to save.');
            }
        } else {
            $this->request->data = $this->EmailCampaign->read();
        }
    }

    public function delete($id) {
        $c = $this->EmailCampaign->findById($id);

        App::uses('HttpSocket', 'Network/Http');
        $HttpSocket = new HttpSocket();

        $result = $HttpSocket->delete(
            'https://api.mailgun.net/v2/vendevor.com/messages',
            array('name'=>$c['EmailCampaign']['name']),
            array('auth'=>array('method'=>'Basic','user'=>'api','pass'=>'key-9cnjtdwy8d-a8i7qj1mtai4jct4931j5'))
        );

        if ($result->isOk()) {
            if ($this->EmailCampaign->delete($id)) {
                $this->Session->setFlash('Email campaign deleted.');
            } else {
                $this->Session->setFlash('Error deleting campaign.');
            }
        } else {
            $this->Session->setFlash('Error: '.$result->reasonPhrase);
        }
        $this->redirect(array('action'=>'index'));
    }

}
?>
