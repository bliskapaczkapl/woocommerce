function Bliskapaczka()
{
}

Bliskapaczka.showMap = function (operators, googleMapApiKey, testMode, codOnly = false) {
    aboutPoint = document.getElementById('bpWidget_aboutPoint');
    aboutPoint.style.display = 'none';

    bpWidget = document.getElementById('bpWidget');
    bpWidget.style.display = 'block';

    Bliskapaczka.updateSelectedCarrier();
    operators = Bliskapaczka.updateOperators(operators, codOnly);
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
            showCod: !codOnly
        }
    );
}

Bliskapaczka.pointSelected = function (data, operators) {
    Bliskapaczka.updatePrice(data.operator, operators);

    bpWidget = document.getElementById('bpWidget');
    bpWidget.style.display = 'none';

    aboutPoint = document.getElementById('bpWidget_aboutPoint');
    aboutPoint.style.display = 'block';

    posDataBlock = document.getElementById('bpWidget_aboutPoint_posData');

    posDataBlock.innerHTML =  data.operator + '</br>'
        + ((data.description) ? data.description + '</br>': '')
        + data.street + '</br>'
        + ((data.postalCode) ? data.postalCode + ' ': '') + data.city

    jQuery( document.body ).trigger( 'update_checkout' );
}

Bliskapaczka.updatePrice = function (posOperator, operators) {
    item = Bliskapaczka.getTableRow();
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
Bliskapaczka.updateOperators = function(operators, codOnly){
    return operators.map(function (o) {
        if (codOnly) {
            o.price = o.price + o.cod;
        }
        return o;
    });
}
// Bliskapaczka.selectPoint = function () {
//     item = Bliskapaczka.getTableRow();

//     if (item) {
//         input = item.find('input.delivery_option_radio').first();
//         if (!input.is(':checked')) {
//             return true;
//         }
//     } else {
//         return true;
//     }

//     posCode = jQuery('#bliskapaczka_posCode').val()
//     posOperator = jQuery('#bliskapaczka_posOperator').val()
//     if (typeof msg_bliskapaczka_select_point != 'undefined' && (!posCode || !posOperator)) {
//         if (!!$.prototype.fancybox) {
//             $.fancybox.open(
//                 [
//                 {
//                     type: 'inline',
//                     autoScale: true,
//                     minHeight: 30,
//                     content: '<p class="fancybox-error">' + msg_bliskapaczka_select_point + '</p>'
//                 }],
//                 {
//                     padding: 0
//                 }
//             );
//         } else {
//             alert(msg_bliskapaczka_select_point);
//         }
//     } else {
//         return true;
//     }
//     return false;
// }

// $(document).ready(function () {
//     if (!!$.prototype.fancybox) {
//         $("a.iframe").fancybox({
//             'type': 'iframe',
//             'width': 600,
//             'height': 600
//         });
//     }

//     $(document).on('submit', 'form[name=carrier_area]', function () {
//         return Bliskapaczka.selectPoint();
//     });
// });
