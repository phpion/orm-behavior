<?php
class Behavior_Commentable extends ORM_Behavior {
	public function get_comments() {		
		return ORM::factory('comment')->get_for_object($this->_orm->pk(), $this->_orm->table_name());
	}
	
	public function save_comment(Model_Comment $comment) {
		$comment->set('object_id', $this->_orm->pk());
		$comment->set('object_type', $this->_orm->table_name());
		
		return $comment->save();
	}
	
	public function trigger_before_find() {
		$query = DB::select(DB::expr('COUNT(comments.id)'))
			->from('comments')
			->where('comments.object_id', '=', DB::expr('`'.$this->_orm->object_name().'`.`'.$this->_orm->primary_key().'`'))
			->where('comments.object_type', '=', $this->_orm->table_name())
			->where('comments.is_deleted', '=', 0)
		;
		
		$this->_orm->db_builder()
			->select(array($query, 'count_comments'))
		;
	}
}