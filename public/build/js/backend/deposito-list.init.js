/*
 SISDATO - Sistema dado para todos
*/

var perPage = 10;
var editlist = false;

var options = {
    valueNames: [
        "id",
        "codubic",
        "descrip",
        "exhibicion",
        "venta",
        "servicio",
        "accountStatus",
    ],
    page: perPage,
    pagination: true,
    plugins: [
        ListPagination({
            left: 2,
            right: 2,
        }),
    ],
};

var depositosList = new List("depositosList", options).on("updated", function (list) {

    list.matchingItems.length == 0 ?
        (document.getElementsByClassName("noresult")[0].style.display = "block") :
        (document.getElementsByClassName("noresult")[0].style.display = "none");

    var isFirst = list.i == 1;
    var isLast = list.i > list.matchingItems.length - list.page;

    // make the Prev and Nex buttons disabled on first and last pages accordingly
    document.querySelector(".pagination-prev.disabled") ?
        document.querySelector(".pagination-prev.disabled").classList.remove("disabled") : "";
    document.querySelector(".pagination-next.disabled") ?
        document.querySelector(".pagination-next.disabled").classList.remove("disabled") : "";
    if (isFirst) {
        document.querySelector(".pagination-prev").classList.add("disabled");
    }
    if (isLast) {
        document.querySelector(".pagination-next").classList.add("disabled");
    }
    if (list.matchingItems.length <= perPage) {
        document.querySelector(".pagination-wrap").style.display = "none";
    } else {
        document.querySelector(".pagination-wrap").style.display = "flex";
    }

    if (list.matchingItems.length == perPage) {
        document.querySelector(".pagination.listjs-pagination").firstElementChild.children[0].click()
    }

    if (list.matchingItems.length > 0) {
        document.getElementsByClassName("noresult")[0].style.display = "none";
    } else {
        document.getElementsByClassName("noresult")[0].style.display = "block";
    }
});

//Depositos List
const xhttp = new XMLHttpRequest();
xhttp.onload = function () {
    var json_records = JSON.parse(this.responseText);
        Array.from(json_records).forEach(function (element) {
            console.log(element);
            depositosList.add({
            id: `<a href="javascript:void(0);" class="fw-medium link-primary">#TB${element.id}</a>`,
                codubic    : element.codubic,
                descrip    : element.descrip,
                exhibicion : (element.exhibicion== "1")? 1:0,
                venta      : (element.venta     == "1")? 1:0,
                servicio   : (element.servicio  == "1")? 1:0,
                accountStatus : isStatus(element.activo)

            });
            depositosList.sort('id', { order: "desc" });
            refreshCallbacks();
        });

        depositosList.remove("id", `<a href="javascript:void(0);" class="fw-medium link-primary">#TB01</a>`);
}
xhttp.open("GET", "sadepo/json");
xhttp.send();

isCount = new DOMParser().parseFromString(
    depositosList.items.slice(-1)[0]._values.id,
    "text/html"
);

var isValue = isCount.body.firstElementChild.innerHTML;

function isStatus(val) {
    switch (val) {
        case "Active":
            return (
                '<span class="badge badge-soft-success text-uppercase">' +
                val +
                "</span>"
            );
        case "Inactive":
            return (
                '<span class="badge badge-soft-danger text-uppercase">' +
                val +
                "</span>"
            );
    }
}

var idField         = document.getElementById("id-field"),
    codubic         = document.getElementById("codubic-field"),
    descrip         = document.getElementById("descrip-field"),
    exhibicionField = document.getElementById("exhibicion-field"),
    ventaField      = document.getElementById("venta-field"),
    servicioField   = document.getElementById("servicio-field"),
    accountStatusField = document.getElementById("account-status-field"),
    addBtn          = document.getElementById("add-btn"),
    editBtns        = document.getElementsByClassName("edit-item-btn"),
    removeBtns      = document.getElementsByClassName("remove-item-btn");

refreshCallbacks();

var accountStatusVal = new Choices(accountStatusField, {
    searchEnabled: false
});

