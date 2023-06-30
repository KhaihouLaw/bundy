<template>
    <div>
        <div class="tms-top-btns flex flex-row justify-evenly mt-4">
            <div role="group" class="filter-btns btn-group bg-blue-800 rounded-2xl py-1 px-2">
                <button @click="filterByDay" type="button" class="day py-0.5 w-20 focus:bg-blue-500 rounded-xl text-white font-black active-filter" disabled>Day</button>
                <button @click="filterByWeek" type="button" class="week py-0.5 w-20 focus:bg-blue-500 rounded-xl text-white font-black">Week</button> 
                <button @click="filterByMonth" type="button" class="month py-0.5 w-20 focus:bg-blue-500 rounded-xl text-white font-black">Month</button>
            </div>
            <div class="date-nav inline-block rounded-xl border-1 border-blue-500 px-2 ml-3" dir="ltr">
                <button @click="previous" class="prev sm:w-8 text-left"><i class="fa fa-caret-left inline-block"></i></button>
                <div class="label inline-block px-3">
                    <span class="active font-black mr-2">{{ this.navLabel }}</span>
                    <i class="fa fa-calendar text-blue-700 text-lg"></i>
                </div>
                <button @click="next" class="next sm:w-8 text-right"><i class="fa fa-caret-right inline-block"></i></button>
            </div>
            <div class="action-btns flex space-x-6 ml-3">
                <button type="button" class="bundy py-1 px-4 bg-blue-700 focus:bg-blue-600 rounded-md text-white font-black">Bundy</button>
                <!--
                <button type="button" class="add-timesheet py-1 px-4 bg-blue-700 focus:bg-blue-600 rounded-md text-white font-black" data-toggle="modal" data-target="#add-timesheet-modal">Add Timesheet</button> 
                -->
            </div>
        </div>
        
        <!-- filtered by day -->
        <div v-if="filter === 'day'" class="calendar-day">
            <div class="container flex flex-row pb-3 justify-center space-x-8">
                <div class="timesheets mt-4" style="width: 40%;">
                    <div class="flex justify-center font-black text-base">TIMESHEET</div>
                    <div class="summary flex justify-center w-full pt-2"></div>
                    <div class="modification flex justify-center mt-4"></div>
                </div>
                <div class="mt-12">
                    <FunctionalCalendar
                        key="cfd"
                        class="f-calendar small-calendar day-filtered w-80"
                        ref='CalendarDay'
                        :sundayStart='true'
                        :day-names='["MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN"]'
                        :is-date-picker='true'
                        :alwaysUseDefaultClasses='true'
                        @dayClicked='dayClicked'
                        @changedMonth='pageChanged'
                        @changedYear='pageChanged'>
                        <template v-slot:navigationArrowLeft>
                            <i class="fa fa-arrow-left"></i>
                        </template>
                        <template v-slot:navigationArrowRight>
                            <i class="fa fa-arrow-right"></i>
                        </template>
                        <template v-slot:default="props">
                            <slot>
                                <span :class='"day " + props.day.date.replace(/\//g, "-") + " border-0 bg-transparent"'>{{ props.day.day }}</span>
                            </slot>
                        </template>
                    </FunctionalCalendar>
                </div>
            </div>
        </div>

        <!-- filtered by week -->
        <div v-else-if="filter === 'week'" class="calendar-week">
            <div class="container flex flex-row pb-4 justify-center space-x-8">
                <div class="timesheets mt-4" style="width: 61%;">
                    <div class="flex justify-center font-black text-base">TIMESHEET</div>
                    <div class="clock-ins flex flex-wrap justify-center w-full pt-2">
                    </div>
                </div>
                <div class="mt-12">
                    <FunctionalCalendar
                        key="cfw"
                        class="f-calendar small-calendar week-filtered w-80"
                        ref='CalendarWeek'
                        :sundayStart='true'
                        :day-names='["MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN"]'
                        :is-date-picker='false'
                        :marked-dates='rangePoints'
                        :marked-date-range='dateRange'
                        :alwaysUseDefaultClasses='true'
                        @changedMonth='pageChanged'
                        @changedYear='pageChanged'>
                        <template v-slot:navigationArrowLeft>
                             <i class="fa fa-arrow-left"></i>
                        </template>
                        <template v-slot:navigationArrowRight>
                             <i class="fa fa-arrow-right"></i>
                        </template>
                        <template v-slot:default="props">
                            <slot>
                                <span :class='"day " + props.day.date.replace(/\//g, "-") + " border-0 bg-transparent"'>{{ props.day.day }}</span>
                            </slot>
                        </template>
                    </FunctionalCalendar>
                </div>
            </div>
        </div>

        <!-- filtered by month -->
        <div v-else class="calendar-month">
            <div class="flex justify-center py-3 font-black text-base">TIMESHEET</div>
            <div class="container overflow-x-auto border-0 py-2">
                <div class="border-0">
                    <FunctionalCalendar
                        key="cfmonth"
                        class='f-calendar calendar-month-filtered w-3/4'
                        ref='CalendarMonth'
                        :sunday-start='true'
                        :day-names='["MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY", "SUNDAY"]'
                        :hidden-elements='["month"]'
                        :is-date-picker='true'
                        :alwaysUseDefaultClasses='true'
                        @dayClicked='dayInMonthClicked'>
                        <template v-slot:default="props">
                            <slot>
                                <span :class='"day " + props.day.date.replace(/\//g, "-") + " border-0 bg-transparent"'>{{ props.day.day }}</span>
                            </slot>
                        </template>
                    </FunctionalCalendar>
                </div>
                <div>
                    <div class="modal day-in-month-modal fade" id="day-in-month-modal" aria-labelledby="dayInMonthModal" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: none !important; width: 500px;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title font-black text-xl">Timesheet</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body flex justify-center" style="padding: 40px 0 40px 0;">
                                    <div class="flex flex-col items-center justify-center space-y-2 lg:flex-row lg:space-x-2">
                                        <div class="timesheet flex justify-center shadow-2xl">No Timesheet</div>
                                        <div class="modification flex justify-center shadow-inner"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- add timesheet modal -->
        <div>
            <div class="modal add-timesheet-modal fade" id="add-timesheet-modal" aria-labelledby="addTsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" style="max-width: none !important; width: 670px;">
                    <div class="modal-content">
                        <form @submit="submitTimesheetModification">
                        <div class="modal-header">
                            <h5 class="modal-title font-black text-xl" id="addTsModalLabel">{{ modal.title }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div role="alert" aria-live="polite" aria-atomic="true" class="ts-alert alert alert-info h-14 flex flex-row" style="padding: 0 !important;">
                                <div class="h-full w-14 bg-blue-500 rounded flex justify-center items-center">
                                    <span class="material-icons-round text-3xl text-white">
                                        error_outline
                                    </span>
                                </div>
                                <span class="flex justify-center items-center w-full text-base font-black">
                                    {{ modal.info }}
                                </span>
                                <button @click="closeAlert" type="button" class="close-alert-btn absolute right-2 top-0"><span aria-hidden="true" class="text-xl font-black">×</span></button>
                            </div>
                            <div class="flex flex-col items-center md:flex-row">
                                <div>
                                    <div><span class="font-black text-lg ml-2">Pick a Date</span></div>
                                    <FunctionalCalendar
                                        :key='modal.key'
                                        class="small-calendar calendar-modal day-filtered w-80"
                                        ref='CalendarModal'
                                        :sundayStart='true'
                                        :day-names='["MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN"]'
                                        :is-date-picker='modal.datePicker'
                                        :marked-dates='modal.markedDates'
                                        :alwaysUseDefaultClasses='true'
                                        @dayClicked='modalDayClicked'>
                                        <template v-slot:navigationArrowLeft>
                                            <i class="fa fa-arrow-left"></i>
                                        </template>
                                        <template v-slot:navigationArrowRight>
                                            <i class="fa fa-arrow-right"></i>
                                        </template>
                                        <template v-slot:default="props">
                                            <slot>
                                                <span :class='"day " + props.day.date.replace(/\//g, "-") + " border-0 bg-transparent"'>{{ props.day.day }}</span>
                                            </slot>
                                        </template>
                                    </FunctionalCalendar>
                                    <div class="flex flex-col items-center mt-2 space-y-2">
                                        <span class="font-black text-lg">Notes for Timesheet Adjustment</span>
                                        <textarea name="notes" class="notes w-80 h-20 border-1 border-blue-200 rounded-xl" required></textarea>
                                    </div>
                                </div>
                                <div class="ts-input font-black md:w-full flex flex-col justify-center mt-4 md:mt-0 md:pl-6 leading-10">
                                    <div>
                                        <span>Clock In</span>
                                        <div class="ml-2"><input type="time" name="clock-in" required disabled></div>
                                    </div>
                                    <div>
                                        <span>Clock Out</span>
                                        <div class="ml-2"><input type="time" name="clock-out" required disabled></div>
                                    </div>
                                    <div>
                                        <span>Lunch Break - Start</span>
                                        <div class="ml-2"><input type="time" name="lunch-start" disabled></div>
                                    </div>
                                    <div>
                                        <span>Lunch Break - End</span>
                                        <div class="ml-2"><input type="time" name="lunch-end" disabled></div>
                                    </div>
                                    <div>
                                        <span>Overtime - Start</span>
                                        <div class="ml-2"><input type="time" name="overtime-start" disabled></div>
                                    </div>
                                    <div>
                                        <span>Overtime - End</span>
                                        <div class="ml-2"><input type="time" name="overtime-end" disabled></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button @click="clearInputs" type="button" class="btn btn-secondary px-4 font-black" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="submit-modification-btn btn btn-primary text-white px-4 font-black" disabled>Save</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['loginToken'],
        data() {
            return {
                ...this.getWeekConfig(),
                ...this.getDayConfig(),
                pageIsChanged: false,
                filter: 'day', // day | week | month
                // ================================================
                // ~~~~~~~ ACTIVATE based on default FILTER ~~~~~~~
                
                // // month
                // previous: this.prevMonth,
                // next: this.nextMonth,
                // navLabel: moment().format('MMMM YYYY'),

                // // week
                // previous: this.prevWeek,
                // next: this.nextWeek,
                // navLabel: this.getWeekLabel(),

                // // day
                previous: this.prevDay,
                next: this.nextDay,
                navLabel: this.getDayNavLabel(moment()),

                // ================================================
                timesheets: null,
                modificationRequests: null,
                modal: {
                    key: 0,
                    title: '',
                    info: '',
                    type: '', // edit | add
                    selectedDate: null,
                    timesheetID: null,
                    datePicker: true,
                    markedDates: [],
                },
                csrfToken: jQuery('meta[name="csrf-token"]').attr('content'),
            }
        },

        beforeMount() {
            this.requestAllTimesheets();
            this.requestAllModificationRequests();
            $(document).keydown((e) => {
                var arrow = { left: 37, up: 38, right: 39, down: 40 };
                switch (e.which) {
                    case arrow.left:
                        $('.date-nav .prev').trigger('click');
                        break;
                    case arrow.right:
                        $('.date-nav .next').trigger('click');
                        break;
                    case arrow.up:
                        if (this.filter === 'day') this.upDay();
                        break;
                    
                    case arrow.down:
                        if (this.filter === 'day') this.downDay();
                        break;
                }
            });
        },

        mounted() {
            const tsRef = this;
            $(() => {
                if(navigator.userAgent.indexOf("Firefox") != -1 ) {
                    $('.ts-input div div').append('<i class="fa fa-clock-o"></i>');
                }
                function editTimesheet() {
                    let tsData = $(this).attr('id').split('_');
                    tsRef.onEditTimesheet({id: parseInt(tsData[1]), date: tsData[2]});
                }
                $('.small-calendar').notify('Use arrow keys to navigate!', { 
                    className: 'info',
                    position: 'top center',
                    autoHideDelay: 1000,
                    arrowSize: 10,
                });
                $(document).on('click', '.calendar-day .summary .edit-btn', editTimesheet);
                $(document).on('click', '.calendar-week .clock-ins .edit-btn', editTimesheet);
                $(document).on('click', '.day-in-month-modal .timesheet .edit-btn', editTimesheet);
                $(document).on('click', '.action-btns .add-timesheet', this.onAddTimesheet);
                $(document).on('click', '.action-btns .bundy', () => {
                    window.location.replace("dashboard");
                });
                
                /**
                 * Timesheet modification cancel button
                 */
                function cancelBtnHoverOn() {
                    $(document).on('mouseenter', '.ts-modif-tbl button.cancel', function() {
                        $(this).removeClass('bg-yellow-500');
                        $(this).addClass('bg-red-500');
                        $(this).html('CANCEL');
                    });
                    $(document).on('mouseleave', '.ts-modif-tbl button.cancel', function() {
                        $(this).removeClass('bg-red-500');
                        $(this).addClass('bg-yellow-500');
                        $(this).html('pending');
                    });
                };
                cancelBtnHoverOn();
                function cancelBtnHoverOff() {
                    $(document).off("mouseenter mouseleave", '.ts-modif-tbl button.cancel');
                }
                // confirm to cancel
                $.notify.addStyle('custom-confirm', {
                    html: 
                        "<div class='notifyjs-container'>" +
                            "<div class='clearfix notifyjs-bootstrap-base notifyjs-bootstrap-warn shadow-xl'  style='white-space: normal !important; text-align: center !important;'>" +
                                "<div class='text-center'/>" +
                                    "<span class='text-base'>"+
                                        "Are you sure you want to "+
                                        "<span data-notify-html='type'></span> "+
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
                            'width': '230px',
                            'background': '#F5F5F5',
                            'padding': '5px',
                            'border-radius': '10px',
                            'box-shadow' : '0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)',
                        }
                    },
                });
                $(document).on('click', '.notifyjs-custom-confirm-base .no', function() { 
                    const status = JSON.parse($('.ts-modif-tbl button.cancel').attr('is-cancelled'));
                    if (!status) {
                        const cancelBtn = '.ts-modif-tbl button.cancel';
                        $(cancelBtn).prop('disabled', false);
                        $(cancelBtn).removeClass('bg-red-500');
                        $(cancelBtn).addClass('bg-yellow-500');
                        $(cancelBtn).html('pending');
                        cancelBtnHoverOn();
                    }
                    $(this).trigger('notify-hide'); 
                });
                $(document).on('click', '.ts-modif-tbl button.cancel', function() {
                    cancelBtnHoverOff();
                    $('.ts-modif-tbl button.cancel').prop('disabled', true);
                    tsRef.cancelModifRequest(this);
                });

                /**
                 * Modal Events
                 */
                function requireModifNotes() {
                    if ($(this).val().length !== 0) {
                        $('.modal .submit-modification-btn').attr('disabled', false); 
                    } else {
                        $('.modal .submit-modification-btn').attr('disabled', true); 
                    }
                }
                $('.add-timesheet-modal .notes').on('keyup', requireModifNotes);
            });
        },

        methods: {

            /**
             * FILTER BY DAY
             */
            filterByDay() {
                this.getDayConfig();
                this.activeFilter('day');
                this.previous = this.prevDay;
                this.next = this.nextDay;
                this.navLabel = this.getDayNavLabel(moment());
                $(() => {
                    this.showDayTimesheets(moment())
                    this.showDayModificationRequest(moment());
                });
            },
            showDayModificationRequest(date, container = '.calendar-day .modification', editButton = '.calendar-day .summary table .date span') {
                let dateFormatted = date.format('YYYY-MM-DD'),
                    modificationRequest = this.modificationRequests[dateFormatted];
                // show edit button on day timesheet
                if (!modificationRequest) {
                    if (!this.timesheets || !this.timesheets['by-date']) return;
                    let timesheet = this.timesheets['by-date'][dateFormatted];
                    if (!timesheet) return;
                    $(editButton).after(`
                        <button 
                            id="ts_` + timesheet.id + '_' + timesheet.timesheet_date + `" 
                            class="edit-btn w-10 h-5 bg-blue-700 rounded-md text-white text-xs font-black" 
                            data-toggle="modal" 
                            data-target="#add-timesheet-modal">
                            EDIT
                        </button>
                    `);
                    return; 
                };
                let modified = this.formatTime({
                        timeIn: modificationRequest.time_in,
                        timeOut: modificationRequest.time_out,
                        lunchStart: modificationRequest.lunch_start,
                        lunchEnd: modificationRequest.lunch_end,
                        otStart: modificationRequest.overtime_start,
                        otEnd: modificationRequest.overtime_end,
                    }),
                    statusColor = '';
                if (modificationRequest.status === 'approved') statusColor = 'bg-green-500';
                else if (modificationRequest.status === 'pending') statusColor = 'bg-yellow-500';
                else if (modificationRequest.status === 'cancelled') statusColor = 'bg-red-500';
                else if (modificationRequest.status === 'rejected') statusColor = 'bg-red-500';
                $(container).html(`
                    <table class="ts-modif-tbl w-96 table-fixed text-center text-md">
                        <thead>
                            <tr class="date">
                                <th colspan="2" class="align-center items-center space-x-3">
                                    <i class="fa fa-edit text-blue-600 text-lg"></i>
                                    <span>Timesheet Adjustment Request</span>`
                                    + (
                                        modificationRequest.status === 'pending'
                                        ? (`<button id="mod-r-` + modificationRequest.id + `" class="cancel ` + statusColor + ` font-black text-white py-1 w-20 rounded-xl" is-cancelled=false>` + modificationRequest.status + `</button>`)
                                        : (`<span class="` + statusColor + ` font-black text-white py-1 px-2 rounded-xl">` + modificationRequest.status + `</span>`)
                                    ) +
                                `</th>
                            </tr>
                            <tr>
                                <th>Summary</th>
                                <th>Modifications</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span>Clock In</span>
                                    <span>Clock Out</span>
                                </td>
                                <td>
                                    <span>` + (modified.timeIn || '--:-- --') + `</span>
                                    <span>` + (modified.timeOut || '--:-- --') + `</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Lunch Break Start</span>
                                    <span>Lunch Break End</span>
                                </td>
                                <td>
                                    <span>` + (modified.lunchStart || '--:-- --') + `</span>
                                    <span>` + (modified.lunchEnd || '--:-- --') + `</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>Overtime Start</span>
                                    <span>Overtime End</span>
                                </td>
                                <td>
                                    <span>` + (modified.otStart || '--:-- --') + `</span>
                                    <span>` + (modified.otEnd || '--:-- --') + `</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="align-center items-center space-x-3">` + ((modificationRequest.notes != 'null') ? modificationRequest.notes : '') + `</td>
                            </tr>
                        </tbody>
                    </table>
                `);
                $('.day-in-month-modal .modal-dialog').css({ width: '850px' });
            },
            showDayTimesheets(date, container = '.calendar-day .timesheets .summary') {
                if (!this.timesheets || !this.timesheets['by-date']) return;
                let timesheet = this.timesheets['by-date'][date.format('YYYY-MM-DD')];
                if (timesheet) {
                    let tsDate = moment(timesheet.timesheet_date).format('dddd, MMMM D, YYYY'),
                        ts = this.formatTime({
                            timeIn: timesheet.time_in,
                            timeOut: timesheet.time_out,
                            lunchStart: timesheet.lunch_start,
                            lunchEnd: timesheet.lunch_end,
                            otStart: timesheet.overtime_start,
                            otEnd: timesheet.overtime_end,
                        });
                    $(container).html(`
                        <table class="ts-day-tbl w-96 table-fixed text-center text-md">
                            <thead>
                                <tr class="date">
                                    <th colspan="2" class="align-center items-center space-x-3">
                                        <i class="fa fa-calendar text-blue-600 text-lg"></i>
                                        <span>` + tsDate + `</span>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Summary</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span>Clock In</span>
                                        <span>Clock Out</span>
                                    </td>
                                    <td>
                                        <span>` + (ts.timeIn || '--:-- --') + `</span>
                                        <span>` + (ts.timeOut || '--:-- --') + `</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span>Lunch Break Start</span>
                                        <span>Lunch Break End</span>
                                    </td>
                                    <td>
                                        <span>` + (ts.lunchStart || '--:-- --') + `</span>
                                        <span>` + (ts.lunchEnd || '--:-- --') + `</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span>Overtime Start</span>
                                        <span>Overtime End</span>
                                    </td>
                                    <td>
                                        <span>` + (ts.otStart || '--:-- --') + `</span>
                                        <span>` + (ts.otEnd || '--:-- --') + `</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    `);
                }
            },
            dayClicked(e) {
                // update selected day
                this.day.selected = moment(e.date, 'D-M-YYYY'); // orig format of e.date is 'D-M-YYYY'
                this.day.month = this.day.selected.month();
                this.navLabel = this.getDayNavLabel(this.day.selected);
                this.pageIsChanged = false;
                const calendarMonth = this.getCalendarMonthNum(); // month: 0 to 11
                // check if current selected day is not in current month page
                if (calendarMonth < this.day.selected.month()) {
                    this.$refs.CalendarDay.NextMonth();
                } else if (calendarMonth > this.day.selected.month()) {
                    this.$refs.CalendarDay.PreMonth();
                }
                // clear previous timesheet, then show next
                $('.calendar-day .timesheets .summary').html('');
                $('.calendar-day .modification').html('');
                this.showDayTimesheets(this.day.selected);
                this.showDayModificationRequest(this.day.selected);
            },
            getDayNavLabel(date) {
                return moment(date).format('dddd, MMMM DD');
            },
            moveDay(move) {
                this.day.selected = this.day.selected.add(move, 'days');
                const day = this.day.selected.format('D-M-YYYY');
                // check if selected day is not in current month page
                if (this.day.month < this.day.selected.month()) {
                    this.day.month = this.day.selected.month();
                    this.$refs.CalendarDay.NextMonth();
                } else if (this.day.month > this.day.selected.month()) {
                    this.day.month = this.day.selected.month();
                    this.$refs.CalendarDay.PreMonth();
                }
                if (this.pageIsChanged) {
                    this.$refs.CalendarDay.ChooseDate(this.day.selected.format('DD/MM/YYYY'));
                    this.pageIsChanged = false;
                }
                // mark selected date
                $(() => $('.calendar-day .day.' + day).parent().trigger('click'));
                this.navLabel = this.getDayNavLabel(this.day.selected);
            },
            prevDay() {
                this.moveDay(-1);
            },
            nextDay() {
                this.moveDay(1);
            },
            upDay() {
                this.moveDay(-7);
            },
            downDay() {
                this.moveDay(7);
            },
            getDayConfig() {
                this.day = {
                    selected: moment(),
                    month: moment().month(),
                };
                return { day: this.day };
            },


            /**
             * FILTER BY WEEK
             */
            filterByWeek() {
                this.getWeekConfig();
                this.activeFilter('week');
                this.previous = this.prevWeek;
                this.next = this.nextWeek;
                this.navLabel = this.getWeekLabel();
                $(() => this.showWeekTimesheets());
            },
            showWeekTimesheets() {
                let day = 1;
                $('.calendar-week .timesheets .clock-ins').html('');
                if (!this.timesheets || !this.timesheets['by-date']) return;
                while (day < 7) {
                    let date = this.weekStart().add(day, 'days').format('YYYY-MM-DD'),
                        timesheet = this.timesheets['by-date'][date];
                    if (timesheet) {
                        let monthDay = moment(timesheet.timesheet_date).format('MMMM DD'),
                            dayName = moment(timesheet.timesheet_date).format('dddd'),
                            ts = this.formatTime({
                                timeIn: timesheet.time_in,
                                timeOut: timesheet.time_out,
                            });
                        $('.calendar-week .timesheets .clock-ins').append(`
                            <div id="` + timesheet.id + `" class="m-2 mt-3">
                                <div class="font-black">
                                    <div class="header flex justify-between px-1 text-base space-x-2">
                                        <span>` + dayName + `, ` + monthDay + `</span>
                                    </div>
                                    <div class="border-2 border-blue-400 rounded-md w-64 py-2.5 px-2.5 leading-7">
                                        <div class="flex justify-between">
                                            <span>Clock In</span>
                                            <span>` + (ts.timeIn || '--:-- --') + `</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Clock Out</span>
                                            <span>` + (ts.timeOut || '--:-- --') + `</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                        if (this.modificationRequests) {
                            let modificationRequest = this.modificationRequests[date];
                            if (!modificationRequest) {
                                $('.calendar-week .clock-ins #' + timesheet.id + ' .header span').after(`
                                    <button 
                                        id="ts_` + timesheet.id + "_" + timesheet.timesheet_date + `" 
                                        class="edit-btn w-10 h-5 bg-blue-700 rounded-md text-white text-xs font-black" 
                                        data-toggle="modal" 
                                        data-target="#add-timesheet-modal">EDIT</button>
                                `);
                            }
                        }
                    }
                    day++;
                }
            },
            weekStart() {
                return this.week.day.startOf('week');
            },
            weekEnd() {
                return this.week.day.endOf('week');
            },
            nextWeek() {
                this.moveWeek(7);
                return;
            },
            prevWeek() {
                this.moveWeek(-7);
                return;
            },
            moveWeek(move) {
                this.week.day = this.week.day.day(move); // next week
                this.rangePoints[0] = {date: this.weekStart().add(1, 'days').format('D/M/YYYY'), class: 'start-date'};
                this.rangePoints[1] = {date: this.weekEnd().format('D/M/YYYY'), class: 'end-date'};
                this.dateRange.start = this.weekStart().add(2, 'days').format('DD/MM/YYYY');
                this.dateRange.end = this.weekEnd().subtract(1, 'days').format('DD/MM/YYYY');
                if (this.week.month < this.week.day.month()) {
                    this.week.month = this.week.day.month();
                    this.$refs.CalendarWeek.NextMonth();
                } else if (this.week.month > this.week.day.month()) {
                    this.week.month = this.week.day.month();
                    this.$refs.CalendarWeek.PreMonth();
                }
                if (this.pageIsChanged) {
                    this.$refs.CalendarWeek.ChooseDate(this.week.day.format('DD/MM/YYYY'));
                    this.pageIsChanged = false;
                }
                this.showWeekTimesheets();
                this.navLabel = this.getWeekLabel();
            },
            getWeekLabel() {
                return this.weekStart().format('MMMM DD') + " - " + this.weekEnd().format('MMMM DD');
            },
            getWeekConfig() {
                this.week = {
                    day: moment(),
                    month: moment().month(),
                };
                this.rangePoints = [ // date format is required
                    {date: this.weekStart().add(1, 'days').format('D/M/YYYY'), class: 'start-date'},
                    {date: this.weekEnd().format('D/M/YYYY'), class: 'end-date'}
                ];
                this.dateRange = {
                    start: this.weekStart().add(2, 'days').format('DD/MM/YYYY'),
                    end: this.weekEnd().subtract(1, 'days').format('DD/MM/YYYY'),
                };
                return {
                    rangePoints: this.rangePoints,
                    dateRange: this.dateRange,
                    week: this.week,
                };
            },


            /**
             * FILTER BY MONTH
             */
            filterByMonth() {
                this.activeFilter('month');
                this.previous = this.prevMonth;
                this.next = this.nextMonth;
                this.navLabel = moment().format('MMMM YYYY');
                this.showMonthTimesheets(moment().format('MMMM'));
                return;
            },
            showMonthTimesheets(month) {
                $(() =>
                    this.timesheets['by-month'][month].forEach(timesheet => {
                        let date = moment(timesheet.timesheet_date).format('D-M-YYYY');
                        if (timesheet.time_in) {
                            $('.calendar-month .vfc-day .vfc-span-day .' + date)
                                .parent()
                                .after('<span class="absolute bg-blue-600 rounded-md m-1 top-8 px-3 text-white font-black text-xs" style="width: 120px; padding-top: 2px; padding-bottom: 2px;">Clock In</span');
                        }
                        if (timesheet.overtime_start) {
                            $('.calendar-month .vfc-day .vfc-span-day .' + date)
                                .parent()
                                .after('<span class="absolute bg-blue-900 rounded-md m-1 px-3 text-white font-black text-xs" style="width: 120px; top: 55px; padding-top: 2px; padding-bottom: 2px;">Overtime In</span')
                        }
                    })
                );
            },
            moveMonth() {
                const calendar = this.$refs.CalendarMonth.listCalendars[0];
                this.navLabel = calendar.dateTop;
                this.showMonthTimesheets(calendar.month);
            },
            prevMonth() {
                this.$refs.CalendarMonth.PreMonth();
                this.moveMonth();
                return;
            },
            nextMonth() {
                this.$refs.CalendarMonth.NextMonth();
                this.moveMonth();
                return;
            },
            dayInMonthClicked(e) {
                const selectedDate = moment(e.date, 'D/M/YYYY');
                $('#day-in-month-modal .modal-dialog').css({ width: '500px' });
                $('#day-in-month-modal .timesheet').html('No Timesheet');
                $('#day-in-month-modal .modification').html('');
                $('#day-in-month-modal').modal('show');
                this.showDayTimesheets(selectedDate, '.day-in-month-modal .timesheet');
                this.showDayModificationRequest(
                    selectedDate, 
                    '.day-in-month-modal .modification',
                    '.day-in-month-modal .timesheet table .date span'
                );
            },

            
            /**
             * GENERAL FUNCTIONS
             */
            pageChanged() { // for day, week filter
                if ((this.filter === 'day') && (this.day.month !== this.getCalendarMonthNum())) return this.pageIsChanged = true; 
                if ((this.filter === 'week') && (this.week.month !== this.getCalendarMonthNum())) return this.pageIsChanged = true; 
                return this.pageIsChanged = false;
            },
            getCalendarMonthNum() { // current calendar page
                let calendarMonth = this.filter === 'day' 
                    ? this.$refs.CalendarDay.listCalendars[0].month
                    : this.$refs.CalendarWeek.listCalendars[0].month;
                return moment(calendarMonth, 'MMMM').format('M') - 1;
            },
            activeFilter(filter) {
                this.pageIsChanged = false;
                $('.filter-btns .' + this.filter).removeClass('active-filter');
                $('.filter-btns .' + this.filter).attr('disabled', false);
                this.filter = filter;
                $('.filter-btns .' + filter).addClass('active-filter');
                $('.filter-btns .' + filter).attr('disabled', true);
            },
            formatTime(obj, format = 'LT') {
                let keys = Object.keys(obj);
                keys.forEach(key => {
                    if (obj[key]) obj[key] = moment(obj[key], 'hh:mm:ss').format(format);
                    else obj[key] = null;
                });
                return obj;
            },
            getRequest(api, success = (res) => null, errorMsg = 'Something went wrong') {
                fetch(api, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Authorization' : 'Bearer ' + this.loginToken, 
                        'X-CSRF-Token': this.csrfToken,
                    },
                }).then(response => {
                    if (response.ok) {
                        response.json().then(result => {
                            success(result);
                        });
                    } 
                    else throw response.statusText + ' ' + response.status;
                }).catch(err => {
                    $('.tms-top-btns').notify(errorMsg, {
                        className: 'error',
                        position: 'top center',
                        autoHide: false,
                    });
                    console.log('Something went wrong,', err);
                });
            },
            requestAllTimesheets() {
                this.getRequest('api/timesheet/employee/clocks', (result) => {
                    this.timesheets = result;
                    // this.showMonthTimesheets(moment().format('MMMM'));
                    // this.showWeekTimesheets();
                    this.showDayTimesheets(moment());
                }, 'Timesheets failed to load');
            },
            requestAllModificationRequests() {
                this.getRequest('api/timesheet/employee/modifications', (response) => {
                    this.modificationRequests = response;
                    this.showDayModificationRequest(moment());                    
                }, 'Timesheet modification requests failed to load');
            },
            confirmNotif(button, type, confirm = () => null) {
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
                    confirm();
                });
            },

            /**
             * MODAL
             */
            renderModalCalendar() {
                this.modal.key += 1;
            },
            onAddTimesheet() {
                if (this.modal.type === 'edit') {
                    this.clearInputs();
                    this.modal.datePicker = true;
                    this.renderModalCalendar();
                };
                this.modal.type = 'add';
                this.modal.title = 'Add Timesheet';
                this.modal.info = 'Using this form will require approval from supervisor.';
                $('.modal .ts-alert').removeClass('hidden');
                if (this.modal.selectedDate) {
                    const formatted = moment(this.modal.selectedDate).format('D/M/YYYY');
                    this.$refs.CalendarModal.ChooseDate(formatted);
                } else {
                    this.disableModalInputs(true);
                } 
            },
            onEditTimesheet(data) {
                if (!this.timesheets || !this.timesheets['by-date']) return;
                let timesheet = this.timesheets['by-date'][data.date];
                let ts = this.formatTime({
                    timeIn: timesheet.time_in,
                    timeOut: timesheet.time_out,
                    lunchStart: timesheet.lunch_start,
                    lunchEnd: timesheet.lunch_end,
                    otStart: timesheet.overtime_start,
                    otEnd: timesheet.overtime_end,
                }, 'kk:mm');
                let date = moment(timesheet.timesheet_date).format('D-M-YYYY');
                $('input[name=clock-in]').val(ts.timeIn); 
                $('input[name=clock-out]').val(ts.timeOut); 
                $('input[name=lunch-start]').val(ts.lunchStart); 
                $('input[name=lunch-end]').val(ts.lunchEnd); 
                $('input[name=overtime-start]').val(ts.otStart); 
                $('input[name=overtime-end]').val(ts.otEnd); 
                this.modal = {
                    title: 'Edit Timesheet',
                    info: 'Editing this timesheet will require approval from the supervisor.',
                    type: 'edit',
                    timesheetID: timesheet.id,
                    selectedDate: timesheet.timesheet_date,
                    datePicker: false,
                    markedDates: [moment(date, 'D-M-YYYY').format('D/M/YYYY')],
                };
                $('.modal .ts-alert').removeClass('hidden');
                this.disableModalInputs(false);
                this.renderModalCalendar();
            },
            submitTimesheetModification(e) {
                e.preventDefault();
                if (!this.modal.selectedDate) return alert('Pick a date to add time sheet');
                var jsonData = JSON.stringify({ 
                        modification_type: this.modal.type,
                        timesheet_id: this.modal.timesheetID,
                        timesheet_date: this.modal.selectedDate,
                        time_in: $('input[name=clock-in]').val(),
                        time_out: $('input[name=clock-out]').val(),
                        lunch_start: $('input[name=lunch-start]').val(),
                        lunch_end: $('input[name=lunch-end]').val(),
                        overtime_start: $('input[name=overtime-start]').val(),
                        overtime_end: $('input[name=overtime-end]').val(),
                        notes: $('textarea[name=notes]').val()
                    });
                fetch('api/timesheet/modification', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Authorization' : 'Bearer ' + this.loginToken, 
                        'X-CSRF-Token': this.csrfToken,
                    },
                    body: jsonData,
                }).then(response => {
                    if (response.ok) {
                        response.json().then(result => {
                            $('.add-timesheet-modal .modal-body').notify('Successfully posted timesheet adjustment request', {
                                className: 'success',
                                position: 'top center',
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 3000);
                        });
                    } else {
                        response.json().then(json => {
                            $('.add-timesheet-modal .modal-body').notify(json.error, {
                                className: 'error',
                                position: 'top center',
                            });
                        });
                        throw response.statusText + ' ' + response.status;
                    }
                }).catch(err => {
                    console.log('Something went wrong,', err);
                });
                $('.add-timesheet-modal .modal-body').notify('Processing...', {
                    className: 'info',
                    position: 'top center',
                    autoHide: false,
                });
                this.clearInputs();
                this.disableModalInputs(true);
                // need to explicitly disable here, check disableModalInputs()
                $('.modal .submit-modification-btn').attr('disabled', true); 
                return false;
            },
            cancelModifRequest(button) {
                this.confirmNotif('.ts-modif-tbl', 'cancel', () => {
                    const 
                        notifElement = '.ts-modif-tbl',
                        requestId = $(button).attr('id').split('-')[2];
                    fetch('api/timesheet/modification/cancel', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Authorization' : 'Bearer ' + this.loginToken, 
                            'X-CSRF-Token': this.csrfToken,
                        },
                        body: JSON.stringify({ 
                            request_id: requestId
                        }),
                    }).then(response => {
                        if (response.ok) {
                            response.json().then(result => {
                                $(notifElement).notify('Successfully cancelled timesheet adjustment request.', {
                                    className: 'success',
                                    position: 'top center',
                                });
                                setTimeout(function() {
                                    location.reload();
                                }, 3000);
                            });
                        } 
                        else throw response.statusText + ' ' + response.status;
                    }).catch(err => {
                        $(notifElement).notify('Something went wrong', {
                            className: 'error',
                            position: 'top center',
                        });
                        console.log('Something went wrong,', err);
                    });
                    $(notifElement).notify('Processing...', {
                        className: 'info',
                        position: 'top center',
                        autoHide: false,
                    });
                });
            },
            clearInputs() {
                $('input[name=clock-in]').val(''); 
                $('input[name=clock-out]').val(''); 
                $('input[name=lunch-start]').val(''); 
                $('input[name=lunch-end]').val(''); 
                $('input[name=overtime-start]').val(''); 
                $('input[name=overtime-end]').val(''); 
                this.$refs.CalendarModal.ChooseDate('today');
                this.modal.selectedDate = null;
                this.modal.markedDates = [];
                this.modal.timesheetID = null;
                this.renderModalCalendar();
            },
            closeAlert() {
                $('.modal .ts-alert').addClass('hidden');
            },
            modalDayClicked(e) {
                if (this.modal.type === 'edit') return null;
                this.modal.selectedDate = null;
                const selectedDate = moment(e.date, 'D/M/YYYY').format('YYYY-MM-DD');
                const timesheet = this.timesheets['by-date'][selectedDate]
                const isModfied = this.modificationRequests[selectedDate];
                if (!timesheet) {
                    this.disableModalInputs(true);
                    return $('.add-timesheet-modal .modal-body').notify('Missing Timesheet', {
                        className: 'warn',
                        position: 'top center',
                    });
                } else if (isModfied) {
                    this.disableModalInputs(true);
                    return $('.add-timesheet-modal .modal-body').notify('Timesheet is Already Modified', {
                        className: 'warn',
                        position: 'top center',
                    });
                }
                this.disableModalInputs(false);
                this.modal.selectedDate = selectedDate;
                this.modal.timesheetID = timesheet.id;
            },
            disableModalInputs(disabled) {
                $('input[name=clock-in]').attr('disabled', disabled); 
                $('input[name=clock-out]').attr('disabled', disabled); 
                $('input[name=lunch-start]').attr('disabled', disabled); 
                $('input[name=lunch-end]').attr('disabled', disabled); 
                $('input[name=overtime-start]').attr('disabled', disabled); 
                $('input[name=overtime-end]').attr('disabled', disabled); 
                // not to cause conflict on empty notes
                // $('.modal .submit-modification-btn').attr('disabled', disabled); 
            },
        }
    }
</script>