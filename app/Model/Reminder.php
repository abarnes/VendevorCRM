<?php
class Reminder extends AppModel {
    public $name = 'Reminder';
    public $belongsTo = array('Lead','Salesman');
}
?>
