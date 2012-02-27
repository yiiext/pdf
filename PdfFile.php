<?php
/**
 * PdfFile represents an existing pdf file
 *
 * @author Rinat Silnov
 * @version 1.0
 */
class PdfFile extends Pdf
{
	/**
	 * @var string absolute path to the PDF file
	 */
	protected $file;

	/**
	 * Constructor.
	 * @param string $file absolute path to the PDF file
	 * @throws CException if the PDF file does not exist
	 */
	function __construct($file)
	{
		if (!file_exists($file))
			throw new CException("File $file not exists");
		$this->file = $file;
	}

	/**
	 * Output content of PDF to the browser.
	 * @param string $name Name of the file.
	 * If name is not specified basename of current PDF file will be used.
	 * @return void
	 * @see file
	 */
	function output($name = null)
	{
		$name = $name ?: str_replace('"', '', basename($this->file));
		return parent::output($name);
	}

	/**
	 * Return PdfForm object for this pdf file
	 * @return PdfForm the form instance
	 */
	function getForm()
	{
		return new PdfForm($this);
	}

	/**
	 * Returns the content of the pdf
	 * @return string
	 */
	function getContent()
	{
		return file_get_content($this->file);
	}

	/**
	 * Add few files to the end of this PDF
	 * @param variable lengths argument list of PdfFile objects
	 * @throws CException if argument list contains not PdfFile values
	 * @return PdfString an instance of PdfString with merged PDF files
	 * on success, false on failure
	 */
	function merge()
	{
		foreach(func_get_args() as $pdf)
			if (!($pdf instanceOf PdfFile))
				throw new CException('All arguments for PdfFile::merge should be an instance of PdfFile');
		$pdftk = Pdftk::getInstance();
		$result = $pdftk->cat(array_merge(array($this),func_get_args()));
		return $result ? new PdfString($result) : false;
	}

	/**
	 * Apply another PDF as background for each page of current PDF
	 * @param PdfFile $back an instance of PdfFile which will be used as a background
	 * @return PdfString an instance of PdfString with applied background
	 * on success, false on failure
	 */
	function applyBackground(PdfFile $back)
	{
		$pdftk = Pdftk::getInstance();
		$result = $pdftk->background($this, $back);
		return $result ? new PdfString($result) : false;
	}

	/**
	 * String magic method
	 * @return string the absolute path to the current pdf file
	 * @see file
	 */
	function __toString()
	{
		return $this->file;
	}
}
