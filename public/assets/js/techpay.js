$(window).on('load', function () {
    $(".page-loader").hide("fast");
});

/**
 * Display a message over the selected element in an overlay
 * @param {type} message text to be displayed
 * @param {type} selector
 * @returns {jQuery} a blockUI object you must call the $(block).unblock() method on to remove the overlay
 *
 * // E.G var overlay = setOverlay('Processing, please wait...', '.login-form');
 */
function setOverlay(message, selector) {
    var block = jQuery(selector).parent();
    jQuery(block).block({
        message: '<span class="spinner-border text-purple" role="status"></span> <span style="vertical-align: super; margin-left: 5px">' + message + '<span class="animated-dots"></span></span>',        // timeout: 5000, //unblock after 2 seconds
        overlayCSS: {
            // backgroundColor: '#fff',
            opacity: 0.7, cursor: 'wait'
        }, css: {
            border: 0,
            padding: '10px 15px',
            color: '#fff',
            width: 'auto',
            'border-radius': 2,
            backgroundColor: '#333',
            'font-size': '14px'
        }
    });
    return block;
}

$(document).ready(function () {

    //check if datatables is loaded
    var isdef = typeof $.fn.DataTable !== 'undefined';
    if (isdef) {
        $('.dt-enabled').DataTable(
            {
                "order": [[0, 'desc']]
            }
        );
    }

    //Global overlay
    $('.block-on-click').on('click', function (e) {
        setOverlay('Processing, please wait...', '.page');
    })

    //Date picker
    if(typeof $.fn.datepicker !== 'undefined') {
        $('.dp-enabled').datepicker({
            format: "dd-mm-yyyy",
            todayBtn: "linked",
            autoclose: true,
            todayHighlight: true
        });

        $('.dp-period').datepicker({
            format: "mm-yyyy",
            minViewMode: 1,
            maxViewMode: 0,
            todayBtn: "linked",
            autoclose: true,
            todayHighlight: true
        });
    }
});


//TODO: move to payments.js
$('#get-account').on('click', function (e) {

    let accountNumber = $('#accountNumber')
    if (accountNumber.val() !== '') {
        let overlay = setOverlay('Fetching bills', '.query-body');
        let nextButton = $(this)
        $(nextButton).addClass('disabled')
        $(accountNumber).addClass('is-valid')
        $('#billsForm').submit()
    } else {
        $(accountNumber).addClass('is-invalid')
    }
})

$('#get-amounts').on('click', function (e) {
    let overlay = setOverlay('Fetching bills', '.query-body');
    let nextButton = $(this)
    $(nextButton).addClass('disabled')
    $('#billsForm').submit()
})

$('#postPayment').on('click', function (e) {
    let overlay = setOverlay('Preparing payment', '#paymentsModal .modal-body');
    let nextButton = $(this)
    $(nextButton).addClass('disabled')
    $('#billsForm').submit()
})
