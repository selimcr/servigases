var Tech506 = Tech506 || {};

Tech506.Sales = {
    Calls: {
        List: {
            yesNoFormat: function(value, row, index) {
                return [
                    (value)? 'Si':'No',
                ].join('');
            },
            operateFormatter: function(value, row, index){
                return [
                    //'<a class="edit" href="javascript:void(0)" title="Editar">',
                    //'<i class="glyphicon glyphicon-edit"></i>',
                    //'</a>',
                    //'<a class="delete" href="javascript:void(0)" title="Eliminar">',
                    //'<i class="glyphicon glyphicon-remove"></i>',
                    //'</a>'
                ].join('');
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
        initNew: function () {
            console.debug("Init New Calls");
            Tech506.UI.vars["services-total-amount"] = 0;
            $(".select2").select2();
            $("#findClientBtn").click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                Tech506.Sales.findClient();
            });
            $("#saveClientBtn").click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                Tech506.Sales.saveClient();
            });
            $("#cleanClientBtn").click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                Tech506.Sales.cleanClient();
            });
            $("#registerBtn").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                Tech506.Sales.registerCall();
            });
            $("#btnLogCall").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                Tech506.Sales.logCall();
            });
            $("#addServiceBtn").click(function(e){
                Tech506.Sales.openAddServiceWindow();
            });
            $("#product").change(function(e){
                Tech506.Sales.loadServicePrices();
            });
            //Tech506.Sales.loadServices();
        }
    },
    openAddServiceWindow: function() {
        $("#product").val(0);
        $("#product").change();
        $('#add-services-container').modal('show');
    },
    loadServicePrices: function() {
        $("#pricesTable tr").remove();
        var productId = $("#product").val();
        if ( productId > 0 ) {
            Tech506.showPleaseWait();
            Tech506.ajaxCall(Tech506.UI.urls['load-prices'], {
                    productId: productId
                },
                function (data) {
                    Tech506.hidePleaseWait();
                    if (data.error) {
                        Tech506.showErrorMessage(data.msg);
                    } else {
                        Tech506.UI.vars["prices"] = data.prices;
                        for(var i=0; i < data.prices.length; i++) {
                            $newRow = '<tr>';
                            $newRow += '<td>' + data.prices[i].name + '</td>';
                            $newRow += '<td>' + data.prices[i].description + '</td>';
                            $newRow += '<td>' + Tech506.UI.formatMoney(data.prices[i].fullPrice, 0) + '</td>';
                            $newRow += '<td><button type="button" class="btn btn-primary btn-xs add-price-btn" rel="' + i + '">';
                            $newRow += ' <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Agregar';
                            $newRow += '</button></td>';
                            $newRow += '</tr>';
                            $("#pricesTable tbody").append($newRow);
                        }

                        $(".add-price-btn").unbind().click(function(e) {
                            var rel = $(this).attr("rel");
                            Tech506.Sales.addServiceToTable(rel);
                        });
                    }
                }, function () {
                    Tech506.hidePleaseWait();
                }, true);
        }
    },
    addServiceToTable: function(rel) {
        var price = Tech506.UI.vars["prices"][rel];
        Tech506.UI.vars["services-total-amount"] += price.fullPrice;
        $newRow = '<tr id="service-row-' + price.id + '">';
        $newRow += '<td>' + $("#product option:selected").text() + " [" + price.name + "]" + '</td>';
        $newRow += '<td>' + Tech506.UI.formatMoney(price.fullPrice, 0) + '</td>';
        $newRow += '<td><button type="button" class="btn btn-primary btn-xs delete-service-btn" '
        $newRow += ' amount="' + price.fullPrice + '" rel="' + price.id + '">';
        $newRow += ' <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>Eliminar';
        $newRow += '</button></td>';
        $newRow += '</tr>';
        $("#servicesTable tbody").append($newRow);
        $("#totalAmount").html(Tech506.UI.formatMoney(Tech506.UI.vars["services-total-amount"], 0));
        $(".delete-service-btn").unbind().click(function(e) {
            if (Tech506.showConfirmationQuestion(Tech506.UI.translates['confirm-delete-service'])) {
                $("#service-row-" + $(this).attr('rel')).remove();
                Tech506.UI.vars["services-total-amount"] -= $(this).attr('amount');
                $("#totalAmount").html(Tech506.UI.formatMoney(Tech506.UI.vars["services-total-amount"], 0));
            }
        });
        $('#add-services-container').modal('hide');
    },
    registerCall: function() {
        //Revisar si el Cliente ya se buscó
        var clientId = $("#clientId").val();
        if(clientId != 0 && Tech506.UI.vars['clientPhone'] != $("#phone").val()) {
            //El cliente no se ha cargado o no existe
            $("#clientId").val(0);
            clientId = 0;
        }

        var phoneValue = $("#phone").val().trim();
        var cellPhoneValue = $("#cellPhone").val().trim();
        var emailValue = $("#email").val().trim();
        var observationsValue = $("#observations").val().trim();
        var referencePointValue = $("#referencePoint").val().trim();
        var state = $("#state option:selected").text();

        if(clientId == 0 && phoneValue === "") {
            Tech506.showInformationMessage(Tech506.UI.translates['call-confirm-error-no-client']);
        } else {
            /*if(observationsValue === "") {
                Tech506.showInformationMessage(Tech506.UI.translates['call-confirm-error-no-observations']);
                return;
            }*/
            $("#phoneLabel").html(phoneValue == ""? "-":phoneValue);
            $("#cellPhoneLabel").html(cellPhoneValue == ""? "-":cellPhoneValue);
            $("#emailLabel").html(emailValue == ""? "-":emailValue);
            var fullNameValue = $("#fullName").val().trim();
            $("#fullNameLabel").html(fullNameValue == ""? "-":fullNameValue);
            $("#observationsText").html(observationsValue == ""? "-":observationsValue);
            $("#referencePointText").html(referencePointValue == ""? "-":referencePointValue);
            $("#stateLabel").html(state == ""? "-":state);
            $("#addressText").html(Tech506.UI.getAddressFromInputs);
            var neighborhoodValue = $("#neighborhood").val().trim();
            var addressDetailValue = $("#addressDetail").val().trim();
            var visitDate = $("#scheduleDate").val().trim();
            var visitHour = $("#scheduleHour").val().trim();
            $("#neighborhoodLabel").html(neighborhoodValue == ""? "-":neighborhoodValue);
            $("#addressDetailText").html(addressDetailValue == ""? "-":addressDetailValue);
            $("#scheduleDateText").html(visitDate == ""? "-":visitDate);
            $("#scheduleHourText").html(visitHour == ""? "-":visitHour);

            //Obtener servicios seleccionados
            var totalAmount = 0;
            var servicesHtml = "";
            var servicesLinesIds = "";
            $('.delete-service-btn').each(function(i, obj) {
                var $productLine = $(obj);
                var $tds = $("#service-row-" + $productLine.attr("rel")).find("td:nth-child(1)");
                var $productName = "";
                $.each($tds, function() {
                    $productName = $(this).text();
                });
                servicesHtml += '<p>' + $productName + ': <b>' + Tech506.UI.formatMoney($productLine.attr("amount") * 1, 2)
                    + '</b></p>';
                servicesLinesIds += (servicesLinesIds ==+ ""? "":",") + $productLine.attr("rel");
                totalAmount += ($productLine.attr("amount") * 1);
            });
            servicesHtml += "<hr>";
            servicesHtml += '<p class="text-right">Total: ' + Tech506.UI.formatMoney(Tech506.UI.vars["services-total-amount"]) + '</p>';
            Tech506.UI.vars["services-ids"] = servicesLinesIds;
            if(servicesLinesIds == "") {
                Tech506.showErrorMessage("No ha seleccionado ningún servicio");
            } else {
                $("#resume-container-services").html(servicesHtml);
                $('#call-resume-container').modal('show');
            }
        }
    },
    loadServices: function() {
        Tech506.ajaxCall(Tech506.UI.urls['load-services'], {
            },
            function (data) {
                $("#services-loader").hide();
                if (data.error) {
                    Tech506.showErrorMessage(data.msg);
                } else {
                    var html = '';
                    var i = 0;
                    var j = 0;
                    for(i=0; i < data.products.length; i++) {
                        var p = data.products[i];
                        html += '<table class="table table-stripped">';
                        html += '<thead>';
                        html += '    <tr>';
                        html += '    <th colspan="3"><span id="productName-' + p.id + '">' + p.name + ':</span> '
                            + p.description + '</th>';
                        html += '    </tr>';
                        html += '</thead>';
                        html += '<tbody>';
                        //console.debug(JSON.stringify(p.types));
                        for(j=0; j < p.types.length; j++) {
                            //console.debug(JSON.stringify(p.types[j]));
                            var t = p.types[j];
                            html += '<tr>';
                            html += '<td><span>' + t.name + '</span> ';
                            html += '<i class="fa fa-info-circle info-help-icon" title="' + t.description + '"></i> ';
                            html += '</td> ';
                            html += '<td>' + Tech506.UI.formatMoney(t.fullPrice) + '</td>';
                            /*html += '<td><input type="checkbox" class="product-line" rel="'
                                + t.id + '" amount="' + t.fullPrice + '" typeName="' + t.name + '"></td>';*/
                            html += "<td>";
                            html += '<input type="radio" class="product-line" name="productType-' + p.id + '" value="'
                                + t.id + '" rel="' + p.id + '" amount="' + t.fullPrice + '" typeName="' + t.name + '">';
                            html += "</td>"
                            html += '</tr>';
                        }
                        html += '    </tbody>';
                        html += '</table>';
                    }
                    html += '<p><h3>Total: </h3><span id="total-amount-label">$0</span></p>';
                    $("#services-container").html(html);
                    Tech506.Sales.initProductLinesEvents();
                }
            }, function () {
                $("#services-loader").hide();
            }, true);
    },
    calculateServicesAmount: function(){
        var totalAmount = 0;
        $('.product-line').each(function(i, obj) {
            if($(obj).is(':checked')) {
                totalAmount += $(obj).attr('amount') * 1;
            }
        });
        $("#total-amount-label").html(Tech506.UI.formatMoney(totalAmount));
    },
    initProductLinesEvents: function() {
        $(".product-line").unbind().change(function(e) {
            //console.debug("Click on Checkbox: " + $(this).attr('amount'));
            Tech506.Sales.calculateServicesAmount();
        }).hover(function() {
            Tech506.UI.vars['rb-checked'] = $(this).is(':checked');
        }).click(function() {
            Tech506.UI.vars['rb-checked'] = !Tech506.UI.vars['rb-checked'];
            $(this).attr("checked", Tech506.UI.vars['rb-checked']);
            Tech506.Sales.calculateServicesAmount();
        });
    },
    cleanClient: function() {
        $("#clientId").val(0);
        $("#phone").val("");
        $("#email").val("");
        $("#cellPhone").val("");
        $("#fullName").val("");
    },
    findClient: function () {
        var phone = $("#phone").val().trim();
        var cellPhone = $("#cellPhone").val().trim();
        var email = $("#email").val().trim();
        if(phone !== "" && !Tech506.Sales.validatePhone()){return;}
        if(phone !== "" || cellPhone != "" || email != "") {
            Tech506.showPleaseWait();
            Tech506.ajaxCall(Tech506.UI.urls['find-client'], {
                    phone: phone,
                    cellPhone: cellPhone,
                    email: email
                },
                function (data) {
                    Tech506.hidePleaseWait();
                    if (data.error) {
                        Tech506.showErrorMessage(data.msg);
                    } else {
                        if(data.found) {
                            Tech506.UI.vars['clientPhone'] = data.phone;
                            $("#clientId").val(data.id);
                            $("#phone").val(data.phone);
                            $("#email").val(data.email);
                            $("#cellPhone").val(data.cellPhone);
                            $("#fullName").val(data.fullName);
                        } else {
                            Tech506.showInformationMessage(data.msg);
                        }
                    }
                }, function () {
                    Tech506.hidePleaseWait();
                }, true);
        } else {
            Tech506.UI.vars['clientPhone'] = "";
            $("#clientId").val(0);
            Tech506.showInfoMessage(Tech506.UI.translates['find-error-no-filters']);
        }
    },
    saveClient: function () {
        Tech506.showPleaseWait();
        Tech506.ajaxCall(Tech506.UI.urls['save-client'], {
                id: $("#clientId").val(),
                phone: $("#phone").val(),
                cellPhone: $("#cellPhone").val(),
                email: $("#email").val(),
                fullName: $("#fullName").val(),
                address: ""
            },
            function (data) {
                Tech506.hidePleaseWait();
                if (data.error) {
                    Tech506.showErrorMessage(data.msg);
                } else {
                    $("#clientId").val(data.id);
                    Tech506.showInfoMessage(data.msg);
                }
            }, function () {
                Tech506.hidePleaseWait();
            }, true);
    },
    logCall: function () {
        if(!Tech506.Sales.validatePhone()){return;}
        Tech506.showPleaseWait();
        Tech506.ajaxCall(Tech506.UI.urls['log-call'], {
                id: $("#clientId").val(),
                phone: $("#phone").val(),
                identification: $("#identification").val(),
                email: $("#email").val(),
                fullName: $("#fullName").val(),
                neighborhood: $("#neighborhood").val(),
                address: $("#address").val(),
                observations: $("#observations").val(),
                serviceAddress: Tech506.UI.getAddressFromInputs(),
                state:  $("#state").val(),
                referencePoint: $("#referencePoint").val(),
                servicesIds: Tech506.UI.vars["services-ids"],
                addressDetail: $("#addressDetail").val(),
                scheduleDate: $("#scheduleDate").val(),
                scheduleHour: $("#scheduleHour").val(),
                sellerId: $("#seller").val()
            },
            function (data) {
                Tech506.hidePleaseWait();
                if (data.error) {
                    Tech506.showErrorMessage(data.msg);
                } else {
                    Tech506.showInfoMessage(data.msg);
                    window.location.href = Tech506.UI.urls["homePage"];
                }
            }, function () {
                Tech506.hidePleaseWait();
                Tech506.showErrorMessage("Ha ocurrido un error al guardar la informacin, por favor revise la informacion e intente de nuevo");
            }, true);
    },
    validatePhone: function() {
        var phoneValue = $("#phone").val().trim();
        if((phoneValue.length != 8)) {
            Tech506.showErrorMessage("El número de teléfono debe tener 8 números");
            return false;
        }
        return true;
    }
};