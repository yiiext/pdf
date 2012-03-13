PDF
===

Расширение PDF позволяет выполнять некоторые действия с PDF-документами, такие как
заполнение PDF-форм, слияние нескольких PDF-документов в один или вывод содержимого
PDF-документа непосредственно в браузер.

Установка и настройка
--------------------------

Поместите файлы расширения в директорию 'extensions' Вашего приложения.
И добавьте следующую строку в секцию 'import' файла конфигурации:

~~~
'ext.pdf.*'
~~~

Убедитесь, что у Вас установлена утилита [pdftk](http://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/).
Вы можете указать путь к исполняемому файлу pdftk используя переменную Pdftk::$path.
По умолчанию используются пути 'pdftk' и '/usr/bin/pdftk'.

Использование
-----

Если вы хотите вывести содержимое PDF-документа, используйте следующий код:

~~~
$pdf = new PdfFile('/home/user/mypdf.pdf');
$pdf->output();
~~~

Для того, чтобы изменить имя выводимого документа, добавьте аргумент при вызове метода: 

~~~
$pdf = new PdfFile('/home/user/mypdf.pdf');
$pdf->output('new_pdf_name.pdf');
~~~

Если Вам нужно заполнить PDF-форму и вывести результаты в браузер, Вы должны расширить класс PdfForm:

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

Теперь Вы можете использовать расширенный класс в Вашем контроллере следующим образом:

~~~
$pdfForm = new TestPdfForm;
$pdfForm->attributes = array('field1' => 'value1', 'field2' => 'value2', 'checkbox' => 'Yes');
if (false !== ($filledPdf = $pdfForm->fill()))
	$filledPdf->output('filledPdfForm.pdf');
~~~

Вы также можете объединить несколько PDF-документов в один:

~~~
$pdf1 = new PdfFile('/home/user/pdf1.pdf');
$pdf2 = new PdfFile('/home/user/pdf2.pdf');
$pdf3 = new PdfFile('/home/user/pdf3.pdf');
$mergedPdf = $pdf1->merge($pdf2, $pdf3);
if ($mergedPdf instanceOf Pdf)
	$mergedPdf->output('out.pdf');
~~~

Вы даже можете объединить PDF-документ с самим собой:

~~~
$pdf = new PdfFile('/home/user/pdf.pdf');
$mergedPdf = $pdf->merge($pdf);
if ($mergedPdf instanceOf Pdf)
	$mergedPdf->output('out.pdf');
~~~

Можно применять один PDF-документ в качестве фона для другого:

~~~
$pdf = new PdfFile('/home/user/in.pdf');
$pdf->applyBackground(new PdfFile('/home/user/back.pdf'))->output('pdf_with_watermark');
~~~

Авторы
-------

Расширение предоставлено для Вас [CleverTech](http://clevertech.biz/).
