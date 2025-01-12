<?php

namespace App\Http\Controllers;

use Spatie\PdfToText\Pdf;

class PdfController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): string
    {
        $path = storage_path('app/pdf/andreavalle.pdf');
        $spatie = '/opt/homebrew/bin/pdftotext';

        return PDF::getText($path, $spatie);
    }
}
