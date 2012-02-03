<?php
/**
 * PdfString represents a PDF string
 *
 * @author Rinat Silnov
 * @version 1.0
 */
class PdfString extends Pdf
{
	/**
	 * @var string a content of the PDF
	 */
	protected $content;

	/**
	 * Constructor
	 * @param $string content
	 */
	function __construct($string)
	{
		$this->content = (string)$string;
	}

	/**
	 * Returns the content of the PDF
	 * @return string
	 */
	function getContent()
	{
		return $this->content;
	}
}
