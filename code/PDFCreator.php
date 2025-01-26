<?php

namespace TJBW\SS_DOMPDF;

use SilverStripe\View\SSViewer;

class PDFCreator
{
    public static function createPDF($template, $data, $fileName, $folder = 'PDF', $paperSize = 'A4', $paperOrientation = 'portrait')
    {
        $template = new SSViewer($template);

        $pdf = new SS_DOMPDF();

        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setHTML($template->process(null, $data));
        $pdf->set_paper($paperSize, $paperOrientation);

        $pdf->render();

        $file = $pdf->toFile($fileName, $folder);

        return $file;
    }

    public static function createPDFDebug($template, $data, $fileName, $paperSize = 'A4', $paperOrientation = 'portrait')
    {
        $template = new SSViewer($template);

        $pdf = new SS_DOMPDF();

        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setHTML($template->process(null, $data));
        $pdf->set_paper($paperSize, $paperOrientation);

        $pdf->render();
        $pdf->streamdebug($fileName);
    }

    public static function createPDFHTML($template, $data, $fileName, $paperSize = 'A4', $paperOrientation = 'portrait')
    {
        $template = new SSViewer($template);

        return $template->process(null, $data);
    }
}
