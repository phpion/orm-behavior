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
	 * @var ORM_Behaviorable
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
	 * @param ORM_Behaviorable $orm ORM object.
	 * @param array $config Configuration array.
	 */
	public function __construct(ORM_Behaviorable $orm, array $config = array()) {
		$this->orm($orm);
		$this->config($config);
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
	 * Checks if behavior is enabled.
	 *
	 * @return bool TRUE if behavior is enabled, FALSE otherwise.
	 */
	public function enabled() {
		return $this->_enabled === TRUE;
	}
	
	/**
	 * Gets or sets ORM object.
	 * 
	 * @param ORM_Behaviorable $orm ORM object.
	 * @return ORM_Behaviorable|ORM_Behavior
	 */
	public function orm(ORM_Behaviorable $orm = NULL) {
		if ($orm === NULL) {
			// Act as a getter
			return $this->_orm;
		}
		
		// Act as a setter
		$this->_orm = $orm;
		
		return $this;
	}
	
	/**
	 * Gets or sets configuration array
	 * 
	 * @param array $config Configuration array
	 * @param bool $override Override current configuration.
	 * @return array|ORM_Behavior
	 */
	public function config(array $config = NULL, $override = FALSE) {
		if ($config === NULL) {
			// Act as a getter
			return $this->_config;
		}
		
		// Act as a setter
		if ($override === TRUE) {
			$this->_config = $config;
		}
		else {
			$this->_config = array_merge($this->_config, $config);
		}
		
		return $this;
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