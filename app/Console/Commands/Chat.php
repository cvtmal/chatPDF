<?php

namespace App\Console\Commands;

use App\Tools\PdfTool;
use EchoLabs\Prism\Enums\Provider;
use EchoLabs\Prism\Prism;
use EchoLabs\Prism\Text\Generator;
use EchoLabs\Prism\ValueObjects\Messages\SystemMessage;
use EchoLabs\Prism\ValueObjects\Messages\UserMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Laravel\Prompts\Concerns\Colors;
use Laravel\Prompts\Themes\Default\Concerns\DrawsBoxes;

use function Laravel\Prompts\textarea;

class Chat extends Command
{
    use Colors;
    use DrawsBoxes;

    protected $signature = 'chat {pdf_path? : Path to the PDF file}';

    protected $description = 'AI Chat with PDF support using Prism tools';

    protected Collection $messages;

    protected PdfTool $pdfTool;

    public function __construct()
    {
        parent::__construct();
        $this->messages = collect();
        $this->pdfTool = new PdfTool;
    }

    public function handle(): void
    {
        // Load PDF if path provided
        if ($pdfPath = $this->argument('pdf_path')) {
            try {
                $this->pdfTool->loadPdf($pdfPath);
                $this->info('PDF loaded successfully.');

                // Add initial system message
                $this->messages->push(new SystemMessage(
                    'You are an AI assistant that helps users understand PDF documents. '.
                    'Use the pdf_query tool to access the PDF content when answering questions. '.
                    'Always base your answers on the actual PDF content. '.
                    'When you need to reference the PDF content, use the pdf_query tool with a relevant query.'
                ));
            } catch (\Exception $e) {
                $this->error('Failed to load PDF: '.$e->getMessage());
                exit(1);
            }
        }

        $prism = $this->prismFactory();

        while (true) {
            $this->chat($prism);
        }
    }

    protected function prismFactory(): Generator
    {
        return Prism::text()
            ->withTools([$this->pdfTool])
            ->withMaxSteps(5)
            ->using(Provider::OpenAI, 'gpt-3.5-turbo');
    }

    private function chat(Generator $prism): void
    {
        $message = textarea('Enter your question about the PDF document');
        $this->messages->push(new UserMessage($message));

        $response = $prism
            ->withMessages($this->messages->toArray())
            ->generate();

        // Display tool usage info for debugging
        if ($response->steps) {
            foreach ($response->steps as $step) {
                if ($step->toolCalls) {
                    foreach ($step->toolCalls as $toolCall) {
                        $this->info('Tool used: '.$toolCall->name);
                        $this->info('Arguments: '.json_encode($toolCall->arguments()));
                    }
                }
            }
        }

        $this->messages = $this->messages->merge($response->responseMessages);
        $this->box('Response', wordwrap($response->text, 60), color: 'magenta');
    }
}
