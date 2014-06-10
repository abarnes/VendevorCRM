<?php
class LeadsController extends AppController {
    public $helpers = array('Form');
    public $components = array(
        'Paginator',
        'Session',
        'Auth' => array(
            'loginAction' => array(
                'controller' => 'salesmen',
                'action' => 'login'
            ),
            'authError' => 'Did you really think you are allowed to see that?',
            'authenticate' => array(
                'Form' => array(
                    'passwordHasher' => 'Blowfish',
                    'fields' => array('username' => 'email')
                )
            )
        )
    );

    public function index($type=0,$subtype=0,$search = '') {
        $this->set('type',$type);
        $this->set('subtype',$subtype);
        $this->set('search_term',$search);

        $conditions = $search=='' ? array():array('Lead.search_term'=>$search);

        //handle search types
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
                    $conditions['Lead.email !='] = "";
                } elseif ($subtype==1) {
                    $conditions['Lead.email'] = "";
                } elseif ($subtype==2) {
                    $conditions['Lead.website !='] = "";
                } elseif($subtype==3) {
                    $conditions['Lead.website'] = "";
                } elseif($subtype==4){
                    $conditions['Lead.website !='] = "";
                    $conditions['Lead.email !='] = "";
                } elseif($subtype==5) {
                    $conditions['Lead.email !='] = "";
                    $conditions['Lead.status'] = 0;
                } else {
                    $conditions['Lead.email !='] = "";
                    $conditions['Lead.website !='] = "";
                    $conditions['Lead.status'] = 0;
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

        if (isset($this->request->data['Lead']['query'])) {
            $this->set('query',$this->request->data['Lead']['query']);
            $query = '%'.$this->request->data['Lead']['query'].'%';
            $conditions[] = array('OR'=>array('Lead.name LIKE' => $query,'Lead.address LIKE'=>$query,'Lead.phone LIKE'=>$query,'Lead.categories LIKE'=>$query,'Lead.id LIKE'=>$query,'Lead.search_term LIKE'=>$query,'Lead.website LIKE'=>$query,'Lead.email LIKE'=>$query));
        } else {
            $this->set('query','');
        }

        $this->Lead->Behaviors->attach('Containable');
        $this->Paginator->settings = array(
            'conditions' => $conditions,
            'limit' => 30,
            'contain'=>array('Salesman'=>array('fields'=>array('Salesman.id','Salesman.name'))),
            'order' => array(
                'Lead.id' => 'asc'
            )
        );
        $leads = $this->Paginator->paginate('Lead');
        $this->set(compact('leads'));

        $names = $this->Lead->query("SELECT DISTINCT leads.search_term FROM leads");
        $this->set('search_terms',$names);
    }

    public function add() {
        if (!empty($this->request->data)) {
           $this->Lead->save($this->request->data);
           $this->Session->setFlash('Lead added.');
           $this->redirect('/leads');
        }

        $s = $this->Lead->Salesman->find('list');
        $s[0] = '(unclaimed)';
        $this->set('salesmen',$s);
    }

    public function edit($id) {
        $this->Lead->id = $id;
        if (!empty($this->request->data)) {
            $this->Lead->save($this->request->data);
            $this->Session->setFlash('Lead updated.');
            $this->redirect('/leads');
        } else {
            $this->request->data = $this->Lead->read();
        }

        $s = $this->Lead->Salesman->find('list');
        $s[0] = '(unclaimed)';
        $this->set('salesmen',$s);
    }

    public function view($id) {
        $this->Lead->Behaviors->attach('Containable');
        $contain = array('Salesman','EmailMessage'=>array('EmailCampaign'),'Comment','Reminder'=>array('conditions'=>array('Reminder.status'=>0)));
        $lead = $this->Lead->find('first',array('contain'=>$contain,'conditions'=>array('Lead.id'=>$id)));
        $this->set('lead',$lead);

        $s = $this->Lead->Salesman->find('list');
        $s[0] = '(unclaimed)';
        $this->set('salesmen',$s);
    }

    public function view_website($id) {
        $this->layout = 'blank';
        $this->set('lead',$this->Lead->findById($id));

        $this->set('tags',$this->Lead->Tag->find('list'));
    }

    public function add_tag($lead_id,$tag,$status) {
        //find current tags
        $this->Lead->Behaviors->attach('Containable');
        $contain = array('Tag'=>array('fields'=>array('Tag.id','Tag.name')));
        $lead = $this->Lead->find('first',array('conditions'=>array('Lead.id'=>$lead_id),'fields'=>array('Lead.id'),'contain'=>$contain));
        $current_tags = array();
        foreach($lead['Tag'] as $t) {
            $current_tags[$t['id']] = $t['id'];
        }

        if ($status==1) {
            $current_tags[$tag] = $tag;
        } else {
            unset($current_tags[$tag]);
        }

        $this->Lead->id = $lead_id;
        $data = array('Lead'=>array('id'=>$lead_id),'Tag'=>array('Tag'=>$current_tags));
        if ($this->Lead->save($data)) {
            exit('success');
        } else {
            exit('error');
        }
    }

    public function delete($id) {
        $this->Lead->delete($id);
        $this->Session->setFlash('Lead deleted.');
        //exit();
        $this->redirect('/leads');
    }

    public function change_status($id,$status) {
        $this->Lead->id = $id;
        $this->Lead->saveField('status',$status);
        $this->Session->setFlash('Lead #'.$id.' status changed');
        exit();
    }

    public function claim($id) {
        $this->Lead->id = $id;
        $this->Lead->saveField('salesman_id',$this->Auth->user('id'));
        $this->Session->setFlash('Lead #'.$id.' claimed');
        exit();
    }

    function export_xls($search = '') {
        $this->layout = 'blank';

        $conditions = $search=='' ? array():array('Lead.search_term'=>$search);
        if (isset($this->request->data)) {
            $query = '%'.$this->request->data['Lead']['query'].'%';
            $conditions[] = array('OR'=>array('Lead.name LIKE' => $query,'Lead.address LIKE'=>$query,'Lead.phone LIKE'=>$query,'Lead.categories LIKE'=>$query,'Lead.id LIKE'=>$query,'Lead.search_term LIKE'=>$query,'Lead.website LIKE'=>$query,'Lead.email LIKE'=>$query));
        }

        $data = $this->Lead->find('all',array('conditions'=>$conditions));

        $this->set('rows',$data);
        $this->response->header(array('Content-Disposition: attachment; filename="Leads_'.date('m-d-Y').'.csv"'));
        $this->response->type(array('xls' => 'application/vnd.ms-excel'));
        $this->response->type('xls');
    }

    public function remove_duplicates() {
        $leads = $this->Lead->find('all');
        $addresses = array();

        foreach ($leads as $l) {
            if (in_array($l['Lead']['address'],$addresses)) {
               $this->Lead->delete($l['Lead']['id']);
            } else {
               $addresses[] = $l['Lead']['address'];
            }
        }

        $this->redirect('/leads');
    }
}
?>
