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
                        "<button class='no p-2 bg-gray-400 text-white w-20 rounded-xl'>Cancel</button>" +
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
 * Department Leave Requests
 */
$(() => {
    const leaveReqtable = '.department-leave-requests #lr-datatable';
    if ($(leaveReqtable).length === 0) return;
    const buttonConig = {
        title: 'Employee Leave Requests',
        exportOptions: {
            columns: ':not(.no-export)'
        }
    }
    $(leaveReqtable).DataTable({
        deferRender: true,
        order: [[1, 'asc']],
        columnDefs: [{targets: [0], orderable: false}],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], 
        // select: true,
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
                                            <div class="border-2 p-2 rounded-2xl flex justify-center w-40 h-12">`+col.data+`</div>
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
    $('.dt-button').removeClass('dt-button');

    function viewLeaveReason() {
        const requestId = $(this).attr('data-leave-id');
        fetch("/api/leave/request/" + requestId, {
            ...getMethod,
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
    function disableButtons(disabled) {
        $('.leave-request-actions .approve-request').prop('disabled', disabled);
        $('.leave-request-actions .reject-request').prop('disabled', disabled);
    }
    function approveLeaveRequest() {
        confirmNotif(this, 'approve', () => {
            const requestId = $(this).attr('data-leave-request-id');
            fetch("/api/leave/request/approve", {
                ...postMethod,
                body: JSON.stringify({ leave_request_id: requestId })
            }).then(response => {
                if (response.ok) {
                    location.reload(); 
                } else {
                    errorNotif(this, response);
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                disableButtons(false);
                console.log('Something went wrong,', err);
            });
            disableButtons(true);
            processingNotif(this);
        });
    }
    function rejectLeaveRequest() {
        confirmNotif(this, 'reject', () => {
            const requestId = $(this).attr('data-leave-request-id');
            fetch("/api/leave/request/reject", {
                ...postMethod,
                body: JSON.stringify({ leave_request_id: requestId })
            }).then(response => {
                if (response.ok) {
                    location.reload(); 
                } else {
                    errorNotif(this, response);
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                disableButtons(false);
                console.log('Something went wrong,', err);
            });
            disableButtons(true);
            processingNotif(this);
        });
    }

    /**
     * Bindings
     */
    $(document).on('click', '.leave-request-actions .approve-request', approveLeaveRequest);
    $(document).on('click', '.leave-request-actions .reject-request', rejectLeaveRequest);
    $(document).on('click', '.view-btn', viewLeaveReason);
});    


/**
 * Department Attendance
 */
$(() => {
    const calendarEl = document.getElementById('attendance-summary-calendar');
    if (!calendarEl) return;
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        weekNumbers: true,
        navLinks: true,
        datesSet: fetchCalendarAttendanceData,
        navLinkDayClick: () => null,
        navLinkWeekClick: calendarAttendanceSummaryWeekClick,
        eventClick: calendarAttendanceSummaryEventClick,
        dateClick: calendarAttendanceSummaryDateClick,
        // dayHeaderContent: (args) => moment(args.date).format('dddd'),
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

    $('.attendance-summary .calendar').find('*').css('border', '0');
    
    async function fetchCalendarAttendanceData(e) {
        const startDate = moment(e.start).format('YYYY-MM-DD');
        const endDate = moment(e.end).format('YYYY-MM-DD');
        const request = `${startDate}/${endDate}`;
        fetch('/api/supervisor/month-attendance-summary/' + request, {
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
        loadingSummaryModal();
        fetch('/api/supervisor/week-attendance-summary/' + startDate + '/' + endDate, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, initAttendanceSummaryDataTable);
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function totalPerEmployeeAttendanceSummary() {
        const { startDate, endDate } = week.getModalDateRange();
        loadingSummaryModal();
        fetch('/api/supervisor/total-per-employee-attendance-summary/' + startDate + '/' + endDate, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, initAttendanceSummaryDataTable);
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function totalPerDayAttendanceSummary() {
        const { startDate, endDate } = week.getModalDateRange();
        loadingSummaryModal();
        fetch('/api/supervisor/total-per-day-attendance-summary/' + startDate + '/' + endDate, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, initAttendanceSummaryDataTable);

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
        fetch('/api/supervisor/date-attendance-summary/' + date, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, initAttendanceSummaryDataTable);
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
        fetch('/api/supervisor/present-employees-summary/' + date, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, initAttendanceSummaryDataTable);
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function getLateEmployeesOnDate(date) {
        fetch('/api/supervisor/late-employees-summary/' + date, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, initAttendanceSummaryDataTable);
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function getOnLeaveEmployeesOnDate(date) {
        fetch('/api/supervisor/on-leave-employees-summary/' + date, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, initAttendanceSummaryDataTable);
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function getAbsentEmployeesOnDate(date) {
        fetch('/api/supervisor/absent-employees-summary/' + date, {
            ...getMethod,
        }).then(response => {
            if (response.ok) {
                response.text().then(result => {
                    showAttendanceSummaryModal(result, initAttendanceSummaryDataTable);
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
    function initAttendanceSummaryDataTable() {
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
                                        `<td class="text-xs" style="cursor: pointer; white-space: pre-wrap; word-wrap: break-word;" class="tabledit-view-mode">`
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
                <"flex justify-center md:justify-between flex-wrap items-center mb-2"
                    <"flex justify-between flex-wrap m-1 md:m-0"l>
                    <"m-1 md:m-0"f>
                >rt
                <"flex justify-center md:justify-between flex-wrap"
                    <"m-2"i><"m-2"p>
                >
            `,
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