document.getElementById("showModal").addEventListener("show.bs.modal", function (e) {
    if (e.relatedTarget.classList.contains("edit-item-btn")) {
        document.getElementById("exampleModalLabel").innerHTML = "Modificar Deposito";
        document.getElementById("showModal").querySelector(".modal-footer").style.display = "block";
        document.getElementById("add-btn").innerHTML = "Modificar";
        $('#codubic-field').prop("disabled", true);
    } else if (e.relatedTarget.classList.contains("add-btn")) {
        document.getElementById("exampleModalLabel").innerHTML = "Informaci&oacute;n deposito nuevo";
        document.getElementById("showModal").querySelector(".modal-footer").style.display = "block";
        document.getElementById("add-btn").innerHTML = "+1 Deposito";
        $('#codubic-field').prop("disabled", false);
    } else {
        document.getElementById("exampleModalLabel").innerHTML = "Listado de depositos";
        document.getElementById("showModal").querySelector(".modal-footer").style.display = "none";
    }
});


function refreshCallbacks() {
    // removeBtns
    if (removeBtns){
        Array.from(removeBtns).forEach(function (btn) {
            btn.addEventListener("click", function (e) {
                e.target.closest("tr").children[1].innerText;
                itemId = e.target.closest("tr").children[1].innerText;

                var itemValues = depositosList.get({
                    id: itemId,
                });

                Array.from(itemValues).forEach(function (x) {
                    deleteid = new DOMParser().parseFromString(x._values.id, "text/html");

                    var isElem = deleteid.body.firstElementChild;
                    var isdeleteid = deleteid.body.firstElementChild.innerHTML;

                    if (isdeleteid == itemId) {


                        $('#delete-record').unbind('click').bind('click',function () {
                                itemId = itemId.replace("#TB", "");

                                $.ajax({
                                    type: 'DELETE',
                                    url: 'depositos/'+itemId,
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:{   },
                                    success: function (data) {
                                        depositosList.remove("id", isElem.outerHTML);
                                        document.getElementById("deleteRecord-close").click();
                                        var depositoscounter = Object.keys(depositosList.items).length;

                                        $('.depositoscounter').html(depositoscounter);
                                        Swal.fire({
                                            position: 'center',
                                            icon: 'success',
                                            title: 'Deposito borrado!',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            showCloseButton: true
                                        });
                                    }
                                });

                        });
                    }
                });
            });
        });
    }

    // editBtns
    if (editBtns){
        Array.from(editBtns).forEach(function (btn) {
            btn.addEventListener("click", function (e) {

                e.target.closest("tr").children[1].innerText;
                itemId = e.target.closest("tr").children[1].innerText;
                var itemValues = depositosList.get({
                    id: itemId,
                });

                Array.from(itemValues).forEach(function (x) {
                    isid = new DOMParser().parseFromString(x._values.id, "text/html");
                    var selectedid = isid.body.firstElementChild.innerHTML;

                    if (selectedid == itemId) {
                        editlist = true;
                        idField.value         = selectedid;
                        descrip.value         = x._values.descrip;
                        codubic.value         = x._values.codubic;
                        exhibicionField.value = x._values.exhibicion;
                        ventaField.value      = x._values.venta;
                        servicioField.value   = x._values.servicio;

                        if(x._values.exhibicion)
                            $('#exhibicion-field').prop( "checked", true );
                        else
                            $('#exhibicion-field').prop( "checked", false );

                        if(x._values.venta)
                            $('#venta-field').prop( "checked", true );
                        else
                            $('#venta-field').prop( "checked", false );

                        if(x._values.servicio)
                            $('#servicio-field').prop( "checked", true );
                        else
                            $('#servicio-field').prop( "checked", false );

                        // statusVal
                        if (accountStatusVal) accountStatusVal.destroy();
                        accountStatusVal = new Choices(accountStatusField, {
                            searchEnabled: false
                        });
                        var val = new DOMParser().parseFromString(x._values.accountStatus, "text/html");
                        var statusSelec = val.body.firstElementChild.innerHTML;
                        accountStatusVal.setChoiceByValue(statusSelec);

                    }
                });
            });
        });
    };
};

