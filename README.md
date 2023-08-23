# PDF
PDF document generation and saving or outputting the document.

Actual PDF document generation is performed by separate interchangeable packages that use a particular PDF library.

For license information check the [LICENSE](LICENSE.md) file.

## Installation
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run
```
composer require --prefer-dist BeastBytes/pdf
```
or add

```json
"beastbytes/pdf": "~1.0.0"
```
to the `require` section of your composer.json.

## Basic Usage
```php

$document = $pdf
    ->generate('viewName')
    ->withAuthor('A. U. Thor')
    ->withSubject('Subject')
    ->withTitle('Title')
;    

$pdf->output($document, Pdf::DESTINATION_INLINE);
```

### Localised View
The document can use localised views using the `withLocale()` method
```php

$document = $pdf
    ->withLocale('de_DE')
    ->generate('viewName')
    ->withAuthor('A. U. Thor')
    ->withSubject('Subject')
    ->withTitle('Title')
;    

$pdf->output($document, Pdf::DESTINATION_INLINE);
```
