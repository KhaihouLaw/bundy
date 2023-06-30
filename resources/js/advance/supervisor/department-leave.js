 $(() => {
    const table = 'table#datatable';
    if ($(table).length === 0) return;
    const buttonConig = {
        title: 'Employee Leave Requests',
        exportOptions: {
            columns: ':not(.no-export)'
        }
    }
    $(table).DataTable({
        deferRender: true,
        order: [[1, 'asc']],
        columnDefs: [{targets: [0], orderable: false}],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], 
        // select: true,
        search: {
            search: "Pending",
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
        view(modelId) {return '/api/leave/request/' + modelId + '/evaluate'},
        get approve() {return '/api/leave/request/approve'},
        get reject() {return '/api/leave/request/reject'},
    }
    
    $(sel.dtBtn).removeClass(cls.dtBtn);

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
    function successOperation() {
        window.c.successModalAlert();
        setTimeout(() => location.reload(), 1000);
    }
    function failedOperation() {
        window.c.failedModalAlert();
        setTimeout(() => location.reload(), 1000);
    }
    function view() {
        const requestId = $(this).attr(attr.dataModelId);
        fetch(api.view(requestId), {
            ...window.c.getMethod(),
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
        window.c.confirmNotif(this, 'approve', () => {
            const requestId = $(this).attr(attr.dataModelId);
            fetch(api.approve, {
                ...window.c.postMethod(),
                body: JSON.stringify({ leave_request_id: requestId })
            }).then(response => {
                if (response.ok) {
                    successOperation();
                } else {
                    window.c.errorNotif(this, response);
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                failedOperation();
                disableButtons(false);
                console.log('Something went wrong,', err);
            });
            disableButtons(true);
            window.c.loadingModalAlert();
        });
    }
    function reject() {
        window.c.confirmNotif(this, 'reject', () => {
            const requestId = $(this).attr(attr.dataModelId);
            fetch(api.reject, {
                ...window.c.postMethod(),
                body: JSON.stringify({ leave_request_id: requestId })
            }).then(response => {
                if (response.ok) {
                    successOperation();
                } else {
                    window.c.errorNotif(this, response);
                    throw response.statusText + ' ' + response.status;
                }
            }).catch(err => {
                failedOperation();
                disableButtons(false);
                console.log('Something went wrong,', err);
            });
            disableButtons(true);
            window.c.loadingModalAlert();
        });
    }

    /**
     * Bindings
     */
    $(document).on('click', sel.approveBtn, approve);
    $(document).on('click', sel.rejectBtn, reject);
    $(document).on('click', sel.viewBtn, view);
});    