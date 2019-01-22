# Currency Layer

The app allows you to connect to https://currencylayer.com API and save exchange rates for USD and CHF daily.


## UpdateCurrency Command


Located: \App\Command\UpdateCurrencyCommand

Execute: bin/console app:update-currency

Connects through CurrencyApi Service to get data from currencylayer.com, creates a record in the database for each currency with current timestamp if not already present.

## CurencyApi Service

Located: \App\Service\CurrencyApi

Gets the result from currencylayer.com using oceanapplications/currencylayer-php-client SDK. Extra function to convert result quotes from one currency to other because of the limitations of the free plan.

## Tests

Located: \App\Tests\Command\UpdateCurrencyCommandTest

Checks if currency layer API returns a successfull response and tests getting the quotes with required base currency.
Also tests the findList() function from currency repository if returns an array.
