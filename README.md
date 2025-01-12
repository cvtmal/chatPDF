# AI PDF Chatbot

This repository contains a Laravel application that leverages [Laravel Prism](https://github.com/nunomaduro/prism) and [Spatie's PDF-to-Text](https://github.com/spatie/pdf-to-text) packages to create an AI chatbot capable of answering questions about PDF documents.

## Features

- **PDF Parsing**: Extract text from PDFs using Spatie's pdf-to-text library.
- **AI Chat Integration**: Use Laravel Prism to power AI-driven question-answering capabilities.

## Prerequisites

Before you begin, ensure you have the following installed:

- PHP 8.2 or higher
- Composer
- Laravel ^11.31
- A PDF parsing tool (default: `pdftotext`)

> **Note**: For PDF parsing, `pdftotext` must be installed on your server. [Installation Guide](https://github.com/spatie/pdf-to-text#requirements)

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/cvtmal/chatPDF
   cd chatPDF
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install && npm run dev
   ```

3. **Set up environment variables**:
   Copy `.env.example` to `.env` and configure your database and other settings:
   ```bash
   cp .env.example .env
   ```

   Update the following variables in the `.env` file:
   ```env
   OPENAI_URL=
   OPENAI_API_KEY=
   OPENAI_ORGANIZATION=
   ANTHROPIC_API_KEY=
   ANTHROPIC_API_VERSION=
   OLLAMA_URL=
   MISTRAL_API_KEY=
   MISTRAL_URL=
   GROQ_API_KEY=
   GROQ_URL=
   XAI_API_KEY=
   XAI_URL=
   GEMINI_API_KEY=
   GEMINI_URL=
   SERPS_API_KEY=
   ```

4. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

5. **Run migrations**:
   ```bash
   php artisan migrate
   ```

6. **Start the application**:
   ```bash
   php artisan serve
   ```

   Visit the application at `http://localhost:8000`.

## Usage

1. Upload a PDF document using the interface.
2. Ask questions related to the uploaded PDF.
3. The chatbot will provide AI-driven answers based on the content of the PDF.

## Configuration

### Prism AI
Ensure that your Laravel Prism configuration is set up correctly. Refer to the [Laravel Prism Documentation](https://github.com/nunomaduro/prism) for setup instructions.

### Spatie PDF-to-Text
The package uses the `pdftotext` binary to extract text from PDFs. Verify that `pdftotext` is correctly installed on your system and accessible in the PATH.

## Testing

Run the test suite to ensure everything is working as expected:
```bash
php artisan test
```

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a feature branch.
3. Commit your changes.
4. Push to the branch.
5. Open a pull request.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Credits

- [Laravel Prism](https://github.com/nunomaduro/prism)
- [Spatie PDF-to-Text](https://github.com/spatie/pdf-to-text)

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
