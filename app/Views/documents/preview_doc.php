<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Preview - <?= esc($document['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .preview-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .document-header {
            border-bottom: 3px solid #3498db;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .document-title {
            color: #2c3e50;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .metadata-table {
            width: 100%;
            margin-bottom: 30px;
        }

        .metadata-table td {
            padding: 10px;
            border: 1px solid #dee2e6;
        }

        .metadata-table td:first-child {
            font-weight: bold;
            background-color: #f8f9fa;
            width: 200px;
        }

        .document-content {
            line-height: 1.8;
            padding: 20px 0;
        }

        /* CKEditor content styles */
        .document-content table {
            border-collapse: collapse;
            width: 100%;
            margin: 1em 0;
        }

        .document-content table td,
        .document-content table th {
            border: 1px solid #bfbfbf;
            padding: 8px;
            min-width: 2em;
        }

        .document-content table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .document-content img {
            max-width: 100%;
            height: auto;
        }

        .document-content figure {
            margin: 1em 0;
        }

        .document-content figure.image {
            display: table;
            clear: both;
            text-align: center;
            margin: 1em auto;
        }

        .document-content figure.image img {
            display: block;
            margin: 0 auto;
        }

        .document-content figure.image.image-style-side {
            float: right;
            margin-left: 1.5em;
            max-width: 50%;
        }

        .document-content figure.image.image_resized {
            max-width: 100%;
            display: block;
            box-sizing: border-box;
        }

        .document-content figure.image.image_resized img {
            width: 100%;
        }

        .document-content .image-style-align-left {
            float: left;
            margin-right: 1.5em;
        }

        .document-content .image-style-align-center {
            margin-left: auto;
            margin-right: auto;
        }

        .document-content .image-style-align-right {
            float: right;
            margin-left: 1.5em;
        }

        .document-content blockquote {
            border-left: 5px solid #ccc;
            font-style: italic;
            margin-left: 0;
            margin-right: 0;
            overflow: hidden;
            padding-left: 1.5em;
            padding-right: 1.5em;
        }

        .document-content ul,
        .document-content ol {
            padding-left: 2em;
        }

        .document-content .text-tiny {
            font-size: 0.7em;
        }

        .document-content .text-small {
            font-size: 0.85em;
        }

        .document-content .text-big {
            font-size: 1.4em;
        }

        .document-content .text-huge {
            font-size: 1.8em;
        }

        /* Text alignment */
        .document-content .text-align-left {
            text-align: left !important;
        }

        .document-content .text-align-center {
            text-align: center !important;
        }

        .document-content .text-align-right {
            text-align: right !important;
        }

        .document-content .text-align-justify {
            text-align: justify !important;
        }

        .document-content p[style*="text-align"] {
            /* Preserve inline text-align styles */
        }

        .action-bar {
            position: sticky;
            top: 0;
            background: white;
            padding: 15px 0;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 20px;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        @media print {
            .action-bar {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="action-bar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="<?= base_url('documents/view/' . $document['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Document
                    </a>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-info me-2">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                    <a href="<?= base_url('documents/download-doc/' . $document['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Download Word
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4 mb-5">
        <div class="preview-container">
            <!-- Document Header -->
            <div class="document-header">
                <h1 class="document-title"><?= esc($document['title']) ?></h1>
            </div>

            <!-- Metadata -->
            <table class="metadata-table">
                <tr>
                    <td>Document Type:</td>
                    <td><?= esc($documentType['name'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <td>Department:</td>
                    <td><?= esc($department['name'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td><?= ucfirst($document['status']) ?></td>
                </tr>
                <?php if (isset($document['version'])): ?>
                    <tr>
                        <td>Version:</td>
                        <td><?= $document['version'] ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>Created:</td>
                    <td><?= date('F d, Y', strtotime($document['created_at'])) ?></td>
                </tr>
                <?php if ($document['effective_date']): ?>
                    <tr>
                        <td>Effective Date:</td>
                        <td><?= date('F d, Y', strtotime($document['effective_date'])) ?></td>
                    </tr>
                <?php endif; ?>
            </table>

            <hr>

            <!-- Document Content -->
            <div class="document-content">
                <?= $document['content'] ?>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>