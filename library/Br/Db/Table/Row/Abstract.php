<?php
/**
* 
*/
abstract class Br_Db_Table_Row_Abstract extends Zend_Db_Table_Row
{
	protected $_validators = array();
	protected $_error;

	public function getValidator($name)
	{
		return array_key_exists($name, $this->_validators) ? $this->_validators : array();
	}
	
	/**
	 * Overwrite the save method to add automatic validation to model.
	 *
	 * If validation fail we return false, and add 
	 */
	public function save() 
	{
		if(!$this->isValid()) {
			return false;
		}
		return parent::save();
	}
	
	/**
	* Return validation messages for isValid function to use in View.
	*/
	public function getMessages()
	{
		
	}
	
	/**
	 * Valids the object against $_validators specified in array.
	 *
	 * If validation fails, method adds messages to $_error var.
	 *
	 * @return bool 
	 */
	public function isValid()
	{
		return true;
	}
}
