const csrfToken = jQuery('meta[name="csrf-token"]').attr('content');
const loginToken = jQuery('meta[name="login-token"]').attr('content');
const xhrHeaders = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'Authorization' : 'Bearer ' + loginToken, 
    'X-CSRF-Token': csrfToken
};
const postMethod = {
    method: "POST",
    credentials: "same-origin",
    headers: xhrHeaders,
};
const getMethod = {
    method: "GET",
    credentials: "same-origin",
    headers: xhrHeaders,
};

const globSel = {
    get notifConfirm() {return '.notifyjs-custom-confirm-base .yes'},
    get notifCancel() {return '.notifyjs-custom-confirm-base .no'},
}

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
            'width' : '300px',
        },
    }
});
function errorNotif(element, response) {
    const config = { 
        style: 'custom-error',
        position: 'top center',
        autoHideDelay: 10000,
        // arrowSize: 10,
    };
    if (typeof response === 'string') {
        $.notify(response, config);
    } else {
        response?.json().then(json => {
            $.notify(json.error, config);
        });
    }
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
 * Modal Alert
 */
 function successModalAlert(message = "Success!", timer = 1000) {
    Swal.fire({
        position: 'center',
        icon: 'success',
        title: `<span class="text-green-500">${message}</span>`,
        showConfirmButton: false,
        timer: timer,
        customClass: {
            popup: 'custom-alert',
            title: 'custom-success-title',
        }
    });
}
function warningModalAlert(message = "Not Allowed!", timer = 1000) {
    Swal.fire({
        position: 'center',
        icon: 'warning',
        title: `<span>${message}</span>`,
        showConfirmButton: false,
        timer: timer,
        customClass: {
            popup: 'custom-alert',
            title: 'custom-warning-title',
        }
    });
}
function failedModalAlert(message = "Failed!", timer = 1000) {
    Swal.fire({
        position: 'center',
        icon: 'error',
        title: `<span class="text-red-500">${message}</span>`,
        showConfirmButton: false,
        timer: timer,
        customClass: {
            popup: 'custom-alert',
            title: 'custom-failed-title',
            icon: 'custom-failed-icon',
        }
    });
}
function loadingModalAlert(message = "Please Wait", timer = null) {
    Swal.fire({
        iconHtml: `
            <div class="spinner-border" style="width: 50px; height: 50px;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        `,
        title: message,
        showConfirmButton: false,
        allowOutsideClick: false,
        timer: timer,
        customClass: {
            popup: 'custom-alert',
            title: 'custom-loading-title',
            icon: 'custom-loading-icon',
        }
    });
}

/**
 * =================================== Attendance Report Generator ===================================
 */
$(() => {
    if ($('.attendance-report').length === 0) return;
    function generateAttendanceReport(event) {
        const startDate = $('form.compute-total-hours input[name=start-date]').val(); 
        const endDate = $('form.compute-total-hours input[name=end-date]').val(); 
        const requestData = JSON.stringify({ start_date: startDate, end_date: endDate });
        fetch('generate-attendance-report', {
            ...postMethod,
            body: requestData
        }).then(response => {
            if (response.ok) {
                response.text().then(table => {
                    $('.report-container div.table').html(table);
                    $('.report-container div.buttons').removeClass('hidden');
                });
                Swal.close();
            } 
            else {
                response.json().then(json => { 
                    $('form.compute-total-hours').notify(json.error, { 
                        className: 'error',
                        position: 'top center',
                        autoHideDelay: 10000,
                        arrowSize: 10,
                    });
                });
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        event.preventDefault();
        loadingModalAlert('Processing Report');
        return false;
    }
    function generateAttendanceReportPDF() {
        const startDate = $('input[name=start-date]').val();
        const endDate = $('input[name=end-date]').val();
        if (!startDate || !endDate) {
            $(this).notify('Invalid Date!', { 
                className: 'error',
                position: 'top center',
                autoHideDelay: 10000,
                arrowSize: 10,
            });
            return;
        }
        window.open('attendance-report/start-date/' + startDate + '/end-date/' + endDate + '/pdf')
    }
    function generateAttendanceReportCSV() {
        var d = new Date();
        var datestring = d.getFullYear() + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2) + "-" + ("0" + d.getHours()).slice(-2) + "" + ("0" + d.getMinutes()).slice(-2);
        $('.report-container table.report').tableToCsv({
            filename: 'attendance-report-' + datestring + '.csv',
            colspanMode: 'replicate',
            excludeColumns: '.avatar'
        });
    }

    /**
     * Bindings
     */
    $('form.compute-total-hours').submit(generateAttendanceReport);
    $('.report-container button.create-report-pdf').on('click', generateAttendanceReportPDF);
    $('.report-container button.create-report-csv').on('click', generateAttendanceReportCSV);
});


/**
 * =================================== Approve / Reject Timesheet Modification ===================================
 */
$(() => {
    if ($('.timesheet-adjustments').length === 0) return;
    const filterKeywords = $('.filter-keywords').val();
    const table = '#datatable';
    const buttonConig = {
        title: 'Employee Timesheet Adjustments',
        exportOptions: {
            columns: ':not(.no-export)'
        }
    }
    $(table).DataTable({
        deferRender: true,
        order: [[2, 'asc']],
        columnDefs: [{targets: [0, 8], orderable: false}],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], 
        // select: true,
        search: {
            search: filterKeywords ? '' : 'Pending',
        },
        responsive: {
            details: {
                renderer: function ( api, rowIdx, columns ) {
                    var data = $.map( columns, function ( col, i ) {
                        return col.hidden ?
                            (col.title != 'Actions'
                                ? '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                    '<td class="font-black text-xs" style="white-space: pre-wrap; word-wrap: break-word;">'+col.title+':'+'</td> '+
                                    '<td>'
                                        +col.data+
                                    '</td>'+
                                '</tr>'
                                : '<tr class="bg-transparent" data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                    `<td colspan=2 class="border-0">
                                        <div class="flex justify-center">
                                            <div class="border-2 p-2 rounded-2xl flex justify-center w-40 h-12 items-center">`+col.data+`</div>
                                        </div>
                                    </td>
                                </tr>`
                            ) : '';
                    } ).join('');
                    return data ? $('<table/>').append( data ) : false;
                }
            }
        },
        dom: `
            <"flex justify-center md:justify-between flex-wrap items-center mb-2"
                <"m-1 md:m-0"B>
                <"flex justify-between flex-wrap m-1 md:m-0"l>
                <"m-1 md:m-0"f>
            >rt
            <"flex justify-center md:justify-between flex-wrap"
                <"m-2"i><"m-2"p>
            >
        `,
        buttons: [
            {
                extend: 'print',
                ...buttonConig,
            },
            {
                extend: 'csvHtml5',
                ...buttonConig
            },
            {
                extend: 'excelHtml5',
                ...buttonConig
            },
            {
                extend: 'pdfHtml5',
                ...buttonConig
            }
        ]
    });

    const cls = {
        get dtBtn() {return 'dt-button'},
    }
    const sel = {
        get dtBtn() {return `.${cls.dtBtn}`},
        get modal() {return `div.modal`},
        get modalContent() {return `${this.modal} div.modal-body div.content`},
        get actions() {return  `.actions`},
        get viewBtn() {return `.view-btn`},
        get approveBtn() {return `button.approve`},
        get rejectBtn() {return `button.reject`},
    }
    const attr = {
        get dataModelId() {return `data-model-id`},
    }
    const api = {
        view(modelId) {return '/api/timesheet/modification/' + modelId + '/compare/2'},
        get approve() {return '/api/timesheet/modification/approve'},
        get reject() {return '/api/timesheet/modification/reject'},
    }

    function loadingModalContent() {
        $(sel.modalContent).html(`
            <div class="flex justify-center items-center w-full" style="height:400px;">
                <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);
        $(sel.modal).modal('show');
    }
    function successOperation(msg) {
        successModalAlert(msg, null);
        setTimeout(() => location.reload(), 1000);
    }
    function failedOperation(msg) {
        failedModalAlert(msg);
        setTimeout(() => location.reload(), 1000);
    }
    function view() {
        const modelId = $(this).attr(attr.dataModelId);
        fetch(api.view(modelId), {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(text => { 
                    $(sel.modalContent).html(text);
                });
            } else {
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
        loadingModalContent();
    }
    function disableButtons(disabled) {
        $(sel.approveBtn).prop('disabled', disabled);
        $(sel.rejectBtn).prop('disabled', disabled);
    }
    function approve() {
        confirmNotif(this, 'approve', () => {
            const requestId = $(this).attr(attr.dataModelId);
            fetch(api.approve, {
                ...postMethod,
                body: JSON.stringify({ request_id: requestId })
            }).then(response => {
                if (response.ok) {
                    successOperation('Approved!');
                } else {
                    errorNotif(this, response);
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                failedOperation();
                disableButtons(false);
                console.log('Something went wrong,', err);
            });
            disableButtons(true);
            loadingModalAlert();
        });
    }
    function reject() {
        confirmNotif(this, 'reject', () => {
            const requestId = $(this).attr(attr.dataModelId);
            fetch(api.reject, {
                ...postMethod,
                body: JSON.stringify({ request_id: requestId })
            }).then(response => {
                if (response.ok) {
                    successOperation('Rejected!');
                } else {
                    errorNotif(this, response);
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                failedOperation();
                disableButtons(false);
                console.log('Something went wrong,', err);
            });
            disableButtons(true);
            loadingModalAlert();
        });
    }

    /**
     * Bindings
     */
    $(document).on('click', sel.approveBtn, approve);
    $(document).on('click', sel.rejectBtn, reject);
    $(document).on('click', sel.viewBtn, view);
});


/**
 * =================================== Approve / Reject Leave Request ===================================
 */
$(() => {
    if ($('.leave-request #leave-request-datatable').length === 0) return;
    const buttonConig = {
        title: 'Employee Leave Requests',
        exportOptions: {
            columns: ':not(.no-export)'
        }
    }
    $('.leave-request #leave-request-datatable').DataTable({
        deferRender: true,
        order: [[1, 'asc']],
        columnDefs: [{targets: [0, 5], orderable: false}],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        search: {
            search: "Pending",
        },
        responsive: {
            details: {
                renderer: function ( api, rowIdx, columns ) {
                    const data = $.map( columns, function ( col, i ) {
                        if (col.hidden) {
                            if (col.title == 'Actions') {
                                return `<tr class="bg-transparent" data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                            <td colspan=2 class="border-0">
                                                <div class="flex justify-center">
                                                    <div class="border-2 p-2 rounded-2xl flex justify-center w-40 h-12 items-center">${col.data}</div>
                                                </div>
                                            </td>
                                        </tr>`;
                            } else if (col.title == 'Reason'){
                                return `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                            <td class="font-black text-xs" style="white-space: pre-wrap; word-wrap: break-word;">${col.title}:</td>
                                            <td>${col.data}</td>
                                        </tr>`;
                            } else {
                                return `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                            <td class="font-black text-xs" style="white-space: pre-wrap; word-wrap: break-word;">${col.title}:</td>
                                            <td class="text-xs" style="cursor: pointer; white-space: pre-wrap; word-wrap: break-word;">${col.data}</td>
                                        </tr>`;
                            }
                        }
                        return '';

                    } ).join('');
                    return data ? $('<table/>').append( data ) : false;
                }
            }
        },
        dom: `
            <"flex justify-center md:justify-between flex-wrap items-center"
                B
                <"flex justify-between flex-wrap"
                    <"mt-2 md:mt-0 md:ml-2"l>
                >
                <"m-2"f>
            >rt
            <"flex justify-center md:justify-between flex-wrap"
                <"m-2"i><"m-2"p>
            >
        `,
        buttons: [
            {
                extend: 'print',
                ...buttonConig
            },
            {
                extend: 'csvHtml5',
                ...buttonConig
            },
            {
                extend: 'excelHtml5',
                ...buttonConig
            },
            {
                extend: 'pdfHtml5',
                ...buttonConig
            }
        ]
    });

    const sel = {
        get dtBtn() {return `.${cls.dtBtn}`},
        get modal() {return `div.modal`},
        get modalContent() {return `${this.modal} div.modal-body div.content`},
        get actions() {return  `.actions`},
        get viewBtn() {return `.view-btn`},
        get approveBtn() {return `button.approve`},
        get rejectBtn() {return `button.reject`},
    }
    const attr = {
        get dataModelId() {return `data-model-id`},
    }
    const api = {
        view(modelId) {return '/api/leave/request/' + modelId + '/evaluate/2'},
        get approve() {return '/api/leave/request/approve'},
        get reject() {return '/api/leave/request/reject'},
    }
    
    function loadingModal() {
        $(sel.modalContent).html(`
            <div class="flex justify-center items-center w-full" style="height:400px;">
                <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);
        $(sel.modal).modal('show');
    }
    function successOperation(msg) {
        successModalAlert(msg);
        setTimeout(() => location.reload(), 1000);
    }
    function failedOperation(msg) {
        failedModalAlert(msg);
        setTimeout(() => location.reload(), 1000);
    }
    function view() {
        const requestId = $(this).attr(attr.dataModelId);
        fetch(api.view(requestId), {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(text => { 
                    $(sel.modalContent).html(text);
                });
            } else {
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
        loadingModal();
    }
    function disableButtons(disabled) {
        $(sel.approveBtn).prop('disabled', disabled);
        $(sel.rejectBtn).prop('disabled', disabled);
    }
    function approve() {
        confirmNotif(this, 'approve', () => {
            const requestId = $(this).attr(attr.dataModelId);
            fetch(api.approve, {
                ...postMethod,
                body: JSON.stringify({ leave_request_id: requestId })
            }).then(response => {
                if (response.ok) {
                    successOperation('Approved!');
                } else {
                    errorNotif(this, response);
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                failedOperation();
                disableButtons(false);
                console.log('Something went wrong,', err);
            });
            disableButtons(true);
            loadingModalAlert();
        });
    }
    function reject() {
        confirmNotif(this, 'reject', () => {
            const requestId = $(this).attr(attr.dataModelId);
            fetch(api.reject, {
                ...postMethod,
                body: JSON.stringify({ leave_request_id: requestId })
            }).then(response => {
                if (response.ok) {
                    successOperation('Rejected!');
                } else {
                    errorNotif(this, response);
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                failedOperation();
                disableButtons(false);
                console.log('Something went wrong,', err);
            });
            disableButtons(true);
            loadingModalAlert();
        });
    }

    /**
     * Bindings
     */
    $(document).on('click', sel.approveBtn, approve);
    $(document).on('click', sel.rejectBtn, reject);
    $(document).on('click', sel.viewBtn, view);
});


/**
 * =================================== Attendance Today ===================================
 */
$(() => {
    const buttonConig = {
        title: 'Employees Attendance Today',
        exportOptions: {
            columns: ':not(.no-export)'
        }
    }

    const attoSel = {
        get rootContainer() {return `div.attendance-today`},
        get presentLateDtCont() {return `${this.rootContainer} .present-and-late-dt-cont`},
        get onLeaveAbsentDtCont() {return `${this.rootContainer} .on-leave-and-absent-dt-cont`},
        get presentLateDt() {return `${this.rootContainer} #present-and-late-datatable`},
        get onLeaveAbsentDt() {return `${this.rootContainer} #on-leave-and-absent-datatable`},
        get presentLateSearch() {return `${this.presentLateDtCont} ${this.dtSearchInput}`},
        get onLeaveAbsentSearch() {return `${this.onLeaveAbsentDtCont} ${this.dtSearchInput}`},
        get departmentsCont() {return `${this.rootContainer} div.departments`},
        get departmentCard() {return `${this.departmentsCont} .dept-card`},
        get leaveReasonModal() {return `${this.rootContainer} .leave-reason-modal`},
        get reasonContainer() {return `${this.leaveReasonModal} .request-reason`},
        get clockTabsCont() {return `${this.rootContainer} div.clock-tabs-cont`},
        get tableLabel() {return `${this.rootContainer} .table-label`},
        getDtFromCont: (dtContainer) => {return `${dtContainer} table`},
        // buttons
        get clockTabsCont() {return `${this.rootContainer} div.clock-tabs-cont`},
        get clockTabBtns() {return `${this.clockTabsCont} button`},
        get clockInsBtn() {return `${this.clockTabsCont} .clock-ins`},
        get notClockedBtn() {return `${this.clockTabsCont} .not-clocked-in`},
        get viewReasonBtn() {return `${this.onLeaveAbsentDt} button.view-reason`},
        // inputs
        get dtSearchInput() {return `input[type="search"]`},
    };

    const dataAttr = {
        get department() {return `data-department`},
        get leaveRequestId() {return `data-leave-request-id`},
    };

    const generic = {
        activeDataTable: null,
        set activeDt(dt) {this.activeDataTable = dt},
        get activeDt() {return this.activeDataTable},
        presentLateDt: null,
        set prsntLateDt(dt) {this.presentLateDt = dt},
        get prsntLateDt() {return this.presentLateDt},
        onLeaveAbsentDt: null,
        set onLvAbsntDt(dt) {this.onLeaveAbsentDt = dt},
        get onLvAbsntDt() {return this.onLeaveAbsentDt},
        activeDtCont: null,
        set dataTableCont(cont) {this.activeDtCont = cont},
        get dataTableCont() {return this.activeDtCont},
        clockTab: true,
        set clockInsTab(toggle) {this.clockTab = toggle},
        get clockInsTab() {return this.clockTab},
    };

    if ($(attoSel.presentLateDt).length === 0) return;

    function initDataTable(tableContainerSel, dtRefSetter = (arg) => null, activate = false) {
        const tableSel = attoSel.getDtFromCont(tableContainerSel);
        const dt = $(tableSel).DataTable({
            deferRender: true,
            order: [[1, 'asc']],
            columnDefs: [{targets: [0], orderable: false}],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], 
            responsive: {
                details: {
                    renderer: function ( api, rowIdx, columns ) {
                        const data = $.map( columns, function ( col, i ) {
                            return col.hidden ?
                                (col.title 
                                    ? '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                        '<td class="font-black text-xs" style="white-space: pre-wrap; word-wrap: break-word;">'+col.title+':'+'</td> '+
                                        `<td class="text-xs" style="cursor: pointer; white-space: pre-wrap; word-wrap: break-word;">`
                                            +col.data+
                                        `</td>
                                    </tr>`
                                    : '<tr class="bg-transparent" data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                        `<td colspan=2 class="border-0">
                                            <div class="border-2 p-2 rounded-2xl flex justify-center">`+col.data+`</div>
                                        </td>
                                    </tr>`
                                ) : '';
                        } ).join('');
                        return data ? $('<table/>').append( data ) : false;
                    }
                }
            },
            dom: `
                <"flex justify-center md:justify-between flex-wrap items-center"
                    B
                    <"flex justify-between flex-wrap"
                        <"mt-2 md:mt-0 md:ml-2"l>
                    >
                    <"m-2"f>
                >rt
                <"flex justify-center md:justify-between flex-wrap"
                    <"m-2"i><"m-2"p>
                >
            `,
            buttons: [
                {
                    extend: 'print',
                    ...buttonConig
                },
                {
                    extend: 'csvHtml5',
                    ...buttonConig
                },
                {
                    extend: 'excelHtml5',
                    ...buttonConig
                },
                {
                    extend: 'pdfHtml5',
                    ...buttonConig
                }
            ]
        }); 
        dtRefSetter(dt);
        if (activate) {
            generic.dataTableCont = tableContainerSel;
            generic.activeDt = dt;
        }
    }

    function onGetDeptAttendance(e) {
        const dept = $(this).attr(dataAttr.department);
        const deptCard = `${attoSel.departmentsCont} div[${dataAttr.department}="${dept}"]`;
        const currSearched = generic.activeDt.search();
        $(attoSel.departmentCard).removeClass('clicked');
        if (currSearched === dept) {
            generic.activeDt.search('').draw();
        } else {
            generic.activeDt.search(dept).draw();
            $(deptCard).addClass('clicked');
        }
    }

    function clearDeptFilter() {
        generic.activeDt.search('').draw();
        $(attoSel.departmentCard).removeClass('clicked');
    }

    function onToggleClocksTab(e) {
        const tabBtn = this;
        generic.clockInsTab = !generic.clockInsTab;
        clearDeptFilter();
        if (generic.clockInsTab) {
            generic.activeDt = generic.prsntLateDt;
            $(attoSel.tableLabel).html('Clocked In Scheduled Employees');
            $(attoSel.presentLateDtCont).removeClass('hidden');
            $(attoSel.onLeaveAbsentDtCont).addClass('hidden');
        } else {
            generic.activeDt = generic.onLvAbsntDt;
            $(attoSel.tableLabel).html('Not Clocked In Scheduled Employees');
            $(attoSel.presentLateDtCont).addClass('hidden');
            $(attoSel.onLeaveAbsentDtCont).removeClass('hidden');
        }
        $(attoSel.clockTabBtns).removeClass('hidden');
        $(tabBtn).addClass('hidden');
    }

    function viewLeaveReason() {
        const requestId = $(this).attr(dataAttr.leaveRequestId);
        fetch("../api/leave/request/" + requestId, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.json().then(json => { 
                    $(attoSel.reasonContainer).html(json.reason);
                });
            } else {
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
        $(attoSel.reasonContainer).html('loading...');
    }

    /**
     * Bindings
     */
    initDataTable(attoSel.presentLateDtCont, (dt) => generic.prsntLateDt = dt, true);
    initDataTable(attoSel.onLeaveAbsentDtCont, (dt) => generic.onLvAbsntDt = dt);
    $(attoSel.departmentCard).on('click', onGetDeptAttendance);
    $(attoSel.clockTabBtns).on('click', onToggleClocksTab);
    $(attoSel.viewReasonBtn).on('click', viewLeaveReason);
});


/**
 * =================================== Attendance Summary ===================================
 */
$(() => {
    const calendarEl = document.getElementById('attendance-summary-calendar');
    if (!calendarEl) return;
    const calendar = new FullCalendar.Calendar(calendarEl, {
        themeSystem: 'bootstrap',
        initialView: 'dayGridMonth',
        weekNumbers: true,
        navLinks: true,
        navLinkDayClick: () => null,
        navLinkWeekClick: calendarAttendanceSummaryWeekClick,
        datesSet: fetchCalendarAttendanceData,
        eventClick: calendarAttendanceSummaryEventClick,
        dateClick: calendarAttendanceSummaryDateClick,
    });
    const dateFormat = 'YYYY-MM-DD';
    const events = (function() {
        let eventDates = null;
        return {
            setDates: (dates) => eventDates = dates,
            getDates: () => eventDates,
        }
    })();
    const week = (function() {
        let modalDateRange = null;
        return {
            setModalDateRange: (start, end) => modalDateRange = {startDate: start, endDate: end},
            getModalDateRange: () => modalDateRange,
        }
    })();

    calendar.render();
    async function fetchCalendarAttendanceData(e) {
        const startDate = moment(e.start).format('YYYY-MM-DD');
        const endDate = moment(e.end).format('YYYY-MM-DD');
        const dateRange = `${startDate}/${endDate}`;
        fetch('/admin/month-attendance-summary/' + dateRange, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.json().then(result => {
                    const eventSrcs = calendar.getEventSources();
                    eventSrcs.forEach(event => event.remove());
                    let attendanceSummary = []
                    Object.keys(result).forEach(date => {
                        Object.keys(result[date]).forEach(attendance => {
                            const title = attendance + ' ' + result[date][attendance];
                            const startDate = date;
                            attendanceSummary.push({
                                title: title,
                                start: startDate,
                                textColor: 'white',
                                className: attendance,
                                ...(function() {
                                    if (attendance == 'present') return {color: 'green'};
                                    else if (attendance == 'late') return {color: '#de9103'};
                                    else if (attendance == 'on-leave') return {color: 'gray'};
                                    else return {color: 'red'}; // absent
                                })()
                            })
                        });

                    })
                    calendar.addEventSource(attendanceSummary);
                    events.setDates(calendar.getEvents().map(event => event.startStr));
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function calendarAttendanceSummaryWeekClick(event) {
        const dateMoment = moment(event);
        const weekStart = dateMoment.add(1, 'days').format(dateFormat)
        const weekEnd = dateMoment.endOf('week').format(dateFormat);
        week.setModalDateRange(weekStart, weekEnd);
        overallAttendanceSummary();
    }
    function overallAttendanceSummary() {
        const { startDate, endDate } = week.getModalDateRange();
        const dateRange = `${startDate}/${endDate}`;
        loadingSummaryModal();
        fetch('/admin/week-attendance-summary/' + dateRange, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, () => initDepartmentGroupedDataTable(7));
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function totalPerEmployeeAttendanceSummary() {
        const { startDate, endDate } = week.getModalDateRange();
        const dateRange = `${startDate}/${endDate}`;
        loadingSummaryModal();
        fetch('/admin/total-per-employee-attendance-summary/' + dateRange, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, () => initDepartmentGroupedDataTable(5));
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function totalPerDayAttendanceSummary() {
        const { startDate, endDate } = week.getModalDateRange();
        const dateRange = `${startDate}/${endDate}`;
        loadingSummaryModal();
        fetch('/admin/total-per-day-attendance-summary/' + dateRange, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, () => initDepartmentGroupedDataTable(7, [[-1], ['All']]));
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function calendarAttendanceSummaryDateClick(event) {
        const date = event.dateStr;
        const hasEvent = events.getDates().includes(date);
        if (!hasEvent) return;
        loadingSummaryModal();
        fetch('/admin/date-attendance-summary/' + date, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, initDepartmentGroupedDataTable);
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function calendarAttendanceSummaryEventClick(e) {
        const date = e.event.startStr;
        const hasEvent = events.getDates().includes(date);
        if (!hasEvent) return;
        loadingSummaryModal();
        const attendanceType = e.event.classNames;
        if (attendanceType.includes('present')) getPresentEmployeesOnDate(date);
        else if (attendanceType.includes('late')) getLateEmployeesOnDate(date);
        else if (attendanceType.includes('on-leave')) getOnLeaveEmployeesOnDate(date);
        else if (attendanceType.includes('absent')) getAbsentEmployeesOnDate(date);
    }
    function getPresentEmployeesOnDate(date) {
        fetch('/admin/present-employees-summary/' + date, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, initAttendanceTypeDataTable);
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function getLateEmployeesOnDate(date) {
        fetch('/admin/late-employees-summary/' + date, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, initAttendanceTypeDataTable);
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function getOnLeaveEmployeesOnDate(date) {
        fetch('/admin/on-leave-employees-summary/' + date, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, initAttendanceTypeDataTable);
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function getAbsentEmployeesOnDate(date) {
        fetch('/admin/absent-employees-summary/' + date, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, initAttendanceTypeDataTable);
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function showAttendanceSummaryModal(data, dataTable = () => null) {
        const modalBody = $('.attendance-summary .modal .modal-body div.summary');
        modalBody.html(data);
        dataTable();
    }
    function initAttendanceTypeDataTable() {
        $('.attendance-summary table.departments').DataTable({
            deferRender: true,
            order: [[0, 'asc']],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], 
            select: true,
            responsive: {
                details: {
                    renderer: function ( api, rowIdx, columns ) {
                        const data = $.map( columns, function ( col, i ) {
                            return col.hidden ?
                                (col.title 
                                    ? '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                        '<td class="font-black text-xs" style="white-space: pre-wrap; word-wrap: break-word;">'+col.title+':'+'</td> '+
                                        `<td class="text-xs" style="cursor: pointer; white-space: pre-wrap; word-wrap: break-word;">`
                                            +col.data+
                                        `</td>
                                    </tr>`
                                    : '<tr class="bg-transparent" data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                        `<td colspan=2 class="border-0">
                                            <div class="border-2 p-2 rounded-2xl flex justify-center">`+col.data+`</div>
                                        </td>
                                    </tr>`
                                ) : '';
                        } ).join('');
                        return data ? $('<table/>').append( data ) : false;
                    }
                }
            },
        });
    }
    function initDepartmentGroupedDataTable(columns = 4, lengthMenu = [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']]) {
        const collapsedGroups = {};
        const summaryDataTable = $('.attendance-summary table.departments').DataTable({
            deferRender: true,
            order: [[columns, 'asc']],
            lengthMenu: lengthMenu, 
            select: true,
            columnDefs: [
                {
                    "targets": [columns],
                    "visible": false
                }
            ],
            rowGroup: {
                dataSrc: columns,
                startRender: function (rows, group) {
                    const collapsed = !collapsedGroups[group];
                    rows.nodes().each(function (r) {
                        r.style.display = collapsed ? 'none' : '';
                    });    
                    return $('<tr/>')
                        .append('<td colspan="' + columns + '">' + group + '</td>')
                        .attr('data-name', group)
                        .toggleClass('collapsed', collapsed);
                }
            },
        });
        $('.attendance-summary table.departments tbody').off("click");
        $('.attendance-summary table.departments tbody').on('click', 'tr.dtrg-start', function() {
            const name = $(this).data('name');
            collapsedGroups[name] = !collapsedGroups[name];
            summaryDataTable.draw(false);
        });
    }
    function loadingSummaryModal() {
        const modalBody = $('.attendance-summary .modal .modal-body div.summary');
        modalBody.html(`
            <div class="flex items-center" style="height:400px;">
                <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);
        $('.attendance-summary .modal').modal('show');
    }

    /**
     * Bindings
     */
    $(document).on('click', '.attendance-summary .modal .week-tabs button.overall', overallAttendanceSummary);
    $(document).on('click', '.attendance-summary .modal .week-tabs button.total-per-employee', totalPerEmployeeAttendanceSummary);
    $(document).on('click', '.attendance-summary .modal .week-tabs button.total-per-day', totalPerDayAttendanceSummary);
});


/**
 * =================================== Employee Management ===================================
 */
$(() => {
    const table = '.emp-management #employee-datatable';
    if ($(table).length === 0) return;
    const buttonConig = {
        title: 'Employees',
        exportOptions: {
            columns: ':not(.no-export)'
        }
    }
    const dataTable = $(table).DataTable({
        deferRender: true,
        order: [[4, 'asc']],
        columnDefs: [{targets: [1, 2, 3, 11], orderable: false}, {targets: [0], visible: false, searchable: false}, {
            orderable: false,
            className: 'select-checkbox',
            targets: 2
        }],
        // pagingType: 'full_numbers',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], 
        select: {
            style: 'os',
            selector: 'td:nth-child(2)'
        },
        responsive: {
            details: {
                renderer: function ( api, rowIdx, columns ) {
                    let rowCount = 0;
                    const data = $.map( columns, function ( col, i ) {
                        col.hidden ? rowCount++ : null;
                        return col.hidden ?
                            (col.title 
                                ? '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                    '<td class="font-black text-xs" style="white-space: pre-wrap; word-wrap: break-word;">'+col.title+':'+'</td> '+
                                    `<td class="text-xs" style="cursor: pointer; white-space: pre-wrap; word-wrap: break-word;">`
                                        +col.data+
                                    `</td>
                                </tr>`
                                : '<tr class="bg-transparent" data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                    `<td colspan=2 class="border-0 p-0 ${rowCount > 1 ? 'py-2' : ''}">
                                        <div class="border-2 p-2 rounded-2xl flex justify-center">`+col.data+`</div>
                                    </td>
                                </tr>`
                            ) : '';
                    } ).join('');
                    return data ? $('<table/>').append( data ) : false;
                }
            }
        },
        // dom: 'lBfrtip', // @note
        dom: `
            <"flex justify-center md:justify-between flex-wrap items-center"
                B
                <"flex justify-between flex-wrap"
                    <"mt-2 md:mt-0 md:ml-2"l>
                >
                <"m-2"f>
            >rt
            <"flex justify-center md:justify-between flex-wrap"
                <"m-2"i><"m-2"p>
            >
        `,
        buttons: [ 
            {
                extend: 'print',
                ...buttonConig
            },
            {
                extend: 'csvHtml5',
                ...buttonConig
            },
            {
                extend: 'excelHtml5',
                ...buttonConig
            },
            {
                extend: 'pdfHtml5',
                ...buttonConig
            }
        ]
    });
    // to customize style of data table buttons
    // $('.dt-button').removeClass('dt-button');

    /**
     * Frequently used CLASS
     */
     const empCls = {
        get affix() {return `emp`},
        get prefix() {return `${this.affix}-`},
        get infix() {return `-${this.affix}-`},
        get suffix() {return `-${this.affix}`},
        get container() {return `${this.prefix}container`},
        get newRow() {return `new-row`},
        get fullName() {return `full-name`},
        get firstName() {return `first-name`},
        get middleName() {return `middle-name`},
        get lastName() {return `last-name`},
        get email() {return `email`},
        get birthDate() {return `birth-date`},
        get department() {return `department`},
        get supervisor() {return `supervisor`},
        get empType() {return `emp-type`},
        get position() {return `position`},
        get sickLeave() {return `sick-leave`},
        get vacayLeave() {return `vacay-leave`},
        get createdRow() {return `${this.container} ${this.newRow}`},
    }
    
    /**
     * Frequently used SELECTORS
     */
    const empSel = {
        get prefix() {return `.${empCls.affix}-`},
        get management() {return `${this.prefix}management`},
        get formModal() {return `${this.management} .${empCls.prefix}form-modal`},
        get modalTitle() {return `${this.management} .${empCls.prefix}form-modal .modal-title`},
        get form() {return `${this.formModal} form.${empCls.affix}`},
        get rowContainer() {return `.${empCls.container}`},
        get selectedEmps() {return `${this.management} .selected-emps`},
        get timesheetCollapseCont() {return `${this.addTimesheetModal} div.collapse`},
        // modals
        get addTimesheetModal() {return 'div.add-timesheet-modal'},
        get addTimesheetForm() {return `${this.addTimesheetModal} form.add-timesheet`},
        get noScheds() {return `${this.addTimesheetModal} div.no-scheds-cont`},
        get timesheetsLogs() {return `${this.addTimesheetModal} div.timesheets-logs-cont`},
        dayCont(day) {return `${this.addTimesheetForm} div.${day}`},
        // tr
        get trContainer() {return `tr${this.rowContainer}`},
        get trResponsive() {return `tr.child`},
        get newRow() {return `.${empCls.newRow}`},
        get trNewRow() {return `${this.rowContainer} ${this.newRow}`},
        tr: (rowId) => `tr${rowId}`,
        // buttons
        get showFormBtn() {return `${this.management} button.add${empCls.suffix}`},
        get editRowBtn() {return `a.edit`},
        get deleteRowBtn() {return `a.delete`},
        get tableEditRowBtn() {return `${table} ${this.editRowBtn}`},
        get tableDeleteRowBtn() {return `${table} ${this.deleteRowBtn}`},
        get submitFormBtn() {return `${this.form} button.submit${empCls.suffix}`},
        get showPassword() {return `${this.form} input.show-passw`},
        get confSelEmpsBtn() {return `${this.management} button.config-emps`},
        get showTimesheetModalBtn() {return `${this.management} a.add-timesheets`},
        get dayCheckBoxBtn() {return `input.day-checkbox`},
        get tmsFormDayCheckBoxBtn() {return `${this.addTimesheetForm} ${this.dayCheckBoxBtn}`},
        get deselectAllBtn() {return `${this.management} table .deselect-all`},
        get selectAllBtn() {return `${this.management} table .select-all`},
        // inputs
        get formInputs() {return `${this.form} input`},
        get formSelects() {return `${this.form} select`},
        get formNumInputs() {return `${this.form} input[type="number"]`},
        get empIdInput() {return `${this.form} input[name="${inputName.empId}"]`},
        get firstNameInput() {return `${this.form} input[name="${inputName.firstName}"]`},
        get middleNameInput() {return `${this.form} input[name="${inputName.middleName}"]`},
        get lastNameInput() {return `${this.form} input[name="${inputName.lastName}"]`},
        get emailInput() {return `${this.form} input[name="${inputName.email}"]`},
        get birthDateInput() {return `${this.form} input[name="${inputName.birthDate}"]`},
        get sickLeaveInput() {return `${this.form} input[name="${inputName.sickLeave}"]`},
        get vacayLeaveInput() {return `${this.form} input[name="${inputName.vacayLeave}"]`},
        get passwordInput() {return `${this.form} input[name="${inputName.password}"]`},
        get confirmPassInput() {return `${this.form} input[name="${inputName.confirmPass}"]`},
        get departmentSelect() {return `${this.form} select[name="${inputName.department}"]`},
        get empTypeSelect() {return `${this.form} select[name="${inputName.empType}"]`},
        get positionSelect() {return `${this.form} select[name="${inputName.position}"]`},
        get startDateInput() {return `${this.addTimesheetForm} input[name="${inputName.startDate}"]`},
        get endDateInput() {return `${this.addTimesheetForm} input[name="${inputName.endDate}"]`},
        get timeInInput() {return `input[name="${inputName.timeIn}"]`},
        get timeOutInput() {return `input[name="${inputName.timeOut}"]`},
        get lunchStartInput() {return `input[name="${inputName.lunchStart}"]`},
        get lunchEndInput() {return `input[name="${inputName.lunchEnd}"]`},
        get overtimeStartInput() {return `input[name="${inputName.otStart}"]`},
        get overtimeEndInput() {return `input[name="${inputName.otEnd}"]`},
        // spans
        get fullNameSpan() {return `span.${empCls.fullName}`},
        get firstNameSpan() {return `span.${empCls.firstName}`},
        get middleNameSpan() {return `span.${empCls.middleName}`},
        get lastNameSpan() {return `span.${empCls.lastName}`},
        get emailSpan() {return `span.${empCls.email}`},
        get birthDateSpan() {return `span.${empCls.birthDate}`},
        get sickLeaveSpan() {return `span.${empCls.sickLeave}`},
        get vacayLeaveSpan() {return `span.${empCls.vacayLeave}`},
        get departmentSpan() {return `span.${empCls.department}`},
        get supervisorSpan() {return `span.${empCls.supervisor}`},
        get empTypeSpan() {return `span.${empCls.empType}`},
        get positionSpan() {return `span.${empCls.position}`},
        // password rules
        get passwRulesContainer() {return `${this.form} div.passw-rules`},
        get passwRules() {return `${this.passwRulesContainer} p`},
        get passwLetter() {return `${this.passwRulesContainer} p.letter`},
        get passwCapital() {return `${this.passwRulesContainer} p.capital`},
        get passwSymbol() {return `${this.passwRulesContainer} p.symbol`},
        get passwNumber() {return `${this.passwRulesContainer} p.number`},
        get passwLength() {return `${this.passwRulesContainer} p.length`},
        // routes
        get schedulesRoute() {return `${table} a.schedules-route`},
        get timesheetsRoute() {return `${table} a.timesheets-route`},
        // clicked buttons
        editBtn: null,
        set clickedEditBtn(empId) {this.editBtn = `${this.editRowBtn}[${dataAttr.empId}=${empId}]`},
        get clickedEditBtn() {return this.editBtn},
        // dropdown button
        get dropleft() {return `.dropleft`},
    };

    const inputName = {
        get empId() {return `employee_id`},
        get firstName() {return `first_name`},
        get middleName() {return `middle_name`},
        get lastName() {return `last_name`},
        get email() {return `email`},
        get birthDate() {return `birth_date`},
        get department() {return `department`},
        get empType() {return `employment_type`},
        get position() {return `position`},
        get sickLeave() {return `sick_leave`},
        get vacayLeave() {return `vacation_leave`},
        get password() {return `password`},
        get confirmPass() {return `confirm_password`},
        get startDate() {return `start_date`},
        get endDate() {return `end_date`},
        get timeIn() {return `time_in`},
        get timeOut() {return `time_out`},
        get lunchStart() {return `lunch_start`},
        get lunchEnd() {return `lunch_end`},
        get otStart() {return `overtime_start`},
        get otEnd() {return `overtime_end`},
    }

    const dataAttr = {
        get empId() {return `data${empCls.infix}id`},
        get userId() {return `data-user-id`},
        get deptId() {return `data-dept-id`},
        get posId() {return `data-pos-id`},
    }

    /**
     * API ROUTES
     */
    const empApi = {
        get create() {return '/api/employee/create';},
        get update() {return '/api/employee/update';},
        get delete() {return '/api/employee/delete';},
        get createForEmployees() {return '/api/timesheet/create-for-employees';},
    }

    const generic = {
        newRowIndx: 1,
        set newRowIndex(i) {this.newRowIndx = i},
        get newRowIndex() {return this.newRowIndx},
        get weekDays() {
            return [
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday',
            ];
        },
    }

    /**
     * @param {string} [rowContainer=tr_selector]
     */
    function autoFillEmpForm(rowContainer) {
        const firstName = $(rowContainer).find(empSel.firstNameSpan).html();
        const middleName = $(rowContainer).find(empSel.middleNameSpan).html();
        const lastName = $(rowContainer).find(empSel.lastNameSpan).html();
        const email = $(rowContainer).find(empSel.emailSpan).html();
        const birthDate = $(rowContainer).find(empSel.birthDateSpan).html();
        const departmentId = $(rowContainer).find(empSel.departmentSpan).attr(dataAttr.deptId);
        const empType = $(rowContainer).find(empSel.empTypeSpan).html();
        const positionId = $(rowContainer).find(empSel.positionSpan).attr(dataAttr.posId);
        const sickLeave = $(rowContainer).find(empSel.sickLeaveSpan).html();
        const vacayLeave = $(rowContainer).find(empSel.vacayLeaveSpan).html();
        $(`${empSel.firstNameInput}`).val(firstName);
        $(`${empSel.middleNameInput}`).val(middleName);
        $(`${empSel.lastNameInput}`).val(lastName);
        $(`${empSel.emailInput}`).val(email);
        $(`${empSel.birthDateInput}`).val(birthDate);
        $(`${empSel.departmentSelect}`).val(departmentId);
        $(`${empSel.empTypeSelect}`).val(empType);
        $(`${empSel.positionSelect}`).val(positionId);
        $(`${empSel.sickLeaveInput}`).val(sickLeave);
        $(`${empSel.vacayLeaveInput}`).val(vacayLeave);
    }
    function clearEmpFormInputs() {
        $(empSel.formInputs).val('');
        $(empSel.formSelects).val('');
        $(empSel.formNumInputs).val(15);
        resetPasswRules();
    }
    function getEmpFormData() {
        const fname = $(empSel.firstNameInput).val();
        const mname = $(empSel.middleNameInput).val();
        const lname = $(empSel.lastNameInput).val();
        const bdate = $(empSel.birthDateInput).val();
        const deptId = $(empSel.departmentSelect).val();
        const empType = $(empSel.empTypeSelect).val();
        const posId = $(empSel.positionSelect).val();
        const sickLeave = $(empSel.sickLeaveInput).val();
        const vacayLeave = $(empSel.vacayLeaveInput).val();
        const email = $(empSel.emailInput).val();
        const pass = $(empSel.passwordInput).val();
        const confrmPass = $(empSel.confirmPassInput).val();
        const empData = {
            first_name: fname,
            middle_name: mname,
            last_name: lname,
            birthdate: bdate,
            department_id: deptId,
            employment_type: empType,
            position_id: posId,
            sick_leave: sickLeave,
            vacation_leave: vacayLeave,
        };
        const usrData = { 
            email: email,
            password: pass,
            password_confirmation: confrmPass,
        };
        return { emp_data: empData, usr_data: usrData };
    }
    function requirePassword(isRequired) {
        $(empSel.passwordInput).prop('required', isRequired);
        $(empSel.confirmPassInput).prop('required', isRequired);
    }
    function validatePassword(){
        const passwEl = $(empSel.passwordInput);
        const confrmPasswEl = $(empSel.confirmPassInput);
        const passwRulesEl = $(empSel.passwRulesContainer);
        const passwVal = passwEl.val();
        const confrmPasswVal = confrmPasswEl.val();
        if (passwVal != confrmPasswVal) {
          confrmPasswEl[0].setCustomValidity("Passwords Don't Match");
        } else {
          confrmPasswEl[0].setCustomValidity('');
        }
        if ((passwVal.length !== 0) || (confrmPasswVal.length !== 0)) {
            const letterRuleEl = $(empSel.passwLetter);
            const capitalRuleEl = $(empSel.passwCapital);
            const symbolRuleEl = $(empSel.passwSymbol);
            const numberRuleEl = $(empSel.passwNumber);
            const lenghtRuleEl = $(empSel.passwLength);
            const lowerCaseLetters = /[a-z]/g;
            const upperCaseLetters = /[A-Z]/g;
            const numbers = /[0-9]/g;
            const specialChars = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
            let letterPassed, capitalPassed, symbolPassed, numberPassed, lengthPassed;
            letterPassed = capitalPassed = symbolPassed = numberPassed = lengthPassed = false;
            passwRulesEl.removeClass('hidden');
            if (passwVal.match(lowerCaseLetters)) {  
                letterRuleEl.removeClass("invalid");
                letterRuleEl.addClass("valid");
                letterPassed = true;
            } else {
                letterRuleEl.removeClass("valid");
                letterRuleEl.addClass("invalid");
                letterPassed = false;
            }
            if (passwVal.match(upperCaseLetters)) {  
                capitalRuleEl.removeClass("invalid");
                capitalRuleEl.addClass("valid");
                capitalPassed = true;
            } else {
                capitalRuleEl.removeClass("valid");
                capitalRuleEl.addClass("invalid");
                capitalPassed = false;
            }
            if (specialChars.test(passwVal)) {  
                symbolRuleEl.removeClass("invalid");
                symbolRuleEl.addClass("valid");
                symbolPassed = true;
            } else {
                symbolRuleEl.removeClass("valid");
                symbolRuleEl.addClass("invalid");
                symbolPassed = false;
            }
            if (passwVal.match(numbers)) {  
                numberRuleEl.removeClass("invalid");
                numberRuleEl.addClass("valid");
                numberPassed = true;
            } else {
                numberRuleEl.removeClass("valid");
                numberRuleEl.addClass("invalid");
                numberPassed = false;
            }
            if (passwVal.length >= 8) {  
                lenghtRuleEl.removeClass("invalid");
                lenghtRuleEl.addClass("valid");
                lengthPassed = true;
            } else {
                lenghtRuleEl.removeClass("valid");
                lenghtRuleEl.addClass("invalid");
                lengthPassed = false;
            }
            if (
                letterPassed && 
                capitalPassed && 
                symbolPassed && 
                numberPassed && 
                lengthPassed
            ) {
                $(empSel.submitFormBtn).prop('disabled', false);
            } else {
                $(empSel.submitFormBtn).prop('disabled', true);
            }
        } else {
            passwRulesEl.addClass('hidden');
            $(empSel.submitFormBtn).prop('disabled', false);
        }

    }
    function toggleShowPassword() {
        const passwEl = $(empSel.passwordInput);
        const confrmPasswEl = $(empSel.confirmPassInput);
        const passwType = passwEl.attr('type');
        if (passwType === "password") {
            passwEl.prop('type', 'text');
            confrmPasswEl.prop('type', 'text');
        } else {
            passwEl.prop('type', 'password');
            confrmPasswEl.prop('type', 'password');
        }
    }
    function resetPasswRules() {
        $(empSel.passwRulesContainer).addClass('hidden');
        $(empSel.passwRules).removeClass('valid');
        $(empSel.passwRules).addClass('invalid');
    }
    /**
     * Synchronizes dataTable's data in different state (responsive state, or not)
     */
    function configureTableOnResize(e) {
        dataTable
            .rows()
            .invalidate('dom')
            .draw();
    }
    
    /**
     * +++++++++++ Editing Employee [START] +++++++++++
     */
    function onRowEditEmp() {
        const clickedEditBtn = this;
        const empId = $(clickedEditBtn).attr(dataAttr.empId);
        const rowSel = empSel.prefix + empId;
        const rowContainer = empSel.trContainer + rowSel;
        empSel.clickedEditBtn = empId;
        autoFillEmpForm(rowContainer);
        requirePassword(false);
        $(empSel.modalTitle).html('Edit Employee');
        $(empSel.submitFormBtn).html('Update');
        $(empSel.form).off();
        $(empSel.form).on('submit', onSubmitEditedEmp);
        $(empSel.formModal).modal('show');
    }
    function updateRowEmp(rowSel, updated) {
        $(`${empSel.trContainer} ${empSel.fullNameSpan + rowSel}`).html(updated.full_name);
        $(`${empSel.trContainer} ${empSel.firstNameSpan + rowSel}`).html(updated.first_name);
        $(`${empSel.trContainer} ${empSel.middleNameSpan + rowSel}`).html(updated.middle_name);
        $(`${empSel.trContainer} ${empSel.lastNameSpan + rowSel}`).html(updated.last_name);
        $(`${empSel.trContainer} ${empSel.emailSpan + rowSel}`).html(updated.email);
        $(`${empSel.trContainer} ${empSel.birthDateSpan + rowSel}`).html(updated.birthdate);
        $(`${empSel.trContainer} ${empSel.supervisorSpan + rowSel}`).html(updated.supervisor);
        $(`${empSel.trContainer} ${empSel.empTypeSpan + rowSel}`).html(updated.employment_type);
        $(`${empSel.trContainer} ${empSel.sickLeaveSpan + rowSel}`).html(updated.sick_leave);
        $(`${empSel.trContainer} ${empSel.vacayLeaveSpan + rowSel}`).html(updated.vacation_leave);
        $(`${empSel.trContainer} ${empSel.departmentSpan + rowSel}`).html(updated.department);
        $(`${empSel.trContainer} ${empSel.departmentSpan + rowSel}`).attr(dataAttr.deptId, updated.department_id);
        $(`${empSel.trContainer} ${empSel.positionSpan + rowSel}`).html(updated.position);
        $(`${empSel.trContainer} ${empSel.positionSpan + rowSel}`).attr(dataAttr.posId, updated.position_id);
        // when data table is in responsive state
        $(`${empSel.trResponsive} ${empSel.fullNameSpan + rowSel}`).html(updated.full_name);
        $(`${empSel.trResponsive} ${empSel.firstNameSpan + rowSel}`).html(updated.first_name);
        $(`${empSel.trResponsive} ${empSel.middleNameSpan + rowSel}`).html(updated.middle_name);
        $(`${empSel.trResponsive} ${empSel.lastNameSpan + rowSel}`).html(updated.last_name);
        $(`${empSel.trResponsive} ${empSel.emailSpan + rowSel}`).html(updated.email);
        $(`${empSel.trResponsive} ${empSel.birthDateSpan + rowSel}`).html(updated.birthdate);
        $(`${empSel.trResponsive} ${empSel.supervisorSpan + rowSel}`).html(updated.supervisor);
        $(`${empSel.trResponsive} ${empSel.empTypeSpan + rowSel}`).html(updated.employment_type);
        $(`${empSel.trResponsive} ${empSel.sickLeaveSpan + rowSel}`).html(updated.sick_leave);
        $(`${empSel.trResponsive} ${empSel.vacayLeaveSpan + rowSel}`).html(updated.vacation_leave);
        $(`${empSel.trResponsive} ${empSel.departmentSpan + rowSel}`).html(updated.department);
        $(`${empSel.trResponsive} ${empSel.departmentSpan + rowSel}`).attr(dataAttr.deptId, updated.department_id);
        $(`${empSel.trResponsive} ${empSel.positionSpan + rowSel}`).html(updated.position);
        $(`${empSel.trResponsive} ${empSel.positionSpan + rowSel}`).attr(dataAttr.posId, updated.position_id);
    }
    function onSubmitEditedEmp(e) {
        const clickedEditBtnSel = empSel.clickedEditBtn;
        const empId = $(clickedEditBtnSel).attr(dataAttr.empId);
        const usrId = $(clickedEditBtnSel).attr(dataAttr.userId);
        const rowId = empSel.prefix + empId;
        const trRowSel = empSel.tr(rowId); 
        const empFormData = getEmpFormData();
        const requestData = JSON.stringify({ emp_id: empId, usr_id: usrId, ...empFormData });
        fetch(empApi.update, {
            ...postMethod,
            body: requestData
        }).then(response => {
            if (response.ok) {
                response.json().then(emp => {
                    updateRowEmp(rowId, emp.data);
                    successModalAlert('Employee Updated!');
                    $(empSel.formModal).modal('hide');
                    clearEmpFormInputs();
                    $(trRowSel).animate({backgroundColor: '#d3d3f5'});
                })
            } else {
                errorNotif(empSel.form, response);
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        loadingModalAlert('Saving Data');
        e.preventDefault();
        return false;
    }
    /**
     * +++++++++++ Editing Employee [END] +++++++++++

    /**
     * +++++++++++ Deleting Employee [START] +++++++++++
     */
    function removeRowEmp(delBtn) {
        const row = $(delBtn).parents('tr');
        row.animate({ backgroundColor: '#ff8787' });
        setTimeout(() => {
            dataTable
                .row(row)
                .remove()
                .draw();
        }, 1100);
    }
    function onDeleteRowEmp(e) {
        const delBtn = this;
        const dropleft = $(delBtn).parents(empSel.dropleft);
        confirmNotif(dropleft, 'delete', () => {
            const empId = $(delBtn).attr(dataAttr.empId);
            $(empSel.selectedEmps).find(`span[${dataAttr.empId}=${empId}]`).remove();
            const requestData = JSON.stringify({ emp_id: empId });
            fetch(empApi.delete, {
                ...postMethod,
                body: requestData
            }).then(response => {
                if (response.ok) {
                    successModalAlert('Deleted!');
                    removeRowEmp(delBtn);
                } else {
                    errorNotif(table, response);
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                failedModalAlert();
                console.log('Something went wrong,', err);
            });
        });
    }
    /**
     * +++++++++++ Deleting Employee [END] +++++++++++
     */

    /**
     * +++++++++++ Adding Employee [START] +++++++++++
     */
    function onAddEmployee() {
        clearEmpFormInputs();
        requirePassword(true);
        $(empSel.modalTitle).html('Create Employee');
        $(empSel.submitFormBtn).html('Create');
        $(empSel.formModal).modal('show');
        $(empSel.form).off();
        $(empSel.form).on('submit', onSubmitCreatedEmp);
    }
    function createEmpRowSpans(emp) {
        return [
            // col 1
            `<img src="${emp.avatar}" class="avatar">`,
            // col 2
            `<span class="emp-${emp.id} ${empCls.fullName}" ${dataAttr.empId}=${emp.id}>${emp.full_name}</span>
             <br>
             <span class="font-sm italic emp-${emp.id} ${empCls.email}">${emp.email}</span>
             <span class="hidden emp-${emp.id} ${empCls.firstName}">${emp.first_name}</span>
             <span class="hidden emp-${emp.id} ${empCls.middleName}">${emp.middle_name}</span>
             <span class="hidden emp-${emp.id} ${empCls.lastName}">${emp.last_name}</span>`,
             // col 3
            `<span class="emp-${emp.id} ${empCls.birthDate}">${emp.birthdate}</span>`,
             // col 4
            `<span class="emp-${emp.id} ${empCls.department}" ${dataAttr.deptId}="${emp.department_id}">${emp.department}</span>`,
             // col 5
            `<span class="emp-${emp.id} ${empCls.supervisor}">${emp.supervisor}</span>`,
             // col 6
            `<span class="emp-${emp.id} ${empCls.position}" ${dataAttr.posId}="${emp.position_id}">${emp.position}</span>`,
             // col 7
            `<span class="emp-${emp.id} ${empCls.empType}">${emp.employment_type}</span>`,
             // col 8
            `<span class="emp-${emp.id} ${empCls.sickLeave}">${emp.sick_leave}</span>`,
             // col 9
            `<span class="emp-${emp.id} ${empCls.vacayLeave}">${emp.vacation_leave}</span>`,
        ];
    }
    function createEmpRowButtons(emp) {
        // col 10
        return `<div class="btn-group dropleft">
                    <button type="button" class="btn btn-lg dropdown-toggle settings" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        &#9881; Settings
                    </button>
                    <div class="dropdown-menu box">
                        <a class="dropdown-item cursor-pointer timesheets-route" href="${emp.timesheets_route}">&#x23F0; View Timesheets</a>
                        <a class="dropdown-item cursor-pointer schedules-route" href="${emp.schedules_route}">&#x1F4C5; View Schedules</a>
                        <a class="dropdown-item cursor-pointer edit" ${dataAttr.empId}="${emp.id}" ${dataAttr.userId}="${emp.user_id}">&#x270f;&#xfe0f; Edit</a>
                        <a class="dropdown-item cursor-pointer delete" ${dataAttr.empId}="${emp.id}">&#x1F5D1; Delete</a>
                    </div>
                </div>`;
    }
    function addNewRowEmp(emp) {
        const spans = createEmpRowSpans(emp);
        const buttons = createEmpRowButtons(emp);
        let rowNode = dataTable.row.add([--generic.newRowIndex, null, null, ...spans, buttons]).draw();
        $(empSel.newRow).animate({backgroundColor: '#c5fcff'}); // makes recently added row BLUE
        dataTable.order([0, 'asc']).draw();
        rowNode = rowNode.node();
        $(rowNode)
            .addClass(`${empCls.createdRow} ${empCls.prefix + emp.id}`)
            .attr(dataAttr.empId, emp.id)
            .css({ 'background-color': '#7070fda6' })
            .animate({ backgroundColor: '#a7fda7' }); // makes newest added row Green
    }
    function onSubmitCreatedEmp(e) {
        const formElement = this;
        const empFormData = getEmpFormData();
        const requestData = JSON.stringify(empFormData);
        fetch(empApi.create, {
            ...postMethod,
            body: requestData,
        }).then(response => {
            if (response.ok) {
                response.json().then(emp => {
                    successModalAlert('Employee Added!');
                    $(empSel.formModal).modal('hide');
                    clearEmpFormInputs();
                    setTimeout(() => addNewRowEmp(emp), 1100);
                });
            } 
            else {
                errorNotif(formElement, response);
                throw response.statusText + " " + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        e.preventDefault();
        loadingModalAlert('Saving Employee Data');
        return false;
    }
    /**
     * +++++++++++ Adding Employee [END] +++++++++++
     */

    /**
     * +++++++++++ Add Timesheets [START] +++++++++++
     */
    function onShowAddTimesheetsModal() {
        $(empSel.timesheetsLogs).addClass('hidden');
        $(empSel.addTimesheetModal).modal('show');
    }
    function selectRow(e, dt, type, indexes) {
        if ( type === 'row' ) {
            const data = dataTable.rows(indexes).nodes().to$();
            const empSpan = data.find(empSel.fullNameSpan);
            const empFname = empSpan.html();
            const empId = empSpan.attr(dataAttr.empId);
            $(empSel.selectedEmps).append(`<span ${dataAttr.empId}=${empId}>${empFname}</span>`);
            $(empSel.confSelEmpsBtn).prop('disabled', false);
            $(empSel.selectAllBtn).addClass('hidden');
            $(empSel.deselectAllBtn).removeClass('hidden');
        }
    }
    function deselectRow(e, dt, type, indexes) {
        if ( type === 'row' ) {
            const rows = Object.values(dataTable.rows(indexes).nodes().to$());
            rows.forEach(row => {
                const empId = $(row).find(empSel.fullNameSpan).attr(dataAttr.empId);
                $(empSel.selectedEmps).find(`span[${dataAttr.empId}=${empId}]`).remove();
            });
            const selectedEmployees = $(empSel.selectedEmps).find('span');
            if (selectedEmployees.length === 0) {
                $(empSel.selectedEmps).css({ height: '0px' });
                $(empSel.confSelEmpsBtn).prop('disabled', true);
            }
        }
    }
    function selectAll() {
        let skip = 1;
        $(empSel.selectedEmps).css({ height: '100px' });
        dataTable.rows().select();
        $(empSel.confSelEmpsBtn).prop('disabled', false);
        $.map(dataTable.rows().nodes().to$(), function (row) {
            if (skip !== 0) {
                skip--;
                return;
            }
            const empSpan = $(row).find(empSel.fullNameSpan);
            const empFname = empSpan.html();
            const empId = empSpan.attr(dataAttr.empId);
            $(empSel.selectedEmps).append(`<span ${dataAttr.empId}=${empId}>${empFname}</span>`);
            $(empSel.confSelEmpsBtn).prop('disabled', false);
        });
    }
    function deselectAll() {
        dataTable.rows().deselect();
        $(empSel.confSelEmpsBtn).prop('disabled', true);
        $(empSel.selectAllBtn).removeClass('hidden');
        $(empSel.deselectAllBtn).addClass('hidden');
    }
    function disableTimeInputs(obj, disabled) {
        $(obj.timeInInput).prop('disabled', disabled);
        $(obj.timeOutInput).prop('disabled', disabled);
        $(obj.lunchStartInput).prop('disabled', disabled);
        $(obj.lunchEndInput).prop('disabled', disabled);
        $(obj.overtimeStartInput).prop('disabled', disabled);
        $(obj.overtimeEndInput).prop('disabled', disabled);
    }
    function clearTimeInputs(obj) {
        $(obj.timeInInput).val('');
        $(obj.timeOutInput).val('');
        $(obj.lunchStartInput).val('');
        $(obj.lunchEndInput).val('');
        $(obj.overtimeStartInput).val('');
        $(obj.overtimeEndInput).val('');   
    }
    function grayOutTimeInputs(obj) {
        $(obj.timeInInput).addClass('bg-gray-100');
        $(obj.timeOutInput).addClass('bg-gray-100');
        $(obj.lunchStartInput).addClass('bg-gray-100');
        $(obj.lunchEndInput).addClass('bg-gray-100');
        $(obj.overtimeStartInput).addClass('bg-gray-100');
        $(obj.overtimeEndInput).addClass('bg-gray-100');
    }
    function toggleGrayOutTimeInputs(obj) {
        $(obj.timeInInput).toggleClass('bg-gray-100');
        $(obj.timeOutInput).toggleClass('bg-gray-100');
        $(obj.lunchStartInput).toggleClass('bg-gray-100');
        $(obj.lunchEndInput).toggleClass('bg-gray-100');
        $(obj.overtimeStartInput).toggleClass('bg-gray-100');
        $(obj.overtimeEndInput).toggleClass('bg-gray-100');
    }
    function resetTmsInputs(obj) {
        disableTimeInputs(obj, true);
        clearTimeInputs(obj);
        grayOutTimeInputs(obj);
    }
    function toggleTmsInputs(obj) {
        disableTimeInputs(obj, (i, v) => !v);
        clearTimeInputs(obj);
        toggleGrayOutTimeInputs(obj);
    }
    function clearAddTimesheetFormInputs() {
        $(empSel.tmsFormDayCheckBoxBtn).prop('checked', false);
        $(empSel.tmsFormDayCheckBoxBtn).prop('required', true);
        $(empSel.startDateInput).val('');
        $(empSel.endDateInput).val('');
        resetTmsInputs({
            timeInInput: `${empSel.addTimesheetModal} ${empSel.timeInInput}`,
            timeOutInput: `${empSel.addTimesheetModal} ${empSel.timeOutInput}`,
            lunchStartInput: `${empSel.addTimesheetModal} ${empSel.lunchStartInput}`,
            lunchEndInput: `${empSel.addTimesheetModal} ${empSel.lunchEndInput}`,
            overtimeStartInput: `${empSel.addTimesheetModal} ${empSel.overtimeStartInput}`,
            overtimeEndInput: `${empSel.addTimesheetModal} ${empSel.overtimeEndInput}`,
        });
        $(empSel.timesheetCollapseCont).collapse('hide');
    }
    function onToggleSchedTimeInput() {
        const dayChecked = $(this).val();
        const schedCont = $(this).parents(`div.${dayChecked}`);
        const timeInInput = $(schedCont).find(empSel.timeInInput);
        const timeOutInput = $(schedCont).find(empSel.timeOutInput);
        const lunchStartInput = $(schedCont).find(empSel.lunchStartInput);
        const lunchEndInput = $(schedCont).find(empSel.lunchEndInput);
        const overtimeStartInput = $(schedCont).find(empSel.overtimeStartInput);
        const overtimeEndInput = $(schedCont).find(empSel.overtimeEndInput);
        toggleTmsInputs({
            timeInInput: timeInInput,
            timeOutInput: timeOutInput,
            lunchStartInput: lunchStartInput,
            lunchEndInput: lunchEndInput,
            overtimeStartInput: overtimeStartInput,
            overtimeEndInput: overtimeEndInput,
        });
        if ($(this).prop('checked')) {
            $(empSel.tmsFormDayCheckBoxBtn).prop('required', false);
        } else {
            const daysCheckBoxes = $(empSel.tmsFormDayCheckBoxBtn);
            let requireCheck = true;
            for (let index = 0; index < Object.keys(daysCheckBoxes).length; index++) {
                if (daysCheckBoxes[index]?.checked) {
                    requireCheck = false;
                    break;
                }
                requireCheck = true;
            }
            $(empSel.tmsFormDayCheckBoxBtn).prop('required', requireCheck);
        }
    }
    function showFailedTimesheets(data) {
        $(empSel.noScheds).html('');
        Object.keys(data).forEach(employee => {
            if (data[employee].length === 0) return;
            let days = '';
            data[employee].forEach(day => days += `<div class="w-40 rounded-pill bg-gradient shadow-sm px-4 py-1 mb-1">${day}</div>`);
            $(empSel.noScheds).append(`
                <div class="flex flex-col">
                    <div class="bg-secondary font-black rounded-pill" style="color: black">${employee}</div>
                    <div class="w-full flex flex-col items-center">${days}</div>
                    <hr class="w-full border-1 border-gray-100 shadow-sm">
                </div>
            `);
        });
        $(empSel.timesheetsLogs).removeClass('hidden');
    }
    function onSubmitEmployeesTimesheets(e) {
        loadingModalAlert(`
            <div style="line-height: 25px !important; margin-top: -46px;">
                <span style="font-size: 35px;">Processing timesheets </span>
            </div>
            <div style="line-height: 15px !important; margin-top: -45px;">
                <span style="font-size: 15px;">Please be patient, this will require some time...</span>
            </div>
        `);
        const formElement = this;
        const selectedEmployees = $(empSel.selectedEmps).find('span');
        const employeesTimesheets = {
            start_date: $(empSel.startDateInput).val(),
            end_date: $(empSel.endDateInput).val(),
            timesheets: {},
            employees: [],
        };
        generic.weekDays.forEach(day => {
            const dayCont = empSel.dayCont(day);
            const isDayChecked = $(`${dayCont} ${empSel.dayCheckBoxBtn}`).prop('checked');
            if (!isDayChecked) return;
            employeesTimesheets.timesheets[day] = {
                time_in: $(`${dayCont} ${empSel.timeInInput}`).val(),
                time_out: $(`${dayCont} ${empSel.timeOutInput}`).val(),
                lunch_start: $(`${dayCont} ${empSel.lunchStartInput}`).val(),
                lunch_end: $(`${dayCont} ${empSel.lunchEndInput}`).val(),
                overtime_start: $(`${dayCont} ${empSel.overtimeStartInput}`).val(),
                overtime_end: $(`${dayCont} ${empSel.overtimeEndInput}`).val(),
            };
        });
        for (let index = 0; index < selectedEmployees.length; index++) {
            const empId = $(selectedEmployees[index]).attr(dataAttr.empId);
            employeesTimesheets.employees.push(empId);
        }
        const requestData = JSON.stringify(employeesTimesheets);
        fetch(empApi.createForEmployees, {
            ...postMethod,
            body: requestData
        }).then(response => {
            if (response.ok) {
                response.json().then(noScheds => {
                    successModalAlert('Operation Successful!');
                    clearAddTimesheetFormInputs();
                    showFailedTimesheets(noScheds);
                });
            } else {
                errorNotif(formElement, response);
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        e.preventDefault();
        return false;
    }
    /**
     * +++++++++++ Add Timesheets [END] +++++++++++
     */

    /**
     * Bindings
     */
    $(document).on('click', empSel.tableEditRowBtn, onRowEditEmp);
    $(document).on('click', empSel.tableDeleteRowBtn, onDeleteRowEmp);
    $(document).on('click', empSel.tmsFormDayCheckBoxBtn, onToggleSchedTimeInput);
    $(document).on('click', empSel.showTimesheetModalBtn, onShowAddTimesheetsModal);
    $(document).on('submit', empSel.addTimesheetForm, onSubmitEmployeesTimesheets);
    $(empSel.deselectAllBtn).on('click', deselectAll);
    $(empSel.selectAllBtn).on('click', selectAll);
    $(empSel.showFormBtn).on('click', onAddEmployee);
    $(empSel.showPassword).on('click', toggleShowPassword);
    $(empSel.passwordInput).on('keyup', validatePassword);
    $(empSel.confirmPassInput).on('keyup', validatePassword);
    dataTable.on('select', selectRow);
    dataTable.on('deselect', deselectRow);
    window.addEventListener('resize', configureTableOnResize, true);
});


/**
 * =================================== Timesheet Management ===================================
 */
$(() => {
    const table = '.timesheet-management #timesheet-datatable';
    if ($(table).length === 0) return;
    const buttonConig = {
        title: 'Timesheets',
        exportOptions: {
            columns: ':not(.no-export)'
        }
    }
    const dataTable = $(table).DataTable({
        deferRender: true,
        order: [[1, 'asc']],
        columnDefs: [{targets: [10], orderable: false}, {targets: [0], visible: false, searchable: false}],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], 
        // select: true,
        autoWidth: false,
        responsive: {
            details: {
                renderer: function ( api, rowIdx, columns ) {
                    const data = $.map( columns, function ( col, i ) {
                        return col.hidden ?
                            (col.title 
                                ? '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                    '<td class="font-black text-xs" style="white-space: pre-wrap; word-wrap: break-word;">'+col.title+':'+'</td> '+
                                    `<td class="text-xs" style="cursor: pointer; white-space: pre-wrap; word-wrap: break-word;">`
                                        +col.data+
                                    `</td>
                                </tr>`
                                : '<tr class="bg-transparent" data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                    `<td colspan=2 class="border-0 p-0 pt-2">
                                        <div class="border-2 p-2 rounded-2xl flex justify-center">`+col.data+`</div>
                                    </td>
                                </tr>`
                            ) : '';
                    } ).join('');
                    const rowAsTable = $(`<table class="timesheet-container" />`).append( data );
                    return data ? rowAsTable : false;
                }
            }
        },
        // dom: 'lBfrtip', // @note
        dom: `
            <"flex justify-center md:justify-between flex-wrap items-center"
                B
                <"flex justify-between flex-wrap"
                    <"mt-2 md:mt-0 md:ml-2"l>
                >
                <"m-2"f>
            >rt
            <"flex justify-center md:justify-between flex-wrap"
                <"m-2"i><"m-2"p>
            >
        `,
        buttons: [ 
            {
                extend: 'print',
                ...buttonConig
            },
            {
                extend: 'csvHtml5',
                ...buttonConig
            },
            {
                extend: 'excelHtml5',
                ...buttonConig
            },
            {
                extend: 'pdfHtml5',
                ...buttonConig
            }
        ]
    });

    const tmsApi = {
        get create() {return `/api/timesheet/create`}, 
        get update() {return `/api/timesheet/update`},
        get delete() {return `/api/timesheet/delete`},
    }
    const generic = {
        get weekDays() {
            return [
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday',
            ];
        },
        get clockTypes() {
            return [
                'time_in',
                'time_out',
                'lunch_start',
                'lunch_end',
                'overtime_start',
                'overtime_end',
            ];
        },
        get clockTypeClass() {
            return [
                'time-in',
                'time-out',
                'lunch-start',
                'lunch-end',
                'ot-start',
                'ot-end',
            ];
        },
    }

    function formatTime(obj, format = 'LT', currentFormat = 'hh:mm:ss') {
        const result = JSON.parse(JSON.stringify(obj));
        let keys = Object.keys(result);
        keys.forEach(key => {
            if (result[key]) result[key] = moment(result[key], currentFormat).format(format);
            else result[key] = null;
        });
        return result;
    }
    function onTrackRowSaveButton(e) {
        $(`${table} button.save`).removeClass('clicked');
        $(this).addClass('clicked');
    }
    function autoFillRowInput(rowContainer) {
        const data = {
            time_in: $(rowContainer).find(`span.time-in`).html(),
            time_out: $(rowContainer).find(`span.time-out`).html(),
            lunch_start: $(rowContainer).find(`span.lunch-start`).html(),
            lunch_end: $(rowContainer).find(`span.lunch-end`).html(),
            overtime_start: $(rowContainer).find(`span.ot-start`).html(),
            overtime_end: $(rowContainer).find(`span.ot-end`).html(),
        };
        const formattedClocks = formatTime(data, 'HH:mm', 'LT');
        $(rowContainer).find(`input[name="time_in"]`).val(formattedClocks.time_in);
        $(rowContainer).find(`input[name="time_out"]`).val(formattedClocks.time_out);
        $(rowContainer).find(`input[name="lunch_start"]`).val(formattedClocks.lunch_start);
        $(rowContainer).find(`input[name="lunch_end"]`).val(formattedClocks.lunch_end);
        $(rowContainer).find(`input[name="overtime_start"]`).val(formattedClocks.overtime_start);
        $(rowContainer).find(`input[name="overtime_end"]`).val(formattedClocks.overtime_end);
    }
    function configureTableOnResize(event) {
        closeAllRowEdit();
        dataTable
            .rows()
            .invalidate('dom')
            .draw();
        dataTable.columns.adjust().responsive.recalc();
    }

    /**
     * +++++++++++ Editing Timesheet [START] +++++++++++
     */
    function onEditRowTimesheet() {
        const row = `tms-${$(this).attr('data-timesheet-id')}`;
        const rowContainer = $(this).parents('.timesheet-container');
        autoFillRowInput(rowContainer);
        // hide
        $(`${table} span.${row}`).addClass('hidden');
        $(`${table} div.actions.${row} button.edit`).addClass('hidden');
        // show
        $(`${table} input.${row}`).removeClass('hidden');
        $(`${table} div.actions.${row} button.cancel`).removeClass('hidden');
        $(`${table} div.actions.${row} button.save`).removeClass('hidden');
    }
    function onCloseRowEdit(e, button = null) {
        if (!button) {
            button = this;
        }
        const container = $(button).parents('.timesheet-container');
        const timesheetId = $(container).find('button.cancel').attr('data-timesheet-id');
        const row = `tms-${timesheetId}`;
        // hide
        $(`${table} span.${row}`).removeClass('hidden');
        $(`${table} div.actions.${row} button.edit`).removeClass('hidden');
        // show
        $(`${table} input.${row}`).addClass('hidden');
        $(`${table} div.actions.${row} button.cancel`).addClass('hidden');
        $(`${table} div.actions.${row} button.save`).addClass('hidden');
    }
    function closeAllRowEdit() {
        // hide
        $(`${table} span.clock`).removeClass('hidden');
        $(`${table} div.actions button.edit`).removeClass('hidden');
        // show
        $(`${table} input.clock`).addClass('hidden');
        $(`${table} div.actions button.cancel`).addClass('hidden');
        $(`${table} div.actions button.save`).addClass('hidden');
    }
    function updateRowTimesheet(row, updatedData) {
        $(`tr.timesheet-container span.time-in.${row}`).html(updatedData.time_in);
        $(`tr.timesheet-container span.time-out.${row}`).html(updatedData.time_out);
        $(`tr.timesheet-container span.lunch-start.${row}`).html(updatedData.lunch_start);
        $(`tr.timesheet-container span.lunch-end.${row}`).html(updatedData.lunch_end);
        $(`tr.timesheet-container span.ot-start.${row}`).html(updatedData.overtime_start);
        $(`tr.timesheet-container span.ot-end.${row}`).html(updatedData.overtime_end);
        // when data table is in responsive state
        $(`tr.child span.time-in.${row}`).html(updatedData.time_in);
        $(`tr.child span.time-out.${row}`).html(updatedData.time_out);
        $(`tr.child span.lunch-start.${row}`).html(updatedData.lunch_start);
        $(`tr.child span.lunch-end.${row}`).html(updatedData.lunch_end);
        $(`tr.child span.ot-start.${row}`).html(updatedData.overtime_start);
        $(`tr.child span.ot-end.${row}`).html(updatedData.overtime_end);
    }
    function onSubmitEditedTimesheet(e) {
        const element = `${table} button.save.clicked`;
        const timesheetId = $(element).attr('data-timesheet-id');
        const row = `tms-${timesheetId}`;
        const timeIn = $(`.timesheet-container td:not([style*="display: none;"]) input[name="time_in"].${row}`).val();
        const timeOut = $(`.timesheet-container td:not([style*="display: none;"]) input[name="time_out"].${row}`).val();
        const lunchStart = $(`.timesheet-container td:not([style*="display: none;"]) input[name="lunch_start"].${row}`).val();
        const lunchEnd = $(`.timesheet-container td:not([style*="display: none;"]) input[name="lunch_end"].${row}`).val();
        const overtimeStart = $(`.timesheet-container td:not([style*="display: none;"]) input[name="overtime_start"].${row}`).val();
        const overtimeEnd = $(`.timesheet-container td:not([style*="display: none;"]) input[name="overtime_end"].${row}`).val();
        const timesheetData = {
            time_in: timeIn,
            time_out: timeOut,
            lunch_start: lunchStart,
            lunch_end: lunchEnd,
            overtime_start: overtimeStart,
            overtime_end: overtimeEnd,
        } 
        const formattedClocks = formatTime(timesheetData);
        const requestData = JSON.stringify({ timesheet_id: timesheetId, timesheet_data: timesheetData });
        fetch(tmsApi.update, {
            ...postMethod,
            body: requestData
        }).then(response => {
            if (response.ok) {
                onCloseRowEdit(null, element);
                updateRowTimesheet(row, formattedClocks);
                successModalAlert();
                $(`tr.${row}`).animate({backgroundColor: '#d3d3f5'});
            } else {
                errorNotif('form.edit-timesheet', response);
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        loadingModalAlert('Saving Data');
        e.preventDefault();
        return false;
    }
    /**
     * +++++++++++ Editing Timesheet [END] +++++++++++
     */

    /**
     * +++++++++++ Deleting Timesheet [START] +++++++++++
     */
    function removeRowTimesheet(element) {
        const timesheetRow = $(element).parents('tr');
        timesheetRow.animate({ backgroundColor: '#ff8787' });
        setTimeout(() => {
            dataTable
            .row(timesheetRow)
            .remove()
            .draw();
        }, 1500);
    }
    function onDeleteTimesheet() {
        const deleteButtonElement = this;
        confirmNotif(deleteButtonElement, 'delete', () => {
            const timesheetId = $(deleteButtonElement).attr('data-timesheet-id');
            const requestData = JSON.stringify({ timesheet_id: timesheetId });
            fetch(tmsApi.delete, {
                ...postMethod,
                body: requestData
            }).then(response => {
                if (response.ok) {
                    successModalAlert('Deleted!');
                    removeRowTimesheet(deleteButtonElement);
                } else {
                    errorNotif('form.edit-timesheet', response);
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                failedModalAlert();
                console.log('Something went wrong,', err);
            });
        });
        $('.notifyjs-custom-confirm-base .yes').prop('type', 'button');
        $('.notifyjs-custom-confirm-base .no').prop('type', 'button');
    }
    /**
     * +++++++++++ Deleting Timesheet [END] +++++++++++
     */

    /**
     * +++++++++++ Adding Timesheet [START] +++++++++++
     */
    function onShowAddTimesheetModal() {
        $('.timesheet-management .modal').modal('show');
    }
    function clearAddTimesheetFormInputs() {
        const modal = '.timesheet-management .add-timesheet-modal';
        $(`${modal} input[name="start_date"]`).val('');
        $(`${modal} input[name="end_date"]`).val('');
        resetTimesheetFormInputs({
            scheduleId: `${modal} select[name="schedule_id"]`,
            timeInInput: `${modal} input[name="time_in"]`,
            timeOutInput: `${modal} input[name="time_out"]`,
            lunchStartInput: `${modal} input[name="lunch_start"]`,
            lunchEndInput: `${modal} input[name="lunch_end"]`,
            overtimeStartInput: `${modal} input[name="overtime_start"]`,
            overtimeEndInput: `${modal} input[name="overtime_end"]`,
        });
        $(`${modal} input.day-checkbox`).prop('checked', false);
        $(`${modal} input.day-checkbox`).prop('required', true);
        $(`${modal} .collapse`).collapse('hide');
    }
    function disableInputs(obj, disable) {
        $(obj.scheduleId).prop('disabled', disable);
        $(obj.timeInInput).prop('disabled', disable);
        $(obj.timeOutInput).prop('disabled', disable);
        $(obj.lunchStartInput).prop('disabled', disable);
        $(obj.lunchEndInput).prop('disabled', disable);
        $(obj.overtimeStartInput).prop('disabled', disable);
        $(obj.overtimeEndInput).prop('disabled', disable);
    }
    function clearTimeInputs(obj) {
        $(obj.timeInInput).val('');
        $(obj.timeOutInput).val('');
        $(obj.lunchStartInput).val('');
        $(obj.lunchEndInput).val('');
        $(obj.overtimeStartInput).val('');
        $(obj.overtimeEndInput).val('');
        $(obj.scheduleId).prop('selectedIndex',0);
    }
    function resetTimesheetFormInputs(obj) {
        disableInputs(obj, true);
        clearTimeInputs(obj);
        $(obj.scheduleId).addClass('bg-gray-100');
        $(obj.timeInInput).addClass('bg-gray-100');
        $(obj.timeOutInput).addClass('bg-gray-100');
        $(obj.lunchStartInput).addClass('bg-gray-100');
        $(obj.lunchEndInput).addClass('bg-gray-100');
        $(obj.overtimeStartInput).addClass('bg-gray-100');
        $(obj.overtimeEndInput).addClass('bg-gray-100');
    }
    function toggleTimesheetFormInputs(obj) {
        disableInputs(obj, (i, v) => !v);
        clearTimeInputs(obj);
        $(obj.scheduleId).toggleClass('bg-gray-100');
        $(obj.timeInInput).toggleClass('bg-gray-100');
        $(obj.timeOutInput).toggleClass('bg-gray-100');
        $(obj.lunchStartInput).toggleClass('bg-gray-100');
        $(obj.lunchEndInput).toggleClass('bg-gray-100');
        $(obj.overtimeStartInput).toggleClass('bg-gray-100');
        $(obj.overtimeEndInput).toggleClass('bg-gray-100');
    }
    function onToggleDayTimesheetInputs() {
        const dayChecked = $(this).val();
        const schedCont = $(this).parents(`div.${dayChecked}`);
        const scheduleId = $(schedCont).find(`select[name="schedule_id"]`);
        const timeInInput = $(schedCont).find(`input[name="time_in"]`);
        const timeOutInput = $(schedCont).find(`input[name="time_out"]`);
        const lunchStartInput = $(schedCont).find(`input[name="lunch_start"]`);
        const lunchEndInput = $(schedCont).find(`input[name="lunch_end"]`);
        const overtimeStartInput = $(schedCont).find(`input[name="overtime_start"]`);
        const overtimeEndInput = $(schedCont).find(`input[name="overtime_end"]`);
        toggleTimesheetFormInputs({
            scheduleId: scheduleId,
            timeInInput: timeInInput,
            timeOutInput: timeOutInput,
            lunchStartInput: lunchStartInput,
            lunchEndInput: lunchEndInput,
            overtimeStartInput: overtimeStartInput,
            overtimeEndInput: overtimeEndInput,
        });
        if ($(this).prop('checked')) {
            $('input.day-checkbox').prop('required', false);
        } else {
            const daysCheckBoxes = $('input.day-checkbox');
            let requireCheck = true;
            for (let index = 0; index < Object.keys(daysCheckBoxes).length; index++) {
                if (daysCheckBoxes[index]?.checked) {
                    requireCheck = false;
                    break;
                }
                requireCheck = true;
            }
            $('input.day-checkbox').prop('required', requireCheck);
        }
    }
    // For inserting <inputs /> and data into NEW ADDED ROW in data table after submitting added timesheet
    function createTimesheetRowInputs(timesheet) {
        const cells = [];
        for (let index = 0; index < generic.clockTypes.length; index++) {
            const cell = `
                <div class="flex justify-center items-center">
                    <div class="overflow-x-auto w-36 h-14 flex justify-center items-center">
                        <span class="clock tms-${timesheet.id} ${generic.clockTypeClass[index]}">${timesheet[generic.clockTypes[index]][1]}</span>
                        <input class="clock tms-${timesheet.id} border-blue-500 rounded-2xl shadow-xl hidden" type="time" name="${generic.clockTypes[index]}" value="${timesheet[generic.clockTypes[index]][0]}" />
                    </div>    
                </div>`;
            cells.push(cell);
        }
        return cells;
    }
    /**
     * For inserting <buttons /> into NEW ADDED ROW in data table after submitting added timesheet
     */
    function createTimesheetRowButtons(timesheet) {
        return `
            <div class="tms-${timesheet.id} actions flex justify-end items-center">
                <div class="absolute">
                    <div class="cont-a flex justify-end items-center">
                        <div>
                            <button class="cancel px-2 rounded-xl border-gray-500 bg-gray-200 hover:text-white hidden" data-timesheet-id="${timesheet.id}" type="button">x</button>
                            <button class="save btn btn-success hidden" data-timesheet-id="${timesheet.id}" type="submit">
                                <i class="far fa-save"></i>
                            </button>
                            <button class="edit btn btn-info shadow-sm" data-timesheet-id="${timesheet.id}" type="button">
                                <i class="fas fa-user-edit"></i>
                            </button>
                        </div>
                        <div class="flex justify-center items-center w-14 h-10">
                            <button class="delete btn btn-danger absolute" data-timesheet-id="${timesheet.id}" type="button">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    let newRowIndex = 1;
    function addNewRowTimesheet(timesheets) {
        let loop = 0; 
        timesheets.forEach(timesheet => {
            setTimeout(() => {
                const inputs = createTimesheetRowInputs(timesheet);
                const buttons = createTimesheetRowButtons(timesheet);
                const rowNode = dataTable.row.add([--newRowIndex, timesheet.date, timesheet.day, ...inputs, null, timesheet.created_at, buttons]).draw(false).node();
                dataTable.order([0, 'asc']).draw();
                $(rowNode)
                    .addClass('timesheet-container new-row')
                    .css({ 'background-color': '#7070fda6' })
                    .animate({backgroundColor: '#a7fda7'});
                loop++;
                if (loop === timesheets.length) dataTable.columns.adjust().responsive.recalc();
            }, 100);
        });
    }
    function onSubmitAddedTimesheet(e) {
        const formElement = this;
        const modal = '.timesheet-management .add-timesheet-modal';
        const timesheetsData = {
            start_date: $(`${modal} input[name="start_date"]`).val(),
            end_date: $(`${modal} input[name="end_date"]`).val(),
            timesheets: {},
        };
        generic.weekDays.forEach(day => {
            const dayCont = `${modal} .${day}`;
            const isChecked = $(`${dayCont} input.day-checkbox`).prop('checked');
            const scheduleId = $(`${dayCont} select[name="schedule_id"]`).val();
            if (!scheduleId || !isChecked) return;
            timesheetsData.timesheets[day] = {
                schedule_id: scheduleId,
                time_in: $(`${dayCont} input[name="time_in"]`).val(),
                time_out: $(`${dayCont} input[name="time_out"]`).val(),
                lunch_start: $(`${dayCont} input[name="lunch_start"]`).val(),
                lunch_end: $(`${dayCont} input[name="lunch_end"]`).val(),
                overtime_start: $(`${dayCont} input[name="overtime_start"]`).val(),
                overtime_end: $(`${dayCont} input[name="overtime_end"]`).val(),
            }
        });
        const requestData = JSON.stringify(timesheetsData);
        fetch(tmsApi.create, {
            ...postMethod,
            body: requestData
        }).then(response => {
            if (response.ok) {
                response.json().then(timesheet => {
                    successModalAlert('Timesheet Added!');
                    $(modal).modal('hide');
                    clearAddTimesheetFormInputs();
                    // show new row and added timesheet data
                    $('tr.new-row').animate({backgroundColor: '#c5fcff'}); // makes recently added row BLUE
                    setTimeout(() => addNewRowTimesheet(timesheet), 1100);
                });
            } else {
                errorNotif(formElement, response);
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        e.preventDefault();
        return false;
    }
    /**
     * +++++++++++ Adding Timesheet [END] +++++++++++
     */

    /**
     * Bindings
     */
    $(document).on('click', `${table} button.edit`, onEditRowTimesheet);
    $(document).on('click', `${table} button.cancel`, onCloseRowEdit);
    $(document).on('click', `${table} button.delete`, onDeleteTimesheet);
    $(document).on('click', `${table} button.save`, onTrackRowSaveButton);
    $(document).on('click', `.timesheet-management .dtr-control`, onCloseRowEdit);
    $(document).on('click', `.timesheet-management button.add-timesheet`, onShowAddTimesheetModal);
    $(document).on('click', `.timesheet-management form.add-timesheet input.day-checkbox`, onToggleDayTimesheetInputs);
    $(document).on('submit', `.timesheet-management form.edit-timesheet`, onSubmitEditedTimesheet);
    $(document).on('submit', `.timesheet-management form.add-timesheet`, onSubmitAddedTimesheet);
    window.addEventListener('resize', configureTableOnResize, true);
});


/**
 * =================================== Schedule Management ===================================
 */
$(() => {
    const table = '.schedule-management #schedule-datatable';
    if ($(table).length === 0) return;
    const buttonConig = {
        title: 'Employee Schedules',
        exportOptions: {
            columns: ':not(.no-export)'
        }
    }
    const dataTable = $(table).DataTable({
        deferRender: true,
        order: [[2, 'asc']],
        columnDefs: [{targets: [1, 10], orderable: false}, {targets: [0], visible: false, searchable: false}],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], 
        responsive: {
            details: {
                renderer: function ( api, rowIdx, columns ) {
                    const data = $.map( columns, function ( col, i ) {
                        return col.hidden ?
                            (col.title 
                                ? '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                    '<td class="font-black text-xs" style="white-space: pre-wrap; word-wrap: break-word;">'+col.title+':'+'</td> '+
                                    `<td class="text-xs" style="cursor: pointer; white-space: pre-wrap; word-wrap: break-word;">`
                                        +col.data+
                                    `</td>
                                </tr>`
                                : '<tr class="bg-transparent" data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                                    `<td colspan=2 class="border-0 p-0 pt-2">
                                        <div class="border-2 p-2 rounded-2xl flex justify-center">`+col.data+`</div>
                                    </td>
                                </tr>`
                            ) : '';
                    } ).join('');
                    const rowAsTable = $(`<table class="schedule-container" />`).append( data );
                    return data ? rowAsTable : false;
                }
            }
        },
        // dom: 'lBfrtip', // @note
        dom: `
            <"flex justify-center md:justify-between flex-wrap items-center"
                B
                <"flex justify-between flex-wrap"
                    <"mt-2 md:mt-0 md:ml-2"l>
                >
                <"m-2"f>
            >rt
            <"flex justify-center md:justify-between flex-wrap"
                <"m-2"i><"m-2"p>
            >
        `,
        buttons: [ 
            {
                extend: 'print',
                ...buttonConig
            },
            {
                extend: 'csvHtml5',
                ...buttonConig
            },
            {
                extend: 'excelHtml5',
                ...buttonConig
            },
            {
                extend: 'pdfHtml5',
                ...buttonConig
            }
        ]
    });
    const addScheduleModal = '.schedule-management .add-schedule-modal';
    const addSchedForm = 'form.add-sched';
    const editSchedForm = 'form.edit-schedule';
    const inputName = {
        get employeeId() {return `employee_id`},
        get ay() {return `academic_year`},
        get period() {return `period`},
        get generateTimesheet() {return `generate_timesheet`},
        get day() {return `day`},
        get startTime() {return `start_time`},
        get endTime() {return `end_time`},
    }
    const schdApi = {
        create: '/api/schedule/create',
        update: '/api/schedule/update',
        delete: '/api/schedule/delete',
    }

    function formatTime(obj, format = 'LT', currentFormat = 'hh:mm:ss') {
        const result = JSON.parse(JSON.stringify(obj));
        let keys = Object.keys(result);
        keys.forEach(key => {
            if (result[key]) result[key] = moment(result[key], currentFormat).format(format);
            else result[key] = null;
        });
        return result;
    }
    function onTrackRowSaveButton(e) {
        $(`${table} button.save`).removeClass('clicked');
        $(this).addClass('clicked');
    }
    function autoFillRowInput(rowContainer) {
        const day = $(rowContainer).find(`span.day`).html();
        const data = {
            start_time: $(rowContainer).find(`span.start-time`).html(),
            end_time: $(rowContainer).find(`span.end-time`).html(),
        };
        const formattedClocks = formatTime(data, 'HH:mm', 'LT');
        $(rowContainer).find(`input[name="${inputName.startTime}"]`).val(formattedClocks.start_time);
        $(rowContainer).find(`input[name="${inputName.endTime}"]`).val(formattedClocks.end_time);
        $(rowContainer).find(`select[name="${inputName.day}"]`).val(day);
    }
    function configureTableOnResize(event) {
        closeAllRowEdit();
        dataTable
            .rows()
            .invalidate('dom')
            .draw();
    }

    /**
     * +++++++++++ Editing Schedule [START] +++++++++++
     */
    function onEditRowSchedule() {
        const row = `sched-${$(this).attr('data-schedule-id')}`;
        const rowContainer = $(this).parents('.schedule-container');
        autoFillRowInput(rowContainer);
        // hide
        $(`${table} span.editable.${row}`).addClass('hidden');
        $(`${table} div.actions.${row} button.edit`).addClass('hidden');
        // show
        $(`${table} input.editable.${row}`).removeClass('hidden');
        $(`${table} select.editable.${row}`).removeClass('hidden');
        $(`${table} div.actions.${row} button.cancel`).removeClass('hidden');
        $(`${table} div.actions.${row} button.save`).removeClass('hidden');
    }
    function onCloseRowEdit(e, button = null) {
        if (!button) button = this;
        const container = $(button).parents('.schedule-container');
        const scheduleId = $(container).find('button.cancel').attr('data-schedule-id');
        const row = `sched-${scheduleId}`;
        // hide
        $(`${table} span.editable.${row}`).removeClass('hidden');
        $(`${table} div.actions.${row} button.edit`).removeClass('hidden');
        // show
        $(`${table} input.editable.${row}`).addClass('hidden');
        $(`${table} select.editable.${row}`).addClass('hidden');
        $(`${table} div.actions.${row} button.cancel`).addClass('hidden');
        $(`${table} div.actions.${row} button.save`).addClass('hidden');
    }
    function closeAllRowEdit() {
        // hide
        $(`${table} span.editable`).removeClass('hidden');
        $(`${table} div.actions button.edit`).removeClass('hidden');
        // show
        $(`${table} input.editable`).addClass('hidden');
        $(`${table} select.editable`).addClass('hidden');
        $(`${table} div.actions button.cancel`).addClass('hidden');
        $(`${table} div.actions button.save`).addClass('hidden');
    }
    function updateRowSchedule(row, {updatedDay, updatedClocks}) {
        $(`tr.schedule-container span.day.${row}`).html(updatedDay);
        $(`tr.schedule-container span.start-time.${row}`).html(updatedClocks.start_time);
        $(`tr.schedule-container span.end-time.${row}`).html(updatedClocks.end_time);
        // when data table is in responsive state
        $(`tr.child span.day.${row}`).html(updatedDay);
        $(`tr.child span.start-time.${row}`).html(updatedClocks.start_time);
        $(`tr.child span.end-time.${row}`).html(updatedClocks.end_time);
    }
    function onSubmitEditedSchedule(e) {
        const submitButtonElement = `${table} button.save.clicked`;
        const scheduleId = $(submitButtonElement).attr('data-schedule-id');
        const row = `sched-${scheduleId}`;
        const day = $(`.schedule-container td:not([style*="display: none;"]) select[name="${inputName.day}"].${row}`).find(":selected").text();
        const startTime = $(`.schedule-container td:not([style*="display: none;"]) input[name="${inputName.startTime}"].${row}`).val();
        const endTime = $(`.schedule-container td:not([style*="display: none;"]) input[name="${inputName.endTime}"].${row}`).val();
        const scheduleTime = {
            start_time: startTime,
            end_time: endTime,
        };
        const scheduleData = {
            day: day,
            ...scheduleTime,
        } 
        const formattedClocks = formatTime(scheduleTime);
        const requestData = JSON.stringify({ schedule_id: scheduleId, schedule_data: scheduleData });
        fetch(schdApi.update, {
            ...postMethod,
            body: requestData
        }).then(response => {
            if (response.ok) {
                const updatedSchedule = {updatedDay: day, updatedClocks: formattedClocks};
                onCloseRowEdit(null, submitButtonElement);
                updateRowSchedule(row, updatedSchedule);
                successModalAlert();
                $(`tr.${row}`).animate({backgroundColor: '#d3d3f5'});
            } else {
                errorNotif(editSchedForm, response);
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        loadingModalAlert('Saving Data');
        e.preventDefault();
        return false;
    }
    /**
     * +++++++++++ Editing Schedule [END] +++++++++++
     */

    /**
     * +++++++++++ Deleting Schedule [START] +++++++++++
     */
    function removeRowSchedule(element) {
        const scheduleRow = $(element).parents('tr');
        scheduleRow.animate({ backgroundColor: '#ff8787' });
        setTimeout(() => {
            dataTable
                .row(scheduleRow)
                .remove()
                .draw();
        }, 1100);
    }
    function onDeleteSchedule() {
        const deleteButtonElement = this;
        confirmNotif(deleteButtonElement, 'delete', () => {
            const scheduleId = $(deleteButtonElement).attr('data-schedule-id');
            const requestData = JSON.stringify({ schedule_id: scheduleId });
            fetch(schdApi.delete, {
                ...postMethod,
                body: requestData
            }).then(response => {
                if (response.ok) {
                    successModalAlert('Deleted!');
                    removeRowSchedule(deleteButtonElement);
                } else {
                    errorNotif(editSchedForm, response);
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                failedModalAlert();
                console.log('Something went wrong,', err);
            });
        });
        $('.notifyjs-custom-confirm-base .yes').prop('type', 'button');
        $('.notifyjs-custom-confirm-base .no').prop('type', 'button');
    }
    /**
     * +++++++++++ Deleting Schedule [END] +++++++++++
     */

    /**
     * +++++++++++ Adding Schedule [START] +++++++++++
     */
    function onShowAddSchedModal() {
        $('.schedule-management .add-schedule-modal').modal('show');
    }
    // For inserting <inputs /> and data into NEW ADDED ROW in data table after submitting added schedule
    function createScheduleRowInputs(schedule) {
        return [
            // November 16 2021, schedule day is not editable
            // to make it editable, just add class: "editable"
            `
                <span class="sched-${schedule.id} day">${schedule.day}</span>
                <select class="sched-${schedule.id} border-blue-500 rounded-2xl shadow-xl hidden" name="day" required>
                    <option value="${schedule.day}" selected disabled hidden>${schedule.day}</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                </select>
            `,
            `
                <span class="editable sched-${schedule.id} start-time">${schedule.start_time[1]}</span>
                <input class="editable sched-${schedule.id} border-blue-500 rounded-2xl shadow-xl hidden" type="time" name="start_time" value="${schedule.start_time[0]}" />
            `,
            `
                <span class="editable sched-${schedule.id} end-time">${schedule.end_time[1]}</span>
                <input class="editable sched-${schedule.id} border-blue-500 rounded-2xl shadow-xl hidden" type="time" name="end_time" value="${schedule.end_time[0]}" />
            `,
            `
                <span class="academic-year-semester">${schedule.ay_semester}</span>
            `,
            `
                <span class="ay-start-date">${schedule.ay_start_date}</span>
            `,
            `
                <span class="ay-end-year">${schedule.ay_end_date}</span>
            `,
            `
                <span class="period">${schedule.ay_period}</span>
            `,
            `
                <span class="period">${schedule.created_at}</span>
            `
        ];
    }
    // For inserting <buttons /> into NEW ADDED ROW in data table after submitting added schedule
    function createScheduleRowButtons(schedule) {
        return `<div class="sched-${schedule.id} actions flex justify-end items-center">
                    <div class="absolute">
                        <div class="cont-a flex justify-end items-center">
                            <div>
                                <button class="cancel px-2 rounded-xl border-gray-500 bg-gray-200 hover:text-white shadow-sm hidden" data-schedule-id="${schedule.id}" type="button">x</button>
                                <button class="save btn btn-success shadow-sm hidden" data-schedule-id="${schedule.id}" type="submit">
                                    <i class="far fa-save"></i>
                                </button>
                                <button class="edit btn btn-info shadow-sm" data-schedule-id="${schedule.id}" type="button">
                                    <i class="fas fa-user-edit"></i>
                                </button>
                            </div>
                            <div class="flex justify-center items-center w-16 h-10">
                                <button class="delete btn btn-danger shadow-sm absolute" data-schedule-id="${schedule.id}" type="button">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
    }

    function clearAddSchedFormInputs() {
        $(`${addSchedForm} select[name="${inputName.ay}"]`).prop('selectedIndex', 0);
        $(`${addSchedForm} input[name="${inputName.period}"]`).val('whole year');
        $(`${addSchedForm} input[name="${inputName.generateTimesheet}"]`).prop('checked', false);
        $(`${addSchedForm} input[name="${inputName.day}"]`).prop('checked', false);
        $(`${addSchedForm} input[name="${inputName.day}"]`).prop('required', true);
        $(`${addSchedForm} input[type="time"]`).val('');
        $(`${addSchedForm} input[type="time"]`).prop('disabled', true);
        $(`${addSchedForm} input[type="time"]`).prop('disabled', true);
    }
    clearAddSchedFormInputs();

    function onToggleSchedTimeInput() {
        const parentRow = $(this).parent().parent();
        $(parentRow).find('input[name="start-time"]').prop('disabled', (i, v) => !v);
        $(parentRow).find('input[name="end-time"]').prop('disabled', (i, v) => !v);
        $(parentRow).find('input[name="start-time"]').prop('required', (i, v) => !v);
        $(parentRow).find('input[name="end-time"]').prop('required', (i, v) => !v);
        $(parentRow).find('input[name="start-time"]').val('');
        $(parentRow).find('input[name="end-time"]').val('');
        if ($(this).prop('checked')) {
            $(`${addSchedForm} .set-time input:checkbox`).prop('required', false);
        } else {
            const daysCheckBoxes = $(`${addSchedForm} .set-time input:checkbox`);
            let requireCheck = true;
            for (let index = 0; index < Object.keys(daysCheckBoxes).length; index++) {
                if (daysCheckBoxes[index]?.checked) {
                    requireCheck = false;
                    break;
                }
                requireCheck = true;
            }
            $(`${addSchedForm} .set-time input:checkbox`).prop('required', requireCheck);
        }
    }

    let newRowIndex = 1;
    function addNewRowSchedule(schedules) {
        $('tr.new-row').animate({backgroundColor: '#c5fcff'});
        schedules.forEach(schedule => {
            const inputs = createScheduleRowInputs(schedule);
            const buttons = createScheduleRowButtons(schedule);
            const rowNode = dataTable.row.add([--newRowIndex, null, ...inputs, buttons]).draw(false).node();
            dataTable.order([0, 'asc']).draw();
            $(rowNode)
                .addClass('schedule-container new-row')
                .css({ 'background-color': '#7070fda6' })
                .animate({backgroundColor: '#a7fda7'});
        });
    }
    function onSubmitAddedSchedule(e) {
        e.preventDefault();
        const formElement = this;
        const employeeId = $(`${addSchedForm} input[name="${inputName.employeeId}"]`).val();
        const academicYearId = $(`${addSchedForm} select[name="${inputName.ay}"]`).val();
        const period = $(`${addSchedForm} input[name="${inputName.period}"]`).val();
        const generateTimesheet = $(`${addSchedForm} input[name="${inputName.generateTimesheet}"]`).prop('checked');
        const daysChecked = $(`${addSchedForm} input[name="${inputName.day}"]:checked`);
        let schedules = {};
        Object.keys(daysChecked).forEach(day => {
            const dayRow = daysChecked[day]?.parentNode?.parentNode;
            if (dayRow) {
                const day = $(dayRow).find('input:checked').val();
                schedules[day] = {
                    start_time: $(dayRow).find('input[name="start-time"]').val(),
                    end_time: $(dayRow).find('input[name="end-time"]').val(),
                };
            }
        });
        const requestData = JSON.stringify({
            employee_id: employeeId,
            academic_year_id: academicYearId,
            period: period,
            generate_timesheet: generateTimesheet,
            schedules: schedules,
        });
        fetch(schdApi.create, {
            ...postMethod,
            body: requestData,
        }).then(response => {
            if (response.ok) {
                response.json().then(schedules => {
                    successModalAlert('Schedule Added!');
                    $(addScheduleModal).modal('hide');
                    clearAddSchedFormInputs();
                    // show new row and added schedule data
                    setTimeout(() => addNewRowSchedule(schedules), 1100);
                });
            } 
            else {
                errorNotif(formElement, response);
                throw response.statusText + " " + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            setTimeout(() => location.reload(), 1100);
            console.log('Something went wrong,', err);
        });
        loadingModalAlert('Saving Schedule');
        return false;
    }
    /**
     * +++++++++++ Adding Schedule [END] +++++++++++
     */

    /**
     * Bindings
     */
    $(document).on('click', `${table} button.edit`, onEditRowSchedule);
    $(document).on('click', `${table} button.cancel`, onCloseRowEdit);
    $(document).on('click', `${table} button.delete`, onDeleteSchedule);
    $(document).on('click', `${table} button.save`, onTrackRowSaveButton);
    $(document).on('click', `.schedule-management button.add-schedule`, onShowAddSchedModal);
    $(document).on('click', `.schedule-management .dtr-control`, onCloseRowEdit);
    $(document).on('submit', `.schedule-management ${editSchedForm}`, onSubmitEditedSchedule);
    $(`${addSchedForm} .set-time input:checkbox`).on('click', onToggleSchedTimeInput);
    $(addSchedForm).on('submit', onSubmitAddedSchedule);
    window.addEventListener('resize', configureTableOnResize, true);
});


/**
 * =================================== Academic Year Management ===================================
 */
$(() => {
    const table = '.ay-management #ay-datatable';
    if ($(table).length === 0) return;
    const buttonConig = {
        title: 'Academic Years',
        exportOptions: {
            columns: ':not(.no-export)'
        }
    };
    const dataTable = $(table).DataTable({
        deferRender: true,
        order: [[2, 'asc']],
        columnDefs: [{targets: [1, 8], orderable: false}, {targets: [0], visible: false, searchable: false}],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], 
        responsive: {
            details: {
                renderer: function ( api, rowIdx, columns ) {
                    const data = $.map( columns, function ( col, i ) {
                        return col.hidden ?
                            (col.title 
                                ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                       <td class="font-black text-xs" style="white-space: pre-wrap; word-wrap: break-word;">${col.title}:</td>
                                       <td class="text-xs" style="cursor: pointer; white-space: pre-wrap; word-wrap: break-word;">${col.data}</td>
                                   </tr>`
                                : `<tr class="bg-transparent" data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                       <td colspan=2 class="border-0 p-0 pt-2">
                                           <div class="border-2 p-2 rounded-2xl flex justify-center">${col.data}</div>
                                       </td>
                                   </tr>`
                            ) : '';
                    } ).join('');
                    const rowAsTable = $(`<table class="${ayCls.container}" />`).append( data );
                    return data ? rowAsTable : false;
                }
            }
        },
        dom: `
            <"flex justify-center md:justify-between flex-wrap items-center"
                B
                <"flex justify-between flex-wrap"
                    <"mt-2 md:mt-0 md:ml-2"l>
                >
                <"m-2"f>
            >rt
            <"flex justify-center md:justify-between flex-wrap"
                <"m-2"i><"m-2"p>
            >
        `,
        buttons: [ 
            {
                extend: 'print',
                ...buttonConig
            },
            {
                extend: 'csvHtml5',
                ...buttonConig
            },
            {
                extend: 'excelHtml5',
                ...buttonConig
            },
            {
                extend: 'pdfHtml5',
                ...buttonConig
            }
        ]
    });


    /**
     * Frequently used CLASS and NAME attr
     */
    const ayCls = {
        get affix() {return `ay`},
        get prefix() {return `${this.affix}-`},
        get infix() {return `-${this.affix}-`},
        get suffix() {return `-${this.affix}`},
        get container() {return `${this.prefix}container`},
        get newRow() {return `new-row`},
        get editable() {return `editable`},
        get description() {return `description`},
        get semester() {return `semester`},
        get startYear() {return `start-year`},
        get endYear() {return `end-year`},
        get startDate() {return `start-date`},
        get endDate() {return `end-date`},
        get createdRow() {return `${this.editable}`}, // add more new created row class here...
    }

    const inputName = {
        get description() {return `description`},
        get semester() {return `semester`},
        get startYear() {return `start_year`},
        get endYear() {return `end_year`},
        get startDate() {return `start_date`},
        get endDate() {return `end_date`},
        get deletePhrase() {return `del-phrase`},
        get password() {return `password`},
    }

    /**
     * Frequently used SELECTORS
     */
    const aySel = {
        get prefix() {return `.${ayCls.affix}-`},
        get dataId() {return `data${ayCls.infix}id`},
        get management() {return `${this.prefix}management`},
        get deleteModal() {return `${this.management} .confirm-delete-modal`},
        get addModal() {return `${this.management} .add${ayCls.infix}modal`},
        get addForm() {return `${this.management} form.add${ayCls.suffix}`},
        get editForm() {return `${this.management} form.edit${ayCls.suffix}`},
        get deleteForm() {return `${this.deleteModal} form.delete${ayCls.suffix}`},
        get rowContainer() {return `.${ayCls.container}`},
        // tr
        get trContainer() {return `tr${this.rowContainer}`},
        get trResponsive() {return `tr.child`},
        get newRow() {return `.${ayCls.newRow}`},
        get trNewRow() {return `${this.rowContainer} ${this.newRow}`},
        // buttons
        get showAddFormBtn() {return `${this.management} button.add${ayCls.suffix}`},
        get editRowBtn() {return `button.edit`},
        get cancelEditRowBtn() {return `button.cancel`},
        get deleteRowBtn() {return `button.delete`},
        get saveRowBtn() {return `button.save`},
        get tableEditRowBtn() {return `${table} ${this.editRowBtn}`},
        get tableCancelEditRowBtn() {return `${table} ${this.cancelEditRowBtn}`},
        get tableDeleteRowBtn() {return `${table} ${this.deleteRowBtn}`},
        get tableSaveRowBtn() {return `${table} ${this.saveRowBtn}`},
        get closeResponsvRow() {return `${this.management} .dtr-control`},
        // inputs
        get descriptionInput() {return `input[name="${inputName.description}"]`},
        get semesterInput() {return `input[name="${inputName.semester}"]`},
        get startYrInput() {return `input[name="${inputName.startYear}"]`},
        get endYrInput() {return `input[name="${inputName.endYear}"]`},
        get startDateInput() {return `input[name="${inputName.startDate}"]`},
        get endDateInput() {return `input[name="${inputName.endDate}"]`},
        get yearInputs() {return `input.year`},
        get delPhraseInput() {return `${this.deleteModal} input[name="${inputName.deletePhrase}"]`},
        get delPsswrdInput() {return `${this.deleteModal} input[name="${inputName.password}"]`},
        // spans
        get descriptionSpan() {return `span.${ayCls.description}`},
        get semesterSpan() {return `span.${ayCls.semester}`},
        get startYrSpan() {return `span.${ayCls.startYear}`},
        get endYrSpan() {return `span.${ayCls.endYear}`},
        get startDateSpan() {return `span.${ayCls.startDate}`},
        get endDateSpan() {return `span.${ayCls.endDate}`},
        // editing
        get editableSpan() {return `${table} span.${ayCls.editable}`},
        get editableInput() {return `${table} input.${ayCls.editable}`},
        get editableSelect() {return `${table} select.${ayCls.editable}`},
        get actionBtnsCont() {return `${table} div.actions`},
        get visibleTd() {return `td:not([style*="display: none;"])`},
        get rowVisibleTd() {return `${this.rowContainer} ${this.visibleTd}`},
        // others
        get givenDelPhrase() {return `${this.deleteModal} .given-delete-phrase`},
    };

    const dataAttr = {
        get ayId() {return `data${ayCls.infix}id`},
    }

    const generic = {
        deleteButton: null,
        set delBtn(btn) {this.deleteButton = btn},
        get delBtn() {return this.deleteButton},
    }

    /**
     * API ROUTES
     */
    const ayApi = {
        get create() {return '/api/academic-year/create';},
        get update() {return '/api/academic-year/update';},
        get delete() {return '/api/academic-year/delete';},
    }

    function initYearPicker() {
        $(aySel.yearInputs).yearpicker({
            year: moment().year(),
            startYear: 2019,
            endYear: 2050,
        });
    }
    initYearPicker();

    function onTrackRowSaveButton(e) {
        const clickedSaveBtn = this;
        $(aySel.tableSaveRowBtn).removeClass('clicked');
        $(clickedSaveBtn).addClass('clicked');
    }
    function autoFillRowInput(rowContainer) {
        const description = $(rowContainer).find(aySel.descriptionSpan).html();
        const semester = $(rowContainer).find(aySel.semesterSpan).html();
        const startYear = $(rowContainer).find(aySel.startYrSpan).html();
        const endYear = $(rowContainer).find(aySel.endYrSpan).html();
        const startDate = $(rowContainer).find(aySel.startDateSpan).html();
        const endDate = $(rowContainer).find(aySel.endDateSpan).html();
        $(rowContainer).find(aySel.descriptionInput).val(description);
        $(rowContainer).find(aySel.semesterInput).val(semester);
        $(rowContainer).find(aySel.startYrInput).val(startYear);
        $(rowContainer).find(aySel.endYrInput).val(endYear);
        $(rowContainer).find(aySel.startDateInput).val(startDate);
        $(rowContainer).find(aySel.endDateInput).val(endDate);
    }
    function clearDeleteFormModal() {
        $(aySel.delPhraseInput).val('');
        $(aySel.delPsswrdInput).val('');
    }
    function configureTableOnResize(event) {
        closeAllRowEdit();
        dataTable
            .rows()
            .invalidate('dom')
            .draw();
    }

    /**
     * +++++++++++ Editing AY [START] +++++++++++
     */
     function onRowEditAy() {
        const clickedEditBtn = this;
        const rowSel = aySel.prefix + $(clickedEditBtn).attr(dataAttr.ayId);
        const rowContainer = $(clickedEditBtn).parents(aySel.rowContainer);
        const spanSel = aySel.editableSpan + rowSel;
        const inputSel = aySel.editableInput + rowSel;
        const selectSel = aySel.editableSelect + rowSel;
        const rowActBtnsCont = aySel.actionBtnsCont + rowSel;
        const saveBtnSel = `${rowActBtnsCont} ${aySel.saveRowBtn}`;
        const editBtnSel = `${rowActBtnsCont} ${aySel.editRowBtn}`;
        const cancelEditBtnSel = `${rowActBtnsCont} ${aySel.cancelEditRowBtn}`;
        autoFillRowInput(rowContainer);
        // hide
        $(spanSel).addClass('hidden');
        $(editBtnSel).addClass('hidden');
        // show
        $(inputSel).removeClass('hidden');
        $(selectSel).removeClass('hidden');
        $(cancelEditBtnSel).removeClass('hidden');
        $(saveBtnSel).removeClass('hidden');
    }
    function onCloseRowEdit(e, clickedButton = null) {
        if (!clickedButton) clickedButton = this;
        const container = $(clickedButton).parents(aySel.rowContainer);
        const ayId = $(container).find(aySel.cancelEditRowBtn).attr(dataAttr.ayId);
        const rowSel = aySel.prefix + ayId;
        const spanSel = aySel.editableSpan + rowSel;
        const inputSel = aySel.editableInput + rowSel;
        const selectSel = aySel.editableSelect + rowSel;
        const rowActBtnsCont = aySel.actionBtnsCont + rowSel;
        const saveBtnSel = `${rowActBtnsCont} ${aySel.saveRowBtn}`;
        const editBtnSel = `${rowActBtnsCont} ${aySel.editRowBtn}`;
        const cancelEditBtnSel = `${rowActBtnsCont} ${aySel.cancelEditRowBtn}`;
        // hide
        $(spanSel).removeClass('hidden');
        $(editBtnSel).removeClass('hidden');
        // show
        $(inputSel).addClass('hidden');
        $(selectSel).addClass('hidden');
        $(cancelEditBtnSel).addClass('hidden');
        $(saveBtnSel).addClass('hidden');
    }
    function closeAllRowEdit() {
        const spanSel = aySel.editableSpan;
        const inputSel = aySel.editableInput;
        const selectSel = aySel.editableSelect;
        const saveBtnSel = `${aySel.actionBtnsCont} ${aySel.saveRowBtn}`;
        const editBtnSel = `${aySel.actionBtnsCont} ${aySel.editRowBtn}`;
        const cancelEditBtnSel = `${aySel.actionBtnsCont} ${aySel.cancelEditRowBtn}`;
        // hide
        $(spanSel).removeClass('hidden');
        $(editBtnSel).removeClass('hidden');
        // show
        $(inputSel).addClass('hidden');
        $(selectSel).addClass('hidden');
        $(cancelEditBtnSel).addClass('hidden');
        $(saveBtnSel).addClass('hidden');
    }
    function updateRowAy(rowSel, updated) {
        $(`${aySel.trContainer} ${aySel.descriptionSpan + rowSel}`).html(updated.description);
        $(`${aySel.trContainer} ${aySel.semesterSpan + rowSel}`).html(updated.semester);
        $(`${aySel.trContainer} ${aySel.startYrSpan + rowSel}`).html(updated.start_year);
        $(`${aySel.trContainer} ${aySel.endYrSpan + rowSel}`).html(updated.end_year);
        $(`${aySel.trContainer} ${aySel.startDateSpan + rowSel}`).html(updated.start_date);
        $(`${aySel.trContainer} ${aySel.endDateSpan + rowSel}`).html(updated.end_date);
        // when data table is in responsive state
        $(`${aySel.trResponsive} ${aySel.descriptionSpan + rowSel}`).html(updated.description);
        $(`${aySel.trResponsive} ${aySel.semesterSpan + rowSel}`).html(updated.semester);
        $(`${aySel.trResponsive} ${aySel.startYrSpan + rowSel}`).html(updated.start_year);
        $(`${aySel.trResponsive} ${aySel.endYrSpan + rowSel}`).html(updated.end_year);
        $(`${aySel.trResponsive} ${aySel.startDateSpan + rowSel}`).html(updated.start_date);
        $(`${aySel.trResponsive} ${aySel.endDateSpan + rowSel}`).html(updated.end_date);
    }
    function onSubmitEditedRowAy(e) {
        const clickedSaveBtnSel = `${table} ${aySel.saveRowBtn}.clicked`;
        const ayId = $(clickedSaveBtnSel).attr(dataAttr.ayId);
        const rowSel = aySel.prefix + ayId;
        const trRowSel = `tr${rowSel}`; 
        // input data
        const description = $(`${aySel.rowVisibleTd} ${aySel.descriptionInput + rowSel}`).val();
        const semester = $(`${aySel.rowVisibleTd} ${aySel.semesterInput + rowSel}`).val();
        const startYear = $(`${aySel.rowVisibleTd} ${aySel.startYrInput + rowSel}`).val();
        const endYear = $(`${aySel.rowVisibleTd} ${aySel.endYrInput + rowSel}`).val();
        const startDate = $(`${aySel.rowVisibleTd} ${aySel.startDateInput + rowSel}`).val();
        const endDate = $(`${aySel.rowVisibleTd} ${aySel.endDateInput + rowSel}`).val();
        const ayData = {
            description: description,
            semester: semester,
            start_year: startYear,
            end_year: endYear,
            start_date: startDate,
            end_date: endDate,
        };
        const requestData = JSON.stringify({ ay_id: ayId, ay_data: ayData });
        fetch(ayApi.update, {
            ...postMethod,
            body: requestData
        }).then(response => {
            if (response.ok) {
                onCloseRowEdit(null, clickedSaveBtnSel);
                updateRowAy(rowSel, ayData);
                successModalAlert();
                $(trRowSel).animate({backgroundColor: '#d3d3f5'});
            } else {
                errorNotif(aySel.editForm, response);
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        loadingModalAlert('Saving Data');
        e.preventDefault();
        return false;
    }
    /**
     * +++++++++++ Editing AY [END] +++++++++++
     */
     
    /**
     * +++++++++++ Deleting AY [START] +++++++++++
     */
    function removeRowAy(deleteButtonElement) {
        const ayRow = $(deleteButtonElement).parents('tr');
        ayRow.animate({ backgroundColor: '#ff8787' });
        setTimeout(() => {
            dataTable
            .row(ayRow)
            .remove()
            .draw();
        }, 1100);
    }
    function onDeleteRowAy() {
        const deleteButtonElement = this;
        generic.delBtn = deleteButtonElement;
        confirmNotif(deleteButtonElement, 'delete', () => {
            clearDeleteFormModal();
            $(aySel.deleteModal).modal('show');
        });
        $(globSel.notifConfirm).prop('type', 'button');
        $(globSel.notifCancel).prop('type', 'button');
    }
    function onSubmitDeleteRowAy() {
        const givenPhrase = $(aySel.givenDelPhrase).html();
        const inputPhrase = $(aySel.delPhraseInput).val();
        if (givenPhrase !== inputPhrase) {
            failedModalAlert();
            errorNotif(aySel.deleteForm, 'Incorrect Phrase!');
            return false;
        }
        const password = $(aySel.delPsswrdInput).val();
        const ayId = $(generic.delBtn).attr(dataAttr.ayId);
        const requestData = JSON.stringify({ ay_id: ayId, password: password });
        fetch(ayApi.delete, {
            ...postMethod,
            body: requestData
        }).then(response => {
            if (response.ok) {
                successModalAlert('Deleted!');
                $(aySel.deleteModal).modal('hide');
                removeRowAy(generic.delBtn);
                generic.delBtn = null;
            } else {
                errorNotif(aySel.deleteForm, response);
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        return false;
    }
    /**
     * +++++++++++ Deleting AY [END] +++++++++++
     */

    /**
     * +++++++++++ Adding AY [START] +++++++++++
     */
    function onAddAy() {
        $(aySel.addModal).modal('show');
    }

    // For inserting <inputs /> and data into NEW ADDED ROW in data table after submitting added ay
    function createAyRowInputs(ay) {
        return [
            `<span class="${ayCls.createdRow} ${ayCls.prefix + ay.id} ${ayCls.description}">${ay.description}</span>`+
            `<input class="${ayCls.createdRow} ${ayCls.prefix + ay.id} border-blue-500 rounded-2xl shadow-xl hidden" type="text" name="${inputName.description}" value="${ay.description}" />`,
            
            `<span class="${ayCls.createdRow} ${ayCls.prefix + ay.id} ${ayCls.semester}">${ay.semester}</span>`+
            `<input class="${ayCls.createdRow} ${ayCls.prefix + ay.id} border-blue-500 rounded-2xl shadow-xl hidden" type="number" name="${inputName.semester}" value="${ay.semester}" />`,

            `<span class="${ayCls.createdRow} ${ayCls.prefix + ay.id} ${ayCls.startYear}">${ay.start_year}</span>`+
            `<input class="${ayCls.createdRow} ${ayCls.prefix + ay.id} year border-blue-500 rounded-2xl shadow-xl hidden" type="number" name="${inputName.startYear}" value="${ay.start_year}" />`,

            `<span class="${ayCls.createdRow} ${ayCls.prefix + ay.id} ${ayCls.endYear}">${ay.end_year}</span>`+
            `<input class="${ayCls.createdRow} ${ayCls.prefix + ay.id} year border-blue-500 rounded-2xl shadow-xl hidden" type="number" name="${inputName.endYear}" value="${ay.end_year}" />`,

            `<span class="${ayCls.createdRow} ${ayCls.prefix + ay.id} ${ayCls.startDate}">${ay.start_date}</span>`+
            `<input class="${ayCls.createdRow} ${ayCls.prefix + ay.id} border-blue-500 rounded-2xl shadow-xl hidden" type="date" name="${inputName.startDate}" value="${ay.start_date}" />`,

            `<span class="${ayCls.createdRow} ${ayCls.prefix + ay.id} ${ayCls.endDate}">${ay.end_date}</span>`+
            `<input class="${ayCls.createdRow} ${ayCls.prefix + ay.id} border-blue-500 rounded-2xl shadow-xl hidden" type="date" name="${inputName.endDate}" value="${ay.end_date}" />`
        ];
    }

    // For inserting <buttons /> into NEW ADDED ROW in data table after submitting added ay
    function createAyRowButtons(ay) {
        return `<div class="${ayCls.prefix + ay.id} actions flex justify-end items-center">
                    <div class="absolute">
                        <div class="cont-a flex justify-end items-center">
                            <div>
                                <button class="cancel px-2 rounded-xl border-gray-500 bg-gray-200 hover:text-white shadow-sm hidden" data${ayCls.infix}id="${ay.id}" type="button">x</button>
                                <button class="save btn btn-success shadow-sm hidden" data${ayCls.infix}id="${ay.id}" type="submit">
                                    <i class="far fa-save"></i>
                                </button>
                                <button class="edit btn btn-info shadow-sm" data${ayCls.infix}id="${ay.id}" type="button">
                                    <i class="fas fa-user-edit"></i>
                                </button>
                            </div>
                            <div class="flex justify-center items-center w-16 h-10">
                                <button class="delete btn btn-danger shadow-sm absolute" data${ayCls.infix}id="${ay.id}" type="button">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
    }

    function clearAddAyFormInputs() {
        $(`${aySel.addForm} ${aySel.descriptionInput}`).val('');
        $(`${aySel.addForm} ${aySel.semesterInput}`).val('');
        $(`${aySel.addForm} ${aySel.startYrInput}`).val('2021');
        $(`${aySel.addForm} ${aySel.endYrInput}`).val('2021');
        $(`${aySel.addForm} ${aySel.startDateInput}`).val('');
        $(`${aySel.addForm} ${aySel.endDateInput}`).val('');
    }
    clearAddAyFormInputs();

    let newRowIndex = 1;
    function addNewRowAy(ay) {
        const inputs = createAyRowInputs(ay);
        const buttons = createAyRowButtons(ay);
        const rowNode = dataTable.row.add([--newRowIndex, null, ...inputs, buttons]).draw(false).node();
        $(aySel.newRow).animate({backgroundColor: '#c5fcff'}); // makes recently added row BLUE
        dataTable.order([0, 'asc']).draw();
        $(rowNode)
            .addClass(`${ayCls.container} ${ayCls.newRow}`)
            .css({ 'background-color': '#7070fda6' })
            .animate({backgroundColor: '#a7fda7'}); // makes newest added row Green
    }
    function onSubmitAddedAy(e) {
        const formElement = this;
        const description = $(`${aySel.addForm} ${aySel.descriptionInput}`).val();
        const semester = $(`${aySel.addForm} ${aySel.semesterInput}`).val();
        const startYear = $(`${aySel.addForm} ${aySel.startYrInput}`).val();
        const endYear = $(`${aySel.addForm} ${aySel.endYrInput}`).val();
        const startDate = $(`${aySel.addForm} ${aySel.startDateInput}`).val();
        const endDate = $(`${aySel.addForm} ${aySel.endDateInput}`).val();
        const requestData = JSON.stringify({
            description: description,
            semester: semester,
            start_year: startYear,
            end_year: endYear,
            start_date: startDate,
            end_date: endDate,
        });
        fetch(ayApi.create, {
            ...postMethod,
            body: requestData,
        }).then(response => {
            if (response.ok) {
                response.json().then(ay => {
                    successModalAlert('Academic Year Added!');
                    $(aySel.addModal).modal('hide');
                    clearAddAyFormInputs();
                    setTimeout(() => addNewRowAy(ay), 1100);
                });
            } 
            else {
                errorNotif(formElement, response);
                throw response.statusText + " " + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        e.preventDefault();
        loadingModalAlert('Saving Academic Year');
        return false;
    }
    /**
     * +++++++++++ Adding AY [END] +++++++++++
     */


    /**
     * Bindings
     */
    $(document).on('click', aySel.tableEditRowBtn, onRowEditAy);
    $(document).on('click', aySel.tableCancelEditRowBtn, onCloseRowEdit);
    $(document).on('click', aySel.closeResponsvRow, onCloseRowEdit);
    $(document).on('click', aySel.tableDeleteRowBtn, onDeleteRowAy);
    $(document).on('click', aySel.tableSaveRowBtn, onTrackRowSaveButton);
    $(document).on('click', aySel.showAddFormBtn, onAddAy);
    $(document).on('submit', aySel.editForm, onSubmitEditedRowAy);
    $(document).on('submit', aySel.deleteForm, onSubmitDeleteRowAy);
    $(aySel.addForm).on('submit', onSubmitAddedAy);
    window.addEventListener('resize', configureTableOnResize, true);
});


/**
 * =================================== Department Management ===================================
 */
$(() => {
    const table = '.dept-management #dept-datatable';
    if ($(table).length === 0) return;
    const buttonConig = {
        title: 'Department',
        exportOptions: {
            columns: ':not(.no-export)'
        }
    };
    const dataTable = $(table).DataTable({
        deferRender: true,
        order: [[2, 'asc']],
        columnDefs: [{targets: [1, 4], orderable: false}, {targets: [0], visible: false, searchable: false}],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], 
        responsive: {
            details: {
                renderer: function ( api, rowIdx, columns ) {
                    const data = $.map( columns, function ( col, i ) {
                        return col.hidden ?
                            (col.title 
                                ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                       <td class="font-black text-xs" style="white-space: pre-wrap; word-wrap: break-word;">${col.title}:</td>
                                       <td class="text-xs">
                                           ${col.data}
                                       </td>
                                   </tr>`
                                : `<tr class="bg-transparent" data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                       <td colspan=2 class="border-0 p-0 pt-2">
                                           <div class="border-2 p-2 rounded-2xl flex justify-center">${col.data}</div>
                                       </td>
                                   </tr>`
                            ) : '';
                    } ).join('');
                    const rowAsTable = $(`<table class="${dptCls.container}" />`).append( data );
                    return data ? rowAsTable : false;
                }
            }
        },
        dom: `
            <"flex justify-center md:justify-between flex-wrap items-center"
                B
                <"flex justify-between flex-wrap"
                    <"mt-2 md:mt-0 md:ml-2"l>
                >
                <"m-2"f>
            >rt
            <"flex justify-center md:justify-between flex-wrap"
                <"m-2"i><"m-2"p>
            >
        `,
        buttons: [ 
            {
                extend: 'print',
                ...buttonConig
            },
            {
                extend: 'csvHtml5',
                ...buttonConig
            },
            {
                extend: 'excelHtml5',
                ...buttonConig
            },
            {
                extend: 'pdfHtml5',
                ...buttonConig
            }
        ]
    });


    /**
     * Frequently used CLASS
     */
    const dptCls = {
        get affix() {return `dept`},
        get prefix() {return `${this.affix}-`},
        get infix() {return `-${this.affix}-`},
        get suffix() {return `-${this.affix}`},
        get container() {return `${this.prefix}container`},
        get newRow() {return `new-row`},
        get editable() {return `editable`},
        get createdRow() {return `${this.editable}`}, // add more new created row class here...
        get department() {return `department`},
        get supervisor() {return `supervisor`},
        get approver() {return `approver`},
    }

    const inputName = {
        get department() {return `department`},
        get supervisor() {return `supervisor`},
        get approver() {return `approver`},
        get delPhrase() {return `del-phrase`},
        get delPassword() {return `password`},
    }

    /**
     * Frequently used SELECTORS
     */
    const dptSel = {
        get prefix() {return `.${dptCls.affix}-`},
        get management() {return `${this.prefix}management`},
        get addModal() {return `${this.management} .add${dptCls.infix}modal`},
        get deleteModal() {return `${this.management} .confirm-delete-modal` },
        get addForm() {return `${this.management} form.add${dptCls.suffix}`},
        get editForm() {return `${this.management} form.edit${dptCls.suffix}`},
        get deleteForm() {return `${this.deleteModal} form.delete${dptCls.suffix}`},
        get rowContainer() {return `.${dptCls.container}`},
        // tr
        get trContainer() {return `tr${this.rowContainer}`},
        get trResponsive() {return `tr.child`},
        get newRow() {return `.${dptCls.newRow}`},
        get trNewRow() {return `${this.rowContainer} ${this.newRow}`},
        // buttons
        get showAddFormBtn() {return `${this.management} button.add${dptCls.suffix}`},
        get editRowBtn() {return `button.edit`},
        get cancelEditRowBtn() {return `button.cancel`},
        get deleteRowBtn() {return `button.delete`},
        get saveRowBtn() {return `button.save`},
        get tableEditRowBtn() {return `${table} ${this.editRowBtn}`},
        get tableCancelEditRowBtn() {return `${table} ${this.cancelEditRowBtn}`},
        get tableDeleteRowBtn() {return `${table} ${this.deleteRowBtn}`},
        get tableSaveRowBtn() {return `${table} ${this.saveRowBtn}`},
        get closeResponsvRow() {return `${this.management} .dtr-control`},
        // inputs
        get departmentInput() {return `input[name="${inputName.department}"]`},
        get supervisorSelect() {return `select[name="${inputName.supervisor}"]`},
        get approverSelect() {return `select[name="${inputName.approver}"]`},
        get delPhraseInput() {return `${this.deleteForm} input[name="${inputName.delPhrase}"]`},
        get delPsswrdInput() {return `${this.deleteForm} input[name="${inputName.delPassword}"]`},
        // spans
        get departmentSpan() {return `span.${dptCls.department}`},
        get supervisorSpan() {return `span.${dptCls.supervisor}`},
        get approverSpan() {return `span.${dptCls.approver}`},
        // editing
        get editableSpan() {return `${table} span.${dptCls.editable}`},
        get editableInput() {return `${table} input.${dptCls.editable}`},
        get editableSelect() {return `${table} select.${dptCls.editable}`},
        get actionBtnsCont() {return `${table} div.actions`},
        get visibleTd() {return `td:not([style*="display: none;"])`},
        get rowVisibleTd() {return `${this.rowContainer} ${this.visibleTd}`},
        // others
        get givenDelPhrase() {return `${this.deleteModal} .given-delete-phrase`},
        get delModalTitle() {return `${this.deleteModal} h5.modal-title`},
    };

    const dataAttr = {
        get empId() {return `data${dptCls.infix}id`},
        get employees() {return `data-employees`},
    }

    const generic = {
        deleteButton: null,
        get delBtn() {return this.deleteButton},
        set delBtn(btn) {this.deleteButton = btn},
    }

    /**
     * API ROUTES
     */
    const deptApi = {
        get create() {return '/api/department/create';},
        get update() {return '/api/department/update';},
        get delete() {return '/api/department/delete';},
    }

    function onTrackRowSaveButton(e) {
        const clickedSaveBtn = this;
        $(dptSel.tableSaveRowBtn).removeClass('clicked');
        $(clickedSaveBtn).addClass('clicked');
    }
    function autoFillRowInput(rowContainer) {
        const department = $(rowContainer).find(dptSel.departmentSpan).html();
        const supervisor = $(rowContainer).find(dptSel.supervisorSpan).html();
        $(rowContainer).find(dptSel.departmentInput).val(department);
        $(rowContainer).find(dptSel.supervisorSelect).val(supervisor);
    }
    function clearDeleteFormModal() {
        $(dptSel.delPhraseInput).val('');
        $(dptSel.delPsswrdInput).val('');
    }
    function configureTableOnResize(event) {
        closeAllRowEdit();
        dataTable
            .rows()
            .invalidate('dom')
            .draw();
    }

    /**
     * +++++++++++ Editing Department [START] +++++++++++
     */
    function onRowEditDept() {
        const clickedEditBtn = this;
        const rowSel = dptSel.prefix + $(clickedEditBtn).attr(dataAttr.empId);
        const rowContainer = $(clickedEditBtn).parents(dptSel.rowContainer);
        const spanSel = dptSel.editableSpan + rowSel;
        const inputSel = dptSel.editableInput + rowSel;
        const selectSel = dptSel.editableSelect + rowSel;
        const rowActBtnsContSel = dptSel.actionBtnsCont + rowSel;
        const saveBtnSel = `${rowActBtnsContSel} ${dptSel.saveRowBtn}`;
        const editBtnSel = `${rowActBtnsContSel} ${dptSel.editRowBtn}`;
        const cancelEditBtnSel = `${rowActBtnsContSel} ${dptSel.cancelEditRowBtn}`;
        autoFillRowInput(rowContainer);
        // hide
        $(spanSel).addClass('hidden');
        $(editBtnSel).addClass('hidden');
        // show
        $(inputSel).removeClass('hidden');
        $(selectSel).removeClass('hidden');
        $(cancelEditBtnSel).removeClass('hidden');
        $(saveBtnSel).removeClass('hidden');
    }
    function onCloseRowEdit(e, clickedButton = null) {
        if (!clickedButton) clickedButton = this;
        const container = $(clickedButton).parents(dptSel.rowContainer);
        const empId = $(container).find(dptSel.cancelEditRowBtn).attr(dataAttr.empId);
        const rowSel = dptSel.prefix + empId;
        const spanSel = dptSel.editableSpan + rowSel;
        const inputSel = dptSel.editableInput + rowSel;
        const selectSel = dptSel.editableSelect + rowSel;
        const rowActBtnsContSel = dptSel.actionBtnsCont + rowSel;
        const saveBtnSel = `${rowActBtnsContSel} ${dptSel.saveRowBtn}`;
        const editBtnSel = `${rowActBtnsContSel} ${dptSel.editRowBtn}`;
        const cancelEditBtnSel = `${rowActBtnsContSel} ${dptSel.cancelEditRowBtn}`;
        // hide
        $(spanSel).removeClass('hidden');
        $(editBtnSel).removeClass('hidden');
        // show
        $(inputSel).addClass('hidden');
        $(selectSel).addClass('hidden');
        $(cancelEditBtnSel).addClass('hidden');
        $(saveBtnSel).addClass('hidden');
    }
    function closeAllRowEdit() {
        const spanSel = dptSel.editableSpan;
        const inputSel = dptSel.editableInput;
        const selectSel = dptSel.editableSelect;
        const saveBtnSel = `${dptSel.actionBtnsCont} ${dptSel.saveRowBtn}`;
        const editBtnSel = `${dptSel.actionBtnsCont} ${dptSel.editRowBtn}`;
        const cancelEditBtnSel = `${dptSel.actionBtnsCont} ${dptSel.cancelEditRowBtn}`;
        // hide
        $(spanSel).removeClass('hidden');
        $(editBtnSel).removeClass('hidden');
        // show
        $(inputSel).addClass('hidden');
        $(selectSel).addClass('hidden');
        $(cancelEditBtnSel).addClass('hidden');
        $(saveBtnSel).addClass('hidden');
    }
    function updateRowDept(rowSel, updated) {
        $(`${dptSel.trContainer} ${dptSel.departmentSpan + rowSel}`).html(updated.department);
        $(`${dptSel.trContainer} ${dptSel.supervisorSpan + rowSel}`).html(updated.supervisor);
        $(`${dptSel.trContainer} ${dptSel.approverSpan + rowSel}`).html(updated.approver);
        // when data table is in responsive state
        $(`${dptSel.trResponsive} ${dptSel.departmentSpan + rowSel}`).html(updated.department);
        $(`${dptSel.trResponsive} ${dptSel.supervisorSpan + rowSel}`).html(updated.supervisor);
        $(`${dptSel.trResponsive} ${dptSel.approverSpan + rowSel}`).html(updated.approver);
    }
    function onSubmitEditedRowDept(e) {
        const clickedSaveBtnSel = `${table} ${dptSel.saveRowBtn}.clicked`;
        const deptId = $(clickedSaveBtnSel).attr(dataAttr.empId);
        const rowSel = dptSel.prefix + deptId;
        const trRowSel = `tr${rowSel}`; 
        // selectors
        const departmentSel = `${dptSel.rowVisibleTd} ${dptSel.departmentInput + rowSel}`;
        const supervisorSel = `${dptSel.rowVisibleTd} ${dptSel.supervisorSelect + rowSel}`;
        const approverSel = `${dptSel.rowVisibleTd} ${dptSel.approverSelect + rowSel}`;
        // input data
        const department = $(departmentSel).val();
        const supervisor = $(supervisorSel).find(":selected").text();
        const approver = $(approverSel).find(":selected").text();
        const deptData = {
            department: department,
            supervisor: supervisor,
            approver: approver,
        };
        const requestData = JSON.stringify({ dept_id: deptId, dept_data: deptData });
        fetch(deptApi.update, {
            ...postMethod,
            body: requestData
        }).then(response => {
            if (response.ok) {
                onCloseRowEdit(null, clickedSaveBtnSel);
                updateRowDept(rowSel, deptData);
                successModalAlert();
                $(trRowSel).animate({backgroundColor: '#d3d3f5'});
            } else {
                errorNotif(trRowSel, response);
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        loadingModalAlert('Saving Data');
        e.preventDefault();
        return false;
    }
    /**
     * +++++++++++ Editing Department [END] +++++++++++
     */

    /**
     * +++++++++++ Deleting Department [START] +++++++++++
     */
    function removeRowDept(delBtn) {
        const row = $(delBtn).parents('tr');
        row.animate({ backgroundColor: '#ff8787' });
        setTimeout(() => {
            dataTable
                .row(row)
                .remove()
                .draw();
        }, 1100);
    }
    function onDeleteRowDept() {
        const delBtn = this;
        confirmNotif(delBtn, 'delete', () => {
            clearDeleteFormModal();
            generic.delBtn = delBtn;
            const rowContainer = $(delBtn).parents(dptSel.rowContainer);
            const department = rowContainer.find(dptSel.departmentSpan).html();
            $(dptSel.delModalTitle).html(`Are you sure you want to delete <i>${department}</i> department forever?`);
            $(dptSel.deleteModal).modal('show');
        });
        $(globSel.notifConfirm).prop('type', 'button');
        $(globSel.notifCancel).prop('type', 'button');
    }
    function onSubmitDeleteRowDept() {
        const givenPhrase = $(dptSel.givenDelPhrase).html();
        const inputPhrase = $(dptSel.delPhraseInput).val();
        if (givenPhrase !== inputPhrase) {
            failedModalAlert();
            errorNotif(dptSel.deleteForm, 'Incorrect Phrase!');
            return false;
        }
        const password = $(dptSel.delPsswrdInput).val();
        const deptId = $(generic.delBtn).attr(dataAttr.empId);
        const requestData = JSON.stringify({ dept_id: deptId, password: password });
        fetch(deptApi.delete, {
            ...postMethod,
            body: requestData
        }).then(response => {
            if (response.ok) {
                successModalAlert('Deleted!');
                $(dptSel.deleteModal).modal('hide');
                removeRowDept(generic.delBtn);
                generic.delBtn = null;
            } else {
                errorNotif(dptSel.deleteForm, response);
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        return false;
    }
    /**
     * +++++++++++ Deleting Department [END] +++++++++++
     */

    /**
     * +++++++++++ Adding Department [START] +++++++++++
     */
    function onAddDept() {
        $(dptSel.addModal).modal('show');
    }
    function createSelect(employees, dpt, role) {
        let select = `
            <select class="${dptCls.createdRow} ${dptCls.prefix + dpt.id} border-blue-500 rounded-2xl shadow-xl hidden" name="${inputName[role]}" required>
                <option value="${dpt[role]}" selected disabled hidden>${dpt[role]}</option>
        `;
        Object.keys(employees).forEach(i => {
            const employee = employees[i];
            const email = employee.email;
            const selectOption = `<option value="${email}">${email}</option>`;
            select += selectOption;
        });
        select += `</select>`;
        return select;
    }
    function createDeptRowInputs(dpt) {
        const emplysSel = `script${dptSel.management}`;
        const emplysDataStr = $(emplysSel).attr(dataAttr.employees);
        const employees = JSON.parse(emplysDataStr);
        const supervisorSelect = createSelect(employees, dpt, inputName.supervisor);
        const approverSelect = createSelect(employees, dpt, inputName.approver);
        return [
            `
                <span class="${dptCls.createdRow} ${dptCls.prefix + dpt.id} ${dptCls.department}">${dpt.department}</span>
                <input class="${dptCls.createdRow} ${dptCls.prefix + dpt.id} border-blue-500 rounded-2xl shadow-xl hidden" type="text" name="${inputName.department}" value="${dpt.department}" />
            `,
            `
                <span class="${dptCls.createdRow} ${dptCls.prefix + dpt.id} ${dptCls.supervisor}">${dpt.supervisor}</span>
                ${supervisorSelect}
            `,
            `
                <span class="${dptCls.createdRow} ${dptCls.prefix + dpt.id} ${dptCls.approver}">${dpt.approver}</span>
                ${approverSelect}
            `
        ];
    }
    function createDeptRowButtons(dpt) {
        return `<div class="${dptCls.prefix + dpt.id} actions flex justify-end items-center">
                    <div class="absolute">
                        <div class="cont-a flex justify-end items-center">
                            <div>
                                <button class="cancel px-2 rounded-xl border-gray-500 bg-gray-200 hover:text-white shadow-sm hidden" data${dptCls.infix}id="${dpt.id}" type="button">x</button>
                                <button class="save btn btn-success shadow-sm hidden" data${dptCls.infix}id="${dpt.id}" type="submit">
                                    <i class="far fa-save"></i>
                                </button>
                                <button class="edit btn btn-info shadow-sm" data${dptCls.infix}id="${dpt.id}" type="button">
                                    <i class="fas fa-user-edit"></i>
                                </button>
                            </div>
                            <div class="flex justify-center items-center w-16 h-10">
                                <button class="delete btn btn-danger shadow-sm absolute" data${dptCls.infix}id="${dpt.id}" type="button">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
    }

    function clearAddDeptFormInputs() {
        $(`${dptSel.addForm} ${dptSel.departmentInput}`).val('');
        $(`${dptSel.addForm} ${dptSel.supervisorSelect}`).val('');
    }
    clearAddDeptFormInputs();

    let newRowIndex = 1;
    function addNewRowDept(dpt) {
        const inputs = createDeptRowInputs(dpt);
        const buttons = createDeptRowButtons(dpt);
        const rowDraw = dataTable.row.add([--newRowIndex, null, ...inputs, buttons]).draw(false);
        $(dptSel.newRow).animate({backgroundColor: '#c5fcff'}); // makes recently added row BLUE
        dataTable.order([0, 'asc']).draw();
        const rowNode = rowDraw.node();
        $(rowNode)
            .addClass(`${dptCls.container} ${dptCls.newRow}`)
            .css({ 'background-color': '#7070fda6' })
            .animate({backgroundColor: '#a7fda7'}); // makes newest added row Green
    }
    function onSubmitAddedDept(e) {
        const formElement = this;
        // selectors
        const departmentSel = `${dptSel.addForm} ${dptSel.departmentInput}`;
        const supervisorSel = `${dptSel.addForm} ${dptSel.supervisorSelect}`;
        // input data
        const department = $(departmentSel).val();
        const supervisor = $(supervisorSel).val();
        const requestData = JSON.stringify({
            department: department,
            supervisor: supervisor,
        });
        fetch(deptApi.create, {
            ...postMethod,
            body: requestData,
        }).then(response => {
            if (response.ok) {
                response.json().then(dpt => {
                    successModalAlert('Department Added!');
                    $(dptSel.addModal).modal('hide');
                    clearAddDeptFormInputs();
                    setTimeout(() => addNewRowDept(dpt), 1100);
                });
            } 
            else {
                errorNotif(formElement, response);
                throw response.statusText + " " + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        e.preventDefault();
        loadingModalAlert('Saving Department');
        return false;
    }
    /**
     * +++++++++++ Adding Department [END] +++++++++++
     */


    /**
     * Bindings
     */
    $(document).on('click', dptSel.tableEditRowBtn, onRowEditDept);
    $(document).on('click', dptSel.tableCancelEditRowBtn, onCloseRowEdit);
    $(document).on('click', dptSel.closeResponsvRow, onCloseRowEdit);
    $(document).on('click', dptSel.tableDeleteRowBtn, onDeleteRowDept);
    $(document).on('click', dptSel.tableSaveRowBtn, onTrackRowSaveButton);
    $(document).on('click', dptSel.showAddFormBtn, onAddDept);
    $(document).on('submit', dptSel.editForm, onSubmitEditedRowDept);
    $(dptSel.addForm).on('submit', onSubmitAddedDept);
    $(dptSel.deleteForm).on('submit', onSubmitDeleteRowDept);
    window.addEventListener('resize', configureTableOnResize, true);

    // prevent enter from submitting the form
    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
});


/**
 * =================================== Advance Punch Clock Management ===================================
 */
$(() => {
    const table = '.apc-management #apc-datatable';
    if ($(table).length === 0) return;
    const buttonConig = {
        title: 'Advance Punch Clock',
        exportOptions: {
            columns: ':not(.no-export)'
        }
    };
    const dataTable = $(table).DataTable({
        deferRender: true,
        order: [[2, 'asc']],
        columnDefs: [{targets: [1, 5], orderable: false}, {targets: [0], visible: false, searchable: false}],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], 
        responsive: {
            details: {
                renderer: function ( api, rowIdx, columns ) {
                    const data = $.map( columns, function ( col, i ) {
                        return col.hidden ?
                            (col.title 
                                ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                       <td class="font-black text-xs" style="white-space: pre-wrap; word-wrap: break-word;">${col.title}:</td>
                                       <td class="text-xs">
                                           ${col.data}
                                       </td>
                                   </tr>`
                                : `<tr class="bg-transparent" data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                       <td colspan=2 class="border-0 p-0 pt-2">
                                           <div class="border-2 p-2 rounded-2xl flex justify-center">${col.data}</div>
                                       </td>
                                   </tr>`
                            ) : '';
                    } ).join('');
                    const rowAsTable = $(`<table class="${apcCls.container}" />`).append( data );
                    return data ? rowAsTable : false;
                }
            }
        },
        dom: `
            <"flex justify-center md:justify-between flex-wrap items-center"
                B
                <"flex justify-between flex-wrap"
                    <"mt-2 md:mt-0 md:ml-2"l>
                >
                <"m-2"f>
            >rt
            <"flex justify-center md:justify-between flex-wrap"
                <"m-2"i><"m-2"p>
            >
        `,
        buttons: [ 
            {
                extend: 'print',
                ...buttonConig
            },
            {
                extend: 'csvHtml5',
                ...buttonConig
            },
            {
                extend: 'excelHtml5',
                ...buttonConig
            },
            {
                extend: 'pdfHtml5',
                ...buttonConig
            }
        ]
    });

    const apcApi = {
        create: '/api/advance-punch-clock/create',
        update: '/api/advance-punch-clock/update',
        delete: '/api/advance-punch-clock/delete',
    }

    /**
     * Frequently used CLASS
     */
    const apcCls = {
        get affix() {return `apc`},
        get prefix() {return `${this.affix}-`},
        get infix() {return `-${this.affix}-`},
        get suffix() {return `-${this.affix}`},
        get container() {return `${this.prefix}container`},
        get newRow() {return `new-row`},
        get createdRow() {return `${this.container} ${this.newRow}`},
        get type() {return `type`},
        get description() {return `description`},
        get accessCode() {return `access-code`},
        get monday() {return `monday`},
        get tuesday() {return `tuesday`},
        get wednesday() {return `wednesday`},
        get thursday() {return `thursday`},
        get friday() {return `friday`},
        get saturday() {return `saturday`},
    }

    /**
     * Frequently used SELECTORS
     */
     const apcSel = {
        get prefix() {return `.${apcCls.affix}-`},
        get management() {return `${this.prefix}management`},
        get modalTitle() {return `${this.management} .${apcCls.prefix}form-modal .modal-title`},
        get formModal() {return `${this.management} .${apcCls.prefix}form-modal`},
        get form() {return `${this.formModal} form.${apcCls.affix}`},
        get formSched() {return `${this.form} fieldset.schedule`},
        get rowContainer() {return `.${apcCls.container}`},
        get timeInputsCont() {return `div.time-inputs`}, 
        // day schedule containers
        get mondayCont() {return `${this.formSched} div.${apcCls.monday}`},
        get tuesdayCont() {return `${this.formSched} div.${apcCls.tuesday}`},
        get wednesdayCont() {return `${this.formSched} div.${apcCls.wednesday}`},
        get thursdayCont() {return `${this.formSched} div.${apcCls.thursday}`},
        get fridayCont() {return `${this.formSched} div.${apcCls.friday}`},
        get saturdayCont() {return `${this.formSched} div.${apcCls.saturday}`},
        // tr
        get trContainer() {return `tr${this.rowContainer}`},
        get trResponsive() {return `tr.child`},
        get newRow() {return `.${apcCls.newRow}`},
        get trNewRow() {return `${this.rowContainer} ${this.newRow}`},
        tr: (rowId) => `tr${rowId}`,
        // buttons
        get showFormBtn() {return `${this.management} button.add${apcCls.suffix}`},
        get viewRowBtn() {return `button.view`},
        get deleteRowBtn() {return `button.delete`},
        get tableViewRowBtn() {return `${table} ${this.viewRowBtn}`},
        get tableDeleteRowBtn() {return `${table} ${this.deleteRowBtn}`},
        get submitFormBtn() {return `${this.form} button.submit${apcCls.suffix}`},
        get addTimeBtn() {return `${this.formSched} button.add-time`},
        // inputs
        get formSelects() {return `${this.form} select`},
        get formText() {return `${this.form} input[type="text"]`},
        get formTime() {return `${this.form} input[type="time"]`},
        get descriptInput() {return `${this.form} input[name="${inputName.description}"]`},
        get accessCodeInput() {return `${this.form} input[name="${inputName.accessCode}"]`},
        get dayInput() {return `${this.form} input[name="${inputName.day}"]`},
        get checkbox() {return `${this.form} .set-time input:checkbox`},
        get typeSelect() {return `${this.form} select[name="${inputName.type}"]`},
        get startTimeInput() {return `input[name="${inputName.startTime}"]`},
        get endTimeInput() {return `input[name="${inputName.endTime}"]`},
        // spans
        get typeSpan() {return `span.${apcCls.type}`},
        get descrptSpan() {return `span.${apcCls.description}`},
        get accssCodeSpan() {return `span.${apcCls.accessCode}`},
        // schedules input time
        get mondayStartTime() {return `div.${apcCls.monday} ${this.startTimeInput}`},
        get mondayEndTime() {return `div.${apcCls.monday} ${this.endTimeInput}`},
        get tuesdayStartTime() {return `div.${apcCls.tuesday} ${this.startTimeInput}`},
        get tuesdayEndTime() {return `div.${apcCls.tuesday} ${this.endTimeInput}`},
        get wednesdayStartTime() {return `div.${apcCls.wednesday} ${this.startTimeInput}`},
        get wednesdayEndTime() {return `div.${apcCls.wednesday} ${this.endTimeInput}`},
        get thursdayStartTime() {return `div.${apcCls.thursday} ${this.startTimeInput}`},
        get thursdayEndTime() {return `div.${apcCls.thursday} ${this.endTimeInput}`},
        get fridayStartTime() {return `div.${apcCls.friday} ${this.startTimeInput}`},
        get fridayEndTime() {return `div.${apcCls.friday} ${this.endTimeInput}`},
        get saturdayStartTime() {return `div.${apcCls.saturday} ${this.startTimeInput}`},
        get saturdayEndTime() {return `div.${apcCls.saturday} ${this.endTimeInput}`},
        // schedules day checkbox
        get mondayChckBx() {return `.${apcCls.monday} input:checkbox`},
        get tuesdayChckBx() {return `.${apcCls.tuesday} input:checkbox`},
        get wednesdayChckBx() {return `.${apcCls.wednesday} input:checkbox`},
        get thursdayChckBx() {return `.${apcCls.thursday} input:checkbox`},
        get fridayChckBx() {return `.${apcCls.friday} input:checkbox`},
        get saturdayChckBx() {return `.${apcCls.saturday} input:checkbox`},
        // clicked buttons
        view: null,
        set clickedViewBtn(apcId) {this.view = `${this.viewRowBtn}[${dataAttr.apcId}=${apcId}]`},
        get clickedViewBtn() {return this.view},
    };
    const inputName = {
        get type() {return `type`},
        get description() {return `description`},
        get accessCode() {return `access_code`},
        get day() {return `day`},
        get startTime() {return `start-time`},
        get endTime() {return `end-time`},
    }
    const dataAttr = {
        get apcId() {return `data${apcCls.infix}id`},
        get schedule() {return `data-schedule`},
    }

    function clearApcFormInputs() {
        $(apcSel.formSelects).prop('selectedIndex', 0);
        $(apcSel.formText).val('');
        $(apcSel.dayInput).prop('checked', false);
        $(apcSel.dayInput).prop('required', true);
        $(apcSel.formTime).val('');
        $(apcSel.formTime).prop('disabled', true);
        $(apcSel.formTime).prop('disabled', true);
    }
    function addApcFormTime() {
        alert('test');
    }
    function getApcFormData() {
        const type = $(apcSel.typeSelect).val();
        const description = $(apcSel.descriptInput).val();
        const accessCode = $(apcSel.accessCodeInput).val();
        const daysChecked = $(`${apcSel.form} input[name="${inputName.day}"]:checked`);
        let schedules = {};
        Object.keys(daysChecked).forEach(day => {
            const dayRow = daysChecked[day]?.parentNode?.parentNode;
            if (dayRow) {
                const day = $(dayRow).find('input[name="day"]').val();
                schedules[day] = {
                    start_time: $(dayRow).find('input[name="start-time"]').val(),
                    end_time: $(dayRow).find('input[name="end-time"]').val(),
                };
            }
        });
        const data = {
            type: type,
            description: description,
            access_code: accessCode,
            schedules: schedules,
        };
        return data;
    }
    function onToggleSchedTimeInput() {
        const parentRow = $(this).parent().parent();
        $(parentRow).find(apcSel.startTimeInput).prop('disabled', (i, v) => !v);
        $(parentRow).find(apcSel.endTimeInput).prop('disabled', (i, v) => !v);
        $(parentRow).find(apcSel.startTimeInput).prop('required', (i, v) => !v);
        $(parentRow).find(apcSel.endTimeInput).prop('required', (i, v) => !v);
        $(parentRow).find(apcSel.startTimeInput).val('');
        $(parentRow).find(apcSel.endTimeInput).val('');
        if ($(this).prop('checked')) {
            $(apcSel.checkbox).prop('required', false);
        } else {
            const daysCheckBoxes = $(apcSel.checkbox);
            let requireCheck = true;
            for (let index = 0; index < Object.keys(daysCheckBoxes).length; index++) {
                if (daysCheckBoxes[index]?.checked) {
                    requireCheck = false;
                    break;
                }
                requireCheck = true;
            }
            $(apcSel.checkbox).prop('required', requireCheck);
        }
    }
    function autoFillApcForm(rowContainer) {
        const type = $(rowContainer).find(apcSel.typeSpan).html();
        const description = $(rowContainer).find(apcSel.descrptSpan).html();
        const accessCode = $(rowContainer).find(apcSel.accssCodeSpan).html();
        const schedStr = $(rowContainer).find(apcSel.viewRowBtn).attr(dataAttr.schedule);
        const schedJson = JSON.parse(schedStr);
        $(apcSel.typeSelect).val(type);
        $(apcSel.descriptInput).val(description);
        $(apcSel.accessCodeInput).val(accessCode);
        // schedules
        if (schedJson?.monday) {
            $(apcSel.mondayChckBx).prop('checked', true);
            $(apcSel.mondayStartTime).prop('disabled', false);
            $(apcSel.mondayEndTime).prop('disabled', false);
            $(apcSel.checkbox).prop('required', false);

            // $(apcSel.mondayStartTime).val(schedJson?.monday?.start_time);
            // $(apcSel.mondayEndTime).val(schedJson?.monday?.end_time);
            // @remind

            schedJson.monday.forEach(sched => {
                console.log('test', sched);
            });
        }
        if (schedJson?.tuesday) {
            $(apcSel.tuesdayChckBx).prop('checked', true);
            $(apcSel.tuesdayStartTime).prop('disabled', false);
            $(apcSel.tuesdayEndTime).prop('disabled', false);
            $(apcSel.tuesdayStartTime).val(schedJson?.tuesday?.start_time);
            $(apcSel.tuesdayEndTime).val(schedJson?.tuesday?.end_time);
            $(apcSel.checkbox).prop('required', false);
        }
        if (schedJson?.wednesday) {
            $(apcSel.wednesdayChckBx).prop('checked', true);
            $(apcSel.wednesdayStartTime).prop('disabled', false);
            $(apcSel.wednesdayEndTime).prop('disabled', false);
            $(apcSel.wednesdayStartTime).val(schedJson?.wednesday?.start_time);
            $(apcSel.wednesdayEndTime).val(schedJson?.wednesday?.end_time);
            $(apcSel.checkbox).prop('required', false);
        }
        if (schedJson?.thursday) {
            $(apcSel.thursdayChckBx).prop('checked', true);
            $(apcSel.thursdayStartTime).prop('disabled', false);
            $(apcSel.thursdayEndTime).prop('disabled', false);
            $(apcSel.thursdayStartTime).val(schedJson?.thursday?.start_time);
            $(apcSel.thursdayEndTime).val(schedJson?.thursday?.end_time);
            $(apcSel.checkbox).prop('required', false);
        }
        if (schedJson?.friday) {
            $(apcSel.fridayChckBx).prop('checked', true);
            $(apcSel.fridayStartTime).prop('disabled', false);
            $(apcSel.fridayEndTime).prop('disabled', false);
            $(apcSel.fridayStartTime).val(schedJson?.friday?.start_time);
            $(apcSel.fridayEndTime).val(schedJson?.friday?.end_time);
            $(apcSel.checkbox).prop('required', false);
        }
        if (schedJson?.saturday) {
            $(apcSel.saturdayChckBx).prop('checked', true);
            $(apcSel.saturdayStartTime).prop('disabled', false);
            $(apcSel.saturdayEndTime).prop('disabled', false);
            $(apcSel.saturdayStartTime).val(schedJson?.saturday?.start_time);
            $(apcSel.saturdayEndTime).val(schedJson?.saturday?.end_time);
            $(apcSel.checkbox).prop('required', false);
        }
    }
    /**
     * Synchronizes dataTable's data in different state (responsive state, or not)
     */
    function configureTableOnResize(e) {
        dataTable
            .rows()
            .invalidate('dom')
            .draw();
    }

    /**
     * +++++++++++ Adding Instance [START] +++++++++++
     */
    function onAddApc() {
        clearApcFormInputs();
        $(apcSel.modalTitle).html('Create Instance');
        $(apcSel.submitFormBtn).html('Create');
        $(apcSel.formModal).modal('show');
        $(apcSel.form).off();
        $(apcSel.form).on('submit', onSubmitCreatedApc);
    }
    function onSubmitCreatedApc(e) {
        const formElement = this;
        const apcFormData = getApcFormData();
        const requestData = JSON.stringify(apcFormData);
        fetch(apcApi.create, {
            ...postMethod,
            body: requestData,
        }).then(response => {
            if (response.ok) {
                response.json().then(apc => {
                    successModalAlert('Department Added!');
                    $(apcSel.formModal).modal('hide');
                    clearApcFormInputs();
                    setTimeout(() => addNewRowApc(apc), 1100);
                });
            } 
            else {
                errorNotif(formElement, response);
                throw response.statusText + " " + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        e.preventDefault();
        loadingModalAlert('Saving Employee Data');
        return false;
    }

    let newRowIndex = 1;
    function addNewRowApc(apc) {
        $('tr.new-row').animate({backgroundColor: '#c5fcff'});
        const inputs = createApcRowInputs(apc);
        const buttons = createApcRowButtons(apc);
        const rowNode = dataTable.row.add([--newRowIndex, null, ...inputs, buttons]).draw(false).node();
        dataTable.order([0, 'asc']).draw();
        $(rowNode)
            .addClass(`${apcCls.createdRow} ${apcCls.prefix + apc.id}`)
            .css({ 'background-color': '#7070fda6' })
            .animate({backgroundColor: '#a7fda7'});
    }
    function createApcRowInputs(apc) {
        return [
            `<span class="editable apc-${apc.id} type">${apc.type}</span>`,
            `<span class="editable apc-${apc.id} description">${apc.description}</span>`,
            `<span class="editable apc-${apc.id} access-code">${apc.access_code}</span>`
        ];
    }
    function createApcRowButtons(apc) {
        return `<div class="apc-${apc.id} actions flex justify-end items-center">
                    <div class="absolute">
                        <div class="cont-a flex justify-end items-center">
                            <div>
                                <button class="view btn btn-info shadow-sm" type="button" ${dataAttr.apcId}=${apc.id} ${dataAttr.schedule}=${JSON.stringify(apc.schedules)}>
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="flex justify-center items-center w-16 h-10">
                                <button class="delete btn btn-danger shadow-sm absolute" ${dataAttr.apcId}=${apc.id} type="button">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
    }
    /**
     * +++++++++++ Adding Instance [START] +++++++++++
     */

    /**
     * +++++++++++ View/Update Instance [START] +++++++++++
     */
    function onRowViewApc() {
        const clickedBtn = this;
        const apcId = $(clickedBtn).attr(dataAttr.apcId);
        const rowSel = apcSel.prefix + apcId;
        const rowContainer = apcSel.trContainer + rowSel;
        apcSel.clickedViewBtn = apcId;
        autoFillApcForm(rowContainer);
        $(apcSel.modalTitle).html('View Instance');
        $(apcSel.submitFormBtn).html('Update');
        $(apcSel.form).off();
        $(apcSel.form).on('submit', onRowUpdateApc);
        $(apcSel.formModal).modal('show');
    }
    function onRowUpdateApc(e) {
        const formElement = this;
        const apcFormData = getApcFormData();
        const apcId = $(apcSel.clickedViewBtn).attr(dataAttr.apcId);
        const rowId = apcSel.prefix + apcId;
        const trRowSel = apcSel.tr(rowId); 
        const updateData = {apc_id: apcId, apc_data: apcFormData};
        const requestData = JSON.stringify(updateData);
        fetch(apcApi.update, {
            ...postMethod,
            body: requestData,
        }).then(response => {
            if (response.ok) {
                response.json().then(apc => {
                    updateRowApc(rowId, apc);
                    successModalAlert('Department Added!');
                    $(apcSel.formModal).modal('hide');
                    clearApcFormInputs();
                    $(trRowSel).animate({backgroundColor: '#d3d3f5'});
                });
            } 
            else {
                errorNotif(formElement, response);
                throw response.statusText + " " + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        e?.preventDefault();
        loadingModalAlert('Saving Employee Data');
        return false;
    }
    function updateRowApc(rowSel, updatedApc) {
        $(`${apcSel.trContainer} ${apcSel.typeSpan + rowSel}`).html(updatedApc.type);
        $(`${apcSel.trContainer} ${apcSel.descriptionSpan + rowSel}`).html(updatedApc.description);
        $(`${apcSel.trContainer} ${apcSel.accssCodeSpan + rowSel}`).html(updatedApc.access_code);
        $(`${apcSel.trContainer} ${rowSel} ${apcSel.viewRowBtn}`).attr(dataAttr.schedule, JSON.stringify(updatedApc.schedules));
        // when data table is in responsive state
        $(`${apcSel.trResponsive} ${apcSel.typeSpan + rowSel}`).html(updatedApc.type);
        $(`${apcSel.trResponsive} ${apcSel.descriptionSpan + rowSel}`).html(updatedApc.description);
        $(`${apcSel.trResponsive} ${apcSel.accssCodeSpan + rowSel}`).html(updatedApc.access_code);
        $(`${apcSel.trResponsive} ${rowSel} ${apcSel.viewRowBtn}`).attr(dataAttr.schedule, JSON.stringify(updatedApc.schedules));
    }
    /**
     * +++++++++++ View/Update Instance [END] +++++++++++
     */

    /**
     * +++++++++++ Deleting Instance [START] +++++++++++
     */
    function removeRowApc(element) {
        const apcRow = $(element).parents('tr');
        apcRow.animate({ backgroundColor: '#ff8787' });
        setTimeout(() => {
            dataTable
                .row(apcRow)
                .remove()
                .draw();
        }, 1100);
    }
    function onDeleteRowApc() {
        const deleteButtonElement = this;
        confirmNotif(deleteButtonElement, 'delete', () => {
            const apcId = $(deleteButtonElement).attr(dataAttr.apcId);
            const requestData = JSON.stringify({ apc_id: apcId });
            fetch(apcApi.delete, {
                ...postMethod,
                body: requestData
            }).then(response => {
                if (response.ok) {
                    successModalAlert('Deleted!');
                    removeRowApc(deleteButtonElement);
                } else {
                    errorNotif(table, response);
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                failedModalAlert();
                console.log('Something went wrong,', err);
            });
        });
        $('.notifyjs-custom-confirm-base .yes').prop('type', 'button');
        $('.notifyjs-custom-confirm-base .no').prop('type', 'button');
    }
    /**
     * +++++++++++ Deleting Instance [END] +++++++++++
     */

    /**
     * Bindings
     */
    $(document).on('click', apcSel.tableViewRowBtn, onRowViewApc);
    $(document).on('click', apcSel.tableDeleteRowBtn, onDeleteRowApc);
    $(apcSel.showFormBtn).on('click', onAddApc);

    $(apcSel.addTimeBtn).on('click', addApcFormTime);
    $(apcSel.checkbox).on('click', onToggleSchedTimeInput);
    window.addEventListener('resize', configureTableOnResize, true);
 });


/**
 * =================================== Leave Report Generator ===================================
 */
$(() => {
    if ($('div.leave-report').length === 0) return;
    const inputName = {
        get startDate() {return 'start-date'},
        get endDate() {return 'end-date'},
    }
    const lrSel = {
        get reportCont() {return '.report-container'},
        get reportTableCont() {return `${this.reportCont} div.table`},
        get reportTable() {return `${this.reportCont} table.report`},
        get reportButtons() {return `${this.reportCont} div.buttons`},
        get formComputer() {return 'form.compute-total-hours'},
        get startDate() {return `${this.formComputer} input[name=${inputName.startDate}]`},
        get endDate() {return `${this.formComputer} input[name=${inputName.endDate}]`},
        get csvBtn() {return `${this.reportCont} button.create-report-csv`},
    }
    const api = {
        get generateReport() {return 'generate-leave-report'},
    }
    
    function generateLeaveReport(event) {
        const startDate = $(lrSel.startDate).val(); 
        const endDate = $(lrSel.endDate).val(); 
        const requestData = JSON.stringify({ start_date: startDate, end_date: endDate });
        fetch(api.generateReport, {
            ...postMethod,
            body: requestData
        }).then(response => {
            if (response.ok) {
                response.text().then(table => {
                    $(lrSel.reportTableCont).html(table);
                    $(lrSel.reportButtons).removeClass('hidden');
                });
                Swal.close();
            } 
            else {
                response.json().then(json => { 
                    $(lrSel.formComputer).notify(json.error, { 
                        className: 'error',
                        position: 'top center',
                        autoHideDelay: 10000,
                        arrowSize: 10,
                    });
                });
                throw response.statusText + ' ' + response.status;
            }
        }).catch(err => {
            failedModalAlert();
            console.log('Something went wrong,', err);
        });
        event.preventDefault();
        loadingModalAlert('Processing Report');
        return false;
    }
    function exportLeaveReportCSV() {
        var d = new Date();
        var datestring = d.getFullYear() + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2) + "-" + ("0" + d.getHours()).slice(-2) + "" + ("0" + d.getMinutes()).slice(-2);
        $(lrSel.reportTable).tableToCsv({
            filename: 'leave-report-' + datestring + '.csv',
            colspanMode: 'replicate',
            excludeColumns: '.avatar'
        });
    }

    /**
     * Bindings
     */
    $(lrSel.formComputer).submit(generateLeaveReport);
    $(lrSel.csvBtn).on('click', exportLeaveReportCSV);
});