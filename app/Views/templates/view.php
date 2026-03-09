<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Template - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .field-item {
            border-left: 3px solid #0d6efd;
            transition: all 0.3s;
        }

        .field-item:hover {
            background-color: #f8f9fa;
        }

        /* Compact table styling for templates */
        iframe {
            display: block;
        }

        /* Print styles for compact tables */
        @media print {
            table {
                border-collapse: collapse;
            }

            table td,
            table th {
                padding: 2px 4px !important;
                line-height: 1 !important;
                height: auto !important;
                min-height: auto !important;
            }

            table tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-md-3 p-0">
                <?= view('common/sidebar') ?>
            </div>

            <div class="p-0">
                <div class="main-content">
                    <?= view('common/header', [
                        'pageTitle' => '<i class="fas fa-file-code me-2"></i>' . $pageTitle,
                        'pageDescription' => $pageDescription
                    ]) ?>

                    <div class="d-flex justify-content-between mb-3">
                        <a href="<?= base_url('templates') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Templates
                        </a>
                        <a href="<?= base_url('templates/edit/' . $template['id']) ?>" class="btn btn-outline-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>

                    <div class="row">
                        <!-- Template Info -->
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <!-- <div class="card-header">
                                    <h5 class="mb-0">Template for - <?= esc($template['name']) ?></h5>
                                </div> -->
                                <div class="card-body">
                                    <div class="mb-3">
                                        <!-- <label class="form-label">PDF Layout Template</label> -->
                                        <!-- <div class="template-preview">
                                            <?= $template['layout_template'] ?>
                                        </div> -->
                                        <?php
                                        // Inject compact CSS into the template for better preview
                                        $templateWithCSS = $template['layout_template'];
                                        $compactCSS = '<style>
                                            table { border-collapse: collapse; }
                                            table td, table th { 
                                                padding: 2px 4px !important; 
                                                line-height: 1 !important; 
                                                height: auto !important; 
                                                min-height: auto !important;
                                                vertical-align: top;
                                            }
                                            body { margin: 0; padding: 10px; font-size: 12px; }
                                            * { margin: 0; padding: 0; }
                                        </style>';
                                        
                                        // Insert CSS at the beginning if there's a head tag, otherwise prepend it
                                        if (strpos($templateWithCSS, '</head>') !== false) {
                                            $templateWithCSS = str_replace('</head>', $compactCSS . '</head>', $templateWithCSS);
                                        } else {
                                            $templateWithCSS = $compactCSS . $templateWithCSS;
                                        }
                                        ?>
                                        <iframe
                                            srcdoc="<?= htmlspecialchars($templateWithCSS) ?>"
                                            width="100%"
                                            height="1000px"
                                            style="border: 1px solid #ccc;">
                                        </iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
</body>

</html>