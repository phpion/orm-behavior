<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Base class for ORM object which can have several behaviors.
 * 
 * @package Kohana/ORM-Behavior
 * @author Michał "phpion" Płonka <michal@notifero.pl>
 */
abstract class Kohana_ORM_Behaviorable extends ORM {
	/**
	 * Behaviors array
	 * 
	 * @var array
	 */
	protected $_behaviors = array();
	
	/**
	 * Attachs behavior.
	 * 
	 * @param ORM_Behavior $behavior Behavior.
	 */
	public function attach_behavior(ORM_Behavior $behavior) {		
		$this->_behaviors[get_class($behavior)] = $behavior;
	}
	
	/**
	 * Detachs behavior.
	 * 
	 * @param ORM_Behavior $behavior Behavior.
	 */
	public function detach_behavior(ORM_Behavior $behavior) {
		$behavior = get_class($behavior);
		
		if ($this->has_behavior($behavior)) {
			unset($this->_behaviors[$behavior]);
		}
	}
	
	/**
	 * Checks if ORM has behavior.
	 * 
	 * @param string $behavior Behavior class name.
	 */
	public function has_behavior($behavior) {
		return isset($this->_behaviors[$behavior]);
	}
	
	/**
	 * Returns behavior.
	 * 
	 * @param string $behavior Behavior class name.
	 * @return string|NULL Behavior, NULL if not found.
	 */
	public function get_behavior($behavior) {
		if ($this->has_behavior($behavior)) {
			return $this->_behaviors[$behavior];
		}
		else {
			return NULL;
		}
	}
	
	/**
	 * Prepares the model database connection, determines the table name,
	 * and loads column information.
	 * 
	 * Creates behaviors objects using configuration array.
	 *
	 * @return void
	 */
	protected function _initialize() {
		parent::_initialize();
		
		foreach ($this->_behaviors as $key => $value) {
			$behavior = is_string($key) ? $key : $value;
			$config = is_array($value) ? $value : array();
			
			unset($this->_behaviors[$key]);
			$this->attach_behavior(new $behavior($this, $config));
		}
	}
	
	/**
	 * Returns database object.
	 * 
	 * @return Database Database object.
	 */
	public function db() {
		return $this->_db;
	}
	
	/**
	 * Returns database query builder.
	 * 
	 * @return Database_Query_Builder_Where Database query builder.
	 */
	public function db_builder() {
		return $this->_db_builder;
	}

	/**
	 * Loads a database result, either as a new record for this model, or as
	 * an iterator for multiple rows.
	 * 
	 * Runs trigger_before_find method on each enabled behavior.
	 *
	 * @chainable
	 * @param  bool $multiple Return an iterator or load a single row
	 * @return ORM|Database_Result
	 */
	protected function _load_result($multiple = FALSE) {
		$this->_call_behaviors('trigger_before_find');
		
		return parent::_load_result($multiple);
	}
	
	/**
	 * Insert a new object to the database
	 * 
	 * Runs tigger_before/after_create methods on each enabled behavior.
	 * 
	 * @param  Validation $validation Validation object
	 * @return ORM
	 */
	public function create(Validation $validation = NULL) {
		if ($this->_loaded) {
			throw new Kohana_Exception('Cannot create :model model because it is already loaded.', array(':model' => $this->_object_name));
		}
		
		if ($this->_call_behaviors('trigger_before_create') & ORM_Behavior::CHAIN_RETURN) {
			return $this;
		}
		
		$return = parent::create($validation);
		
		$this->_call_behaviors('trigger_after_create');
			
		return $return;
	}

	/**
	 * Updates a single record or multiple records
	 * 
	 * Runs tigger_before/after_update methods on each enabled behavior.
	 *
	 * @chainable
	 * @param  Validation $validation Validation object
	 * @return ORM
	 */
	public function update(Validation $validation = NULL) {
		if (!$this->_loaded) {
			throw new Kohana_Exception('Cannot update :model model because it is not loaded.', array(':model' => $this->_object_name));
		}
	
		if ($this->_call_behaviors('trigger_before_update') & ORM_Behavior::CHAIN_RETURN) {
			return $this;
		}
	
		$return = parent::update($validation);
	
		$this->_call_behaviors('trigger_after_update');
			
		return $return;
	}
	
	/**
	 * Deletes a single record while ignoring relationships.
	 * 
	 * Runs trigger_before/after_delete methods on each enabled behavior.
	 *
	 * @chainable
	 * @return ORM
	 */
	public function delete() {
		if (!$this->_loaded) {
			throw new Kohana_Exception('Cannot delete :model model because it is not loaded.', array(':model' => $this->_object_name));
		}
		
		if ($this->_call_behaviors('trigger_before_delete') & ORM_Behavior::CHAIN_RETURN) {
			return $this;
		}		
		
		$return = parent::delete();
		
		$this->_call_behaviors('trigger_after_delete');
			
		return $return;
	}
	
	/**
	 * Calls method on each enabled behavior.
	 * 
	 * @param string $method Method.
	 * @return int Behaviors chain execution status. 
	 */
	protected function _call_behaviors($method) {
		$method = (string)$method;
		
		$return = ORM_Behavior::CHAIN_CONTINUE;
		
		foreach ($this->_behaviors as $behavior) {
			if ($behavior->enabled() === TRUE && is_callable(array($behavior, $method))) {
				$status = call_user_func(array($behavior, $method));
				
				if ($status & ORM_Behavior::CHAIN_BREAK) {
					return $status;
				}
				else if ($status & ORM_Behavior::CHAIN_RETURN) {
					$return = $status;
				}
			}
		}
		
		return $return;
	}
	
	/**
	 * Delegates call for unexising method to behaviors.
	 * 
	 * @param string $method Method.
	 * @param array $args Arguments.
	 * @throws Kohana_Exception
	 * @return mixed Method execution result.
	 */
	public function __call($method, array $args) {
		foreach ($this->_behaviors as $behavior) {
			if ($behavior->enabled() === TRUE && is_callable(array($behavior, $method))) {
				return call_user_func_array(array($behavior, $method), $args);
			}
		}
		
		throw new Kohana_Exception('The :method method does not exist in the :class class', array(':method' => $method, ':class' => get_class($this)));
	}
}