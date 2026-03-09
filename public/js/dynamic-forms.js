// Dynamic Form Functionality

document.addEventListener('DOMContentLoaded', function() {
    
    // Add table row functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-table-row')) {
            const button = e.target.closest('.add-table-row');
            const fieldName = button.getAttribute('data-field');
            const columns = JSON.parse(button.getAttribute('data-columns'));
            const table = button.previousElementSibling.querySelector('tbody');
            const rowCount = table.querySelectorAll('tr').length;
            
            let row = '<tr>';
            columns.forEach(col => {
                row += `<td><input type="text" class="form-control form-control-sm" name="form_data[${fieldName}][${rowCount}][${col.name}]" value=""></td>`;
            });
            row += '<td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>';
            row += '</tr>';
            
            table.insertAdjacentHTML('beforeend', row);
        }
    });
    
    // Remove table row functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            const row = e.target.closest('tr');
            if (confirm('Remove this row?')) {
                row.remove();
            }
        }
    });
    
    // Auto-save functionality (optional)
    let autoSaveTimeout;
    document.querySelectorAll('input, textarea, select').forEach(element => {
        element.addEventListener('change', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                console.log('Auto-saving...');
                // Implement auto-save logic here if needed
            }, 2000);
        });
    });
});
