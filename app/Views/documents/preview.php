<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Preview - <?= esc($document['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .preview-container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .document-header {
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .document-title {
            color: #2c3e50;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .metadata-table {
            margin-bottom: 30px;
        }
        .metadata-table td {
            padding: 8px;
            border-bottom: 1px solid #e9ecef;
        }
        .metadata-table td:first-child {
            font-weight: 600;
            width: 200px;
            color: #495057;
        }
        .section-title {
            color: #007bff;
            font-size: 18px;
            font-weight: 600;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
        }
        .field-group {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        .field-value {
            color: #212529;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .action-buttons {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            text-align: center;
        }
        @media print {
            .action-buttons { display: none; }
            .preview-container { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <!-- Document Header -->
        <div class="document-header">
            <div class="document-title"><?= esc($document['title']) ?></div>
            <div class="text-muted">
                <i class="fas fa-file-alt me-2"></i><?= esc($documentType['name'] ?? 'N/A') ?>
            </div>
        </div>

        <!-- Metadata -->
        <table class="metadata-table table table-borderless">
            <tr>
                <td><i class="fas fa-building me-2"></i>Department:</td>
                <td><?= esc($department['name'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <td><i class="fas fa-toggle-on me-2"></i>Status:</td>
                <td><span class="badge bg-secondary"><?= ucfirst($document['status']) ?></span></td>
            </tr>
            <?php if (!empty($document['effective_date'])): ?>
            <tr>
                <td><i class="fas fa-calendar-check me-2"></i>Effective Date:</td>
                <td><?= date('F d, Y', strtotime($document['effective_date'])) ?></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($document['review_date'])): ?>
            <tr>
                <td><i class="fas fa-calendar-day me-2"></i>Review Date:</td>
                <td><?= date('F d, Y', strtotime($document['review_date'])) ?></td>
            </tr>
            <?php endif; ?>
        </table>

        <!-- Dynamic Form Data -->
        <?php if (!empty($fields)): ?>
            <?php foreach ($fields as $section => $sectionFields): ?>
                <div class="section-title"><?= esc($section) ?></div>
                <?php foreach ($sectionFields as $field): ?>
                    <div class="field-group">
                        <div class="field-label"><?= esc($field['field_label']) ?></div>
                        <div class="field-value">
                            <?php
                            $fieldName = $field['field_name'];
                            $value = $formData[$fieldName] ?? '';
                            
                            if ($field['field_type'] === 'table' && is_array($value)) {
                                // Render table data
                                $columns = json_decode($field['options'], true);
                                echo '<table class="table table-sm table-bordered">';
                                echo '<thead><tr>';
                                foreach ($columns as $col) {
                                    echo '<th>' . esc($col['label']) . '</th>';
                                }
                                echo '</tr></thead><tbody>';
                                foreach ($value as $row) {
                                    echo '<tr>';
                                    foreach ($columns as $col) {
                                        echo '<td>' . esc($row[$col['name']] ?? '') . '</td>';
                                    }
                                    echo '</tr>';
                                }
                                echo '</tbody></table>';
                            } elseif ($field['field_type'] === 'checkbox') {
                                echo $value ? '<i class="fas fa-check-square text-success"></i> Yes' : '<i class="far fa-square"></i> No';
                            } else {
                                echo esc($value ?: 'N/A');
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Content (if no template) -->
        <?php if (!empty($document['content']) && empty($fields)): ?>
            <div class="section-title">Content</div>
            <div class="field-value">
                <?= $document['content'] ?>
            </div>
        <?php endif; ?>

        <!-- Attachments Section -->
        <?php 
        $attachmentModel = new \App\Models\DocumentAttachment();
        $attachments = $attachmentModel->getDocumentAttachmentsWithUploaders($document['id']);
        ?>
        <?php if (!empty($attachments)): ?>
            <div class="section-title">Attachments</div>
            <div class="field-value">
                <div class="list-group">
                    <?php foreach ($attachments as $attachment): ?>
                        <a href="<?= base_url($attachment['file_path']) ?>" target="_blank" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <i class="<?= \App\Models\DocumentAttachment::getFileIcon($attachment['file_type']) ?> me-2"></i>
                                    <strong><?= esc($attachment['file_name']) ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?= \App\Models\DocumentAttachment::formatFileSize($attachment['file_size']) ?> 
                                        | Uploaded: <?= date('M d, Y', strtotime($attachment['created_at'])) ?>
                                    </small>
                                </div>
                                <i class="fas fa-download text-primary"></i>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>Print
            </button>
            <button onclick="exportPDF()" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Close
            </button>
        </div>
    </div>

    <script>
        function exportPDF() {
            window.print();
        }
    </script>
</body>
</html>
