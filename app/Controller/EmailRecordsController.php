<?php
class EmailRecordsController extends AppController {
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
        $this->Auth->allow('open');
    }

    public function open() {
        if (!empty($_POST) && isset($_POST['ms-id'])) {
            $data = array(
                'email_message_id'=>$_POST['ms-id'],
                'city'=>$_POST['city'],
                'device_type'=>$_POST['device-type'],
                'country'=>$_POST['country'],
                'region'=>$_POST['region'],
                'client_name'=>$_POST['client-name'],
                'user_agent'=>$_POST['user-agent'],
                'client_os'=>$_POST['client-os'],
                'ip'=>$_POST['ip'],
                'client_type'=>$_POST['client-type'],
                'event'=>$_POST['event'],
                'created'=>date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s",$_POST['timestamp'])))
            );
            $this->EmailRecord->create();
            $this->EmailRecord->save($data);

            $this->EmailRecord->EmailMessage->id = $_POST['ms-id'];
            $s = $this->EmailRecord->EmailMessage->field('status');

            $statuses = array('opened'=>1,'failed'=>2,'complained'=>3,'rejected'=>4,'clicked'=>5);
            if ($s!=5) {
                if ($s!=1 || ($s==1 && $statuses[$_POST['event']]==5)) {
                    $this->EmailRecord->EmailMessage->id = $_POST['ms-id'];
                    $status = isset($statuses[$_POST['event']]) ? $statuses[$_POST['event']]:0;
                    $this->EmailRecord->EmailMessage->saveField('status',$status);
                }
            }
        }
        exit('success');
    }

    //moved to cron. unused now
    public function query() {
        App::uses('HttpSocket', 'Network/Http');
        $HttpSocket = new HttpSocket();

        $data = array(
            'event'=>'rejected OR complained OR failed',
            'begin'=>strtotime("-3 hours"),
            'end'=>strtotime("Now")
        );

        $result = $HttpSocket->get(
            'https://api.mailgun.net/v2/vendevor.com/events',
            $data,
            array('auth'=>array('method'=>'Basic','user'=>'api','pass'=>'key-9cnjtdwy8d-a8i7qj1mtai4jct4931j5'))
        );

        if ($result->isOk()) {
            $f = json_decode($result->body,true);
            if (!empty($f['items'])) {
                foreach ($f['items'] as $l) {
                    if (!empty($l) && isset($l['user-variables']['ms-id'])) {
                        $data = array(
                            'email_message_id'=>$l['user-variables']['ms-id'],
                            /*'city'=>$l['geolocation']['city'],
                            'device_type'=>$l['client-info']['device-type'],
                            'country'=>$l['geolocation']['country'],
                            'region'=>$l['geolocation']['region'],
                            'client_name'=>$l['client-info']['client-name'],
                            'user_agent'=>$l['client-info']['user-agent'],
                            'client_os'=>$l['client-info']['client-os'],
                            'ip'=>$l['ip'],
                            'client_type'=>$l['client-info']['client-type'],*/
                            'event'=>$l['event'],
                            'created'=>date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s",$l['timestamp'])))
                        );
                        $this->EmailRecord->create();
                        $this->EmailRecord->save($data);

                        $this->EmailRecord->EmailMessage->id = $l['user-variables']['ms-id'];
                        $s = $this->EmailRecord->EmailMessage->field('status');

                        $statuses = array('opened'=>1,'failed'=>2,'complained'=>3,'rejected'=>4,'clicked'=>5);
                        if (isset($statuses[$s]) && isset($statuses[$l['event']]) && $s!=5) {
                            if ($s!=1 || ($s==1 && $statuses[$l['event']]==5)) {
                                $this->EmailRecord->EmailMessage->id = $l['ms-id'];
                                $status = isset($statuses[$l['event']]) ? $statuses[$l['event']]:0;
                                $this->EmailRecord->EmailMessage->saveField('status',$status);
                            }
                        }
                    }
                }
            }
            exit('success');
        } else {
            $e = json_decode($result->body,true);
            exit('Error: '.$e['message']);
        }
    }

}
?>
