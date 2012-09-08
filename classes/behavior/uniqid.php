<?php
class Behavior_Uniqid extends ORM_Behavior {	
	public function trigger_before_create() {
		$this->get_orm()->set('uniqid', uniqid());
	}
}