<?php
class ErrorsShell extends AppShell {
    public $uses = array('EmailRecord');
    
    public function main() {
        App::uses('HttpSocket', 'Network/Http');
        $HttpSocket = new HttpSocket();
        date_default_timezone_set('America/New_York');

        $data = array(
            'event'=>'rejected OR failed OR complained',
            'begin'=>strtotime("-15 minutes"),
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
                            'city'=>'',
                            'device_type'=>'',
                            'country'=>'',
                            'region'=>'',
                            'client_name'=>'',
                            'user_agent'=>'',
                            'client_os'=>'',
                            'ip'=>'',
                            'client_type'=>'',
                            'event'=>$l['event'],
                            'created'=>date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s",$l['timestamp'])))
                        );
                        $this->EmailRecord->create();
                        $this->EmailRecord->save($data);

                        $statuses = array('opened'=>1,'failed'=>2,'complained'=>3,'rejected'=>4);
                        $this->EmailRecord->EmailMessage->id = $l['user-variables']['ms-id'];
                        $status = isset($statuses[$l['event']]) ? $statuses[$l['event']]:0;
                        $this->EmailRecord->EmailMessage->saveField('status',$status);

                        $this->EmailRecord->recursive = 0;
                        $m = $this->EmailRecord->EmailMessage->find('first',array('fields'=>array('EmailMessage.lead_id','EmailMessage.id'),'conditions'=>array('EmailMessage.id'=>$l['user-variables']['ms-id'])));

                        if (!empty($m)) {
                            $this->EmailRecord->EmailMessage->Lead->id = $m['EmailMessage']['lead_id'];
                            $this->EmailRecord->EmailMessage->Lead->saveField('status',3);
                            $this->EmailRecord->EmailMessage->Lead->id = false;
                        }
                    }
                }
            }
        } else {
            $e = json_decode($result->body,true);
            $this->out('Error: '.$e['message']);
        }
        
        $this->out(date('M d Y H:i:s',strtotime("now")).' events recorded');
        $this->hr();
    }
}
?>