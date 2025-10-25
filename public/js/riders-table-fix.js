$(document).ready(function() {
    // Check if dataTableBuilder exists and only initialize if it's not already initialized
    if ($('#dataTableBuilder').length > 0 && !$.fn.DataTable.isDataTable('#dataTableBuilder')) {
        try {
            // Initialize with minimal configuration for server-side rendered table
            $('#dataTableBuilder').DataTable({
                "paging": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "processing": false,
                "serverSide": false,
                "dom": 'rt',
                "language": {
                    "emptyTable": "No data available"
                }
            });
        } catch (e) {
            console.log("DataTables initialization error:", e);
        }
    }
});
