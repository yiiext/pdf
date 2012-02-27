<?php
/**
 * Pdftk is a singleton wrapper on pdftk (the PDF toolkit)
 *
 * @author Rinat Silnov
 * @version 1.0
 */
class Pdftk
{
	private static $_instance;

	/**
	 * @var string a console command for pdftk
	 */
	protected $pdftk;

	/**
	 * @var string possible comma separated paths to the pdftk console command
	 */
	public static $path = 'pdftk, /usr/bin/pdftk';

	/**
	 * Constructor. Check if pdftk installed
	 *
	 * @throws CException if the pdftk is not found
	 */
	private function __construct()
	{
		foreach(explode(',', self::$path) as $pdftk) {
			exec("$pdftk --version", $data, $return);
			if (0 === $return) {
				$this->pdftk = $pdftk;
				return;
			}
		}
		throw new CException('Pdftk not found');
	}

	/**
	 * Returns the pdftk singleton.
	 * @return Pdftk the Pdftk singleton.
	 */
	public static function getInstance()
	{
		if (empty(self::$_instance))
			self::$_instance = new Pdftk;
		return self::$_instance;
	}

	/**
	 * Get dump of data fields information of pdf form
	 * @param PdfFile $pdf an instance of PdfFile
	 * @return array of strings returned by pdftk dump_data_fields execution.
	 */
	public function dumpDataFields(PdfFile $pdf)
	{
		exec("{$this->pdftk} $pdf dump_data_fields", $data, $return);
		return $data;
	}

	/**
	 * Fill the pdf form with FDF data
	 * @param PdfForm $pdfForm an instance of PdfForm which will be filled
	 * @param string $fdf string of FDF data that will be used for filling pdf form
	 * @return string of filled pdf on success, false on failure
	 */
	public function fillForm(PdfForm $pdfForm, $fdf)
	{
		$descriptorspec = array(
		   0 => array("pipe", "r"),
		   1 => array("pipe", "w"),
		);

		$process = proc_open("{$this->pdftk} $pdfForm fill_form - output -", $descriptorspec, $pipes, '/tmp');

		if (!is_resource($process))
			return false;
		fwrite($pipes[0], $fdf);
		fclose($pipes[0]);

		$content = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		return -1 !== proc_close($process) ? $content : false;
	}

	/**
	 * Catenate array of pdf files
	 * @param array $files an array of pdf files which will be catenated
	 * @return string of merged pdf on success, false on failure
	 */
	public function cat(array $files)
	{
		$descriptorspec = array(1 => array("pipe", "r"));
		$files = implode(' ', $files);
		if (false === ($handle = popen("{$this->pdftk} $files cat output -", 'r')))
			return false;
		$content = stream_get_contents($handle);
		return -1 !== pclose($handle) ? $content : false;
	}

	/**
	 * Applies  a  PDF watermark to the background of a single input PDF.
	 * @param PdfFile $in an instance of PdfFile the background will be applied to
	 * @param PdfFile $back an instance of PdfFile which will be used as a background
	 * @return PdfString an instance of PdfString with applied background
	 * on success, false on failure
	 */
	public function background(PdfFile $in, PdfFile $back)
	{
		if (false === ($handle = popen("{$this->pdftk} $in background $back output -", 'r')))
			return false;
		$content = stream_get_contents($handle);
		return -1 !== pclose($handle) ? $content : false;
	}
}
