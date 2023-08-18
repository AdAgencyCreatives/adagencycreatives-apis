var sweetAlertOptions = {
    type: null,
    title: '',
    text: '',
    icon: undefined,

    imageUrl: null,
    imageWidth: null,
    imageHeight: null,
    imageAlt: null,

    position: 'center',
    timer: null,
    timerProgressBar: false,

    showCloseButton: false,

    confirmButtonColor: '#d33', // '#d33'
    confirmButtonText: 'OK',
    confirmButtonAriaLabel: '',
    focusConfirm: false,

    showCancelButton: false,
    cancelButtonColor: '#3E3E3E', // '#3E3E3E'
    cancelButtonText: 'Cancel',
    cancelButtonAriaLabel: '',
    focusCancel: false,

    reverseButtons: false,
    allowOutsideClick: false,
    allowEscapeKey: false,

    footer: null,
};

function sweetalert_simple(title, message, options = {}) {

    sweetAlertOptions.title = title;
    sweetAlertOptions.text = message;
    sweetAlertOptions = $.extend(sweetAlertOptions, options);

    Swal.fire(sweetAlertOptions)
        .then(function (response) {
            console.log(response);
            return response.value;
        });

}

function sweetalert_success(title, message, options = {}) {
    sweetAlertOptions.type = "success";
    sweetAlertOptions.title = title;
    sweetAlertOptions.text = message;
    sweetAlertOptions = $.extend(sweetAlertOptions, options);

    Swal.fire(sweetAlertOptions)
        .then(function (response) {
            console.log(response);
            return response.value;
        });
}

function sweetalert_info(title, message, options = {}) {
    sweetAlertOptions.type = "info";
    sweetAlertOptions.title = title;
    sweetAlertOptions.text = message;
    sweetAlertOptions = $.extend(sweetAlertOptions, options);

    Swal.fire(sweetAlertOptions)
        .then(function (response) {
            console.log(response);
            return response.value;
        });
}

function sweetalert_warning(title, message, options = {}) {
    sweetAlertOptions.type = "warning";
    sweetAlertOptions.title = title;
    sweetAlertOptions.text = message;
    sweetAlertOptions = $.extend(sweetAlertOptions, options);

    Swal.fire(sweetAlertOptions)
        .then(function (response) {
            console.log(response);
            return response.value;
        });
}

function sweetalert_danger(title, message, options = {}) {
    sweetAlertOptions.type = "error";
    sweetAlertOptions.title = title;
    sweetAlertOptions.text = message;
    sweetAlertOptions = $.extend(sweetAlertOptions, options);

    Swal.fire(sweetAlertOptions)
        .then(function (response) {
            console.log(response);
            return response.value;
        });
}

function confirmSubmission(formEl, title, message, options = {}) {
    event.preventDefault();

    sweetAlertOptions.type = "warning";
    sweetAlertOptions.title = title;
    sweetAlertOptions.text = message;
    sweetAlertOptions.confirmButtonText = 'Yes';
    sweetAlertOptions.cancelButtonText = 'No';
    sweetAlertOptions.showCancelButton = true;
    sweetAlertOptions.reverseButtons = true;
    sweetAlertOptions.focusCancel = true;
    sweetAlertOptions.focusConfirm = false;
    sweetAlertOptions = $.extend(sweetAlertOptions, options);

    Swal.fire(sweetAlertOptions)
        .then(function (response) {
            console.log(response);
            if (response.value) {
                formEl.submit();
                return true;
            } else {
                return false;
            }
        });
}

function confirmAjaxRequest(el, title, message, options = {}) {
    event.preventDefault();

    sweetAlertOptions.type = "warning";
    sweetAlertOptions.title = title;
    sweetAlertOptions.text = message;
    sweetAlertOptions.confirmButtonText = 'Yes';
    sweetAlertOptions.cancelButtonText = 'No';
    sweetAlertOptions.showCancelButton = true;
    sweetAlertOptions.reverseButtons = true;
    sweetAlertOptions.focusCancel = true;
    sweetAlertOptions.focusConfirm = false;
    sweetAlertOptions = $.extend(sweetAlertOptions, options);

    return Swal.fire(sweetAlertOptions)
        .then(function (response) {
            console.log(response);
            return response.value;
        });
}

function confirmAction(el, title, message, options = {}) {
    event.preventDefault();

    sweetAlertOptions.type = "warning";
    sweetAlertOptions.title = title;
    sweetAlertOptions.text = message;
    sweetAlertOptions.confirmButtonText = 'Yes';
    sweetAlertOptions.cancelButtonText = 'No';
    sweetAlertOptions.showCancelButton = true;
    sweetAlertOptions.focusConfirm = false;
    sweetAlertOptions.focusCancel = true;
    sweetAlertOptions.reverseButtons = true;
    sweetAlertOptions = $.extend(sweetAlertOptions, options);

    console.log(el.href);
    return Swal.fire(sweetAlertOptions)
        .then(function (response) {
            if (response.value) {
                window.location.href = el.href;
                return true;
            } else {
                return false;
            }
        });
}
