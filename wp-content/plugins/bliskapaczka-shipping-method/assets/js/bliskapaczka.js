function Bliskapaczka()
{
}

Bliskapaczka.showMap = function (operators, googleMapApiKey, testMode, codOnly = false) {

    if (!!event.pageX === false) {
    return false;
    }
    bpWidget = document.getElementById('bpWidget');

    myModal = document.getElementById('bliskapaczka-modal');
    bpWidget.classList.add('bliskapaczka-modal-content');
    bpWidget.style.display = 'block';
    myModal.classList.add('bliskapaczka-modal');
    myModal.style.display = 'block';

    let posCode = jQuery('#bliskapaczka-point-code').val();
    let posOperator = jQuery('#bliskapaczka-point-operator').val();
 
    if (posCode === "") {
        jQuery('#bliskapaczka-point-operator').val("");
    }

    jQuery('input[value="bliskapaczka"]').trigger('click');
    Bliskapaczka.updateSelectedCarrier();
    
    BPWidget.init(
        bpWidget,
        {
            googleMapApiKey: googleMapApiKey,
            callback: function (data) {

                posCodeForm = document.getElementById('bliskapaczka-point-code')
                posOperatorForm = document.getElementById('bliskapaczka-point-operator')

                posCode = posCodeForm.value = data.code;
                posOperator = posOperatorForm.value = data.operator;

                Bliskapaczka.pointSelected(data, operators);
            },
            operators: operators,
            posType: 'DELIVERY',
            testMode: testMode,
            codOnly: codOnly,
            showCod: false,
            selectedPos: {
            	code: posCode,
            	operator: posOperator
            }
        }
    );
}

Bliskapaczka.pointSelected = function (data, operators) {
    Bliskapaczka.updatePrice(data.operator, operators);
    var modal = document.getElementById("bliskapaczka-modal");
    modal.style.display = "none";
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

/**
 * Show loader spinner on element.
 * 
 * ex. Bliskapaczka.loadBlock('div.my_class');
 * 
 * @param {String} selector jQuery element selector string 
 */
Bliskapaczka.loadBlock = function( selector ) {
	jQuery( selector ).addClass( 'processing' ).block( {
		message: null,
		overlayCSS: {
			background: '#fff',
			opacity: 0.6
		}
	});
}

/**
 * Hide loader spinner on element
 * 
 * ex. Bliskapaczka.loadUnblock('div.my_class');
 * 
 * @param {String} selector jQuery element selector 
 */
Bliskapaczka.loadUnblock = function( selector ) {
	jQuery( selector ).removeClass( 'processing' ).unblock();
};

document.addEventListener("DOMContentLoaded", function () {

    jQuery('#bliskapaczka-modal').on('click', function (event) {
        if ((jQuery(event.target).children().hasClass('bliskapaczka-modal-content')) || event.target.className === 'bliskapaczka-modal') {
            jQuery(this).hide();
        }

    });

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

    /**
     * Remember choosed courier and show new total order price on cart page
     */
    jQuery('body').on('click', '.bliskapaczka_courier_item_wrapper', function () {
    	
    	 // loader
    	 Bliskapaczka.loadBlock('div.cart_totals');
    	
    	 const previousCourier =  jQuery('.bliskapaczka_courier_item_wrapper .checked').attr('data-operator');
    	 const currentCourier = jQuery(this).attr('data-operator');
    	 
    	 // if data no changed then return
    	 if (previousCourier === currentCourier) {
    		 Bliskapaczka.loadUnblock('div.cart_totals');
    		 return;
    	 }
    	 
    	 jQuery('.bliskapaczka_courier_item_wrapper').removeClass('checked');
    	 jQuery(this).addClass('checked');
    	 
    	 // remember selected courier
    	 var data = {
	        action: 'bliskapaczka_delivery_to_door_switch_courier', //the function in php functions to call
	        bliskapaczka_door_operator: currentCourier,
	        security: BliskapaczkaAjax.security,
    	 };
    	 
    	 jQuery
	    	 .post(BliskapaczkaAjax.ajax_url, data, function( response ) {
	    		 if (typeof response !== 'undefined' && response.order_total_html !== 'undefined') {
	    			 jQuery( '.order-total td' ).html( response.order_total_html );
	    		 }
	    		 
	    		 // if the shipping method is not checked, we update it
	    		 if ( ! jQuery('input[value="bliskapaczka-courier"]').is(':checked'))  {
	        		 jQuery('input[value="bliskapaczka-courier"]').trigger('click');
	        	 } 
	    		 
	    	 }, 'json')
	    	 .always(function() {
	    		 Bliskapaczka.loadUnblock('div.cart_totals');
	    	 });
    });

});

