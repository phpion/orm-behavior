<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Behavior class for ORM object(s).
 *  
 * @package Kohana/ORM-Behavior
 * @author Michał "phpion" Płonka <michal@notifero.pl>
 */
abstract class Kohana_ORM_Behavior {
	/**
	 * ORM object.
	 * 
	 * @var ORM
	 */
	protected $_orm;
	
	/**
	 * Configuration array.
	 * 
	 * @var array
	 */
	protected $_config = array();
	
	/**
	 * Behavior is enabled or disabled.
	 * 
	 * @var bool
	 */
	protected $_enabled = TRUE;
	
	/**
	 * Constructor.
	 * 
	 * @param ORM $orm ORM object.
	 * @param array $config Configuration array.
	 */
	public function __construct(ORM $orm, array $config = array()) {
		$this->set_orm($orm);
		$this->set_config($config);
	}
	
	/**
	 * Trigger run before finding object(s).
	 */
	public function trigger_before_find() {
	
	}
	
	/**
	 * Trigger run before creating object.
	 */
	public function trigger_before_create() {
		
	}
	
	/**
	 * Trigger run after creating object.
	 */
	public function trigger_after_create() {
		
	}
	
	/**
	 * Trigger run before updating object.
	 */
	public function trigger_before_update() {
		
	}
	
	/**
	 * Trigger run after updating object.
	 */
	public function trigger_after_update() {
		
	}
	
	/**
	 * Trigger run before deleting object.
	 */
	public function trigger_before_delete() {
		
	}
	
	/**
	 * Trigger run after deleting object.
	 */
	public function trigger_after_delete() {
	
	}
	
	/**
	 * Enables behavior.
	 * 
	 * @return ORM_Behavior $this
	 */
	public function enable() {
		$this->_enabled = TRUE;
		
		return $this;
	}

	/**
	 * Disables behavior.
	 *
	 * @return ORM_Behavior $this
	 */
	public function disable() {
		$this->_enabled = FALSE;
		
		return $this;
	}
	
	/**
	 * Sets ORM object.
	 * 
	 * @param ORM $orm ORM object.
	 * @return ORM_Behavior $this
	 */
	public function set_orm(ORM $orm) {
		$this->_orm = $orm;
		
		return $this;
	}
	
	/**
	 * Returns ORM object.
	 * 
	 * @return ORM ORM object.
	 */
	public function get_orm() {
		return $this->_orm;
	}
	
	/**
	 * Sets configuration array
	 * 
	 * @param array $config Configuration array
	 * @return ORM_Behavior $this
	 */
	public function set_config(array $config) {
		$this->_config = $config;
		
		return $this;
	}
	
	/**
	 * Returns configuration array.
	 * 
	 * @return array Configuration array.
	 */
	public function get_config() {
		return $this->_config;
	}
	
	/**
	 * Checks if behavior is enabled.
	 * 
	 * @return bool TRUE if behavior is enabled, FALSE otherwise.
	 */
	public function enabled() {
		return $this->_enabled === TRUE;
	}
	
	/**
	 * Continue behaviors chain execution.
	 * 
	 * @var int
	 */
	const CHAIN_CONTINUE = 1;
	
	/**
	 * Break behaviors chain execution and allows to execute main code.
	 * 
	 * @var int
	 */
	const CHAIN_BREAK = 2;
	
	/**
	 * Return current ORM object after all behaviors execution.
	 * 
	 * @var int
	 */
	const CHAIN_RETURN = 4;
}