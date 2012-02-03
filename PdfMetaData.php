<?php
/**
 * PdfMetaData represents the meta-data for a PDF form class
 * retrieved by the pdftk.
 *
 * @author Rinat Silnov
 * @version 1.0
 */
class PdfMetaData
{

	/**
	 * @var array PDF form fields
	 */
	public $fields;

	private $_pdf;

	/**
	 * Constructor.
	 * @param PdfFile $pdf the PDF file instance
	 */
	public function __construct(PdfFile $pdf)
	{
		$this->_pdf = $pdf;
		$pdftk = Pdftk::getInstance();
		$result = $pdftk->dumpDataFields($pdf);
		for($i = 0, $count = count($result); $i < $count; $i++) {
			$column = array();
			while($i < $count && '---' !== $result[$i]) {
				list($name, $value) = explode(':', $result[$i], 2);
				$column[trim($name)] = trim($value);
				$i++;
			}
			if (isset($column['FieldName']))
				$this->fields[$column['FieldName']] = $column;
		}
	}

}
