<?php
class Lead extends AppModel {
    public $name = 'Lead';
    public $belongsTo = array('Salesman');
    public $hasMany = array('Reminder','EmailMessage','Comment');
    public $hasAndBelongsToMany = array(
        'EmailList' =>
        array(
            'className' => 'EmailList',
            'joinTable' => 'email_lists_leads',
            'foreignKey' => 'lead_id',
            'associationForeignKey' => 'email_list_id',
            'unique' => true,
            'conditions' => ''
        ),
        'Tag' =>
        array(
            'className' => 'Tag',
            'joinTable' => 'leads_tags',
            'foreignKey' => 'lead_id',
            'associationForeignKey' => 'tag_id',
            'unique' => true,
            'conditions' => ''
        )
    );

    public function beforeSave($options = array()) {
        if (isset($this->data['Lead']['status']) && isset($this->data['Lead']['id']) && $this->data['Lead']['id']>0) {
            if ($this->data['Lead']['status']==8 || $this->data['Lead']['status']==7) {
                $this->EmailListsLead->deleteAll(array('EmailListsLead.lead_id' => $this->data['Lead']['id']), false);
            }
        }
        return true;
    }
}
?>
