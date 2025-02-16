<?php

use App\Tools\PdfTool;

afterEach(fn() => Mockery::close());

it('returns a message when no PDF is loaded', function () {
    $pdfTool = new PdfTool();
    $result = $pdfTool('any query');
    expect($result)->toBe('No PDF document is currently loaded.');
});

it('loads a PDF and returns its content', function () {
    $expected = 'Fake PDF content';

    // When loadPdf() is called, Spatie\PdfToText\Pdf::getText() should return $expected.
    Mockery::mock('alias:Spatie\PdfToText\Pdf')
        ->shouldReceive('getText')
        ->once()
        ->with('dummy/path.pdf', '/opt/homebrew/bin/pdftotext')
        ->andReturn($expected);

    $pdfTool = new PdfTool();
    $pdfTool->loadPdf('dummy/path.pdf');

    $result = $pdfTool('any query');
    expect($result)->toBe($expected);
});
