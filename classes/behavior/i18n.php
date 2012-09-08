<?php
class Behavior_I18n extends ORM_Behavior {
	public function trigger_before_find() {
		$db_builder = $this->_orm->db_builder();
		$table_name = $this->table_name();
		$foreign_key = $this->foreign_key();
		
		$db_builder
			->join($table_name)
			->on($table_name.'.'.$foreign_key, '=', $this->_orm->object_name().'.id')
			->on($table_name.'.lang_id', '=', DB::expr("'".I18n::lang()."'"))
		;
		
		foreach (array_keys($this->_config['columns']) as $column) {
			$db_builder
				->select(array($table_name.'.'.$column, $column))
			;
		}
	}
	
	public function table_name() {
		return isset($this->_config['table_name']) ? $this->_config['table_name'] : $this->_orm->table_name().'_i18n';
	}
	
	public function foreign_key() {
		return isset($this->_config['foreign_key']) ? $this->_config['foreign_key'] : $this->_orm->object_name().'_'.$this->_orm->primary_key();
	}
}