<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'DMS - Document Management System' ?></title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <?= view('common/styles') ?>
  <?php if (isset($additionalStyles)): ?>
    <?= $additionalStyles ?>
  <?php endif; ?>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="p-0">
        <?= view('common/sidebar') ?>
      </div>

      <!-- Main Content -->
      <div class="p-0">
        <div class="main-content">
          <?= view('common/header') ?>
          
          <!-- Page Content -->
          <div class="flex-grow-1">
            <?= $this->renderSection('content') ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?= view('common/footer') ?>
  
  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <?= view('common/scripts') ?>
  <?php if (isset($additionalScripts)): ?>
    <?= $additionalScripts ?>
  <?php endif; ?>
</body>
</html>
