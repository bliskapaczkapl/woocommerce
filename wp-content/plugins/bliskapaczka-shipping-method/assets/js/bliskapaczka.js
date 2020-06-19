function Bliskapaczka()
{
}

Bliskapaczka.showMap = function (operators, googleMapApiKey, testMode, codOnly = false) {

    if (!!event.pageX === false) {
    return false;
    }
    bpWidget = document.getElementById('bpWidget');

    myModal = document.getElementById('myModal');
    bpWidget.classList.add('modal-content');
    bpWidget.style.display = 'block';
    myModal.classList.add('modal');
    myModal.style.display = 'block';

    let posCode = jQuery('#bliskapaczka_posCode').val();
    let posOperator = jQuery('#bliskapaczka_posOperator').val();
 
    if (posCode === "") {
        jQuery('#bliskapaczka_posOperator').val("");
    }

    jQuery('input[value="bliskapaczka"]').trigger('click');
    Bliskapaczka.updateSelectedCarrier();
    
    BPWidget.init(
        bpWidget,
        {
            googleMapApiKey: googleMapApiKey,
            callback: function (data) {

                posCodeForm = document.getElementById('bliskapaczka_posCode')
                posOperatorForm = document.getElementById('bliskapaczka_posOperator')

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
    var modal = document.getElementById("myModal");
    modal.style.display = "none";
    jQuery( document.body ).trigger( 'update_checkout' );
}

Bliskapaczka.updatePrice = function (posOperator, operators) {
    item = Bliskapaczka.getTableRow();
    var shippingMethod = jQuery('input[class="shipping_method"]:checked');
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

        jQuery('#myModal').on('click', function (event) {
            if ((jQuery(event.target).children().hasClass('modal-content')) || event.target.className === 'modal') {
                jQuery(this).hide();
            }

        })

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
    jQuery('body.woocommerce-cart').on('click', '.bliskapaczka_courier_item_wrapper', function () {
    	
    	 // loader
    	 Bliskapaczka.loadBlock('div.cart_totals');
    	
    	 const previousCourier =  jQuery('.bliskapaczka_courier_item_wrapper .checked').attr('data-operator');
    	 const currentCourier = jQuery(this).attr('data-operator');
    	 jQuery('.bliskapaczka_courier_item_wrapper').removeClass('checked');
    	 jQuery('input[value="bliskapaczka-courier"]').trigger('click');
    	 
    	 // if data no changed then return
    	 if (previousCourier === currentCourier) {
    		 Bliskapaczka.loadUnblock('div.cart_totals');
    		 return;
    	 }
    	 
    	 // remember selected courier
    	 var data = {
	        action: 'bliskapaczka_wc_cart_switch_courier', //the function in php functions to call
	        bliskapaczka_posOperator: currentCourier,
	        security: BliskapaczkaAjax.security
    	 };
    	 
    	 jQuery
	    	 .post(BliskapaczkaAjax.ajax_url, data, function( response ) {
	    		 if (typeof response !== 'undefined' && response.order_total_html !== 'undefined') {
	    			 jQuery( '.cart_totals .order-total td' ).html( response.order_total_html );
	    		 }
	    	 }, 'json')
	    	 .always(function() {
	    		 Bliskapaczka.loadUnblock('div.cart_totals');
	    	 });
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

