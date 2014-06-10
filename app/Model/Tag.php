<?php
class Tag extends AppModel {
    public $name = 'Tag';
    public $hasAndBelongsToMany = array(
        'Lead' =>
        array(
            'className' => 'Lead',
            'joinTable' => 'leads_tags',
            'foreignKey' => 'tag_id',
            'associationForeignKey' => 'lead_id',
            'unique' => true,
            'conditions' => ''
        )
    );
}
?>
