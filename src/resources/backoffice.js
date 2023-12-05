backoffice = {    
    version: '1.0',
    locale: null,
    url: null,
    ajax: true,
    currentUrl: null,
    defaultUrl: "",
    init: function() {

        // captyure hash change and load url
        if(backoffice.ajax) {
            jQuery(window).on('hashchange', function() {
                var hash = window.location.hash;
                hash = hash.replace("#", "/");
                backoffice.loadUrl(hash);
            });
        }

        // capute errors ajax
        $(document).ajaxError(function myErrorHandler(event, xhr, ajaxOptions, thrownError) {
            event.preventDefault();
            if (xhr.status == 401) {
                window.location.href = "/login";
            }
            else {
                if(xhr.responseJSON != 'undefined' && xhr.responseJSON != undefined) {
                    alerts.show('ko', 'An error occurred: '+xhr.responseJSON);
                }
                else {
                    alerts.show('ko', 'An error occurred: '+xhr.statusText);
                }
            }
        });
                                
        // loading overlay
        $(function() {
            // define loading overlay
            $.LoadingOverlaySetup({
                color: "rgba(255, 255, 255, 0.8)",
                maxSize: "64px",
                minSize: "64px",
                imagePosition: "center center",
                image: "/back/images/ajax_loading.gif"
            });
        });

        $.datepicker.setDefaults($.datepicker.regional["'"+backoffice.locale+"'"]);
        
        // load app
        if(backoffice.ajax) {
            backoffice.loadDefaultUrl();
        }

    },
    // load default url
    loadDefaultUrl: function()     {
        var hash = window.location.hash;
        hash = hash.replace("#", "/");        
        backoffice.loadUrl(hash);
    },
    // load url
    loadUrl: function(url) {      
        if(url=='') {            
            url = backoffice.defaultUrl;
        }        
        $("body").LoadingOverlay('show');
        if(url != '') {
            backoffice.currentUrl = url;
            $.ajax({
                method: "get",
                url: url,
                success: function(data) {
                    $("body").LoadingOverlay('hide');
                    $(".content-wrapper").html(data);
                    window.scrollTo(1, 1);
                },
                error: function (xhr, ajaxOptions, thrownError) {                    
                    // error capture in general
                },
                complete: function() {
                    $("body").LoadingOverlay('hide');
                }
            });
        }
    },
    // refresh
    refresh: function() {
        backoffice.loadUrl(backoffice.currentUrl);
    }
}