// Add Deposito
var count = 12;
var forms = document.querySelectorAll('.tablelist-form')

Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {

        event.preventDefault();

        var errorMsg = document.getElementById("alert-error-msg");
        errorMsg.classList.remove("d-none");

        setTimeout(() => errorMsg.classList.add("d-none"), 2000);

        var text;

        if (codubic.value == "") {
            text = "Codigo del deposito nuevo es requerido";
            errorMsg.innerHTML = text;
            return false;
        }else if (descrip.value == "") {
            text = "Nombre deposito nuevo es requerido";
            errorMsg.innerHTML = text;
            return false;
        }else if (exhibicionField.value == "" && ventaField.value == "" && servicioField.value == "") {
            text = "Debe seleccionar la funcion del deposito";
            errorMsg.innerHTML = text;
            return false;
        }
        if (
            codubic.value !== "" &&
            descrip.value !== "" &&
            !editlist
        ) {

            var codubicdata = codubic.value;
            var descripdata = descrip.value;
            var exhibicion  = (exhibicionField.checked == true)? 1: '0';
            var venta       = (ventaField.checked == true)?      1: '0';
            var servicio    = (servicioField.checked == true)?   1: '0';

            //console.log(codubic,descrip,exhibicion,venta,servicio);

            $.ajax({
                type: 'POST',
                url: 'depositos',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{   codubic: codubicdata, descrip: descripdata, venta  : venta, exhibicion  : exhibicion, servicio  : servicio},
                success: function (data) {
                     window.location.href="depositos";

                }
            });


        }
        else if (
            codubic.value  !== ""       &&
            descrip.value !== ""       &&
            accountStatusField.value !== ""    &&
            editlist
        ) {
            var editValues = depositosList.get({
                id: idField.value,
            });

            Array.from(editValues).forEach(function (x) {
                isid = new DOMParser().parseFromString(x._values.id, "text/html");

                var selectedid = isid.body.firstElementChild.innerHTML;
                if (selectedid == itemId) {

                    itemId = itemId.replace("#TB", "");
                    x.values({
                        id: '<a href="javascript:void(0);" class="fw-medium link-primary">' + idField.value + "</a>",
                        codubic      : codubic.value,
                        descrip      : descrip.value,
                        exhibicion   : exhibicionField.value,
                        venta        : ventaField.value,
                        servicio     : servicioField.value,
                        accountStatus: isStatus(accountStatusField.value),
                    });

                    $.ajax({
                        type: 'PUT',
                        url: 'depositos/'+itemId,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{codubic      : codubic.value,
                              descrip      : descrip.value,
                              exhibicion   : exhibicionField.value,
                              venta        : ventaField.value,
                              servicio     : servicioField.value,
                              activo       : accountStatusField.value },
                        success: function (data) {

                        }
                    });

                }
            });

            document.getElementById("alert-error-msg").classList.add("d-none");
            document.getElementById("close-modal").click();
            clearFields();
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Deposito actualizado exitosamente!',
                showConfirmButton: false,
                timer: 2000,
                showCloseButton: true
            });
        }
        return true;
    })
});

// choices status
var statusInput = new Choices(document.getElementById('idStatus'), {
    searchEnabled: false,
});

statusInput.passedElement.element.addEventListener('change', function (event) {
    var statusInputValue = event.detail.value;
    depositosList.filter(function (data) {
        matchData = new DOMParser().parseFromString(
            data.values().accountStatus,
            "text/html"
        );
        var status = matchData.body.firstElementChild.innerHTML;
        var statusFilter = false;

        if (status == "All" || statusInputValue == "All") {
            statusFilter = true;
        } else {
            statusFilter = status == statusInputValue;
        }
        if (statusFilter) {
            return statusFilter;
        }
    });

    depositosList.update();
}, false);

function clearFields() {
    codubic.value            = "";
    descrip.value            = "";
    exhibicionField.value    = "";
    ventaField.value         = "";
    servicioField.value      = "";
    accountStatusField.value = "";
    if (accountStatusVal) accountStatusVal.destroy();
    accountStatusVal = new Choices(accountStatusField);
}

document.getElementById("showModal").addEventListener("hidden.bs.modal", function () {
    clearFields();
});

document.querySelector(".pagination-next").addEventListener("click", function () {
    document.querySelector(".pagination.listjs-pagination") ?
        document.querySelector(".pagination.listjs-pagination").querySelector(".active") ?
            document.querySelector(".pagination.listjs-pagination").querySelector(".active").nextElementSibling.children[0].click() : "" : "";
});

document.querySelector(".pagination-prev").addEventListener("click", function () {
    document.querySelector(".pagination.listjs-pagination") ?
        document.querySelector(".pagination.listjs-pagination").querySelector(".active") ?
            document.querySelector(".pagination.listjs-pagination").querySelector(".active").previousSibling.children[0].click() : "" : "";
});
