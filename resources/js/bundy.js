$(document).ready(function() {
    const 
        loginToken = jQuery('meta[name="login-token"]').attr('content'),
        csrfToken = jQuery('meta[name="csrf-token"]').attr('content');
    let timesheet = $('.bundy-script').attr('data-timesheet');

    if (!timesheet) {
        $.notify.addStyle('custom-warn', {
            html: `
                <div class="notifyjs-container" style="left: 38px; top: -9px;">
                    <div class="notifyjs-bootstrap-base notifyjs-bootstrap-warn" style='white-space: normal !important; text-align: center !important;'>
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

        return $('.notifications').notify('You do not have a timesheet set for today, please contact the HR for assistance.', { 
            style: 'custom-warn',
            position: 'top center',
            autoHide: false,
            arrowSize: 10,
        });
    }

    timesheet = JSON.parse(timesheet);
    
    const notifyConfig = {
        position: 'top center',
        autoHideDelay: 10000,
        arrowSize: 10,
    }
    let alert = '.bundy-root .alert';
    let message = alert + ' .msg';
    let now = new Date(),
        year = now.getFullYear(),
        month = now.getMonth(),
        day = now.getDate();

    // custom warn confirm notify
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
    // custom info notify
    $.notify.addStyle('custom-info', {
        html: `
            <div class="notifyjs-container" style="left: 38px; top: -9px;">
                <div class="notifyjs-bootstrap-base notifyjs-bootstrap-info flex flex-col items-center">
                    <span data-notify-html="label"></span>
                    <span data-notify-text="hours"></span>
                </div>
            </div>`,
        classes: {
            base: {
                'white-space': 'nowrap',
                'border-radius' : '5px',
                'font-size' : '15px',
            },
        }
    });
    function confirmNotif(button, type, punch = () => null) {
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
            punch();
        });
    }
    function punchNotif (type, message) {
        $('.notifications').notify(message, { 
            className: type,
            ...notifyConfig,
        });
    }
    function totalHrsNotif (label, hours) {
        $('.notifications').notify({ label: label, hours: hours }, { 
            style: 'custom-info', 
            ...notifyConfig,
        });
    }
    function formatTime (time) {
        return new Date(0,0,0,...(time.split(':')))
        .toLocaleString([], { hour: 'numeric', minute: 'numeric', hour12: true});
    }
    function punchClock (type, btn, success = () => null) {
        let clockData = JSON.stringify({
            schedule_id: timesheet.schedule_id,
            type: type,
            location: window.bundy
        });
        fetch("api/timesheet/punch", {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
                "Accept": "text",
                "X-Requested-With": "XMLHttpRequest",
                "Authorization" : "Bearer " + loginToken, 
                "X-CSRF-Token": csrfToken
            },
            body: clockData
        }).then(response => {
            if (response.ok) {
                response.text().then(text => {
                    $('.clock .' + type).html(text);
                    $(btn).addClass('disabled');
                    success();
                    statusAlert(type, 'success')
                });
            } 
            else {
                response.json().then(json => { 
                    statusAlert(type, 'err', json?.error)
                });
                throw response.statusText + " " + response.status;
            }
        }).catch(err => {
            console.log('Something went wrong,', err);
        });
    }
    function submitUndertime(notes, success = () => null) {
        let clockData = JSON.stringify({
            schedule_id: timesheet.schedule_id,
            type: 'time-out',
            undertime_notes: notes,
            location: window.bundy
        });
        fetch("api/timesheet/punch", {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
                "Accept": "text",
                "X-Requested-With": "XMLHttpRequest",
                "Authorization" : "Bearer " + loginToken, 
                "X-CSRF-Token": csrfToken
            },
            body: clockData
        }).then(response => {
            if (response.ok) {
                response.text().then(text => {
                    $('.clock .time-out').html(text);
                    $('.clock-out-btn').addClass('disabled');
                    $('.toggle-undertime').bootstrapToggle('disable');
                    $('.undertime-modal .notes').val('')
                    $('.undertime-modal').modal('toggle');
                    success();
                    statusAlert('time-out', 'success');
                });
            } 
            else throw response.statusText + " " + response.status;
        }).catch(err => {
            statusAlert('time-out', 'err')
            console.log('Something went wrong,', err);
        });
        $('.undertime-modal button:submit').notify('Processing...', {
            className: 'info',
            ...notifyConfig,
        })
    }
    function calculateTotalHrs(dt2, dt1) {
        let totalSeconds = Math.abs((dt2.getTime() - dt1.getTime())) / 1000;
        let totalMins = totalSeconds / 60;
        totalMins = Math.floor(totalMins); // removed remaining seconds
        let wholeHrs = Math.floor(totalMins / 60); // removed remaining mins
        let wholeToMins = wholeHrs * 60;
        let minsRemain = totalMins - wholeToMins;
        let secsRemain = Math.abs(totalSeconds - (totalMins * 60));
        let strHrs = wholeHrs.toString();
        let strMins = minsRemain.toString();
        let strSecs = secsRemain.toString();
        if (strHrs.length === 1) strHrs = "0" + strHrs;
        if (strMins.length === 1) strMins = "0" + strMins;
        if (strSecs.length === 1) strSecs = "0" + strSecs;
        return  strHrs + ':' + strMins + ':' + strSecs + ' hrs';
    }
    function showTotalHrs (start, end, label) {
        fetch('api/timesheet/schedule/' + timesheet.schedule_id, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Authorization' : 'Bearer ' + loginToken, 
            },
        }).then(response => {
            if (response.ok) {
                response.json().then(result => {
                    let tIn = result[start].split(':'),
                        tOut = result[end].split(':'),
                        dtIn = new Date(year, month, day, tIn[0], tIn[1], tIn[2]),
                        dtOut = new Date(year, month, day, tOut[0], tOut[1], tOut[2]);
                    totalHrsNotif(label, calculateTotalHrs(dtIn, dtOut));
                });
            } 
            else throw response.statusText + ' ' + response.status;
        }).catch(err => {
            statusAlert(type, 'err')
            console.log('Something went wrong,', err);
        });
    }
    function statusAlert (type, status, errMsg = 'Something went wrong!') {
        $(alert).removeClass('alert-info text-black bg-white border-blue-700');
        $(message).removeClass('flex-col -mr-6');
        if (status === 'success') {
            $(alert).removeClass('alert-danger text-red-500');
            $(alert).addClass('alert-success text-green-500');
            if (type === 'time-in') punchNotif('success', 'Clocked In Successfully!');
            // else if (type === 'time-out') showTotalHrs('time_in', 'time_out', 'Total Hours Worked');
            else if (type === 'time-out') punchNotif('success', 'Clocked Out Successfully!');
            else if (type === 'lunch-start') punchNotif('success', 'Lunch Started Successfully!');
            else if (type === 'lunch-end') punchNotif('success', 'Lunch Ended Successfully!');
            else if (type === 'overtime-start') punchNotif('success', 'Overtime Started Successfully!');
            // else if (type === 'overtime-end') showTotalHrs('overtime_start', 'overtime_end', 'Total Overtime Hours');
            else if (type === 'overtime-end') punchNotif('success', 'Overtime Ended Successfully!');
            if (!(type === 'time-out') && !(type === 'overtime-end') ) $(alert).removeClass('hidden');
        }
        else punchNotif('error', errMsg);
    }

    /**
     * Clocks
     */
    (function initClocksState() {
        $('.toggle-undertime').bootstrapToggle('on')
    })();
    function clockIn() {
        confirmNotif(this, 'clock in', () => {
            punchClock('time-in', this, function () {
                $('.clock-out-btn').removeClass('disabled');
                $('.toggle-undertime').bootstrapToggle('enable');
                $('.lunch-start-btn').removeClass('disabled');
            });
        });
    }
    function clockOut() {
        if (!$('.toggle-undertime').prop('checked')) {
            $('.undertime-modal').modal('toggle');
        } else {
            confirmNotif(this, 'do a regular clock out', () => {
                punchClock('time-out', this, function () {
                    $('.toggle-undertime').bootstrapToggle('disable');
                    $('.lunch-start-btn').addClass('disabled');
                    $('.overtime-start-btn').removeClass('disabled');
                });
            });
        }
    }
    function lunchStart() {
        confirmNotif(this, 'start lunch', () => {
            punchClock('lunch-start', this, function () {
                $('.clock-out-btn').addClass('disabled');
                $('.toggle-undertime').bootstrapToggle('disable');
                $('.lunch-start-btn').addClass('disabled');
                $('.lunch-end-btn').removeClass('disabled');
            });
        });
    }
    function lunchEnd() {
        confirmNotif(this, 'end lunch', () => {
            punchClock('lunch-end', this, function () {
                $('.clock-out-btn').removeClass('disabled');
                $('.toggle-undertime').bootstrapToggle('enable');
                $('.lunch-end-btn').addClass('disabled');
            });
        });
    }
    function overtimeStart() {
        confirmNotif(this, 'start overtime', () => {
            punchClock('overtime-start', this, function () {
                $('.overtime-end-btn').removeClass('disabled');
            });
        });
    }
    function overtimeEnd() {
        confirmNotif(this, 'end overtime', () => {
            punchClock('overtime-end', this);
        });
    }
    function toggleUndertime(e) {
        if (e.target.checked) {
            $('.clock-out-btn').css({ 'background-color':'#3490dc' });
        } else {
            $('.clock-out-btn').css({ 'background-color':'#044d8a' });
        }
    }
    function onSubmitUndertime(e) {
        e.preventDefault();
        const undertimeNotes = $('.undertime-modal .notes').val();
        submitUndertime(undertimeNotes, function () {
            $('.lunch-start-btn').addClass('disabled');
            $('.overtime-start-btn').removeClass('disabled');
        });
        return false;
    }

    /**
     * Event Bindings
     */

    // confirmation warn
    $(document).on('click', '.notifyjs-custom-confirm-base .no', function() { 
        $(document).off('click', '.notifyjs-custom-confirm-base .yes');
        $(this).trigger('notify-hide'); 
    });

    // clocks
    $('.clock-in-btn').on('click', clockIn);
    $('.clock-out-btn').on('click', clockOut);
    $('.lunch-start-btn').on('click', lunchStart);
    $('.lunch-end-btn').on('click', lunchEnd);
    $('.overtime-start-btn').on('click', overtimeStart);
    $('.overtime-end-btn').on('click', overtimeEnd);
    $('.toggle-undertime').on('change', toggleUndertime);
    $('.undertime-modal form').on('submit', onSubmitUndertime);
    $('.cancel-undertime').on('click', function(){
        $('.undertime-modal').modal('toggle');
    });

    // display time records
    if (timesheet.time_in) $('.clock .time-in').html(formatTime(timesheet.time_in));
    if (timesheet.time_out) $('.clock .time-out').html(formatTime(timesheet.time_out));
    if (timesheet.lunch_start) $('.clock .lunch-start').html(formatTime(timesheet.lunch_start));
    if (timesheet.lunch_end) $('.clock .lunch-end').html(formatTime(timesheet.lunch_end));
    if (timesheet.overtime_start) $('.clock .overtime-start').html(formatTime(timesheet.overtime_start));
    if (timesheet.overtime_end) $('.clock .overtime-end').html(formatTime(timesheet.overtime_end));

    // enable disable button rules
    if (timesheet.time_in) $('.clock-in-btn').addClass('disabled');
    if (timesheet.time_out && !timesheet.overtime_start) $('.overtime-start-btn').removeClass('disabled');
    if (timesheet.overtime_start && !timesheet.overtime_end) $('.overtime-end-btn').removeClass('disabled');
    if (timesheet.time_in && !timesheet.time_out && !timesheet.lunch_start) {
        $('.clock-out-btn').removeClass('disabled');
        $('.toggle-undertime').bootstrapToggle('enable');
        $('.lunch-start-btn').removeClass('disabled');
    }
    if (timesheet.time_in && !timesheet.time_out && timesheet.lunch_start) {
        $('.clock-out-btn').removeClass('disabled');
        $('.toggle-undertime').bootstrapToggle('enable');
    }
    if (timesheet.lunch_start && !timesheet.lunch_end) {
        $('.clock-out-btn').addClass('disabled');
        $('.toggle-undertime').bootstrapToggle('disable');
        $('.lunch-end-btn').removeClass('disabled');
    }

    //GEO
    window.bundy = {
        lat: 0,
        long: 0
    };
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(savePosition);
    }
    function savePosition(position) {
        window.bundy = {
            lat: position.coords.latitude,
            long: position.coords.longitude
        };
    }

});
