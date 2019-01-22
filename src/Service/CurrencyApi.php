<?php
namespace App\Service;

use OceanApplications\currencylayer;

class CurrencyApi
{
    
	private $apiKey;
	private $defaultCurrency;
	private $exchangeCurrencies;
	
	public function __construct($apiKey, $defaultCurrency, $exchangeCurrencies)
    {
        $this->apiKey = $apiKey;
		$this->defaultCurrency = $defaultCurrency;
		$this->exchangeCurrencies = $exchangeCurrencies;
    }
	
	// Get data from currency layers
	public function getData() {
		
		$currencylayer = new currencylayer\client($this->apiKey);
		
		$result = $currencylayer
			->source($this->defaultCurrency)
			->currencies(implode(',', $this->exchangeCurrencies))
			->live();
		
		return $result;
	}
	
	// Check base currency and manipulate the quotes
	public function getQuotes($result, $baseCurrency) {
		if ($baseCurrency != $this->defaultCurrency) {
			$quote = $this->defaultCurrency . $baseCurrency;
			$quotes = [];

			if (isset($result['quotes'][$quote])) {

				// Difference between base and default conversion
				$factor = 1 / $result['quotes'][$quote];

				foreach ($result['quotes'] as $conversion => $value):
					// Currency identifier for current quote
					$identifier = str_replace($result['source'], '', $conversion);

					// Calculate new values for base currency
					if ($identifier == $baseCurrency) {
						$newConversion = $baseCurrency.$this->defaultCurrency;

						$quotes[$newConversion] = $factor;
					} else {
						$newConversion = $baseCurrency.$identifier;
						$quotes[$newConversion] = $factor * $value;
					}
				endforeach;
			} else {
				throw new InvalidArgumentException("Expected defaut currency to base currency conversion requested.");
			}
		} else {
			$quotes = $result['quotes'];
		}
		
		return $quotes;
	}
	
}