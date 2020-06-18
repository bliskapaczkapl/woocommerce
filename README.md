[![Build Status](https://travis-ci.org/bliskapaczkapl/woocommerce.svg?branch=master)](https://travis-ci.org/bliskapaczkapl/woocommerce)

# Moduł Bliskapaczka dla WooCommerce 

## Instalacja modułu

### Wymagania
W celu poprawnej instalacji modułu wymagane są:
- php >= 5.6

### Instalacja modułu

Instrukcja instalacji, konfiguracji wtyczki oraz paczka instalacyjna znajduje się pod adresem
[https://bliskapaczka.pl/narzedzia/integracja-sklep-wordpress-woocommerce](https://bliskapaczka.pl/narzedzia/integracja-sklep-wordpress-woocommerce)

### Tryb testowy

Tryb testowy, czli komunikacja z testową wersją znajdującą się pod adresem [sandbox-bliskapaczka.pl](https://sandbox-bliskapaczka.pl/) można uruchomić zaznaczająć w ustwieniach modułu opcję `Test mode enabled`.

#### Dodatkowe opłaty

Wybór przewoźnika DPD lub FedEx dla zleceń D2D z usługą pobrania może wiązać się z dodatkową opłatą podczas wyceny w serwisie bliskapaczka.pl ze względu na obowiązkowe ubezpieczenie, którego wymaga przewoźnika.
Kwota pobrania wolna od dodatkowych opłat uwzględniona jest w cenniku na naszej stronie internetowej bliskapaczka.pl/cennik


## Docker demo

`docker-compose up`

Front PrestaShop jest dostępny po wpisaniu w przeglądarcę adresu `http://127.0.0.1:8080`.

Po wejści na powyższy adres należy zainstalować WordPress'a oraz plugin WooCommerce a następnie należy zainstalować i skonfigurować moduł Bliskapaczka.pl według instrukcji powyżej.

## Rozwój modułu

### Instalacja zależności
```
composer install --dev
```

Katalog `vendor` należy przenieść do katalogu `wp-content/plugins/bliskapaczka-shipping-method`

### Jak uruchomić testy jednostkowe
```
cd wp-content/plugins/bliskapaczka-shipping-method
php vendor/bin/phpunit --bootstrap wp-content/plugins/bliskapaczka-shipping-method/tests/bootstrap.php wp-content/plugins/bliskapaczka-shipping-method/tests/unit/
```

### Tłumaczenia
Instalacja narzędzi do tłumaczeń, zobacz [I18n_for_WordPress_Developers](https://codex.wordpress.org/I18n_for_WordPress_Developers#Translating_Plugins_and_Themes)

```
wp-cli i18n make-pot wp-content/plugins/bliskapaczka-shipping-method/ wp-content/plugins/bliskapaczka-shipping-method/i18n/languages/bliskapaczka-shipping-method.pot
```