<?php

use App\Http\Controllers\PdfController;

afterEach(fn() => Mockery::close());

it('returns PDF text from the controller', function () {
    $expected = 'Fake PDF text from controller';

    Mockery::mock('alias:Spatie\PdfToText\Pdf')
        ->shouldReceive('getText')
        ->once()
        ->andReturn($expected);

    $controller = new PdfController();
    $response = $controller->__invoke();

    expect($response)->toBe($expected);
});
