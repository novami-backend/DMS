<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Document Search - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .search-result-card {
            transition: all 0.3s;
            border-left: 4px solid #007bff;
        }
        .search-result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .highlight {
            background-color: #fff3cd;
            padding: 0.2em;
        }
        .search-filters {
            background-color: #f8f9fa;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3 p-0">
                <?= view('common/sidebar') ?>
            </div>

            <!-- Main Content -->
            <div class="p-0">
                <div class="main-content">
                    <!-- Header -->
                    <?= view('common/header', [
                        'pageTitle' => '<i class="fas fa-search me-2"></i>Advanced Document Search',
                        'pageDescription' => 'Search documents using advanced filters and full-text search'
                    ]) ?>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <a href="<?= base_url('documents') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Documents
                        </a>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#searchFilters">
                                <i class="fas fa-filter me-2"></i>Filters
                            </button>
                        </div>
                    </div>

                    <!-- Search Form -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="<?= base_url('search') ?>" class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Search Query</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" name="q" class="form-control" 
                                               value="<?= esc($searchTerm ?? '') ?>" 
                                               placeholder="Enter search terms..." required>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-2"></i>Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Search Filters (Collapsible) -->
                    <div class="collapse mb-4" id="searchFilters">
                        <div class="card search-filters">
                            <div class="card-body">
                                <h5 class="card-title">Search Filters</h5>
                                <form method="GET" action="<?= base_url('search') ?>" class="row g-3">
                                    <input type="hidden" name="q" value="<?= esc($searchTerm ?? '') ?>">
                                    <div class="col-md-4">
                                        <label class="form-label">Document Type</label>
                                        <select name="type_id" class="form-select">
                                            <option value="">All Types</option>
                                            <?php foreach ($documentTypes as $type): ?>
                                                <option value="<?= $type['id'] ?>" <?= (isset($filters['type_id']) && $filters['type_id'] == $type['id']) ? 'selected' : '' ?>>
                                                    <?= esc($type['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Department</label>
                                        <select name="department_id" class="form-select">
                                            <option value="">All Departments</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= $dept['id'] ?>" <?= (isset($filters['department_id']) && $filters['department_id'] == $dept['id']) ? 'selected' : '' ?>>
                                                    <?= esc($dept['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Created By</label>
                                        <select name="created_by" class="form-select">
                                            <option value="">All Users</option>
                                            <!-- This would be populated with users in a real implementation -->
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter me-2"></i>Apply Filters
                                        </button>
                                        <a href="<?= base_url('search') ?>" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>Clear Filters
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <?php if (isset($searchTerm) && !empty($searchTerm)): ?>
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>Search Results
                                    <span class="badge bg-light text-dark ms-2"><?= count($results) ?> found</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($results)): ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h4>No documents found</h4>
                                        <p class="text-muted">Try adjusting your search terms or filters</p>
                                    </div>
                                <?php else: ?>
                                    <div class="row">
                                        <?php foreach ($results as $result): ?>
                                            <div class="col-12 mb-3">
                                                <div class="card search-result-card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="flex-grow-1">
                                                                <h5 class="card-title">
                                                                    <a href="<?= base_url('documents/edit/' . $result['document_id']) ?>" class="text-decoration-none">
                                                                        <?= esc($result['document_title']) ?>
                                                                    </a>
                                                                </h5>
                                                                <div class="mb-2">
                                                                    <span class="badge bg-info me-2"><?= esc($result['type_name'] ?? 'N/A') ?></span>
                                                                    <span class="badge bg-secondary me-2"><?= esc($result['department_name'] ?? 'N/A') ?></span>
                                                                </div>
                                                                <p class="card-text text-muted">
                                                                    <?php 
                                                                    // Truncate and highlight search terms in content
                                                                    $content = substr(strip_tags($result['indexed_content'] ?? ''), 0, 200) . '...';
                                                                    if (!empty($searchTerm)) {
                                                                        $content = str_ireplace($searchTerm, '<span class="highlight">' . $searchTerm . '</span>', $content);
                                                                    }
                                                                    echo $content;
                                                                    ?>
                                                                </p>
                                                                <small class="text-muted">
                                                                    Indexed: <?= date('M d, Y H:i', strtotime($result['indexed_at'])) ?>
                                                                </small>
                                                            </div>
                                                            <div class="ms-3">
                                                                <div class="btn-group-vertical btn-group-sm">
                                                                    <a href="<?= base_url('documents/edit/' . $result['document_id']) ?>" class="btn btn-outline-primary" title="Edit">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <a href="<?= base_url('documents/history/' . $result['document_id']) ?>" class="btn btn-outline-info" title="History">
                                                                        <i class="fas fa-history"></i>
                                                                    </a>
                                                                    <a href="<?= base_url('documents/share/' . $result['document_id']) ?>" class="btn btn-outline-success" title="Share">
                                                                        <i class="fas fa-share-alt"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Welcome/Instructions -->
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-search fa-4x text-primary mb-4"></i>
                                <h3>Advanced Document Search</h3>
                                <p class="lead text-muted">
                                    Search through all your documents using powerful full-text search capabilities
                                </p>
                                <div class="row justify-content-center mt-4">
                                    <div class="col-md-8">
                                        <div class="text-start">
                                            <h5>Search Features:</h5>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success me-2"></i>Full-text search across document content</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Filter by document type and department</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Search by tags and keywords</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Relevance-based ranking</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
    <script>
        // Auto-focus search input when filters are shown
        document.getElementById('searchFilters').addEventListener('shown.bs.collapse', function () {
            document.querySelector('input[name="q"]').focus();
        });
    </script>
</body>
</html>