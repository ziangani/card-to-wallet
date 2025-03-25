(function ($) {
    "use strict";

    // Scroll-Top
    $(".scroll-top").hide();
    $(window).on("scroll", function () {
        if ($(this).scrollTop() > 300) {
            $(".scroll-top").fadeIn();
        } else {
            $(".scroll-top").fadeOut();
        }
    });
    $(".scroll-top").on("click", function () {
        $("html, body").animate(
            {
                scrollTop: 0,
            },
            700
        );
    });

    $(document).ready(function () {
        $(".select2").select2({
            theme: "bootstrap",
        });

        $("#checkShipping").on("change", function () {
            $(".shipping-form").toggle();
        });

        $(".paypal").hide();
        $(".stripe").hide();
        $(".bank").hide();
        $(".cash-on-delivery").hide();

        $("#paymentMethodChange").on("change", function () {
            if ($("#paymentMethodChange").val() == "PayPal") {
                $(".paypal").show();
                $(".stripe").hide();
                $(".bank").hide();
                $(".cash-on-delivery").hide();
            } else if ($("#paymentMethodChange").val() == "Stripe") {
                $(".paypal").hide();
                $(".stripe").show();
                $(".bank").hide();
                $(".cash-on-delivery").hide();
            } else if ($("#paymentMethodChange").val() == "Bank") {
                $(".paypal").hide();
                $(".stripe").hide();
                $(".bank").show();
                $(".cash-on-delivery").hide();
            } else if ($("#paymentMethodChange").val() == "Cash On Delivery") {
                $(".paypal").hide();
                $(".stripe").hide();
                $(".bank").hide();
                $(".cash-on-delivery").show();
            } else if ($("#paymentMethodChange").val() == "") {
                $(".paypal").hide();
                $(".stripe").hide();
                $(".bank").hide();
                $(".cash-on-delivery").hide();
            }
        });
    });

    // Preloader
    $("#status").fadeOut(); // will first fade out the loading animation
    $("#preloader").delay(350).fadeOut("slow"); // will fade out the white DIV that covers the website.
    $("body").delay(350).css({
        overflow: "visible",
    });

    // Wow Active
    new WOW().init();

    // Mean Menu
    jQuery(".mean-menu").meanmenu({
        meanScreenWidth: "991",
    });

    // Popup
    $(".video-button").magnificPopup({
        type: "iframe",
    });

    $(".magnific").magnificPopup({
        gallery: {
            enabled: true,
        },
        type: "image",
    });

    $(".gal-video").magnificPopup({
        disableOn: 700,
        type: "iframe",
        mainClass: "mfp-fade",
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false,
    });

    $("#dataTable2").DataTable({
        scrollX: true,
        order: [[1, "desc"]],
    });

    // Autocomplete Off Globally
    $(document).on("focus", ":input", function () {
        $(this).attr("autocomplete", "off");
    });
})(jQuery);
