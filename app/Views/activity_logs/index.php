<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .log-login {
            background-color: #d1ecf1;
        }

        .log-create {
            background-color: #d4edda;
        }

        .log-update {
            background-color: #fff3cd;
        }

        .log-delete {
            background-color: #f8d7da;
        }
    </style>
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
                    <!-- Header -->
                    <?= view('common/header', [
                        'pageTitle' => '<i class="fas fa-history me-2"></i>Activity Logs',
                        'pageDescription' => 'System activity and user actions'
                    ]) ?>

                    <!-- Logs Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <input type="text" id="filterUser" class="form-control" placeholder="Filter by User">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" id="filterAction" class="form-control" placeholder="Filter by Action">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" id="minDate" class="form-control" placeholder="From Date">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" id="maxDate" class="form-control" placeholder="To Date">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>User</th>
                                            <th>Action</th>
                                            <th>Details</th>
                                            <th>IP Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logs as $log): ?>
                                            <tr class="log-<?= strtolower(explode(' ', $log['action'])[0]) ?>">
                                                <td><?= date('M j, Y H:i:s', strtotime($log['timestamp'])) ?></td>
                                                <td><?= esc($log['name']) ?></td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?= esc($log['action']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($log['details']): ?>
                                                        <small class="text-muted"><?= esc($log['details']) ?></small>
                                                    <?php else: ?>
                                                        <span class="text-muted">No details</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= esc($log['ip_address']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
    <script>
        $(document).ready(function() {
            var table = $('.table').DataTable({
                order: [
                    [0, 'desc']
                ],
                pageLength: 10
            });

            // User filter
            $('#filterUser').on('keyup change', function() {
                table.column(1).search(this.value).draw();
            });

            // Action filter
            $('#filterAction').on('keyup change', function() {
                table.column(2).search(this.value).draw();
            });

            // Date range filter using DataTables API
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var min = $('#minDate').val();
                var max = $('#maxDate').val();
                var timestamp = data[0]; // column 0 = Timestamp

                if (!timestamp) return false;

                // Parse the displayed date string into a Date object
                var rowDate = new Date(timestamp);

                if ((min === "" || new Date(min) <= rowDate) &&
                    (max === "" || new Date(max) >= rowDate)) {
                    return true;
                }
                return false;
            });

            // Trigger redraw when date inputs change
            $('#minDate, #maxDate').on('change', function() {
                table.draw();
            });
        });
    </script>
</body>

</html>