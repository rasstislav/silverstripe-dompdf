# PDF Generation via DOMPDF Library

This is a fork of https://github.com/burnbright/silverstripe-dompdf for use with Composer.

Maintainer: Jeremy Shipman (jeremy@burnbright.net)

Makes use of: https://github.com/dompdf/dompdf 
Dompdf websites: http://dompdf.github.com/, http://pxd.me/dompdf/www/

Input:

 * HTML string (which could be rendered template)
 * HTML File
 
Output

 * PDF File location
 * SS File
 * PDF binary stream to browser

## Installation
>###Composer
* On the command line, cd into your sites root folder
* Run `composer require gdmedia/silverstripe-dompdf`
* Run dev/build?flush=all in your browser

>###Manually
* Download the module from https://github.com/guru-digital/silverstripe-dompdf/archive/master.zip
* Extract the files into your silverstripe root folder
* Run dev/build?flush=all in your browser


## Example usage

```php
	$pdf = new SS_DOMPDF();
	$pdf->setHTML($mydataobject->renderWith('MyTemplate'));
	$pdf->render();
	$pdf->toFile('mypdf.pdf');
```
	
## Debugging

The $pdf->streamdebug(); function is useful for quickly viewing pdfs, particularly
if your browser supports displaying pdfs, rather than downloading.

You can check your html before it is converted like this:

```php
	echo $mydataobject->renderWith('MyTemplate');die();
```
	
## Useful Tips

 * Use tables for layout if you get errors from floating divs.
 * See the [official dompdf website](http://pxd.me/dompdf/www/) for more info