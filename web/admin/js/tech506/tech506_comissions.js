var Tech506 = Tech506 || {};

Tech506.Comissions = {
    Apply: {
        init: function () {
            $(".select2").select2();

            $("#employee-type").change(function(e) {
                $("#users-panel").html("");
                var role = $(this).val();
                var userSelect = $("#user");
                userSelect.empty();
                userSelect.append('<option value="0">' + 'Todos' + '</option>');
                Tech506.UI.vars['users'].forEach(function(user) {
                    if ( role == 0 || role == user.role) {
                        userSelect.append('<option value="' + user.id + '">' + user.name + '</option>');
                    }
                });
                $("#user").select2("destroy");
                $("#user").select2();
            });

            $("#user").change(function(e) {
                $("#users-panel").html("");
            });

            $("#getCommissionsForm").submit(function(e) {
                e.preventDefault();
                Tech506.Comissions.Apply.getPendingCommissions();
                return false;
            });

            $("#btn-new").click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                $("#name").val("");
                Tech506.UI.vars["id"] = 0;
                $("#panel-catalog-title").html($(this).attr("title"));
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#panel-catalog").removeClass("hidden");
            });
        },
        getPendingCommissions: function () {
            $("#users-panel").html("");
            var $employeeType = $("#employee-type").val();
            var $userId = $("#user").val();
            Tech506.showPleaseWait();
            Tech506.ajaxCall($("#getCommissionsForm").attr("action"), {
                    employeeType: $employeeType,
                    id: $userId,
                },
                function (data) {
                    Tech506.hidePleaseWait();
                    if (data.error) {
                        Tech506.showErrorMessage(data.msg);
                    } else {
                        var technicianTable = "";
                        var saleTable = "";
                        var baseHtml = $("#user-commissions-panel").html();
                        for(var i=0; i < data.list.length; i++) {
                            var user = data.list[i];
                            var userHtml = baseHtml.replace('${userName}', user.user.name)
                                .replace('${userEmail}', user.user.email)
                                .replace('${userPhone}', user.user.phone)
                                .replaceAll('USERID', user.user.id)
                            ;
                            userHtml = '<div id="user-commissions-panel-' + user.user.id + '" class="col-lg-12 col-md-12 col-sm-12">' + userHtml;
                            var contentHtml = "";
                            if(user.totalForSales > 0) {
                                contentHtml += '';
                                contentHtml += '<p>Comisiones por Ventas</p>';
                                contentHtml += '<table class="table table-striped">';
                                contentHtml += '    <thead>';
                                contentHtml += '    <tr class="headings">';
                                contentHtml += '    <th class="col-lg-1 column-title">Servicio #</th>';
                                contentHtml += '<th class="col-lg-6 column-title">Descripci&oacute;n</th>';
                                contentHtml += '    <th class="col-lg-2 column-title">Fecha de Venta</th>';
                                contentHtml += '<th class="col-lg-3 column-title text-right">Monto</th>';
                                contentHtml += '    </tr>';
                                contentHtml += '    </thead>';
                                contentHtml += '    <tbody>';
                                var totalSale = 0;
                                for(var j=0; j < user.services.length; j++) {
                                    var service = user.services[j];
                                    for(var h=0; h < service.details.length; h++) {
                                        var detail = service.details[h];
                                        totalSale += detail.seller * 1;
                                        contentHtml += '<tr>';
                                        contentHtml += '<td>' + service.id + '</td>';
                                        contentHtml += '<td>' + detail.name + '</td>';
                                        contentHtml += '<td>' + detail.saleDate + '</td>';
                                        contentHtml += '<td class="text-right">' + Tech506.UI.formatMoneyWithDecimals(detail.seller *1, 2) + '</td>';
                                        contentHtml += '</tr>';
                                    }
                                }
                                contentHtml += '</tbody>';
                                contentHtml += '<tfoot>';
                                contentHtml += '    <tr>';
                                contentHtml += '      <th></th>';
                                contentHtml += '      <th>Total</th>';
                                contentHtml += '      <th></th>';
                                contentHtml += '      <th class="text-right">$' + Tech506.UI.formatMoneyWithDecimals(totalSale, 2) + '</th>';
                                contentHtml += '    </tr>';
                                contentHtml += '</tfoot>';
                                contentHtml += '</table>';
                            }

                            if(user.totalForTechnician > 0) {
                                contentHtml += '';
                                contentHtml += '<p>Comisiones por Servicios</p>';
                                contentHtml += '<table class="table table-striped">';
                                contentHtml += '    <thead>';
                                contentHtml += '    <tr class="headings">';
                                contentHtml += '    <th class="col-lg-1">Servicio #</th>';
                                contentHtml += '    <th class="col-lg-3">Descripcion</th>';
                                contentHtml += '    <th class="col-lg-2">Fecha de Servicio</th>';
                                contentHtml += '    <th class="col-lg-2 text-right">Monto T&eacute;cnico</th>';
                                contentHtml += '    <th class="col-lg-2 text-right">Monto Transporte</th>';
                                contentHtml += '    <th class="col-lg-2 text-right">Comisi&oacute;n Total</th>';
                                contentHtml += '    </tr>';
                                contentHtml += '    </thead>';
                                contentHtml += '    <tbody>';
                                var totalTechnician = 0;
                                var totalTransportation = 0;
                                for(var j=0; j < user.services.length; j++) {
                                    var service = user.services[j];
                                    for(var h=0; h < service.details.length; h++) {
                                        var detail = service.details[h];
                                        totalTechnician += detail.technician * 1;
                                        totalTransportation += detail.transportation * 1;
                                        contentHtml += '<tr>';
                                        contentHtml += '<td>' + service.id + '</td>';
                                        contentHtml += '<td>' + detail.name + '</td>';
                                        contentHtml += '<td>' + detail.serviceDate + '</td>';
                                        contentHtml += '<td class="text-right">' + Tech506.UI.formatMoneyWithDecimals(detail.technician *1, 2) + '</td>';
                                        contentHtml += '<td class="text-right">' + Tech506.UI.formatMoneyWithDecimals(detail.transportation *1, 2) + '</td>';
                                        contentHtml += '<td class="text-right">' +
                                            Tech506.UI.formatMoneyWithDecimals((detail.technician * 1) + (detail.transportation *1), 2) + '</td>';
                                        contentHtml += '</tr>';
                                    }
                                }
                                contentHtml += '</tbody>';
                                contentHtml += '<tfoot>';
                                contentHtml += '    <tr>';
                                contentHtml += '      <th></th>';
                                contentHtml += '      <th>Total</th>';
                                contentHtml += '      <th></th>';
                                contentHtml += '      <th class="text-right">$' + Tech506.UI.formatMoneyWithDecimals(totalTechnician, 2) + '</th>';
                                contentHtml += '      <th class="text-right">$' + Tech506.UI.formatMoneyWithDecimals(totalTransportation, 2) + '</th>';
                                contentHtml += '      <th class="text-right">$' + Tech506.UI.formatMoneyWithDecimals(totalTechnician + totalTransportation, 2) + '</th>';
                                contentHtml += '    </tr>';
                                contentHtml += '</tfoot>';
                                contentHtml += '</table>';
                            }
                            userHtml = userHtml.replace('${contentHtml}', contentHtml);
                            userHtml += "</div>";
                            $("#users-panel").append(userHtml);
                            Tech506.Comissions.Apply.setBtnUserApplyEvent();
                        }
                        //$('#form-password').data('bootstrapValidator').resetForm(true);
                        //Tech506.showInfoMessage(data.msg);
                    }
                }, function () {
                    Tech506.hidePleaseWait();
                }, true);
        },
        setBtnUserApplyEvent: function() {
            $(".btnApplyForUser").unbind();
            $(".btnApplyForUser").click(function(e) {
                Tech506.UI.vars["user-id"] = $(this).attr('rel');
                bootbox.dialog({
                    message: Tech506.UI.translates['confirm.user.commission.application'],
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
                                Tech506.showPleaseWait();
                                Tech506.ajaxCall($("#getCommissionsForm").attr("action"), {
                                        employeeType: 0,
                                        id: Tech506.UI.vars["user-id"],
                                    },
                                    function (data) {
                                        Tech506.hidePleaseWait();
                                        if (data.error) {
                                            Tech506.showErrorMessage(data.msg);
                                        } else {
                                            $("#user-commissions-panel-"+Tech506.UI.vars["user-id"]).remove();
                                            Tech506.showInformationMessage("Comisiones aplicadas");
                                        }
                                    });

                                /*bootbox.prompt(Tech506.UI.translates['enter-change-status-msg'], function(result) {
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
                                });*/
                            }
                        }
                    }
                });

                /*if(Tech506.showConfirmationQuestion("Esta seguro???")) {
                    $("#user-commissions-panel-"+rel).remove();
                    Tech506.showInformationMessage("Comisiones aplicadas");
                }*/
            });
        }
    }
};