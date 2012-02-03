<?php
/**
 * Pdf is the base class providing the common features needed for other extension
 * classes
 *
 * @author Rinat Silnov
 * @version 1.0
 */
abstract class Pdf extends CComponent
{
	/**
	 * Output content of the PDF to the browser.
	 *
	 * @param string $name name of the file that will be outputed
	 */
	function output($name=null)
	{
		header('Content-Type: application/pdf');
		header("Content-Disposition: inline; filename=$name");
		echo $this->content;
	}

	/**
	 * Returns the content of PDF
	 * Child classes should override this method to provide the actual content
	 * of PDF
	 * @return string
	 */
	abstract function getContent();
}
