<?php
namespace App\Tests\Command;

use App\Command\UpdateCurrencyCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\EntityManagerInterface;

use App\Service\CurrencyApi;

class UpdateCurrencyCommandTest extends KernelTestCase
{
	
	// test if currency list is a key value pair air
	public function testCurrencyList() {
		
		$kernel = static::createKernel();
        $kernel->boot();
		
		$container = $kernel->getContainer();
		
        /** @var User $user */
        $currencies = $container->get('doctrine')->getRepository(Currency::class)->findList('identifier', 'id');
		
		$this->assertTrue(is_array($currencies));
		
	}
	
	// test if currency list is a key value pair air
	public function testGetCurrencyLayer() {
		
		$apiKey = '4068b87d84961337a11c4438f7f59790';
		$defaultCurrency = 'USD';
		$exchangeCurrencies = ['EUR', 'CHF'];
		
		// Check result success
		$currencyLayer = new CurrencyApi($apiKey, $defaultCurrency, $exchangeCurrencies);
		$result = $currencyLayer->getData();
		
		$this->assertTrue($result['success']);
		
		// Check quote transformation
		if ($result['success']) {
			$baseCurrency = 'EUR';
			$quotes = $currencyLayer->getQuotes($result, $baseCurrency);
			
			$checkQuote = true;
			foreach (array_keys($quotes) as $quote):
				// Check if quote starts with base currency
				if (substr($quote, 0, strlen($baseCurrency)) !== $baseCurrency) {
					$checkQuote = false;
					break;
				}
			endforeach;
			
			$this->assertTrue($checkQuote);
		}
	}
	
}