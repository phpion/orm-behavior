<?php
class Model_Comment extends ORM_Behaviorable {
	protected $_behaviors = array(
		'Behavior_Undeletable',
		'Behavior_Uniqid'
	);	

	public function get_for_object($object_id, $object_type) {
		return $this
			->where('object_id', '=', (int)$object_id)
			->where('object_type', '=', (string)$object_type)
			->find_all()
		;
	}
}