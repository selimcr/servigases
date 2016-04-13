var Tech506 = Tech506 || {};

Tech506.Register = {
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
        console.debug("Register new Service");
        Tech506.UI.vars["services-total-amount"] = 0;
        $(".select2").select2();
        $("#scheduleHour").timepicker();
        $("#findClientBtn").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            Tech506.Register.findClient();
        });
        $("#saveClientBtn").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            Tech506.Register.saveClient();
        });
        $("#cleanClientBtn").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            Tech506.Register.cleanClient();
        });
        $("#registerBtn").click(function(e){
            e.preventDefault();
            e.stopPropagation();
            Tech506.Register.registerCall();
        });
        $("#btnLogCall").click(function(e){
            e.preventDefault();
            e.stopPropagation();
            Tech506.Register.logCall();
        });
        $("#addServiceBtn").click(function(e){
            Tech506.Register.openAddServiceWindow();
        });
        $("#product").change(function(e){
            Tech506.Register.loadServicePrices();
        });
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
                            Tech506.Register.addServiceToTable(rel);
                        });
                    }
                }, function () {
                    Tech506.hidePleaseWait();
                }, true);
        }
    },
    addServiceToTable: function(rel) {
        var rowCount = $('#servicesTable >tbody >tr').length + 1;
        var price = Tech506.UI.vars["prices"][rel];
        Tech506.UI.vars["services-total-amount"] += price.fullPrice;
        $newRow = '<tr class="service-row" id="service-row-' + rowCount + '" rel="0/' + price.productId + '/' + price.id + '/' +
            price.fullPrice + '/' + price.sellerWin + '/' + price.technicianWin + '/' + price.transportationCost
            + '/' + price.utility + '/">';
        $newRow += '<td>' + $("#product option:selected").text() + " [" + price.name + "]" + '</td>';
        $newRow += '<td>' + Tech506.UI.formatMoney(price.fullPrice, 0) + '</td>';
        if (Tech506.UI.vars['is-managin']) {
            $newRow += '<td>' + Tech506.UI.formatMoney(price.sellerWin, 0) + '</td>';
            $newRow += '<td>' + Tech506.UI.formatMoney(price.technicianWin, 0) + '</td>';
            $newRow += '<td>' + Tech506.UI.formatMoney(price.transportationCost, 0) + '</td>';
            $newRow += '<td>' + Tech506.UI.formatMoney(price.utility, 0) + '</td>';
        }
        $newRow += '<td>';
        if (Tech506.UI.vars['is-managin']) {
            $newRow += '<button type="button" class="btn btn-primary btn-xs edit-service-btn removeOnCanceled" '
            $newRow += ' amount="' + price.fullPrice + '" rel="' + rowCount + '">';
            $newRow += ' <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>Editar';
            $newRow += '</button>';
        }
        $newRow += ' <button type="button" class="btn btn-primary btn-xs delete-service-btn removeOnCanceled" '
        $newRow += ' amount="' + price.fullPrice + '" rel="' + rowCount + '">';
        $newRow += ' <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>Eliminar';
        $newRow += '</button>';
        $newRow += '</td>';
        $newRow += '</tr>';
        $("#servicesTable tbody").append($newRow);
        $("#totalAmount").html(Tech506.UI.formatMoney(Tech506.UI.vars["services-total-amount"], 0));
        Tech506.Register.setEventsOfServicesRows();
        $('#add-services-container').modal('hide');
    },
    setEventsOfServicesRows: function() {
        $(".delete-service-btn").unbind().click(function(e) {
            if (Tech506.showConfirmationQuestion(Tech506.UI.translates['confirm-delete-service'])) {
                var value = $("#service-row-" + $(this).attr('rel')).attr('rel');
                var values = value.split("/");
                Tech506.UI.vars["services-total-amount"] -= values[3];
                $("#totalAmount").html(Tech506.UI.formatMoney(Tech506.UI.vars["services-total-amount"], 0));
                $("#service-row-" + $(this).attr('rel')).remove();
            }
        });
        $(".edit-service-btn").unbind().click(function(e) {
            var rel = $(this).attr('rel');
            Tech506.UI.vars["editing-service-row"] = rel;
            var value = $("#service-row-" + rel).attr('rel');
            var values = value.split("/");
            Tech506.UI.vars["editing-service-row-values"] = values;
            Tech506.UI.vars["editing-service-row-old-price"] = values[3];
            $("#fullPrice").val(values[3]);
            $("#sellerWin").val(values[4]);
            $("#technicianWin").val(values[5]);
            $("#transportationCost").val(values[6]);
            $("#utility").val(values[7]);
            $("#description").val(values[8]);
            /*
            0: detail.id
            1: detail.productSaleType.product.id
            2: detail.productSaleType.id
            3: detail.fullPrice
            4: detail.sellerWin
            5: detail.technicianWin
            6: detail.transportationCost
            7: detail.utility
            8: detail.observations
            */
            $('#edit-services-container').modal('show');
        });
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
                $data = $("#service-row-" + $productLine.attr("rel")).attr("rel");
                var values = $data.split("/");
                servicesLinesIds += (servicesLinesIds ==+ ""? "":",") + values[2];
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
                    Tech506.Register.initProductLinesEvents();
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
            Tech506.Register.calculateServicesAmount();
        }).hover(function() {
            Tech506.UI.vars['rb-checked'] = $(this).is(':checked');
        }).click(function() {
            Tech506.UI.vars['rb-checked'] = !Tech506.UI.vars['rb-checked'];
            $(this).attr("checked", Tech506.UI.vars['rb-checked']);
            Tech506.Register.calculateServicesAmount();
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
        if(phone !== "" && !Tech506.Register.validatePhone()){return;}
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
        if(!Tech506.Register.validatePhone()){return;}
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
        if((phoneValue.length < 7)) {
            Tech506.showErrorMessage("El número de teléfono debe tener al menos 7 números");
            return false;
        }
        return true;
    }
};