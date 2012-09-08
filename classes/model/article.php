<?php
class Model_Article extends ORM_Behaviorable {
	protected $_behaviors = array(
		'Behavior_Commentable',
		'Behavior_Undeletable',
		'Behavior_I18n' => array(
			'columns' => array(
				'title' => '',
				'content' => ''
			)
		)
	);
}