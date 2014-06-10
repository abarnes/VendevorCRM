<?php
class EmailMessagesController extends AppController {
    public $scaffold;
    public $components = array(
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

    public function beforeFilter() {
        $this->Auth->allow('unsubscribe');
    }

    public function send_to_list($list=null) {
        if (!empty($this->request->data)) {
            $this->EmailMessage->Lead->EmailList->Behaviors->attach('Containable');
            $leads = $this->EmailMessage->Lead->EmailList->find('first',array('conditions'=>array('EmailList.id'=>$list),'contain'=>array('Lead'=>array('fields'=>array('Lead.id','Lead.email','Lead.name','Lead.phone','Lead.address','Lead.website')))));

            $success = 0;
            $error = 0;
            $contacted = array();
            foreach ($leads['Lead'] as $l) {
                $this->request->data['EmailMessage']['html'] = $this->_format($this->request->data['EmailMessage']['html'],$l);
                $this->request->data['EmailMessage']['text'] = $this->_format($this->request->data['EmailMessage']['text'],$l);

                $this->EmailMessage->create();
                if ($this->EmailMessage->save(array('EmailMessage'=>array('lead_id'=>$l['id'],'email_campaign_id'=>$this->request->data['EmailMessage']['campaign'])))) {
                    $m_id = $this->EmailMessage->id;
                    $result = $this->_send($l['email'],$m_id,$this->request->data['EmailMessage']['campaign'],$this->request->data['EmailMessage'],true);

                    if ($result) {
                        $contacted[] = array('Lead'=>array('id'=>$l['id'],'status'=>1));
                        $success++;
                    } else {
                        $error++;
                    }
                }
                $this->EmailMessage->id = false;
            }

            if (!empty($contacted)) {
                $this->EmailMessage->Lead->saveMany($contacted);
            }

            $this->Session->setFlash($success.' Emails sent, '.$error.' errors.');
            $this->redirect(array('controller'=>'email_lists','action'=>'index'));
        }

        $this->set('list',$list);
        $campaigns = $this->EmailMessage->EmailCampaign->find('list',array('order'=>array('EmailCampaign.created'=>'desc')));
        $campaigns[0] = 'none';
        $this->set('campaigns',$campaigns);
    }

    public function send($lead_id) {
        $campaigns = $this->EmailMessage->EmailCampaign->find('list',array('order'=>array('EmailCampaign.created'=>'DESC'),'fields'=>array('EmailCampaign.id','EmailCampaign.name')));
        $campaigns[0] = 'none';
        $this->set('campaigns',$campaigns);

        if (!empty($this->request->data)) {
            $this->EmailMessage->create();
            if ($this->EmailMessage->save(array('EmailMessage'=>array('lead_id'=>$lead_id,'email_campaign_id'=>$this->request->data['EmailMessage']['campaign'])))) {
                $m_id = $this->EmailMessage->id;
                $result = $this->_send($this->request->data['EmailMessage']['to'],$m_id,$this->request->data['EmailMessage']['campaign'],$this->request->data['EmailMessage'],false);
                if ($result) {
                    $this->EmailMessage->Lead->id = $lead_id;
                    $this->EmailMessage->Lead->saveField('status',1);

                    $this->Session->setFlash('Message sent');
                    $this->redirect(array('controller'=>'leads','action'=>'view',$lead_id));
                } else {
                    $this->EmailMessage->delete($m_id);
                    $this->Session->setFlash($result);
                }
            } else {
                $this->Session->setFlash('Error creating message record (email not sent).');
            }
        }
        $this->EmailMessage->Lead->recursive = 0;
        $this->set('lead',$this->EmailMessage->Lead->findById($lead_id));
    }

    public function _send($to,$message_id,$campaign_id,$settings,$unsubscribe=false) {
        App::uses('HttpSocket', 'Network/Http');
        $HttpSocket = new HttpSocket();

        $data = array(
            'to'=>$to,
            'from'=>$settings['from'],
            'subject'=>$settings['subject'],
            'v:ms-id'=>$message_id
        );
        if (isset($settings['cc']) && $settings['cc']!='') $data['cc'] = $settings['cc'];
        if (isset($settings['bcc']) && $settings['bcc']!='') $data['bcc'] = $settings['bcc'];
        if (isset($settings['text']) && $settings['text']!='') {
            $data['text'] = $settings['text'];
            if ($unsubscribe) $data['text'].='7295 Dr Garrison Rd, Mansfield, TX 76063 \r\n To unsubscribe click: http://unsubscribe.vendevor.com:8889/crm/u?msid='.$message_id;
        }
        if (isset($settings['html']) && $settings['html']!='') {
            $data['html'] = $settings['html'];
            if ($unsubscribe) $data['html'].='<br><p style="width:100%;font-size:9px;font-weight:200;">15455 Dallas Parkway, Suite 375<br>Addison, TX 75001<br><a href="http://unsubscribe.vendevor.com:8889/crm/u?msid='.$message_id.'">unsubscribe</a></p>';
        }
        if ($campaign_id>0) $data['o:campaign'] = $campaign_id;

        $result = $HttpSocket->post(
            'https://api.mailgun.net/v2/vendevor.com/messages',
            $data,
            array('auth'=>array('method'=>'Basic','user'=>'api','pass'=>'key-9cnjtdwy8d-a8i7qj1mtai4jct4931j5'))
        );

        if ($result->isOk()) {
            return true;
        } else {
            $e = json_decode($result->body,true);
            return 'Error: '.$e['message'];
        }
    }

    public function unsubscribe() {
        if (!empty($_GET) && isset($_GET['msid'])) {
            if ($_GET['msid']) {
                $this->EmailMessage->id = $_GET['msid'];
                $this->EmailMessage->saveField('status',6);

                $message = $this->EmailMessage->findById($_GET['msid']);
                if (!empty($message)) {
                    $this->EmailMessage->Lead->id = $message['EmailMessage']['lead_id'];
                    $this->EmailMessage->Lead->saveField('status',7); //mark not interested
                }
            }
        }
        exit('<br><h4 style="text-align:center;">Successfully unsubscribed.</h4>');
    }

    public function _format($corrected,$l) {
        //format the email
        $keys = array('name','address','website','phone','email');
        foreach ($keys as $key) { //handle if statements
            $start = strpos($corrected,"?#".$key.'{');
            while ($start!==false) {
                $remaining = substr($corrected,$start+strlen($key)+3);
                $end = strpos($remaining,"}#")+$start;
                if ($l[$key]!='') {
                    $replacement_text = substr($remaining,0,strpos($remaining,"}#"));
                    $corrected = str_replace(substr($corrected,$start,$end-$start+strlen($key)+5),$replacement_text,$corrected);
                } else {
                    $corrected = str_replace(substr($corrected,$start,$end-$start+strlen($key)+5),"",$corrected);
                }

                $start = strpos($corrected,"?#".$key.'{');
            }

            $start = strpos($corrected,"!#".$key.'{');
            while ($start!==false) {
                $remaining = substr($corrected,$start+strlen($key)+3);
                $end = strpos($remaining,"}#")+$start;
                if ($l[$key]=='') {
                    $replacement_text = substr($remaining,0,strpos($remaining,"}#"));
                    $corrected = str_replace(substr($corrected,$start,$end-$start+strlen($key)+5),$replacement_text,$corrected);
                } else {
                    $corrected = str_replace(substr($corrected,$start,$end-$start+strlen($key)+5),"",$corrected);
                }

                $start = strpos($corrected,"!#".$key.'{');
            }
        }

        foreach ($keys as $key) {
            $start = strpos($corrected,"#".$key);
            while ($start!==false) {
                $remaining = substr($corrected,$start+strlen($key)+1);
                $end = strpos($remaining,"#")+$start;
                $chunk = substr($corrected,$start,$end-$start+strlen($key)+2);

                if ($l[$key]!="") {
                    $corrected = str_replace($chunk,$l[$key],$corrected);
                } else {;
                    if (strpos($chunk,"::")!==false) {
                        $absent = substr($chunk,strpos($chunk,"::")+3,-2);
                        $corrected = str_replace($chunk,$absent,$corrected);
                    } else {
                        $corrected = str_replace($chunk,"",$corrected);
                    }
                }

                $start = strpos($corrected,"#".$key);
            }
        }

        return $corrected;
    }
}
?>
