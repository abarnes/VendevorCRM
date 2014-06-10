<?php
class Salesman extends AppModel {
    public $name = 'Salesman';
    public $hasMany = array('Lead','EmailCampaign','Reminder');

    public function beforeSave($options = array()) {
        /*if (isset($this->data[$this->alias]['password'])) {
            Security::setHash('blowfish');
            $this->data[$this->alias]['password'] = Security::hash($this->data[$this->alias]['password']);
        }
        return true;*/

        if(isset($this->data[$this->alias]['password']))
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        return true;
    }

}
?>
