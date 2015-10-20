var Tech506 = Tech506 || {};

Tech506.Sales = {
    Services: {
        StatusChange: {
            init: function() {
                $(".btn-change-status").click(function(e){
                    var rel = $(this).attr('rel');
                    var newStatus = $(this).attr('newStatus');
                    Tech506.Sales.Services.StatusChange.changeStatus(rel, newStatus);
                });
            },
            changeStatus: function(serviceId, newStatus) {
                Tech506.UI.vars['service-id'] = serviceId;
                Tech506.UI.vars['new-status'] = newStatus;
                var confirmMsg = (newStatus == 3)?
                    Tech506.UI.translates['confirm.cancel.service']:Tech506.UI.translates['confirm.finalize.service'];
                bootbox.dialog({
                    message: confirmMsg,
                    title: 'Confirmar',
                    buttons: {
                        no: {
                            label: Tech506.UI.translates['no'],
                            className: "btn-danger",
                            callback: function() {
                            }
                        },
                        yes: {
                            label: Tech506.UI.translates['yes'],
                            className: "btn-primary",
                            callback: function() {
                                bootbox.prompt(Tech506.UI.translates['enter-change-status-msg'], function(result) {
                                    if (result === null) {
                                        Tech506.showErrorMessage(Tech506.UI.translates['change-status-empty-msg']);
                                    } else {
                                        Tech506.showPleaseWait();
                                        Tech506.ajaxCall(Tech506.UI.urls['service-change-status'], {
                                                serviceId: Tech506.UI.vars['service-id'],
                                                status: Tech506.UI.vars['new-status'],
                                                msg: result
                                            },
                                            function (data) {
                                                Tech506.hidePleaseWait();
                                                if (data.error) {
                                                    Tech506.showErrorMessage(data.msg);
                                                } else {
                                                    $("#service-row-" + Tech506.UI.vars['service-id']).remove();
                                                    Tech506.showInfoMessage(data.msg);
                                                }
                                            }, function () {
                                                Tech506.hidePleaseWait();
                                            }, true);
                                    }
                                });
                            }
                        }
                    }
                });
            }
        },
        ScheduleOnList: {
            init: function() {
                $('.datepicker').datepicker({
                    format: "dd/mm/yyyy",
                    todayBtn: true,
                    language: "es",
                    todayHighlight: true,
                    autoclose: true
                }).on('changeDate', function(ev){
                    $(this).attr("value", ev.target.value);
                    var rel = $(this).attr("rel");
                    Tech506.Sales.Services.ScheduleOnList.updateServiceScheduleInfo(rel);
                });
                $(".timepicker").timepicker().on('changeTime.timepicker', function(e){
                    console.log('The time is ' + e.time.value);
                    /*console.log('The hour is ' + e.time.hours);
                    console.log('The minute is ' + e.time.minutes);
                    console.log('The meridian is ' + e.time.meridian);*/
                    $(this).attr("value", e.time.value);
                    var rel = $(this).attr("rel");
                    Tech506.Sales.Services.ScheduleOnList.updateServiceScheduleInfo(rel);
                });
                $(".service-control").blur(function(e){
                    var rel = $(this).attr("rel");
                    Tech506.Sales.Services.ScheduleOnList.updateServiceScheduleInfo(rel);
                });
                $(".service-technician").change(function(e){
                    var rel = $(this).attr("rel");
                    Tech506.Sales.Services.ScheduleOnList.updateServiceScheduleInfo(rel);
                });
                $(".btn-edit").click(function(e){
                    Tech506.showPleaseWait();
                    location.href = Tech506.UI.urls['manage-service'] + "/" + $(this).attr("rel");
                });
                $(".btn-cancel").click(function(e) {
                    Tech506.UI.vars["service-id"] = $(this).attr("rel");
                    bootbox.dialog({
                        message: Tech506.UI.translates['confirm-delete-msg'],
                        title: Tech506.UI.translates['confirm-delete-title'] + ": " + Tech506.UI.vars["service-id"],
                        buttons: {
                            no: {
                                label: Tech506.UI.translates['no'],
                                className: "btn-danger",
                                callback: function() {
                                }
                            },
                            yes: {
                                label: Tech506.UI.translates['yes'],
                                className: "btn-primary",
                                callback: function() {
                                    Tech506.showPleaseWait();
                                    Tech506.ajaxCall(Tech506.UI.urls['service-change-status'], {
                                            serviceId: Tech506.UI.vars["service-id"],
                                            status: 3
                                        },
                                        function (data) {
                                            Tech506.hidePleaseWait();
                                            if (data.error) {
                                                Tech506.showErrorMessage(data.msg);
                                            } else {
                                                $("#service-row-" + Tech506.UI.vars["service-id"]).remove();
                                                Tech506.showInfoMessage(data.msg);
                                            }
                                        }, function () {
                                            Tech506.hidePleaseWait();
                                        }, true);
                                }
                            }
                        }
                    });
                });
            },
            updateServiceScheduleInfo: function(rel) {
                var date = $("#date-"+rel).val();
                var hour = $("#time-"+rel).val();
                var technician = $("#technician-"+rel).val();
                Tech506.ajaxCall(Tech506.UI.urls['service-schedule-update'], {
                        id: rel,
                        date: date,
                        hour: hour,
                        technicianId: technician
                    },
                    function (data) {
                        if (data.error) {
                            Tech506.showErrorMessage(data.msg);
                        } else {
                        }
                    }, function () {
                        Tech506.hidePleaseWait();
                    }, true);
            }
        },
        Schedule: {
            init: function() {
                $(".select2").select2();
                $('#scheduleDate').datepicker({
                    format: "dd/mm/yyyy",
                    todayBtn: true,
                    language: "es",
                    todayHighlight: true,
                    autoclose: true
                });
                $("#scheduleHour").timepicker();
                $("#addServiceBtn").click(function(e){
                    Tech506.Register.openAddServiceWindow();
                });
                $("#product").change(function(e){
                    Tech506.Register.loadServicePrices();
                });
                /**/
                $("#registerBtn").click(function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    Tech506.Sales.Services.scheduleService();
                });
                $("#addPartBtn").click(function(e){
                    Tech506.Sales.Services.addPartLine();
                });
                $("#saveLinesBtn").click(function(e){
                    Tech506.Sales.Services.saveServiceParts();
                });
                $(".partRemoveIcon").unbind().click(function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var rowNumber = $(this).attr('rel');
                    $("#part-row-" + rowNumber).remove();
                });
                $("#cancelButton").click(function(e){
                    Tech506.Sales.Services.cancelService();
                });
                $("#finalizeButton").click(function(e){
                    Tech506.Sales.Services.finalizeService();
                });

                if(Tech506.UI.vars["status"] > 2) {
                    Tech506.Sales.Services.blockEverything();
                }

                $("#edit-service-from").submit(function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    Tech506.Sales.Services.saveServiceRowInformation();
                    return false;

                });
                $("#save-service-row-btn").click(function(e) {
                    Tech506.Sales.Services.saveServiceRowInformation();
                });

                $("#save-service-list-btn").click(function(e){
                    Tech506.Sales.Services.saveServicesRows();
                });

                Tech506.Register.setEventsOfServicesRows();

                Tech506.hidePleaseWait();
            }
        },
        List: {
            yesNoFormat: function(value, row, index) {
                return [
                    (value)? 'Si':'No',
                ].join('');
            },
            operateFormatter: function(value, row, index){
                var url = Tech506.UI.urls['manage-service'] + "/";
                    // "http://localhost/servigases/web/admin/services/schedule/open/";
                return [
                    '<a class="scheduleIcon" href="' + url + row.id + '" title="Programar">',
                    '<i class="glyphicon glyphicon-calendar"></i>',
                    '</a>'
                ].join('');

                /*if(row.statusCode == 1 || row.statusCode == 2) {

                } else {
                    return [
                        //'<a class="edit" href="javascript:void(0)" title="Editar">',
                        //'<i class="glyphicon glyphicon-edit"></i>',
                        //'</a>',
                        //'<a class="delete" href="javascript:void(0)" title="Eliminar">',
                        //'<i class="glyphicon glyphicon-remove"></i>',
                        //'</a>'
                    ].join('');
                }*/
            },
            operateEvents: {
                'click .like': function (e, value, row, index) {
                    alert('You click like action, row: ' + JSON.stringify(row));
                },
                'click .delete': function (e, value, row, index) {
                },
                'click .edit': function(e, value, row, index) {
                }
            },
            init: function(){

            }
        },
        saveServicesRows: function() {
            var allServices = "";
            $('.service-row').each(function(){
                allServices += $(this).attr("rel") + "<>";
            });
            Tech506.showPleaseWait();
            Tech506.ajaxCall(Tech506.UI.urls['service-rows-save'], {
                    serviceId: $("#serviceId").val(),
                    data:   allServices
                },
                function (data) {
                    Tech506.hidePleaseWait();
                    if (data.error) {
                        Tech506.showErrorMessage(data.msg);
                    } else {
                        Tech506.showInfoMessage(data.msg);
                        location.reload();
                    }
                }, function () {
                    Tech506.showErrorMessage("Ha ocurrido un error al guardar la informacin, por favor revise la informacion e intente de nuevo");
                    Tech506.hidePleaseWait();
                }, true);
        },
        saveServiceRowInformation: function() {
            var fullPrice = $("#fullPrice").val() * 1;
            var sellerWin = $("#sellerWin").val() * 1;
            var technicianWin = $("#technicianWin").val() * 1;
            var transportationCost = $("#transportationCost").val() * 1;
            var utility = $("#utility").val() * 1;
            var sumTotal = (sellerWin + technicianWin + transportationCost + utility);
            if( sumTotal == fullPrice) {
                var newRel = Tech506.UI.vars["editing-service-row-values"][0] + "/" +
                    Tech506.UI.vars["editing-service-row-values"][1] + '/' + Tech506.UI.vars["editing-service-row-values"][2];
                newRel += '/' + fullPrice;
                newRel += '/' + sellerWin;
                newRel += '/' + technicianWin;
                newRel += '/' + transportationCost;
                newRel += '/' + utility;
                newRel += '/' + $("#description").val();
                var row = $("#service-row-" + Tech506.UI.vars["editing-service-row"]);
                row.attr('rel', newRel);
                row.find('td').eq(1).text(Tech506.UI.formatMoneyWithDecimals(fullPrice,2));
                row.find('td').eq(2).text(Tech506.UI.formatMoneyWithDecimals(sellerWin,2));
                row.find('td').eq(3).text(Tech506.UI.formatMoneyWithDecimals(technicianWin,2));
                row.find('td').eq(4).text(Tech506.UI.formatMoneyWithDecimals(transportationCost,2));
                row.find('td').eq(5).text(Tech506.UI.formatMoneyWithDecimals(utility,2));
                Tech506.UI.vars["services-total-amount"] -= Tech506.UI.vars["editing-service-row-old-price"];
                Tech506.UI.vars["services-total-amount"] += fullPrice;
                $("#totalAmount").html(Tech506.UI.formatMoneyWithDecimals(Tech506.UI.vars["services-total-amount"],2));

                $('#edit-services-container').modal('hide');
            } else {
                Tech506.showErrorMessage("El precio total no es igual a la suma de las partes [" +
                    sumTotal + "-" + fullPrice + "]");
            }
        },
        saveServiceParts: function() {
            Tech506.UI.vars["parts-error"] = 0;
            Tech506.UI.vars["parts-list"] = "";
            $('.partRow').each(function(i, obj) {
                var row = $(obj);
                var rowId = row.attr('rel');
                var name = $("#part-name-"+rowId).val().trim();
                var costTechnician = $("#part-cost-"+rowId).val().trim() * 1;
                var costReal = $("#part-cost-real-"+rowId).val().trim() * 1;
                var price = $("#part-price-"+rowId).val().trim() * 1;
                var commision = $("#part-commision-"+rowId).val().trim() * 1;
                var utility = $("#part-utility-"+rowId).val().trim() * 1;

                var separator = "<>";
                var rowText = name + separator + costTechnician + separator + costReal + separator + price + separator
                    + commision + separator + utility;
                if(name === ""
                    || !Tech506.UI.isValidPositiveOrZeroNumber(costTechnician)
                    || !Tech506.UI.isValidPositiveOrZeroNumber(costReal)
                    || !Tech506.UI.isValidPositiveOrZeroNumber(price)
                    || !Tech506.UI.isValidPositiveOrZeroNumber(commision)
                    || !Tech506.UI.isValidPositiveOrZeroNumber(utility)) {
                    Tech506.UI.vars["parts-error"] = 1;
                    return;
                }
                if(costReal > costTechnician) {
                    Tech506.UI.vars["parts-error"] = 2;
                    return;
                }
                if((costReal + commision + utility) != price) {
                    Tech506.UI.vars["parts-error"] = 3;
                    return;
                }
                Tech506.UI.vars["parts-list"] += rowText + "><";
            });
            switch(Tech506.UI.vars["parts-error"]) {
                case 0:
                    Tech506.UI.vars["parts-list"] = Tech506.UI.vars["parts-list"].substring(0,
                        Tech506.UI.vars["parts-list"].length-2);
                    //console.debug(Tech506.UI.vars["parts-list"]);
                    Tech506.showPleaseWait();
                    Tech506.ajaxCall(Tech506.UI.urls['service-save-parts'], {
                            serviceId: $("#serviceId").val(),
                            parts: Tech506.UI.vars["parts-list"]
                        },
                        function (data) {
                            Tech506.hidePleaseWait();
                            if (data.error) {
                                Tech506.showErrorMessage(data.msg);
                            } else {
                                Tech506.showInfoMessage(data.msg);
                                //window.location.href = Tech506.UI.urls["homePage"];
                            }
                        }, function () {
                            Tech506.hidePleaseWait();
                        }, true);
                    break;
                case 1:
                    Tech506.showErrorMessage("Hay valores inv�lidos, por favor revise los datos ingresados");
                    break;
                case 2:
                    Tech506.showErrorMessage("El costo real no puedo ser mayor al costo al t�cnico");
                    break;
                case 3:
                    Tech506.showErrorMessage("El precio de venta es distinto a la suma del costo real, comisi�n y utilidad");
                    break;
                default:
                    break;
            }
        },
        addPartLine: function() {
            Tech506.UI.vars["partsTotal"]++;
            $newRow = '<tr class="partRow" id="part-row-' + Tech506.UI.vars["partsTotal"] + '" rel="' + Tech506.UI.vars["partsTotal"] + '">';
            $newRow += '<td><input type="text" class="form-control" id="part-name-' + Tech506.UI.vars["partsTotal"] + '" value=""></td>';
            $newRow += '<td><input type="number" class="form-control" id="part-price-' + Tech506.UI.vars["partsTotal"] + '" value=""></td>';
            $newRow += '<td><input type="number" class="form-control" id="part-cost-real-' + Tech506.UI.vars["partsTotal"] + '" value=""></td>';
            $newRow += '<td><input type="number" class="form-control" id="part-cost-' + Tech506.UI.vars["partsTotal"] + '" value=""></td>';
            $newRow += '<td><input type="number" class="form-control" id="part-commision-' + Tech506.UI.vars["partsTotal"] + '" value=""></td>';
            $newRow += '<td><input type="number" class="form-control" id="part-utility-' + Tech506.UI.vars["partsTotal"] + '" value=""></td>';
            $newRow += '<td><a class="partRemoveIcon" href="#" title="Eliminar" rel="'
                + Tech506.UI.vars["partsTotal"] + '"><i class="glyphicon glyphicon-remove"></i></a></td>';
            $newRow += '</tr>';
            $("#partsTable tr:last").after($newRow);

            $(".partRemoveIcon").unbind().click(function(e){
                e.preventDefault();
                e.stopPropagation();
                var rowNumber = $(this).attr('rel');
                $("#part-row-" + rowNumber).remove();
            });
        },
        finalizeService: function() {
            if(Tech506.showConfirmationQuestion(Tech506.UI.translates['confirm.finalize.service'])){
                Tech506.showPleaseWait();
                Tech506.ajaxCall(Tech506.UI.urls['service-change-status'], {
                        serviceId: $("#serviceId").val(),
                        status: 4
                    },
                    function (data) {
                        Tech506.hidePleaseWait();
                        if (data.error) {
                            Tech506.showErrorMessage(data.msg);
                        } else {
                            $("#status-label").html(Tech506.UI.translates['services-status-4']);
                            $("#status-desc-label").html(Tech506.UI.translates['services-status-desc-4']);
                            $("#finalizeButton").remove();
                            Tech506.Sales.Services.blockEverything();
                            Tech506.showInfoMessage(data.msg);
                        }
                    }, function () {
                        Tech506.hidePleaseWait();
                    }, true);
            }
        },
        cancelService: function() {
          if(Tech506.showConfirmationQuestion(Tech506.UI.translates['confirm.cancel.service'])){
              Tech506.showPleaseWait();
              Tech506.ajaxCall(Tech506.UI.urls['service-change-status'], {
                      serviceId: $("#serviceId").val(),
                      status: 3
                  },
                  function (data) {
                      Tech506.hidePleaseWait();
                      if (data.error) {
                          Tech506.showErrorMessage(data.msg);
                      } else {
                          $("#status-label").html(Tech506.UI.translates['services-status-3']);
                          $("#status-desc-label").html(Tech506.UI.translates['services-status-desc-3']);
                          Tech506.Sales.Services.blockEverything();
                          Tech506.showInfoMessage(data.msg);
                      }
                  }, function () {
                      Tech506.hidePleaseWait();
                  }, true);
          }
        },
        blockEverything: function(){
            $(".removeOnCanceled").remove();
            $(".read-only-on-canceled").attr("readonly", "readonly");
            $(".read-only-on-canceled").attr("disabled", "disabled");
            $(".partRemoveIcon").remove();
        },
        scheduleService: function () {
            var serviceId = $("#serviceId").val();
            var technicianId = $("#technician").val();
            var scheduleDate = $("#scheduleDate").val();
            if(technicianId == 0 || scheduleDate == ""){
                Tech506.showErrorMessage("No puede programar el servicio sin seleccionar el t�cnico y la fecha de visita");
                return;
            }
            Tech506.showPleaseWait();
            Tech506.ajaxCall(Tech506.UI.urls['schedule-service'], {
                    serviceId: serviceId,
                    technicianId: technicianId,
                    scheduleDate: scheduleDate,
                    observations: $("#observations").val(),
                    referencePoint: $("#referencePoint").val(),
                    state: $("#state").val(),
                    address: Tech506.UI.getAddressFromInputs(),
                    addressDetail: $("#addressDetail").val(),
                    scheduleHour: $("#scheduleHour").val(),
                    sellerId:   $("#seller").val(),
                    neighborhood: $("#neighborhood").val()
                },
                function (data) {
                    Tech506.hidePleaseWait();
                    if (data.error) {
                        Tech506.showErrorMessage(data.msg);
                    } else {
                        $("#status-label").html(Tech506.UI.translates['services-status-2']);
                        $("#status-desc-label").html(Tech506.UI.translates['services-status-desc-2']);
                        $("#finalizeButton").removeClass("hide").show();
                        Tech506.showInfoMessage(data.msg);
                    }
                }, function () {
                    Tech506.showErrorMessage("Ha ocurrido un error al guardar la informacin, por favor revise la informacion e intente de nuevo");
                    Tech506.hidePleaseWait();
                }, true);
        }
    }
};