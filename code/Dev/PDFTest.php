<?php

namespace TJBW\SS_DOMPDF\Dev;

use TJBW\SS_DOMPDF\PDFCreator;
use SilverStripe\Dev\BuildTask;

class PDFTest extends BuildTask
{
    protected $title = 'Create Test PDF';
    protected $description = 'Generates a test document in PDF format.';

    public function run($request)
    {
        $data = [
            'Title' => 'Test',
            'Version' => '1.0.0',
        ];

        $file = PDFCreator::createPDF('PdfTest', $data, 'test.pdf');

        echo <<<HTML
            <a href="{$file->AbsoluteLink()}" target="_blank">Generated PDF</a>
        HTML;
    }
}
