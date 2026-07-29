/*
 SISDATO - Sistema dado para todos
*/

var perPage = 10;
var editlist = false;

var options = {
    valueNames: [
        "id",
        "codvend",
        "sellerName",
        "email",
        "phone",
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

var sellersList = new List("sellersList", options).on("updated", function (list) {

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

//Sellers List
const xhttp = new XMLHttpRequest();
xhttp.onload = function () {
    var json_records = JSON.parse(this.responseText);
        Array.from(json_records).forEach(function (element) {

            sellersList.add({
            id: `<a href="javascript:void(0);" class="fw-medium link-primary">#TB${element.id}</a>`,
            codvend       : element.codvend,
            sellerName    : element.sellerName,
            email         : element.email,
            phone         : element.phone,
            accountStatus : isStatus(element.accountStatus)

            });
            sellersList.sort('id', { order: "desc" });
            refreshCallbacks();
        });

        sellersList.remove("id", `<a href="javascript:void(0);" class="fw-medium link-primary">#TB01</a>`);
}
xhttp.open("GET", "savend/json");
xhttp.send();

isCount = new DOMParser().parseFromString(
    sellersList.items.slice(-1)[0]._values.id,
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
    sellerNameField = document.getElementById("seller-name-field"),
    sellerCodField  = document.getElementById("seller-cod-field"),
    emailField      = document.getElementById("email-field"),
    phoneField      = document.getElementById("phone-field"),
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
        document.getElementById("exampleModalLabel").innerHTML = "Modificar Vendedor";
        document.getElementById("showModal").querySelector(".modal-footer").style.display = "block";
        document.getElementById("add-btn").innerHTML = "Modificar";
        $('#seller-cod-field').prop("disabled", true);
    } else if (e.relatedTarget.classList.contains("add-btn")) {
        document.getElementById("exampleModalLabel").innerHTML = "Informaci&oacute;n vendedor nuevo";
        document.getElementById("showModal").querySelector(".modal-footer").style.display = "block";
        document.getElementById("add-btn").innerHTML = "+1 Vendedor";
        $('#seller-cod-field').prop("disabled", false);
    } else {
        document.getElementById("exampleModalLabel").innerHTML = "List Seller";
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

                var itemValues = sellersList.get({
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
                                    url: 'vendedores/'+itemId,
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:{   },
                                    success: function (data) {
                                        sellersList.remove("id", isElem.outerHTML);
                                        document.getElementById("deleteRecord-close").click();
                                        var vendedorescounter = Object.keys(sellersList.items).length;

                                        $('.vendedorescounter').html(vendedorescounter);
                                        Swal.fire({
                                            position: 'center',
                                            icon: 'success',
                                            title: 'User Deleted successfully!',
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
                var itemValues = sellersList.get({
                    id: itemId,
                });

                Array.from(itemValues).forEach(function (x) {
                    isid = new DOMParser().parseFromString(x._values.id, "text/html");
                    var selectedid = isid.body.firstElementChild.innerHTML;

                    if (selectedid == itemId) {
                        editlist = true;
                        idField.value         = selectedid;
                        sellerNameField.value = x._values.sellerName;
                        sellerCodField.value  = x._values.codvend;
                        emailField.value      = x._values.email;
                        phoneField.value      = x._values.phone;
                        // statusVal
                        if (accountStatusVal) accountStatusVal.destroy();
                        accountStatusVal = new Choices(accountStatusField, {
                            searchEnabled: false
                        });
                        var val = new DOMParser().parseFromString(x._values.accountStatus, "text/html");
                        var statusSelec = val.body.firstElementChild.innerHTML;
                        accountStatusVal.setChoiceByValue(statusSelec);

                        /*flatpickr("#date-field", {
                            dateFormat: "d M, Y",
                            defaultDate: x._values.createDate,
                        });*/
                    }
                });
            });
        });
    };
};

// Add Seller
var count = 12;
var forms = document.querySelectorAll('.tablelist-form')

Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {

        event.preventDefault();

        var errorMsg = document.getElementById("alert-error-msg");
        errorMsg.classList.remove("d-none");

        setTimeout(() => errorMsg.classList.add("d-none"), 2000);

        var text;

        var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

        if (sellerCodField.value == "") {
            text = "Codigo del vendedor nuevo es requerido";
            errorMsg.innerHTML = text;
            return false;
        }else if (sellerNameField.value == "") {
            text = "Nombre vendedor nuevo es requerido";
            errorMsg.innerHTML = text;
            return false;
        }else if (!emailField.value.match(validRegex)) {
            text = "Formato de email incorrecto";
            errorMsg.innerHTML = text;
            return false;
        }else if (phoneField.value == "") {
            text = "Numero de telefono requerido";
            errorMsg.innerHTML = text;
            return false;
        }


        if (
            sellerCodField.value !== "" &&
            sellerNameField.value !== "" &&
            emailField.value.match(validRegex) &&
            phoneField.value !== "" &&
            !editlist
        ) {

            var codvend = sellerCodField.value;
            var descrip = sellerNameField.value;
            var email   = emailField.value;
            var telef   = phoneField.value;

            $.ajax({
                type: 'POST',
                url: 'vendedores',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{   codvend: codvend, descrip: descrip, email  : email, telef  : telef},
                success: function (data) {

                    sellersList.add({
                        id        : '<a href="javascript:void(0);" class="fw-medium link-primary">#TB' + count + "</a>",
                        codvend   : sellerCodField.value,
                        sellerName: sellerNameField.value,
                        email     : emailField.value,
                        phone     : phoneField.value,
                        accountStatus : isStatus('Active')

                    });
                    var vendedorescounter = Object.keys(sellersList.items).length;
                    $('.vendedorescounter').html(vendedorescounter);

                    sellersList.sort('id', { order: "desc" });

                    document.getElementById("alert-error-msg").classList.add("d-none");
                    document.getElementById("close-modal").click();

                    clearFields();
                    refreshCallbacks();
                    count++;

                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Seller details added successfully!',
                        showConfirmButton: false,
                        timer: 2000,
                        showCloseButton: true
                    });
                }
            });


        }
        else if (
            sellerCodField.value  !== ""       &&
            sellerNameField.value !== ""       &&
            emailField.value.match(validRegex) &&
            phoneField.value !== ""            &&
            accountStatusField.value !== ""    &&
            editlist
        ) {
            var editValues = sellersList.get({
                id: idField.value,
            });

            Array.from(editValues).forEach(function (x) {
                isid = new DOMParser().parseFromString(x._values.id, "text/html");

                var selectedid = isid.body.firstElementChild.innerHTML;
                if (selectedid == itemId) {

                    itemId = itemId.replace("#TB", "");
                    x.values({
                        id: '<a href="javascript:void(0);" class="fw-medium link-primary">' + idField.value + "</a>",
                        codvend      : sellerCodField.value,
                        sellerName   : sellerNameField.value,
                        email        : emailField.value,
                        phone        : phoneField.value,
                        accountStatus: isStatus(accountStatusField.value),
                    });

                    $.ajax({
                        type: 'PUT',
                        url: 'vendedores/'+itemId,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{codvend      : sellerCodField.value,
                              sellerName   : sellerNameField.value,
                              email        : emailField.value,
                              phone        : phoneField.value,
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
                title: 'Vendedor actualizado exitosamente!',
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
    sellersList.filter(function (data) {
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

    sellersList.update();
}, false);

function clearFields() {
    sellerCodField.value     = "";
    sellerNameField.value    = "";
    emailField.value         = "";
    phoneField.value         = "";
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
