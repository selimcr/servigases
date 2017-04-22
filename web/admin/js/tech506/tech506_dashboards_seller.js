var Tech506 = Tech506 || {};

Tech506.Dashboards = {
    Sellers: {
        init: function() {
            $("#filterForm").submit(function(e) {
                e.preventDefault();
                e.stopPropagation();
                $("#catalog-list").bootstrapTable('refresh');
                return false;
            });
        },
        yesNoFormat: function(value, row, index) {
            return [
                (value)? 'Si':'No',
            ].join('');
        },
        formatInfoColumn: function(value, row, index) {
            var html = '<div class="client-info">';
            html += '<p class="name">Asesor: ' + row.seller + '' + '</p>';
            html += "<p># " + row.id + "</p>";
            html += '<p><span>Fecha Ingreso: </span>' + row.creationDate + '' + '</p>';
            html += '<p><span>Estado: </span>' + row.status + '' + '</p>';
            html += '</div>';
            return html;
        },
        formatClientColumn: function(value, row, index) {
            var html = '<div class="client-info">';
            html += '<p class="name">' + row.client + '</p>';
            html += "<p><span>Celular: </span>" + row.clientCellPhone + "</p>";
            html += "<p><span>Email: </span>" + row.clientEmail + "</p>";
            html += "<p><span>Extra: </span>" + row.clientExtraInfo + "</p>";
            html += '</div>';
            return html;
        },
        formatSchedulingColumn: function(value, row, index) {
            var html = '<div class="client-info">';
            html += '<p class="name">Técnico: ' + row.technician + '</p>';
            html += "<p><span>Fecha y Hora: </span>" + row.visitDate + " " + row.visitHour + "</p>";
            html += '</div>';
            return html;
        },
        formatVisitColumn: function(value, row, index) {
            var html = '<div class="client-info">';
            html += "<p><span>Dirección: </span>" + row.location + "</p>";
            html += "<p><span>Detalles: </span>" + row.addressDetail + "</p>";
            html += "<p><span>Punto de Referencia: </span>" + row.referencePoint + "</p>";
            html += '</div>';
            return html;
        },
        operateFormatter: function(value, row, index){
            var url = Tech506.UI.urls['manage-service'] + "/";
            return [
                '<a class="scheduleIcon" href="' + url + row.id + '" title="Ver/Editar">',
                '<i class="glyphicon glyphicon-edit"></i>',
                '</a>'
            ].join('');
        },
        catalogTableParams: function(params) {
            console.debug("CatalogTableParams!!! o.o");
            params["status"] = $("#serviceStatusFilter").val();
            params["technician"] = $("#techniciansFilter").val();
            params["search"] = $("#search").val();
            //params["date"] = Tech506.UI.vars["date"];
            return params;
        },
        operateEvents: {
            'click .like': function (e, value, row, index) {
                alert('You click like action, row: ' + JSON.stringify(row));
            },
            'click .delete': function (e, value, row, index) {
            },
            'click .edit': function(e, value, row, index) {
            }
        }
    }
};