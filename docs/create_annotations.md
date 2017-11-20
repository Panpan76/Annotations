# Annotations
-----------
## Documentation

[Back to the summary](../README.md)

### How to create annotations ?

Annotations are *just* matching a specific string.

To create an annotation, you have to create a class that implements `Annotations\Interfaces\CheckAnnotation`. With this annotation you will have to use `checkAnnotation()` :

```php
<?
public function checkAnnotation(string $annotation):array;
```

The input string `$annotation` is automatically given by the `Annotations\Parser`, when a line in a file could be an annotation (see [How to create annotations](create_annotations.md)).

You must return an array, by default, if there is no matches, you have to return an empty array.

So now, lets see what you have to do in this method.

```php
<?
use Annotations\Interfaces\CheckAnnotation;

class MyCustomAnnotation implements CheckAnnotation{
  const KEY = 'customKey';

  const REGEX_CUSTOM = '/myCustom\s+([^\s]*)\s+(.*)/';

  public function checkAnnotation(string $annotation):array{
    if(preg_match(self::REGEX_CUSTOM, $annotation, $matches)){
      return array(
        'value1' => $matches[1],
        'value2' => $matches[2]
      );
    }
    return array();
  }
}

```

First, our class `MyCustomAnnotation` implements the interface `Annotations\Interfaces\CheckAnnotation` as we said before.
We defined 2 constants :
- `KEY` If there is many annotations of this class for a same element, the results will be merge. So you could have :
```php
<?
array('customKey' => array(
  array('something', 'somethingElse'), // Annotation one
  array('otherThing', 'again'), // Annotation two
));
```
- `REGEX_CUSTOM` The name is your choice, but it must be use in `checkAnnotation()`. The value is the regex of your choice, (without the `@` of the beggining of the annotation). You can use modifier of regular expression if you need it.

### Declare your new Annotations class

Now you have created one (or many) class for your annotations and you want to use it.
The default annotations types are defined in the folder `Annotations\Types\`. To add your own folder of annotations types to the parser, you have to use
```php
<?
$parser = new Annotations\Parser();
$parser->setAnnotationsTypesDirectory(string $directory);
```
After that, the parser will check your annoations types folder too.

> Note
>
> You must be carefull with the annotations you use : the parser will stop on the first matching annotation type
