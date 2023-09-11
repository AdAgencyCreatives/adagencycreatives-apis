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

    $.each(meta_links, function (index, link) {
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
        console.log(currentPage);
        console.log(filters);
        fetchData(currentPage, filters);
    }
}



$('#per-page-select').change(function () {
    perPage = $(this).val();
    currentPage = 1;
    console.log(perPage);
    loadPage(currentPage);
});

$(document).on('click', '.pagination-number', function () {
    var page = parseInt($(this).text());
    loadPage(page);
});

$(document).on('click', '#pagination-prev', function () {
    loadPage(currentPage - 1);
});

$(document).on('click', '#pagination-next', function () {
    loadPage(currentPage + 1);
});

function displayNoRecordsMessage(colspan) {
    console.log('yeah inside');
    var messageRow = '<tr><td colspan="' + colspan + '" class="text-center">No records found.</td></tr>';
    $('tbody').html(messageRow);
}


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

    var msg = resource.charAt(0).toUpperCase() + resource.slice(1) + ' has been deleted.';
    $.ajax({
        url: 'api/v1/' + url + '/' + userId,

        method: 'DELETE',
        data: {
            _token: csrfToken
        },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            Swal.fire({
                title: 'Success',
                text: msg,
                icon: 'success'
            }).then((result) => {
                fetchData(currentPage);
            });
        },
        error: function () {
            alert('Failed to delete the user.');
        }
    });
}

$('#clear-button').on('click', function () {
    location.reload(); // Refresh the page
});

function getRoleBadge(role) {
    const roleColors = {
        admin: 'success',
        advisor: 'secondary',
        agency: 'info',
        creative: 'primary',
    };

    const badgeColor = roleColors[role] || 'danger';

    return '<span class="badge rounded-pill bg-' + badgeColor + '">' + role.charAt(0).toUpperCase() + role.slice(1) + '</span>';
}

function getStatusBadge(status) {
    const statusColors = {
        pending: 'warning',
        active: 'success',
        inactive: 'danger'
    };

    const badgeColor = statusColors[status] || 'secondary';

    return '<span class="badge rounded-pill bg-' + badgeColor + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
}

function getPlanBadge(plan) {
    const planColors = {
        "Post a Creative Job": 'warning',
        "Multiple Creative Jobs": 'success',
        "Premium Creative Jobs": 'primary',
    };

    const badgeColor = planColors[plan] || 'secondary';

    return '<span class="badge rounded-pill bg-' + badgeColor + '">' + plan + '</span>';
}

function displayJobOptionsBadges(job) {
    const optionColors = {
        "is_remote": 'success',
        "is_hybrid": 'info',
        "is_onsite": 'secondary',
        "is_featured": 'warning',
        "is_urgent": 'danger'
    };

    var optionDisplayNames = {
        "is_remote": "Remote",
        "is_hybrid": "Hybrid",
        "is_onsite": "Onsite",
        "is_featured": "Featured",
        "is_urgent": "Urgent"
    };
    var output = "";
    $.each(job, function (option, value) {
        if (value === 1 && option in optionDisplayNames && option in optionColors) {
            var displayName = optionDisplayNames[option];
            var badgeColor = optionColors[option];
            var badge = '<span class="badge bg-' + badgeColor + ' me-2">' + displayName + '</span>';

            output += badge;
        }
    });

    return output;
}


function populateFilter(categories, div_id) {
    var selectElement = $(div_id);
    $.each(categories, function (index, category) {
        var option = $('<option>', {
            value: category.id,
            text: category.name
        });

        selectElement.append(option);
    });
}

function populateFilterWithUUID(categories, div_id) {
    var selectElement = $(div_id);
    $.each(categories, function (index, category) {
        var option = $('<option>', {
            value: category.uuid,
            text: category.name,
        });

        selectElement.append(option);
    });
}

