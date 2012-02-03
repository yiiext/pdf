PDF
===

PDF extension is intended to provide features for PDF manipulation, such as
filling PDF forms, merging PDF files together or outputting PDF to the browser
directly.

Installing and Configuring
--------------------------

Unpack extension files into extensions directory of your application. Add the following
to includes list in the main config:

~~~
'ext.pdf.*'
~~~

Make sure you have [pdftk](http://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/)
installed. You can specify path to the pdftk console command using Pdftk::$path variable.
By default it checks 'pdftk' and '/usr/bin/pdftk' paths.

Usage
-----

If you want to output a PDF file to the browser you can use following code:

~~~
$pdf = new PdfFile('/home/user/mypdf.pdf');
$pdf->output();
~~~

If you want to change the name of the file outputted you can add an argument
to the output method:

~~~
$pdf = new PdfFile('/home/user/mypdf.pdf');
$pdf->output('new_pdf_name.pdf');
~~~

If you need to fill out a PDF form and output result to the browser you should
extend PdfForm:

~~~
TestPdfForm extends PdfForm
{

	function filePath()
	{
		return '/home/user/testPdfForm.pdf';
	}

	function rules()
	{
		return array(
			array('field1, field2, checkbox', 'safe'),
		);
	}

}
~~~

And now you can use it in your controller this way:

~~~
$pdfForm = new TestPdfForm;
$pdfForm->attributes = array('field1' => 'value1', 'field2' => 'value2', 'checkbox' => 'Yes');
if (false !== ($filledPdf = $pdfForm->fill()))
	$filledPdf->output('filledPdfForm.pdf');
~~~

You can also merge some PDF files together:

~~~
$pdf1 = new PdfFile('/home/user/pdf1.pdf');
$pdf2 = new PdfFile('/home/user/pdf2.pdf');
$pdf3 = new PdfFile('/home/user/pdf3.pdf');
$mergedPdf = $pdf1->merge($pdf2, $pdf3);
if ($mergedPdf instanceOf Pdf)
	$mergedPdf->output('out.pdf');
~~~

You can even merge PDF with itself

~~~
$pdf = new PdfFile('/home/user/pdf.pdf');
$mergedPdf = $pdf->merge($pdf);
if ($mergedPdf instanceOf Pdf)
	$mergedPdf->output('out.pdf');
~~~

Credits
-------

This extension is brought to you by [CleverTech](http://clevertech.biz/).