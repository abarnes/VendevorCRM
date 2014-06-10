<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1 style="float:left;">Salesforce Can Suck It<sup>&copy;</sup> CRM</h1>

            <div class="actions" style="float:right;padding:0;width:auto;">
                <?php echo $this->Html->link('Leads',array('controller'=>'leads','action'=>'index')); ?>
                <?php echo $this->Html->link('Reminders',array('controller'=>'reminders','action'=>'index')); ?>
                <?php echo $this->Html->link('Campaigns',array('controller'=>'email_campaigns','action'=>'index')); ?>
                <?php echo $this->Html->link('Email Lists',array('controller'=>'email_lists','action'=>'index')); ?>
                <?php echo $this->Html->link('Tags',array('controller'=>'tags','action'=>'index')); ?>
                <?php echo $this->Html->link('Salesmen',array('controller'=>'salesmen','action'=>'index')); ?>
                <?php echo $this->Html->link('Logout',array('controller'=>'salesmen','action'=>'logout')); ?>
            </div>
		</div>
		<div id="content">

			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			<p>
				<?php echo $cakeVersion; ?>
			</p>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
