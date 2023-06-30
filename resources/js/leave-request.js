$(() => {
    const csrfToken = jQuery('meta[name="csrf-token"]').attr('content');
    const loginToken = jQuery('meta[name="login-token"]').attr('content');
    const xhrHeaders = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization' : 'Bearer ' + loginToken, 
        'X-CSRF-Token': csrfToken
    };

    /**
     * Notification
     */
     $.notify.addStyle('custom-confirm', {
        html: 
            "<div class='notifyjs-container'>" +
                "<div class='clearfix notifyjs-bootstrap-base notifyjs-bootstrap-warn shadow-xl'  style='white-space: normal !important; text-align: center !important;'>" +
                    "<div class='text-center'/>" +
                        "<span class='text-base'>"+
                            "Are you sure you want to "+
                            "<span data-notify-html='type'></span>"+
                        "?</span>" +
                        "<div class='flex flex-row justify-center mt-3 space-x-2'>" +
                            "<button class='no p-2 btn-secondary w-20 rounded-xl'>Cancel</button>" +
                            "<button class='yes p-2 bg-blue-600 text-white w-20 rounded-xl'>Confirm</button>" +
                        "</div>" +
                    "</div>" +
                "</div>" +
            "</div>",
        classes: {
            base: {
                'width': '185px',
                'background': '#F5F5F5',
                'padding': '5px',
                'border-radius': '10px',
                'box-shadow' : '0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)',
            }
        },
    });
    $.notify.addStyle('custom-error', {
        html: `
            <div class="notifyjs-container" style="left: 38px; top: -9px;">
                <div class="notifyjs-bootstrap-base notifyjs-bootstrap-error" style='white-space: normal !important; text-align: center !important;'>
                    <span data-notify-html />
                </div>
            </div>`,
        classes: {
            base: {
                'border-radius' : '5px',
                'font-size' : '15px',
                'width' : '185px',
            },
        }
    });
    function errorNotif(element, response) {
        response.json().then(json => $(element).notify(json.error, { 
            style: 'custom-error',
            position: 'top center',
            autoHideDelay: 10000,
            arrowSize: 10,
        }));
    }
    function processingNotif(element) {
        $(element).notify('Processing...', { 
            className: 'info',
            autoHide: false,
            arrowSize: 10,
            position: 'top center',
        });
    }
    function confirmNotif(button, type, callback = () => null) {
        $('.notifyjs-custom-confirm-base .no')?.trigger('click');
        $(document).off('click', '.notifyjs-custom-confirm-base .yes');
        $(button).notify({type: type}, { 
            style: 'custom-confirm',
            autoHide: false,
            clickToHide: false,
            position: 'top center'
        });
        // confirm button
        $(document).on('click', '.notifyjs-custom-confirm-base .yes', function() {
            $(this).trigger('notify-hide'); 
            callback();
        });
    }
    $(document).on('click', '.notifyjs-custom-confirm-base .no', function() { 
        $(document).off('click', '.notifyjs-custom-confirm-base .yes');
        $(this).trigger('notify-hide'); 
    });

    /**
     * Add new leave request
     */
    function updateAttributes() {
        if ($('.leave-cont .options .checkboxes').is(':hidden')) {
            $('.leave-cont input:checkbox').prop('checked', false);
            $('.leave-cont input:checkbox').prop('required', false);
            $('.leave-cont select.leave').prop('required', true);
        } else {
            $('.leave-cont .options .checkboxes input').prop('required', true);
            $('.leave-cont select.leave').prop('required', false);
            $('.leave-cont select.leave').val('');
        }
    } updateAttributes();

    $(window).on('resize', function(){
        updateAttributes();
    });
    $(document).on('click', '.leave-cont input:checkbox', function(e) {
        $('.leave-cont input:checkbox').prop('checked', false);
        $('.leave-cont input:checkbox').prop('required', false);
        $(this).prop('checked', true);
    });
    $(document).on('submit', '.leave-request-root form', function(e) {
        e.preventDefault();
        let leaveTypeId = null;
        const startDate = $('.leave-cont input.start-date').val();
        const endDate = $('.leave-cont input.end-date').val();
        const reason = $('.leave-cont textarea.reason').val();
        const reviewerId = parseInt($('.leave-cont select[name="approver"]').val());
        // disable buttons
        $('.leave-request-root form button').prop('disabled', true);
        if (!$('.leave-cont .options .checkboxes').is(':hidden')) {
            leaveTypeId = parseInt($('.leave-cont input:checked').val());
        } else {
            leaveTypeId = parseInt($('.leave-cont select.leave').val());
        }
        fetch("../api/leave/request/submit", {
            method: "POST",
            credentials: "same-origin",
            headers: xhrHeaders,
            body: JSON.stringify({
                leave_type_id: leaveTypeId,
                start_date: startDate,
                end_date: endDate,
                assigned_reviewer_id: reviewerId,
                reason: reason
            })
        }).then(response => {
            if (response.ok) {
                window.location.replace("list");
            } else {
                errorNotif('.leave-cont button:submit', response);
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            $('.leave-request-root form button').prop('disabled', false);
            console.log('Something went wrong,', err);
        });
        processingNotif('.leave-cont button:submit');
        return false;
    });
    $(document).on('click', '.leave-request-root button.cancel', function(e) {
        window.location.replace("list");
    });


    /**
     * Leave request listing
     */

    function viewLeaveReason() {
        const requestId = $(this).attr('data-leave-id');
        fetch("../api/leave/request/" + requestId, {
            method: "GET",
            credentials: "same-origin",
            headers: xhrHeaders,
        }).then(response => {
            if (response.ok) {
                response.json().then(json => { 
                    $('.request-reason').html(json.reason);
                });
            } else {
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
        $('.request-reason').html('loading...');
    }
    function cancelLeaveRequest() {
        confirmNotif(this, 'cancel', () => {
            const requestId = $(this).attr('data-leave-id');
            fetch("../api/leave/request/cancel", {
                method: "POST",
                credentials: "same-origin",
                headers: xhrHeaders,
                body: JSON.stringify({ leave_request_id: requestId })
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    errorNotif(this, response);
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                $(this).prop('disabled', false)
            });
            $(this).prop('disabled', true);
            processingNotif(this);
        });
    }
    $('.leave-request-list .view-btn').click(viewLeaveReason);
    $('.leave-request-list .cancel-btn').click(cancelLeaveRequest);   
});