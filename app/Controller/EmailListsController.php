<?php
class EmailListsController extends AppController {
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
        $this->EmailList->Behaviors->attach('Containable');
        $this->Paginator->settings = array(
            'limit' => 30,
            'contain'=>array('Lead'=>array('fields'=>array('Lead.id'))),
            'order' => array(
                'EmailList.created' => 'desc'
            )
        );
        $email_lists = $this->Paginator->paginate('EmailList');
        $this->set(compact('email_lists'));
    }

    public function add() {
        $names = $this->EmailList->Lead->EmailMessage->Lead->query("SELECT DISTINCT leads.search_term FROM leads");
        $d = array('0'=>'(none)');
        foreach($names as $s) {
            foreach($s['leads'] as $n) {
                $d[$n] = $n;
            }
        }
        $this->set('search_terms',$d);
    }

    public function select($final = false) {
        if (!empty($this->request->data)) {
            if (!$final) {
                $split = explode("/",$this->request->data['EmailList']['filter']);
                $type = $split[0];
                $subtype = $split[1];

                $conditions = $this->request->data['EmailList']['search_term']=='0' ? array():array('Lead.search_term'=>$this->request->data['EmailList']['search_term']);
                $conditions['Lead.email !=']='';
                switch ($type) {
                    case 0:
                        //find all
                        break;
                    case 1:
                        //find by status
                        $conditions['Lead.status'] = $subtype;
                        break;
                    case 2:
                        //email & website empty or not empty
                        if ($subtype==0) {

                        } elseif ($subtype==1) {

                        } elseif ($subtype==2) {
                            $conditions['Lead.website !='] = "";
                        } elseif($subtype==3) {
                            $conditions['Lead.website'] = "";
                        } else {
                            $conditions['Lead.website !='] = "";
                            $conditions['Lead.email !='] = "";
                        }
                        break;
                    case 3:
                        //all my leads
                        break;
                    case 4:
                        //all unclaimed leads
                        $conditions['Lead.salesman_id <'] = 1;
                        break;
                }

                $this->EmailList->Lead->recursive = 0;
                $leads = $this->EmailList->Lead->find('all',array('conditions'=>$conditions,'fields'=>array('Lead.id','Lead.name','Lead.website','Lead.email','Lead.status')));
                $this->set('leads',$leads);
                $this->set('final',true);
            } else {
                //save email list
                $this->EmailList->create();
                $this->EmailList->save(array('EmailList'=>array('name'=>$this->request->data['EmailList']['name'])));
                $id = $this->EmailList->id;

                $leads = array();
                foreach ($this->request->data['EmailList'] as $k=>$lead) {
                    if (substr($k,0,1)=="l" && $lead==true) {
                        $leads[] = substr($k,1);
                    }
                }

                $data = array('EmailList'=>array('id'=>$id));
                $data['Lead']['Lead'] = $leads;
                $this->EmailList->id = $id;
                if ($this->EmailList->save($data)) {
                    $this->Session->setFlash('Email list created.');
                    $this->redirect(array('action'=>'index'));
                } else {
                    $this->Session->setFlash('Error saving records.');
                }
            }
        } else {
            $this->set('final',$final);
        }
    }

    public function edit($id){
        $this->EmailList->id = $id;
        if (!empty($this->request->data)) {
            if ($this->EmailList->save($this->request->data)) {
                $leads = array();
                foreach ($this->request->data['EmailList'] as $k=>$lead) {
                    if (substr($k,0,1)=="l" && $lead==true) {
                        $leads[] = substr($k,1);
                    }
                }

                $data = array('EmailList'=>array('id'=>$id));
                $data['Lead']['Lead'] = $leads;
                $this->EmailList->id = $id;
                if ($this->EmailList->save($data)) {
                    $this->Session->setFlash('Email list updated.');
                    $this->redirect(array('action'=>'index'));
                } else {
                    $this->Session->setFlash('Error saving list.');
                }
            } else {
                $this->Session->setFlash('Error saving list.');
            }
        } else {
            $this->request->data = $this->EmailList->read();
        }
    }

}
?>
