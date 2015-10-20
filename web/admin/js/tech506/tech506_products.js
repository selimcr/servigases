var Tech506 = Tech506 || {};

Tech506.Products = {
    List: {
        yesNoFormat: function(value, row, index) {
            return [
                (value)? 'Si':'No',
            ].join('');
        },
        operateFormatter: function(value, row, index){
            return [
                '<a class="edit" href="javascript:void(0)" title="Editar">',
                '<i class="glyphicon glyphicon-edit"></i>',
                '</a>',
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
                if(Tech506.showConfirmationQuestion(Tech506.UI.translates['confirm-delete'])){
                    Tech506.showPleaseWait();
                    Tech506.ajaxCall(Tech506.UI.urls['delete'], {
                            id:     row.id
                        },
                        function(data){
                            Tech506.hidePleaseWait();
                            if(data.error){
                                Tech506.showErrorMessage(data.msg);
                            } else {
                                $("#catalog-list").bootstrapTable('refresh');
                                Tech506.showInfoMessage(data.msg);
                            }
                        }, function(){
                            Tech506.hidePleaseWait();
                        }, true);
                }
            },
            'click .edit': function(e, value, row, index) {
                $("#panel-catalog").addClass("hidden");
                Tech506.UI.vars["id"] = row.id;
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#description").val(row.description);
                $("#name").val(row.name);
                $("#panel-catalog-title").html(Tech506.UI.translates["edit"]);
                $("#panel-catalog").removeClass("hidden");
            }
        },
        init: function(){
            $("#btn-new").click(function(e){
                $("#name").val("");
                $("#description").val("");
                $("#category").val(0);
                Tech506.UI.vars["id"] = 0;
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#panel-catalog-title").html($(this).attr("title"));
            });

            $("#btn-cancel").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                $("#panel-catalog").addClass("hidden");
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
            });

            $('#form-catalog').bootstrapValidator({
                excluded: ':disabled',
                message: Tech506.UI.translates['invalid.value'],
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                onSuccess: function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    Tech506.showPleaseWait();
                    $("#id").val(Tech506.UI.vars["id"]);
                    /*$.ajax({
                        type: "POST",
                        dataType: "JSON",
                        url: Tech506.UI.urls['save'],
                        data: $("#form-catalog").serialize(), // serializes the form's elements.
                        success: function(data) {
                            Tech506.hidePleaseWait();
                            if(data.error){
                                Tech506.showErrorMessage(data.msg);
                            } else {
                                Tech506.UI.vars["id"] = data.userId;
                                if(Tech506.UI.vars["new-avatar"]) {
                                    Tech506.UI.vars["msg"] = data.msg;
                                    $('#avatar').fileinput('upload');
                                } else {
                                    $("#panel-catalog").addClass("hidden");
                                    $('#form-catalog').data('bootstrapValidator').resetForm(true);
                                    $("#catalog-list").bootstrapTable('refresh');
                                    $('#form-content').modal('hide');
                                    Tech506.showInfoMessage(data.msg);
                                }
                            }
                        }
                    });*/
                    return false;
                },
                fields: {
                    'name': {
                        validators: {
                            notEmpty: {
                                message: Tech506.UI.translates['field.not.empty']
                            }
                        }
                    }
                }
            });
        }
    },
    Categories: {
        init: function(){
            $("#btn-new").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                $("#name").val("");
                $("#description").val("");
                Tech506.UI.vars["id"] = 0;
                $("#panel-catalog-title").html($(this).attr("title"));
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#panel-catalog").removeClass("hidden");
            });

            $("#btn-cancel").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                $("#panel-catalog").addClass("hidden");
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
            });

            $('#form-catalog').bootstrapValidator({
                excluded: ':disabled',
                message: Tech506.UI.translates['invalid.value'],
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                onSuccess: function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    Tech506.showPleaseWait();
                    Tech506.ajaxCall(Tech506.UI.urls['save'], {
                            id:             Tech506.UI.vars["id"],
                            name:           $("#name").val(),
                            description:    $("#description").val()
                        },
                        function(data){
                            Tech506.hidePleaseWait();
                            if(data.error){
                                Tech506.showErrorMessage(data.msg);
                            } else {
                                $("#panel-catalog").addClass("hidden");
                                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                                $("#catalog-list").bootstrapTable('refresh');
                                Tech506.showInfoMessage(data.msg);
                            }
                        }, function(){
                        }, true);
                    return false;
                },
                fields: {
                    'name': {
                        validators: {
                            notEmpty: {
                                message: Tech506.UI.translates['field.not.empty']
                            }
                        }
                    },
                    'description': {
                        validators: {
                            notEmpty: {
                                message: Tech506.UI.translates['field.not.empty']
                            }
                        }
                    }
                }
            });
        }
    },
    Products: {
        operateFormatter: function(value, row, index){
            return [
                '<a class="edit" href="javascript:void(0)" title="Editar">',
                '<i class="glyphicon glyphicon-edit"></i>',
                '</a>',
                '&nbsp;&nbsp;<a class="viewPrice" href="javascript:void(0)" title="Ver Precios">',
                '<i class="glyphicon glyphicon-usd"></i>',
                '</a>'
            ].join('');
        },
        catalogTableParams: function(params) {
            params["category"] = Tech506.UI.vars["category"]
            return params;
        },
        init: function(){
            $("#btn-new").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                $("#name").val("");
                $("#description").val("");
                $("#category").val(1);
                Tech506.UI.vars["id"] = 0;
                $("#panel-catalog-title").html($(this).attr("title"));
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#panel-catalog").removeClass("hidden");
            });

            $("#btn-cancel").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                $("#panel-catalog").addClass("hidden");
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
            });

            $("#categoryFilter").change(function(e){
                Tech506.UI.vars["category"] = $(this).val();
                $("#catalog-list").bootstrapTable('refresh');
            });

            $('#form-catalog').bootstrapValidator({
                excluded: ':disabled',
                message: Tech506.UI.translates['invalid.value'],
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                onSuccess: function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    Tech506.showPleaseWait();
                    Tech506.ajaxCall(Tech506.UI.urls['save'], {
                            id:             Tech506.UI.vars["id"],
                            name:           $("#name").val(),
                            description:    $("#description").val(),
                            category:       $("#category").val(),
                            onlyOnManaging: $("#onlyOnManaging").is(':checked') ? 1 : 0
                        },
                        function(data) {
                            Tech506.hidePleaseWait();
                            if(data.error){
                                Tech506.showErrorMessage(data.msg);
                            } else {
                                $("#panel-catalog").addClass("hidden");
                                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                                $("#catalog-list").bootstrapTable('refresh');
                                Tech506.showInfoMessage(data.msg);
                            }
                        }, function(){
                        }, true);
                    return false;
                },
                fields: {
                    'name': {
                        validators: {
                            notEmpty: {
                                message: Tech506.UI.translates['field.not.empty']
                            }
                        }
                    },
                    'description': {
                        validators: {
                            notEmpty: {
                                message: Tech506.UI.translates['field.not.empty']
                            }
                        }
                    }
                }
            });
        },
        operateEvents: {
            'click .like': function (e, value, row, index) {
                alert('You click like action, row: ' + JSON.stringify(row));
            },
            'click .viewPrice': function (e, value, row, index) {
                var url = Tech506.UI.urls['product-prices'].replace('XXX', row.id);
                window.location.href = url;
            },
            'click .edit': function(e, value, row, index) {
                $("#panel-catalog").addClass("hidden");
                Tech506.UI.vars["id"] = row.id;
                $("#category").val(row.categoryId);
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#description").val(row.description);
                $("#name").val(row.name);
                if(row.onlyOnManaging) {
                    $("#onlyOnManaging").attr("checked", "checked");
                } else {
                    $("#onlyOnManaging").removeAttr("checked");
                }
                $("#panel-catalog-title").html(Tech506.UI.translates["edit"]);
                $("#panel-catalog").removeClass("hidden");
            }
        }
    },
    Prices: {
        catalogTableParams: function(params) {
            params["product"] = $("#product").val();
            return params;
        },
        init: function() {
            $("#product").change(function(){
                $("#panel-catalog").addClass("hidden");
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#catalog-list").bootstrapTable('refresh');
            });
            $(".select2_group").select2({});

            $("#btn-new").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                $("#name").val("");
                $("#description").val("");
                $("#fullPrice").val("");
                $("#sellerWin").val("");
                $("#technicianWin").val("");
                $("#transportationCost").val("");
                $("#utility").val("");
                $("#onlyForAdmin").removeAttr("checked");
                Tech506.UI.vars["id"] = 0;
                $("#panel-catalog-title").html($(this).attr("title"));
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#panel-catalog").removeClass("hidden");
            });

            $("#btn-cancel").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                $("#panel-catalog").addClass("hidden");
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
            });

            $('#form-catalog').bootstrapValidator({
                excluded: ':disabled',
                message: Tech506.UI.translates['invalid.value'],
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                onSuccess: function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    Tech506.showPleaseWait();
                    Tech506.ajaxCall(Tech506.UI.urls['save'], {
                            id:             Tech506.UI.vars["id"],
                            name:           $("#name").val(),
                            description:    $("#description").val(),
                            fullPrice:      $("#fullPrice").val(),
                            sellerWin:      $("#sellerWin").val(),
                            technicianWin:  $("#technicianWin").val(),
                            transportationCost:    $("#transportationCost").val(),
                            utility:        $("#utility").val(),
                            productId:      $("#product").val(),
                            onlyForAdmin:   $("#onlyForAdmin").is(':checked') ? 1 : 0
                        },
                        function(data) {
                            Tech506.hidePleaseWait();
                            if(data.error){
                                Tech506.showErrorMessage(data.msg);
                            } else {
                                $("#panel-catalog").addClass("hidden");
                                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                                $("#catalog-list").bootstrapTable('refresh');
                                Tech506.showInfoMessage(data.msg);
                            }
                        }, function(){
                        }, true);
                    return false;
                },
                fields: {
                    'name': {
                        validators: {
                            notEmpty: {
                                message: Tech506.UI.translates['field.not.empty']
                            }
                        }
                    },
                    'description': {
                        validators: {
                            notEmpty: {
                                message: Tech506.UI.translates['field.not.empty']
                            }
                        }
                    },
                    'fullPrice': {
                        validators: {
                            integer: {
                                message: Tech506.UI.translates['field.not.integer']
                            },
                            notEmpty: {
                                message: Tech506.UI.translates['field.not.empty']
                            }
                        }
                    },
                    'sellerWin': {
                        validators: {
                            integer: {
                                message: Tech506.UI.translates['field.not.integer']
                            },
                            notEmpty: {
                                message: Tech506.UI.translates['field.not.empty']
                            }
                        }
                    },
                    'technicianWin': {
                        validators: {
                            integer: {
                                message: Tech506.UI.translates['field.not.integer']
                            },
                            notEmpty: {
                                message: Tech506.UI.translates['field.not.empty']
                            }
                        }
                    },
                    'transportationCost': {
                        validators: {
                            integer: {
                                message: Tech506.UI.translates['field.not.integer']
                            },
                            notEmpty: {
                                message: Tech506.UI.translates['field.not.empty']
                            }
                        }
                    },
                    'utility': {
                        validators: {
                            integer: {
                                message: Tech506.UI.translates['field.not.integer']
                            },
                            notEmpty: {
                                message: Tech506.UI.translates['field.not.empty']
                            }
                        }
                    }
                }
            });
        },
        operateFormatter: function(value, row, index){
            return Tech506.UI.formatMoney(value);
        },
        operateEvents: {
            'click .like': function (e, value, row, index) {
                alert('You click like action, row: ' + JSON.stringify(row));
            },
            'click .delete': function (e, value, row, index) {
                if(Tech506.showConfirmationQuestion(Tech506.UI.translates['confirm-delete'])){
                    Tech506.showPleaseWait();
                    Tech506.ajaxCall(Tech506.UI.urls['delete'], {
                            id:     row.id
                        },
                        function(data){
                            Tech506.hidePleaseWait();
                            if(data.error){
                                Tech506.showErrorMessage(data.msg);
                            } else {
                                $("#catalog-list").bootstrapTable('refresh');
                                Tech506.showInfoMessage(data.msg);
                            }
                        }, function(){
                            Tech506.hidePleaseWait();
                        }, true);
                }
            },
            'click .edit': function(e, value, row, index) {
                $("#panel-catalog").addClass("hidden");
                Tech506.UI.vars["id"] = row.id;
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#description").val(row.description);
                $("#name").val(row.name);
                $("#fullPrice").val(row.fullPrice);
                $("#sellerWin").val(row.sellerWin);
                $("#technicianWin").val(row.technicianWin);
                $("#transportationCost").val(row.transportationCost);
                $("#utility").val(row.utility);
                if(row.onlyForAdminValue) {
                    $("#onlyForAdmin").attr("checked", "checked");
                } else {
                    $("#onlyForAdmin").removeAttr("checked");
                }
                $("#panel-catalog-title").html(Tech506.UI.translates["edit"]);
                $("#panel-catalog").removeClass("hidden");
            }
        }
    }
};