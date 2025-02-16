<?php

use App\Console\Commands\Chat;
use App\Tools\PdfTool;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Console\OutputStyle;
use EchoLabs\Prism\Text\Generator;

afterEach(fn() => Mockery::close());

/**
 * A test double for the Chat command that lets us inject a mocked PdfTool
 * and overrides exit behavior.
 */
class TestChatCommand extends Chat
{
    public function __construct(PdfTool $pdfTool)
    {
        parent::__construct();
        // Override the PdfTool instance set in the parent constructor.
        $this->pdfTool = $pdfTool;
    }

    // Override handle() to throw an exception instead of calling exit().
    public function handle(): void
    {
        if ($pdfPath = $this->argument('pdf_path')) {
            try {
                $this->pdfTool->loadPdf($pdfPath);
                $this->info('PDF loaded successfully.');
                // (Optionally, add the initial system message.)
            } catch (\Exception $e) {
                $this->error('Failed to load PDF: ' . $e->getMessage());
                // Instead of exit(1), throw an exception so we can catch it in our test.
                throw new Exception('Exit: 1', 1);
            }
        }

        // Continue with the rest of the command.
        $prism = $this->prismFactory();
        while (true) {
            $this->chat($prism);
        }
    }

    // Override chat() to break the infinite loop.
    public function chat(Generator $prism): void
    {
        throw new Exception('Break loop');
    }
}

it('exits with error if PDF fails to load', function () {
    // Create a mock PdfTool that throws an exception when loadPdf is called.
    $mockPdfTool = Mockery::mock(PdfTool::class);
    $mockPdfTool->shouldReceive('loadPdf')
        ->once()
        ->with('nonexistent.pdf')
        ->andThrow(new Exception('File not found'));

    // Create our test command instance, injecting the mock.
    $testCommand = new TestChatCommand($mockPdfTool);

    // Set up the command.
    $testCommand->setLaravel(app());
    $testCommand->setName('chat');

    // Use an input that provides the pdf_path argument.
    $input = new ArrayInput([
        'pdf_path' => 'nonexistent.pdf',
    ]);
    $testCommand->setInput($input);
    $testCommand->mergeApplicationDefinition();

    // Set up output wrapped in an OutputStyle instance.
    $output = new BufferedOutput();
    $style = new OutputStyle($input, $output);
    $testCommand->setOutput($style);

    // Run the command and catch the exception.
    try {
        $testCommand->run($input, $style);
        throw new Exception('Expected exception not thrown');
    } catch (Exception $e) {
        expect($e->getMessage())->toBe('Exit: 1');
    }

    $commandOutput = $output->fetch();
    expect($commandOutput)->toContain('Failed to load PDF: File not found');
});

it('loads PDF successfully and outputs the success message', function () {
    // Create a mock PdfTool that simulates successful PDF loading.
    $mockPdfTool = Mockery::mock(PdfTool::class);
    $mockPdfTool->shouldReceive('loadPdf')
        ->once()
        ->with('dummy.pdf')
        ->andReturn('Fake PDF content');

    // Create our test command instance, injecting the mock.
    $testCommand = new TestChatCommand($mockPdfTool);

    // Set up the command.
    $testCommand->setLaravel(app());
    $testCommand->setName('chat');

    $input = new ArrayInput([
        'pdf_path' => 'dummy.pdf',
    ]);
    $testCommand->setInput($input);
    $testCommand->mergeApplicationDefinition();

    $output = new BufferedOutput();
    $style = new OutputStyle($input, $output);
    $testCommand->setOutput($style);

    // Run the command. Since PDF loads successfully, the command outputs success
    // and then enters the loop, where our overridden chat() immediately throws.
    try {
        $testCommand->run($input, $style);
        throw new Exception('Expected exception not thrown');
    } catch (Exception $e) {
        expect($e->getMessage())->toBe('Break loop');
    }

    $commandOutput = $output->fetch();
    expect($commandOutput)->toContain('PDF loaded successfully.');
});
