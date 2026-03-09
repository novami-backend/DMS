# Required Libraries for Document Export

To enable PDF and Word document export functionality, you need to install the following libraries:

## Installation Commands

Run these commands in your project root directory:

```bash
# Install TCPDF for PDF generation
composer require tecnickcom/tcpdf

# Install PHPWord for Word document generation
composer require phpoffice/phpword
```

## Verification

After installation, verify the libraries are installed by checking your `composer.json` file. You should see:

```json
"require": {
    "tecnickcom/tcpdf": "^6.6",
    "phpoffice/phpword": "^1.1"
}
```

## Features Enabled

Once installed, users will be able to:
- Export documents as PDF with formatted content and metadata
- Export documents as Word (.docx) files with full formatting
- Access export buttons from the document view page

## Usage

From any document view page, users will see two export buttons:
- **Download as PDF** - Exports the document in PDF format
- **Download as Word** - Exports the document in DOCX format

Both exports include:
- Document title and metadata
- Document type and department information
- Status and version information
- Full document content with HTML formatting preserved
