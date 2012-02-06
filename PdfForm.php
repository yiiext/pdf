<?php
/**
 * PdfForm extends CModel and provides the features needed to fill a PDF form
 *
 * @author Rinat Silnov
 * @version 1.0
 */
abstract class PdfForm extends CModel
{

	private $_attributes = array();
	private $_md;
	private $_pdf;

	/**
	 * Returns the absolute path of the associated PDF file.
	 * @return string the path to the PDF form
	 */
	abstract public function filePath();

	/**
	 * Constructor.
	 * @param string $scenario name of the scenario that this model is used in.
	 * See {@link CModel::scenario} on how scenario is used by models.
	 * @see getScenario
	 */
	function __construct($scenario='')
	{
		$this->setScenario($scenario);
		$this->_pdf = new PdfFile($this->filePath());
		$this->_md = new PdfMetaData($this->_pdf);
		$this->init();
		$this->attachBehaviors($this->behaviors());
		$this->afterConstruct();
	}

	/**
	 * Initializes this model.
	 * This method is invoked when an PDF form instance is newly created and has
	 * its {@link scenario} set.
	 * You may override this method to provide code that is needed to initialize the model (e.g. setting
	 * initial property values.)
	 */
	public function init()
	{

	}

	/**
	 * Returns the list of all attribute names of the model.
	 * This would return all field names of the PDF file associated with this PdfForm class.
	 * @return array list of attribute names.
	 */
	public function attributeNames()
	{
		return array_keys($this->getMetaData()->fields);
	}

	/**
	 * Fill the PDF form
	 * @param boolean $runValidation whether to perform validation before filling the form.
	 * If the validation fails, the data will not be filled in.
	 * @param array $attributes list of attributes that need to be saved. Defaults to null,
	 * meaning all attributes that are loaded from PDF meta data will be filled.
	 * @return PdfString instance of filled form on sucess false on failure
	 */
	public function fill($runValidation=true, $attributes=null)
	{
		if (!$runValidation || $this->validate($attributes)) {
			$pdftk = Pdftk::getInstance();
			if (false !== ($result = $pdftk->fillForm($this, $this->getFdf($attributes)))) {
				return new PdfString($result);
			}
		}
		return false;
	}

	/**
	 * Get FDF string for filling PDF form
	 * @param array $attributes the attributes that will be used for PDF filling
	 * @throws CException if the field with this name not found in the PDF form
	 * @return string a FDF string with data
	 */
	protected function getFdf(array $attributes = null)
	{
		$fdf = "%FDF-1.2\n%âãÏÓ\n1 0 obj\n<</FDF << /Fields [ ";
		foreach ($this->getAttributes($attributes) as $attribute => $val) {
			if (!array_key_exists($attribute, $this->getMetaData()->fields))
				throw new CException("There is no field '$field' in $this->_pdf file");
			$fdf .= '<</V(' . trim(self::escapeValue($val)) . ')/T(' . $attribute . ')>>';
		}
		$fdf .= "]>>>>\nendobj\ntrailer\n<</Root 1 0 R>>\n%%EOF";
		return $fdf;
	}

	/**
	 * Escape PDF form value
	 *
	 * @param string $str
	 * @return string
	 */
	static function escapeValue($str)
	{
		$str = (string) $str;
		$result = '';
		for ($i = 0, $strLen = strlen($str); $i < $strLen; ++$i) {
			if (ord($str{$i}) == 0x28 || ord($str{$i}) == 0x29 || ord($str{$i}) == 0x5c) {
				$result .= chr(0x5c) . $str{$i};
			} else if (ord($str{$i}) < 32 || 126 < ord($str{$i})) {
				$result .= sprintf("\\%03o", ord($str{$i}));
			} else {
				$result .= $str{$i};
			}
		}
		return $result;
	}

	/**
	 * Returns all column attribute values.
	 * @param mixed $names names of attributes whose value needs to be returned.
	 * If this is true (default), then all attribute values will be returned, including
	 * those that are not loaded from the PDF Meta Data (null will be returned for those attributes).
	 * If this is null, all attributes except those that are not loaded from PDF meta data will be returned.
	 * @return array attribute values indexed by attribute names.
	 */
	public function getAttributes($names=true)
	{
		$attributes = $this->_attributes;
		foreach ($this->getMetaData()->fields as $name => $value) {
			if (property_exists($this, $name))
				$attributes[$name] = $this->$name;
			else if ($names === true && !isset($attributes[$name]))
				$attributes[$name] = null;
		}
		if (is_array($names)) {
			$attrs = array();
			foreach ($names as $name) {
				if (property_exists($this, $name))
					$attrs[$name] = $this->$name;
				else
					$attrs[$name] = isset($attributes[$name]) ? $attributes[$name] : null;
			}
			return $attrs;
		}
		else
			return $attributes;
	}

	/**
	 * String magic method
	 * @return string the absolute path to the current pdf form
	 * @see file
	 */
	function __toString()
	{
		return (string) $this->_pdf;
	}

	/**
	 * PHP getter magic method.
	 * This method is overridden so that the PDF form's attributes can be accessed like properties.
	 * @param string $name property name
	 * @return mixed property value
	 * @see getAttribute
	 */
	public function __get($name)
	{
		if (isset($this->_attributes[$name]))
			return $this->_attributes[$name];
		else if (isset($this->getMetaData()->fields[$name]))
			return null;
		else
			return parent::__get($name);
	}

	/**
	 * PHP setter magic method.
	 * This method is overridden so that model attributes can be accessed like properties.
	 * @param string $name property name
	 * @param mixed $value property value
	 */
	public function __set($name, $value)
	{
		if ($this->setAttribute($name, $value) === false)
			parent::__set($name, $value);
	}

	/**
	 * Sets the named attribute value.
	 * You may also use $this->AttributeName to set the attribute value.
	 * @param string $name the attribute name
	 * @param mixed $value the attribute value.
	 * @return boolean whether the attribute exists and the assignment is conducted successfully
	 */
	public function setAttribute($name, $value)
	{
		if (property_exists($this, $name))
			$this->$name = $value;
		else if (isset($this->getMetaData()->fields[$name]))
			$this->_attributes[$name] = $value;
		else
			return false;
	}

	/**
	 * Returns the meta-data for this model
	 * @return PdfMetaData the meta for this PdfForm class.
	 */
	function getMetaData()
	{
		return $this->_md;
	}

}
