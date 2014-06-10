<?php
class RemindersController extends AppController {
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
       $this->Reminder->Behaviors->attach('Containable');
       $this->Paginator->settings = array(
           'conditions' => array('Reminder.status'=>0),
           'limit' => 30,
           'contain'=>array('Salesman'=>array('fields'=>array('Salesman.id','Salesman.name')),'Lead'=>array('fields'=>array('Lead.id','Lead.name'))),
           'order' => array(
               'Reminder.date' => 'asc'
           )
       );
       $reminders = $this->Paginator->paginate('Reminder');
       $this->set(compact('reminders'));
   }

    public function add($lead) {
        if (!empty($this->request->data)) {
            $this->request->data['Reminder']['lead_id'] = $lead;
            if ($this->Reminder->save($this->request->data)) {
                $this->Session->setFlash('Reminder saved.');
            } else {
                $this->Session->setFlash('Failed to save reminder');
            }
        }

        $this->redirect(array('controller'=>'leads','action'=>'view',$lead));
    }

    public function edit($id) {
        $this->Reminder->id = $id;
        if (!empty($this->request->data)) {
            if ($this->Reminder->save($this->request->data)) {
                $this->Session->setFlash('Reminder updated.');
                $this->redirect(array('controller'=>'reminders'));
            } else {
                $this->Session->setFlash('Failed to save');
            }
        } else {
            $this->request->data = $this->Reminder->read();
        }
        $this->set('salesmen',$this->Reminder->Salesman->find('list'));
    }

    public function view($id) {
        $this->set('reminder',$this->Reminder->findById($id));
    }

    public function complete($id,$view="") {
        $this->Reminder->id = $id;
        if ($this->Reminder->saveField('status',1)) {
            $this->Session->setFlash('Reminder completed.');
        } else {
            $this->Session->setFlash('Error marking complete');
        }

        if ($view!="") {
            $this->redirect(array('controller'=>'leads','action'=>'view',$view));
        } else {
            $this->redirect(array('controller'=>'reminders','action'=>'index'));
        }
    }

}
?>
