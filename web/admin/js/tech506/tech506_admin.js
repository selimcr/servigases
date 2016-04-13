if (typeof console == "undefined" || typeof console.log == "undefined" || typeof console.debug == "undefined")
    var console = { log:function () {
}, debug:function () {
} };
if (typeof jQuery !== 'undefined') {
    console.debug("JQuery found!!!");
} else {
    console.debug("JQuery not found!!!");
}

String.prototype.endsWith = function(suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
};

$(document).ready(function () {
    Tech506.init();
});
var Tech506 = {
    module:"",
    imagesURL:"",
    assetsURL:"",
    isIe:false,
    session:{},
    //pleaseWaitDiv: $('<div class="modal hide" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false"><div class="modal-header"><h1>Processing...</h1></div><div class="modal-body"><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div></div></div>'),
    pleaseWaitDiv: $("#pleaseWaitDialog"),
    showPleaseWait: function() {
        Tech506.pleaseWaitDiv.modal();
    },
    hidePleaseWait: function () {
        Tech506.pleaseWaitDiv.modal('hide');
    },
    logout:function (url) {
        location.href = url;
    },
    roundTo:function (original) {
        //return Math.round(original*100)/100;
        return original.toFixed(2);
    },
    init:function () {
        var module = Tech506.module;
        console.debug("Module: " + module);
        if (module) {
            switch (module) {
                case "users-list": Tech506.Users.List.init(); break;
                case "technicians-list": Tech506.Technicians.List.init(); break;
                case "sellers-list": Tech506.Sellers.List.init(); break;
                case "my-account": Tech506.Profile.init(); break;
                case "sale-new": Tech506.Register.initNew(); break;
                case "services-list": Tech506.Sales.Services.List.init(); break;
                case "services-schedule": Tech506.Sales.Services.Schedule.init(); break;
                case "products-categories-list": Tech506.Products.Categories.init(); break;
                case "products-list": Tech506.Products.Products.init(); break;
                case "product-prices": Tech506.Products.Prices.init(); break;
                case "services-schedule-list": Tech506.Sales.Services.ScheduleOnList.init(); break;
                case "services-change-status": Tech506.Sales.Services.StatusChange.init(); break;
                case "commisions-apply": Tech506.Comissions.Apply.init(); break;
                case "report-daily-schedule": Tech506.Reports.DailySchedule.init(); break;
                default: break;
            }
        }
        Tech506.setNavigation();
        Tech506.UI.init();
    },
    setNavigation: function() {
        var path = window.location.pathname;
        path = path.replace(/\/$/, "");
        path = decodeURIComponent(path);

        //Set Dashboard
        if(path.endsWith('admin') || path.endsWith("admin/")){
            $("#menu_dashboard_item").addClass("active");
        } else
        // Set Patientes Item Active
        if(path.indexOf('patients') > -1){
            $("#menu_patients_item").addClass("active");
        } else
        if (path.indexOf("sports")  > -1) {
            $("#menu_sports_item").addClass("active");
            $("#menu_forms_item").addClass("active");
        } else if(path.indexOf("pentions")  > -1){
            $("#menu_pentions_item").addClass("active");
            $("#menu_forms_item").addClass("active");
        } else if (path.indexOf("catalog/")  > -1) {
            $("#menu_forms_item").addClass("active");
        } else if (path.indexOf("users")  > -1) {
            $("#menu_users_item").addClass("active");
        }

    },
    ajaxCall:function (url, params, succedFunction, errorFunction) {
        var request = $.ajax({
            url:url,
            type:"POST",
            data:params,
            dataType:"json",
            cache: false
        });

        request.done(succedFunction);

        request.fail(errorFunction);
    },
    ajaxSyncCall:function (url, params, succedFunction, errorFunction) {
        var request = $.ajax({
            url:        url,
            type:       "POST",
            data:       params,
            dataType:   "json",
            async:      false
        });

        request.done(succedFunction);

        request.fail(errorFunction);
    },
    showInfoMessage:function (message) {
        Tech506.showInformationMessage(message);
    },
    showInformationMessage:function (message) {
        bootbox.alert(message, function() {
        });
    },
    showErrorMessage:function (message) {
        alert(message);
    },
    showConfirmationQuestion:function (message) {
        return confirm(message);
    },
    UI:{
        translates:{},
        urls:{},
        vars:{},
        intervals:{},
        init:function () {
            $('.date-input').daterangepicker({
                locale: {
                    format: 'DD/MM/YYYY',
                    "daysOfWeek": [
                        "D",
                        "L",
                        "M",
                        "K",
                        "J",
                        "V",
                        "S"
                    ],
                    "monthNames": [
                        "Enero",
                        "Febrero",
                        "Marzo",
                        "Abril",
                        "Mayo",
                        "Junio",
                        "Julio",
                        "Agosto",
                        "Septiembre",
                        "Octubre",
                        "Noviembre",
                        "Diciembre"
                    ],
                    "firstDay": 1
                },
                singleDatePicker: true,
                calender_style: "picker_4",
                showDropdowns: true
            }, function (start, end, label) {
                //console.log(start.toISOString(), end.toISOString(), label);
            });
        },
        validateForm:function (formSelector) {
            //alert("validating form");
            var result = $(formSelector).validationEngine('validate');
            //alert("result "+result);
            return result;
        },
        formatMoney: function(value) {
            return Tech506.UI.formatMoneyWithDecimals(value, 2);
        },
        formatMoneyWithDecimals: function(value, decimals) {
            return "$" + (value * 1).toFixed(decimals).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
        },
        getAddressFromInputs: function() {
            return $("#dir1").val() + " :: " + $("#dir2").val() + " :: " + $("#dir3").val() + " :: " + $("#dir4").val();
        },
        isValidNumber: function(n){
            return !isNaN(parseFloat(n)) && isFinite(n);
        },
        isValidPositiveOrZeroNumber: function(n){
            return Tech506.UI.isValidNumber(n) && (n >= 0);
        }
    }
};

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};

