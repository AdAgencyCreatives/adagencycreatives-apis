function updatePaginationButtons(links, meta_links) {
    var pagination = $('.pagination');
    pagination.empty();
    if (!links.prev) {
        pagination.append(
            '<li class="paginate_button page-item previous disabled" id="pagination-prev"><a href="#" aria-controls="datatables-reponsive" aria-disabled="true" aria-role="link"data-dt-idx="previous" class="page-link">Previous</a></li>'
        );
    } else {
        pagination.append(
            '<li class="paginate_button page-item previous" id="pagination-prev"><a href="#" aria-controls="datatables-reponsive" aria-role="link"data-dt-idx="previous" class="page-link">Previous</a></li>'
        );

    }

    $.each(meta_links, function(index, link) {
        var isActive = link.active === true ? 'active' : '';
        var url = link.url;

        if (link.label !== '&laquo; Previous' && link.label !== 'Next &raquo;') {
            pagination.append(
                '<li class="paginate_button page-item pagination-number ' + isActive +
                '"><a href="#" aria-controls="datatables-reponsive" aria-role="link" class="page-link">' +
                link.label + '</a></li>'
            );
        }
    });


    if (links.next) {
        pagination.append(
            '<li class="paginate_button page-item next" id="pagination-next"><a href="#" aria-controls="datatables-reponsive" aria-role="link" data-dt-idx="next" class="page-link">Next</a></li>'
        );
    } else {
        pagination.append(
            '<li class="paginate_button page-item next disabled"><a href="#" aria-controls="datatables-reponsive" aria-role="link" data-dt-idx="next" class="page-link">Next</a></li>'
        );

    }
}

function updateTableInfo(meta) {
    var from = meta.from !== null ? meta.from : 0; 
    var to = meta.to !== null ? meta.to : 0;
    var total = meta.total !== null ? meta.total : 0;

    var dynamicText = `Showing ${from} to ${to} of ${total} entries`;
    $('#table_entries_info').text(dynamicText);
}

function loadPage(page) {
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        fetchData(currentPage);
    }
}



$('#per-page-select').change(function() {
    perPage = $(this).val();
    currentPage = 1;
    loadPage(currentPage);
});

$(document).on('click', '.pagination-number', function() {
    var page = parseInt($(this).text());
    loadPage(page);
});

$(document).on('click', '#pagination-prev', function() {  
    loadPage(currentPage - 1);
});

$(document).on('click', '#pagination-next', function() {  
    loadPage(currentPage + 1);
});


function deleteConfirmation(userId, resource, url, csrfToken) {
    Swal.fire({
        title: 'Confirm Delete',
        text: 'Are you sure you want to delete this ' + resource + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteResource(userId, resource, url, csrfToken);
        }
    });
}

function deleteResource(userId, resource, url, csrfToken) {

    var msg = resource.charAt(0).toUpperCase() + resource.slice(1) +  ' has been deleted.';
    $.ajax({
        url: 'api/v1/'  + url + '/' + userId, 
        
        method: 'DELETE',
        data: {
            _token: csrfToken
        },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            Swal.fire({
                title: 'Success',
                text: msg,
                icon: 'success'
            }).then((result) => {
                fetchData(currentPage);
            });
        },
        error: function() {
            alert('Failed to delete the user.');
        }
    });
}

$('#clear-button').on('click', function() {
    location.reload(); // Refresh the page
});