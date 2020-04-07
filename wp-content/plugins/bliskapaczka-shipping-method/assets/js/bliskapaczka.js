function Bliskapaczka()
{
}

Bliskapaczka.showMap = function (operators, googleMapApiKey, testMode, codOnly = false) {



    bpWidget = document.getElementById('bpWidget');
    bpWidget.style.display = 'block';
    myModal = document.getElementById('myModal');
    if (window.location.href.search('sandbox') !== -1) {

        myModal.style.display = 'block';
    } else {
        bpWidget.classList.remove('modal-content');
        myModal.classList.remove('modal');
        bpWidget.style.height = '600px';
    }


    if (jQuery('#bliskapaczka_posCode').attr('value') === "") {
        jQuery('#bliskapaczka_posOperator').attr('value', "")
    }
    jQuery('input[value="bliskapaczka"]').trigger('click');
    Bliskapaczka.updateSelectedCarrier();
    BPWidget.init(
        bpWidget,
        {
            googleMapApiKey: googleMapApiKey,
            callback: function (data) {
                console.log(data)
                console.log('BPWidget callback:', data.code, data.operator)

                posCodeForm = document.getElementById('bliskapaczka_posCode')
                posOperatorForm = document.getElementById('bliskapaczka_posOperator')

                posCodeForm.value = data.code;
                posOperatorForm.value = data.operator;

                Bliskapaczka.pointSelected(data, operators);
            },
            operators: operators,
            posType: 'DELIVERY',
            testMode: testMode,
            codOnly: codOnly,
            showCod: false
        }
    );
}

Bliskapaczka.pointSelected = function (data, operators) {
    Bliskapaczka.updatePrice(data.operator, operators);
    var modal = document.getElementById("myModal");
    modal.style.display = "none";
    jQuery( document.body ).trigger( 'update_checkout' );
}

Bliskapaczka.updatePrice = function (posOperator, operators) {
    item = Bliskapaczka.getTableRow();
    var shippingMethod = jQuery('input[class="shipping_method"]:checked');
    console.log(shippingMethod)
    if (item) {
        priceDiv = item.find('.delivery_option_price').first();

        for (var i = 0; i < operators.length; i++) {
            if (operators[i].operator == posOperator) {
                price = operators[i].price;
            }
        }

        priceDiv.html(priceDiv.text().replace(/([\d\.,]{2,})/, price));
    }
}

Bliskapaczka.updateSelectedCarrier = function () {
    item = Bliskapaczka.getTableRow();
    
    if (item) {
        input = item.find('input.delivery_option_radio').first();
        // Magic because in themes/default-bootstrap/js/order-carrier.js is defined event onchanged input
        input.click();
    
        items = jQuery('td.delivery_option_radio span')
        items.each(function (index, element) {
            jQuery(this).removeClass('checked');
        });
        item.find('td.delivery_option_radio span').first().addClass('checked');
    }
}

Bliskapaczka.getTableRow = function () {
    item = null;
    itemList = jQuery('.order_carrier_content').find('.delivery_option:contains("bliskapaczka")');
    
    if (itemList.length > 0) {
        item = jQuery(itemList[0]);
    }

    return item;
}

Bliskapaczka.checkFirstCourier = function() {
    if (jQuery('.bliskapaczka_courier_item_wrapper.checked').length === 0) {
        if (jQuery('.bliskapaczka_courier_item_wrapper').length !== 0) {
            jQuery(jQuery('.bliskapaczka_courier_item_wrapper')[0]).addClass('checked');
            jQuery('#bliskapaczka_posOperator').val(jQuery(jQuery('.bliskapaczka_courier_item_wrapper')[0])
              .attr('data-operator'));
        }
    }
}
document.addEventListener("DOMContentLoaded", function () {
    if (window.location.href.search('sandbox') !== -1) {
        // Get the modal
        var modal = document.getElementById("myModal");

// Get the button that opens the modal
        var btn = document.getElementById("myBtn");

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    }

    jQuery('form.checkout').on('change', 'input[name="payment_method"]', function(){
        jQuery(document.body).trigger("update_checkout");

    });
    jQuery('body').on('updated_checkout', function(){
        if (jQuery('input[value="bliskapaczka"]').is(':checked')) {
            var a = jQuery('a[href="#bpWidget_wrapper"]');
            var arguments = a.attr('onclick');
            eval(arguments);
        }
    });

    jQuery('body').on('click', '.bliskapaczka_courier_item_wrapper', function () {
        jQuery('.bliskapaczka_courier_item_wrapper').removeClass('checked');
        jQuery(this).addClass('checked');
        jQuery('#bliskapaczka_posOperator').val(jQuery(this)
          .attr('data-operator'));
        jQuery('input[value="bliskapaczka-courier"]').trigger('click');
        jQuery(document.body).trigger("update_checkout");
    })

});
