
// used for registration errors
window.errorInput = function (input, errMsg) {
    $('.register-form .' + input + '-container').addClass('border-red-400');
    $('.' + input + '-container .warning').removeClass('hidden');
    $('.' + input + '-container').next().text(errMsg); // p tag next to input tag
}

$(document).ready(function() {
    // customize highlight for including warning icons
    let customInputs = [
        'first-name',
        'middle-name',
        'last-name',
        'birth-date',
        'department',
        'email',
        'password',
        'password-confirm'
    ];
    let focus = `
        border-blue-300
        border-indigo-300 
        ring ring-indigo-200 
        ring-opacity-50
    `;
    function inputContainerFocus (containerSelector, inputSelector) {
        $(inputSelector).focus(function(e) {
            $(containerSelector).addClass(focus);
        });
        $(inputSelector).focusout(function(e) {
            $(containerSelector).removeClass(focus)
        });
    }
    customInputs.forEach((input) => {
        inputContainerFocus(
            '.register-form .' + input + '-container', 
            '.register-form .' + input
        );
    });

    // retain department select value
    let department = sessionStorage.getItem("selected-department");  
    if (department !== null) $('.department-container .department').val(department);
    $('.department-container select').on('change', function () { 
        sessionStorage.setItem("selected-department", $(this).val());
    });
});
