<?php
class Behavior_Undeletable extends ORM_Behavior {
	public function trigger_before_find() {
		$this->_orm->db_builder()
			->where($this->_orm->object_name().'.is_deleted', '=', 0)
		;
	}
	
	public function trigger_before_delete() {
		$this->_orm->set('is_deleted', 1);
		$this->_orm->save();
		
		return static::CHAIN_RETURN;
	}
}