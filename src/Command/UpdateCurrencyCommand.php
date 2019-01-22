<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use App\Service\CurrencyApi;
use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateCurrencyCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:update-currency';
	
	// api key for currency layer
	protected $apiKey = '4068b87d84961337a11c4438f7f59790';
	
	// base currency for app
	protected $baseCurrency = 'EUR';
	
	// default currency for currency layer
	protected $defaultCurrency = 'USD';
	
	// currencies to convert for currency layer
	protected $exchangeCurrencies = ['EUR', 'CHF'];
	
	private $entityManager;
    private $passwordEncoder;
    private $validator;
    private $currencies;
	private $exchange_rates;
	
	public function __construct(EntityManagerInterface $em, CurrencyRepository $currencies, ExchangeRateRepository $exchange_rates)
    {
        parent::__construct();
		
        $this->entityManager = $em;
        $this->currencies = $currencies;
		$this->exchange_rates = $exchange_rates;
    }
	
	
    protected function configure()
    {
		
        $this
			// the short description shown while running "php bin/console list"
			->setDescription('Update currency database.')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you to connect to https://currencylayer.com API and get currency info.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		
		$date = new \DateTime();
		
		// Get data
		$currencyLayer = new CurrencyApi($this->apiKey, $this->defaultCurrency, $this->exchangeCurrencies);
		$result = $currencyLayer->getData();
		
		// Check operation success
		if ($result['success']) {
			
			// Get currency identifiers
			$currencies = $this->currencies->findList('identifier', 'id');
			
			// Convert from currency layer default currency to app base currency
			// Free plan offers just conversions from USD
			$quotes = $currencyLayer->getQuotes($result, $this->baseCurrency);
			
			$updated = 0;
			
			// Parse quotes and save results to database
			foreach ($quotes as $conversion => $value):
				$identifier = str_replace($this->baseCurrency, '', $conversion);
				
				if (isset($currencies[$identifier])) {
					$currencyId = $currencies[$identifier];
					
					// Check if a record already exists for current date
					$old = $this->exchange_rates->findOneBy([
						'created' => $date,
						'currency' => $currencyId,
					]);
					
					if (!$old) {
						$updated ++;
						
						// Insert value for current date
						$exchangeRate = new ExchangeRate;
						$exchangeRate->setCurrency($this->entityManager->getReference('App:Currency', $currencyId));
						$exchangeRate->setValue($value);
						$exchangeRate->setCreated($date);
						
						$this->entityManager->persist($exchangeRate);
					}
				}
				
			endforeach;
			
			// Insert data to database
			if ($updated > 0) {
				$this->entityManager->flush();
				$this->entityManager->clear();
				
				$output->writeln("Successfull operation: $updated currencies were update for current date.");
			} else {
				$output->writeln('Successfull operation: Currencies for current date were already updated.');
			}
				
		} else {
			$output->writeln('Failed operation: There was a problem with currency layer client response.');
		}
		
    }
	
	

}