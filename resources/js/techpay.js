
jQuery(document).ready(function () {

    let item_amount = 0;
    let order_id = ''
    let mobile = ''
    let check_status = false
    let payment_mode = 'momo'
    let overlay = ''
    let fields = []

    setInterval(() => {
        checkTransactionStatus()
    }, 5000);


    // $('#modalDocs').modal('show')

    $('.finish').click(function () {

        let sectionsData = {};
        $('.pane').each(function () {
            let sectionIndex = $(this).data('section');
            sectionsData[sectionIndex] = {};

            if (sectionIndex === 'business_ownership') {
                // Handle business ownership section specially
                let owners = [];
                $('.owner-section').each(function(ownerIndex) {
                    let owner = {};
                    $(this).find('input, select').each(function() {
                        let fieldName = $(this).attr('name');
                        owner[fieldName] = $(this).val();
                    });
                    if (Object.keys(owner).length > 0) {
                        owners.push(owner);
                    }
                });
                sectionsData[sectionIndex] = owners;
            } else {
                // Handle other sections normally
                $(this).find('input, select').each(function () {
                    let fieldName = $(this).attr('name');
                    let fieldValue = $(this).val();
                    sectionsData[sectionIndex][fieldName] = fieldValue;
                });
            }
        });
        console.log(sectionsData);
        let submitOverlay = setOverlay('Submitting data. Please wait...', '.main-content');
        let mobile = $('#mobile').val();

        $.ajax({
            url: 'admissions/submit',
            method: 'POST',
            data: {
                fields: JSON.stringify(sectionsData),
                mobile: mobile
            },
            success: function (response) {
                submitOverlay.unblock();
                if (response.status === 'SUCCESS') {
                    swal('Submission Successful', response.statusMessage, 'success');
                } else {
                    swal('Submission Failed', response.statusMessage, 'warning');
                }
            },
            error: function () {
                submitOverlay.unblock();
                swal('Submission Error', 'There was an error submitting your data. Please try again later.', 'error');
            }
        });

    })

    // $('.foik').each(function () {
    //     if ($(this).is('input[type="text"]') || $(this).is('textarea')) {
    //         $(this).val('Dummy Text');
    //     } else if ($(this).is('input[type="number"]')) {
    //         $(this).val(Math.floor(Math.random() * 100));
    //     } else if ($(this).is('input[type="email"]')) {
    //         $(this).val('john@example.com');
    //     } else if ($(this).is('select')) {
    //         var options = $(this).find('option').filter(function () {
    //             return $(this).val() !== "" && $(this).val() !== null;
    //         });
    //         var randomIndex = Math.floor(Math.random() * options.length);
    //         $(this).val(options.eq(randomIndex).val());
    //     }
    // });


    function validateRequiredFields(index) {
        // return true;
        let isValid = true;
        $('.pane-' + index + ' .foik').each(function () {
            if ($(this).prop('required') && $(this).val() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        return isValid;
    }

    $('.foik').change(function () {
        //remove is-invalid class when user starts typing then add it back if the field is empty. add is-valid class if the field is not empty
        if ($(this).val() === '') {
            $(this).addClass('is-invalid');
            $(this).removeClass('is-valid');
        } else {
            $(this).removeClass('is-invalid');
            $(this).addClass('is-valid');
        }
    })

    $('.start').click(function () {
        if ($('.read-terms').is(':checked')) {
            let index = 0;
            $('.pane-' + index).hide();
            $('.pane-' + (index + 1)).show()
            $('.step-item.index-' + (index)).removeClass('active');
            $('.step-item.index-' + (index + 1)).addClass('active');
        } else {
            swal('Tick the Checkbox', 'Please tick the checkbox to confirm that you have read and understood the requirements.', 'warning')
        }
    })

    $('.go-back').click(function () {
        let index = $(this).data('index');
        $('.pane-' + index).hide();
        $('.pane-' + (index - 1)).show()
        $('.step-item.index-' + (index)).removeClass('active');
        $('.step-item.index-' + (index - 1)).addClass('active');
    })

    $('.move-on').click(function () {

        let index = $(this).data('index');

        // if (index == 5 && !validateSupportingDocuments()) {
        //     return false;
        // }

        if (validateRequiredFields(index)) {
            $('.pane-' + index).hide();
            $('.pane-' + (index + 1)).show();
            $('.step-item.index-' + (index)).removeClass('active');
            $('.step-item.index-' + (index + 1)).addClass('active');
        } else {
            // alert('Please fill all required fields.');
        }


        //get all fields and their values and group by section then build them as an accordion on the .summary-accordion
        let fields = [];
        //get fields and their values separated by section
        $('.foik').each(function () {
            let section = $(this).data('section');
            let field = $(this).attr('placeholder');
            let value = $(this).val();

            fields.push({
                section: section,
                field: field,
                value: value
            });

        });
        fields = JSON.stringify(fields);
        //build the accordian
        let accordian = '';
        let sections = [];
        fields = JSON.parse(fields)
        fields.forEach(function (field) {
            if (!sections.includes(field.section)) {
                sections.push(field.section)
            }
        })
        sections.forEach(function (section, index) {
            let section_fields = fields.filter(function (field) {
                return field.section === section;
            });
            let uniqueId = 'heading' + index;
            let section_accordian = '<div class="accordion-item">' +
                '<h2 class="accordion-header" id="' + uniqueId + '">' +
                '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' + uniqueId + '" aria-expanded="false" aria-controls="collapse' + uniqueId + '">' +
                section +
                '</button>' +
                '</h2>' +
                '<div id="collapse' + uniqueId + '" class="accordion-collapse collapse" aria-labelledby="' + uniqueId + '" data-bs-parent="#summary-accordion">' +
                '<div class="accordion-body">' +
                '<table class="table table-bordered">' +
                '<thead>' +
                '<tr>' +
                '<th>Field</th>' +
                '<th>Value</th>' +
                '</tr>' +
                '</thead>' +
                '<tbody>';
            section_fields.forEach(function (field) {
                section_accordian += '<tr>' +
                    '<td>' + field.field + '</td>' +
                    '<td>' + field.value + '</td>' +
                    '</tr>';
            });
            section_accordian += '</tbody>' +
                '</table>' +
                '</div>' +
                '</div>' +
                '</div>';
            accordian += section_accordian;
        });
        $('.summary-accordion').html(accordian)

    })
    Dropzone.autoDiscover = false;

    $('#make-payment').on('click', function () {
        let fields = [];
        //get fields and their values separated by section
        $('.foik').each(function () {
            let section = $(this).data('section')
            let field = $(this).attr('name')
            let value = $(this).val()
            fields.push({
                section: section,
                field: field,
                value: value
            })
        })
        fields = JSON.stringify(fields);
        console.log(fields)

        if (payment_mode !== 'momo') {
            $.ajax({
                url: '{{url("admissions/payment/create")}}',
                method: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "payment_mode": 'card',
                    "fields": fields
                },
                beforeSend: function () {
                    overlay = setOverlay('Processing payment. Please wait...', '.payments-wrapper .card .card-body');
                },
                success: function (data) {
                    if (data.status === 'SUCCESS') {
                        order_id = data.reference
                        $('.d-order-id').text(order_id)
                        $('.payments-wrapper').hide()
                        $('.receipt-wrapper').show()
                        setOverlay('Redirecting, Please wait...', '#main-wrapper');
                        window.location.href = data.checkout
                    } else {
                        swal('Could not initiate payment', data.statusText, 'warning')
                    }
                    overlay.unblock()
                },
                error: function (data) {
                    overlay.unblock()
                    swal('Something went wrong', 'Ensure you have an active internet connection or try again later.')
                }
            })
        } else {
            var input = $('#mobileNumber').val();
            var pattern = /^(096|076|095|075|097|077)\d+$/;
            if (!pattern.test(input)) {
                $('.mobile-help').text('Invalid mobile. Please enter a number that starts with 096, 076, 095, 075, 097, or 077.');
                return false;
            }
            if (input.length < 10) {
                $('.mobile-help').text('Mobile number too short. Please enter a valid 10 digit number.');
                return false;
            }
            if (input.length > 10) {
                $('.mobile-help').text('Mobile number too long. Please enter a valid 10 digit number.');
                return false;
            }
            $('.mobile-help').text('');
            //Create the transaction
            item_amount = $('#amount_field').val();
            mobile = $('#mobileNumber').val();
            $('.d-mobile').text(mobile)
            $('.d-amount').text(item_amount)
            overlay = null;
            //create a ajax request to create the transaction
            $.ajax({
                url: '{{url("admissions/payment/create")}}',
                method: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "mobile": mobile,
                    "payment_mode": "momo",
                    "fields": fields
                },
                beforeSend: function () {
                    overlay = setOverlay('Processing payment. Please wait...', '.payments-wrapper .card .card-body');
                },
                success: function (data) {
                    if (data.status === 'SUCCESS') {
                        order_id = data.reference
                        $('.d-order-id').text(order_id)
                        $('.payments-wrapper').hide()
                        $('.receipt-wrapper').show()
                        check_status = true
                    } else {
                        swal('Could not initiate payment', data.statusText, 'warning')
                    }
                    overlay.unblock()
                },
                error: function (data) {
                    overlay.unblock()
                    swal('Something went wrong', 'Ensure you have an active internet connection or try again later.', 'info')
                }
            })
        }

    })

    //Prevent browser reload
    $(window).bind('beforeunload', function () {
        if (check_status) {
            return 'Are you sure you want to leave before completing the payment?';
        }
    });

    //swap between payment methods
    $('.method-tab').on('click', function () {
        let tab = $(this).data('tab')
        payment_mode = tab
    })

    function checkTransactionStatus() {
        var reference = order_id
        if (check_status) {
            $.ajax({
                url: '{{url("admissions/payment/status")}}',
                method: 'get',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "reference": order_id
                },
                success: function (data) {
                    if (data.status === 'SUCCESS') {
                        $('.transaction-status-indicator').removeClass('text-azure').addClass('text-success')
                        $('.transaction-status-indicator').html('<i class="fa fa-check"></i> Paid');

                        $('.new-transaction').removeClass('disabled')
                        $('.print-receipt').removeClass('disabled')
                        $('.print-receipt').attr('href', '{{url("admissions/payment/receipt/")}}/' + data.ref)

                        $('.step3').removeClass('active')
                        $('.step4').addClass('active')

                        $('#successModal').modal('show');

                        check_status = false
                    } else if (data.status === 'FAILED') {
                        $('.transaction-status-indicator').removeClass('text-azure').addClass('text-danger')
                        $('.transaction-status-indicator').html('<i class="fa fa-times"></i> Failed');

                        $('.new-transaction').removeClass('disabled')

                        $('.step3').removeClass('active')
                        $('.step4').addClass('active')
                        check_status = false
                    } else if (data.status !== 'PENDING') {
                        swal('Could not get status', data.statusText, 'warning')
                        $('.new-transaction').removeClass('disabled')
                        $('.transaction-status-indicator').removeClass('text-azure').addClass('text-danger')
                        $('.transaction-status-indicator').html('<i class="fa fa-times"></i> ERROR');
                        // check_status = false
                    }
                },
                error: function (data) {
                    overlay.unblock()
                    // swal('Something went wrong', 'Ensure you have an active internet connection or try again later.')
                }
            })
        }
    }

    $('#mobileNumber').on('input change', function () {
        var input = $(this).val();
        var inputValue = $(this).val();
        // Remove non-numeric characters using a regular expression
        var numericValue = inputValue.replace(/\D/g, '');
        // Set the cleaned value back to the input field
        $(this).val(numericValue);

        $('.p-mode').css('filter', 'grayscale(1)'); // reset all logos to grey
        if (input.startsWith('096') || input.startsWith('076')) {
            $('.p-mode.mtn').css('filter', 'grayscale(0)');
        }
        if (input.startsWith('095') || input.startsWith('075')) {
            $('.p-mode.zamtel').css('filter', 'grayscale(0)');
        }
        if (input.startsWith('097') || input.startsWith('077')) {
            $('.p-mode.airtel').css('filter', 'grayscale(0)');
        }
    });

    $('.p-mode').on('click', function () {
        if ($('#mobileNumber').val() == '' || $('#mobileNumber').val().length === 3) {
            $('#mobileNumber').val($(this).data('prefix'))
            var className = $(this).data('class');
            $('.p-mode').css('filter', 'grayscale(1)');
            $('.p-mode.' + className).css('filter', 'grayscale(0)');
        }
        $('#mobileNumber').focus()
    })

})
