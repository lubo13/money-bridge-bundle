# Symfony Bridge Bundle for moneyphp/money library

[v1.0.0](https://github.com/lubo13/money-bridge-bundle/releases)

### The aims of this bridge bundle are:
#### Simple Symfony Bridge Bundle for moneyphp/money library useful for integration with [Api Platform](https://api-platform.com/). If you need more complex bundle with Symfony form integration, Twig filters and etc. look at -> tbbc/money-bundle package

1. Compatibility with Doctrine - Money\Money and Money\Currency are mapped to be used as [Embeddables classes](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/embeddables.html)
2. Mapped with Symfony Serializer groups - there are 2 groups per class, for input and output. (money:output, money:input, currency:output, currency:input)
3. Mapped with Validator constraint - Moneys' amount property is mapped with NotBlank and with custom Amount constraint (there is the possibility to change behaviour - amount to be only positive or positive_and_zero). Currency is mapped with Currency constraint and Currencies' code property is mapped with custom CurrencyCodeChoice constraint (there is the possibility to change allowed Currencies' codes) 

## Requirements

PHP 7.4+; Symfony 4.4+; moneyphp/money 3.3+

## Install

Via Composer

```bash
$ composer require lubo13/money-bridge-bundle
```

Bundle should be auto enabled, just check and if not add it to config/bundles.php
```
Money\Bridge\MoneyBridgeBundle::class => ['all' => true],
```

## Usage

By default, bundles come with preconfigured options that you can change in your favour.

To change some of the preconfigured options create money_bridge.yaml file in config/packages with the following content:

```yaml
money_bridge:
    integer_part: 12 # How many digits will you have before the dot -> 1234.
    fractional_part: !php/const Money\Bridge\Util\AmountFormatter::MONEY_FRACTIONAL_PART # How many digits will you have after the dot -> .00
    default_currency_code: 'EUR' # With this option you can control default currency (useful with usage of Serializer or [Api Platform](https://api-platform.com/)).
    allowed_currency_code: [ 'EUR' ] # With this option you can control allowed currency when the Currency is validated.
    amount_validation_rule: positive # There are two option positive or positive_or_zero. With this option, you can control input data on Deserialization process. (useful with usage of Serializer or [Api Platform](https://api-platform.com/))

```

#### To use Money and Currency library as embeddables classes:

```
/**
* @ORM\Embedded(class="Money\Money")
*/
private Money $price;
```

or

```
/**
* @ORM\Embedded(class="Money\Money")
*/
private $price;
```

Also if you write your migration manual you need to add the two new columns to your table:

```sql

$this->addSql('ALTER TABLE YOUR_TABLE_NAME ADD price_amount BIGINT DEFAULT NULL');
$this->addSql('ALTER TABLE YOUR_TABLE_NAME ADD price_currency_code VARCHAR(3) NULL');
```

#### To allow property of Money and Currency to be serialized and deserialized properly in your favour you can use the following groups:

    For deserialize:
    
        money:input -> Moneys' amount property
        
        currency:input -> for Currencies' code property
    
    For serialize:
    
        money:output -> Moneys' amount property
        
        currency:output -> for Currencies' code property

#### Format price amount somewhere in your app to be compatible with Money library:
```
\Money\Bridge\Util\AmountFormatter::getAmountWithoutFractional('100.99', 2);
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
