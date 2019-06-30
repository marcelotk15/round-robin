# Round-Robin (Laravel 5 Package)
[![Latest Stable Version](https://poser.pugx.org/marcelotk15/round-robin/v/stable)](https://packagist.org/packages/marcelotk15/round-robin) [![Total Downloads](https://poser.pugx.org/marcelotk15/round-robin/downloads)](https://packagist.org/packages/marcelotk15/round-robin)
[![Latest Unstable Version](https://poser.pugx.org/marcelotk15/round-robin/v/unstable)](https://packagist.org/packages/marcelotk15/round-robin)
[![License](https://poser.pugx.org/marcelotk15/round-robin/license)](https://packagist.org/packages/marcelotk15/round-robin)
[![StyleCI](https://github.styleci.io/repos/82237035/shield?branch=master)](https://github.styleci.io/repos/82237035)

Round-Robin is an easy way to create schedule with round-robin(rr) technique. I used the mnito's base code for this. Look here: https://github.com/mnito/round-robin

## Installation
1) In order to install Laravel Round-Robin, just add the following to your composer.json. Then run `composer update`:
```json
"marcelotk15/round-robin": "0.1.*"
```
or run `composer require marcelotk15/round-robin`

2) Open your `config/app.php` and add the following to the `providers` array:
```php
Laravel\RoundRobin\RoundRobinServiceProvider::class,
```

3) Open your `config/app.php` and add the following to the `facades` array:
```php
'RoundRobin' => Laravel\RoundRobin\RoundRobinFacade::class,
```


## Controllers and etc
```php
use Laravel\RoundRobin\RoundRobin;
```


## Using (Examples)
Setuping (without Facade):
```php
$teams = ['Arsenal', 'Atlético de Madrid', 'Borussia', 'Barcelona','Liverpool', 'Bayer 04', 'Real Madrid'];
$schedule = new RoundRobin($teams)->make();
// or with 'from' static method
$schedule = RoundRobin::from($teams)->make();
```

With a facade:
```php
$teams = ['Arsenal', 'Atlético de Madrid', 'Borussia', 'Barcelona','Liverpool', 'Bayer 04', 'Real Madrid'];
$schedule = RoundRobin::from($teams)->make();
```


Generate a schedule without randomly shuffling the teams using the $shuffle boolean parameter:
```php
$teams = ['Arsenal', 'Atlético de Madrid', 'Borussia', 'Barcelona','Liverpool', 'Bayer 04', 'Real Madrid'];
$schedule = RoundRobin::from($teams)->doNotShuffle()->make();
```

Use your own seed with the $seed integer parameter for predetermined shuffling:
```php
$teams = ['Arsenal', 'Atlético de Madrid', 'Borussia', 'Barcelona','Liverpool', 'Bayer 04', 'Real Madrid'];
$schedule = RoundRobin::from($teams)->shuffle(15)->make();
```

If you want a double Round-robin:
```php
$teams = ['Arsenal', 'Atlético de Madrid', 'Borussia', 'Barcelona','Liverpool', 'Bayer 04', 'Real Madrid'];
$schedule = RoundRobin::from($teams)->doubleRoundRobin()->make();
```

If you want a get a *Schedule* Object:
```php
$teams = ['Arsenal', 'Atlético de Madrid', 'Borussia', 'Barcelona','Liverpool', 'Bayer 04', 'Real Madrid'];
$schedule = RoundRobin::from($teams)->makeSchedule();
```

## License
Laravel Round-Robin is free software distributed under the terms of the MIT license.