function populateFilterWithSelectedValue(categories, div_id) {
    var selectElement = $(div_id);

    $.each(categories, function (index, category) {
        var option = $('<option>', {
            value: category.id,
            text: category.name
        });

        selectElement.append(option);
    });
}

function populateUserFilter(users, div_id) {
    var selectElement = $(div_id);
    $.each(users, function (index, user) {
        var option = $('<option>', {
            value: user.uuid,
            text: user.first_name + ' ' + user.last_name + ' -' + user.role + ' (' + user.posts_count + ')'
        });

        selectElement.append(option);
    });
}

function populateGroupFilter(groups, div_id) {
    var selectElement = $(div_id);

    $.each(groups, function (index, group) {
        var option = $('<option>', {
            value: group.uuid,
            text: group.name
        });

        selectElement.append(option);
    });
}

function updateStatus(userId, resource, url, csrfToken, selectedStatus) {
    Swal.fire({
        title: 'Confirm Update',
        text: 'Are you sure you want to update this ' + resource + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Update',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            updateResourceStatus(userId, selectedStatus, url, csrfToken);
        }
    });
}

function updateResourceStatus(userId, selectedStatus, url, csrfToken) {
    $.ajax({
        url: 'api/v1/' + url + '/' + userId,

        method: 'PUT',
        data: {
            status: selectedStatus,
            _token: csrfToken
        },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            Swal.fire({
                title: 'Success',
                text: 'Status has been updated.',
                icon: 'success'
            }).then((result) => {
                fetchData(currentPage);
            });
        },
        error: function () {
            alert('Failed to update.');
        }
    });
}

function fetchCategories() {
    $.ajax({
        url: '/api/v1/get_categories',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            populateFilter(response.data, '#category');

        },
        error: function () {
            alert('Failed to fetch categories from the API.');
        }
    });
}

function fetchIndustries() {
    $.ajax({
        url: '/api/v1/industry-experiences',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            populateFilter(response.data, '#industry');
        },
        error: function () {
            alert('Failed to fetch industries from the API.');
        }
    });
}

function fetchMedias() {
    $.ajax({
        url: '/api/v1/media-experiences',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            populateFilter(response.data, '#media');

        },
        error: function () {
            alert('Failed to fetch medias from the API.');
        }
    });
}

function fetchYearsOfExperience() {
    $.ajax({
        url: '/api/v1/years-of-experience',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            populateFilter(response.data, '#years_of_experience');

        },
        error: function () {
            alert('Failed to fetch years of experience from the API.');
        }
    });
}

function fetchYearsOfExperienceWithSelectedValue(user_experience) {
    $.ajax({
        url: '/api/v1/years-of-experience',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            var selectElement = $('#years_of_experience');
            $.each(response.data, function (index, experience) {
                var option = $('<option>', {
                    value: experience.name,
                    text: experience.name
                });

                selectElement.append(option);
            });

            $('#years_of_experience').val(user_experience);
            $('#years_of_experience').trigger('change');

        },
        error: function () {
            alert('Failed to fetch years-of-experiences from the API.');
        }
    });
}

function fetchStates() {
    $.ajax({
        url: '/api/v1/locations',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            populateFilterWithUUID(response.data, '#state');

        },
        error: function () {
            alert('Failed to fetch states from the API.');
        }
    });
}

function getCitiesByState(stateId) {

    if (stateId === '-100') {
        return;
    }
    var filterParam = `filter[state_id]=${stateId}&per_page=-1`;
    $.ajax({
        url: '/api/v1/locations', // Replace with the actual URL for fetching cities
        method: 'GET',
        data: filterParam,
        success: function (response) {
            console.log(response.data);
            var citySelect = $('#city');
            citySelect.empty(); // Clear previous options
            $.each(response.data, function (index, city) {
                console.log(city);
                citySelect.append($('<option>', {
                    value: city.uuid,
                    text: city.name
                }));
            });
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error('Error:', errorThrown);
        }
    });
}

