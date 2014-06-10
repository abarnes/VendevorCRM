<?php
class EmailList extends AppModel {
    public $name = 'EmailList';
    public $hasAndBelongsToMany = array(
        'Lead' =>
        array(
            'className' => 'Lead',
            'joinTable' => 'email_lists_leads',
            'foreignKey' => 'email_list_id',
            'associationForeignKey' => 'lead_id',
            'unique' => true,
            'conditions' => ''
        )
    );
}
?>
