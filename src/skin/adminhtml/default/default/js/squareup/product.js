document.addEventListener("DOMContentLoaded", function () {
    var buttons = document.getElementsByClassName("update-quantity");
    var removeButtons = document.getElementsByClassName("remove");
    var modal = document.querySelector(".modal");
    var trigger = document.querySelector(".add-location");
    var closeButton = document.querySelector(".close-button");
    var saveButton = document.querySelector(".save-location-button");
    var qtyInput = document.querySelector(".new-location-qty");
    var qtyInputAlert = document.querySelector(".qty-alert-message");

    for (var i = 0; i < buttons.length; i++) {
        buttons[i].addEventListener("click", callAjax);
    }

    for (var j = 0; j < removeButtons.length; j++) {
        removeButtons[j].addEventListener("click", callAjaxRemove);
    }

    trigger.addEventListener("click", function () {
        modal.classList.toggle("show-modal");
    });

    closeButton.addEventListener("click", function () {
        modal.classList.toggle("show-modal");
    });

    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.classList.toggle("show-modal");
        }
    });

    if (saveButton) {
        saveButton.addEventListener("click", function () {
            if (qtyInput.value.length === 0) {
                qtyInputAlert.innerHTML = "Please enter a quantity value";
                return null;
            }

            if (isNaN(parseInt(qtyInput.value)) || parseInt(qtyInput.value) === 0) {
                qtyInputAlert.innerHTML = "Quantity must be a number greater than 0";
                return null;
            }

            document.getElementById("my-custom-form").submit();
        });
    }

});

function callAjax(event)
{
    var form = new FormData();
    var quantity = event.target.closest("tr").getElementsByClassName("squareup-quantity")[0].value;
    var location = event.target.closest("tr").getElementsByClassName("location-name")[0];
    var location_id = location.getAttribute('data-location');

    form.append("quantity", quantity);
    form.append("location_id", location_id);
    form.append("productId", productId);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", baseUrl + "square_magento/index/updateInventory", true);
    xhr.setRequestHeader("cache-control", "no-cache");
    xhr.onload = function () {
        if (200 === xhr.status) {
            if (location_id == locationId) {
                document.getElementById("inventory_qty").setAttribute('value', quantity);
                document.getElementById("original_inventory_qty").setAttribute('value', quantity);
                document.getElementById("inventory_qty").value = quantity;
            }
            alert("Inventory was updated.");
        } else if (400 === xhr.status) {
            alert("Product is not associated with any website. Product update failed.");
        }
    };
    xhr.send(form);
}

function callAjaxRemove(event){
    var r = confirm('Are you sure you want to remove the product from that location?');
    if(r){
        var form = new FormData();
        var location = event.target.closest("tr").getElementsByClassName("location-name")[0];
        var location_id = location.getAttribute('data-location');

        form.append("locationId", location_id);
        form.append("productId", productId);
        if(typeof(childProductId) !== "undefined"){
            form.append("childProductId", childProductId);
        }
        form.append("form_key", window.FORM_KEY);
        form.append("isAjax", true);

        var xhr = new XMLHttpRequest();
        xhr.open("POST", removeSquareLocationUrl);
        xhr.setRequestHeader("cache-control", "no-cache");
        xhr.onload = function () {
            if (200 === xhr.status) {
                var location = event.target.closest("tr");
                location.remove();
                alert("Location was removed.");
            }else{
                alert("Cannot remove square inventory record.");
            }

            window.location.reload();
        };
        xhr.send(form);
    }else{

    }
}
