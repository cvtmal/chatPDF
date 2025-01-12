<?php

namespace App\Tools;

use EchoLabs\Prism\Tool;
use Spatie\PdfToText\Pdf;

class PdfTool extends Tool
{
    private ?string $pdfContent = null;
    private string $spatiePath = '/opt/homebrew/bin/pdftotext';

    public function __construct()
    {
        $this
            ->as('pdf_query')
            ->for('Query the loaded PDF document content')
            ->withStringParameter('query', 'The specific information to look for in the PDF')
            ->using($this);
    }

    public function loadPdf(string $path): void
    {
        $this->pdfContent = Pdf::getText($path, $this->spatiePath);
    }

    public function __invoke(string $query): string
    {
        if (!$this->pdfContent) {
            return "No PDF document is currently loaded.";
        }

        // Return a context-relevant portion of the PDF content
        // You might want to implement more sophisticated search/context extraction here
        return $this->pdfContent;
    }
}