# Annotations
-----------
## Documentation

[Back to the summary](../README.md)

### How to use annotations ?

First of all, you need to understand that, annotations are just special commentary in the code.
An annotation start with a `@` in a multi-lines commentary block. There must be one annotation by line.
The multi-lines commentary block that contains annotations must be place just before a `namespace`, `class`, `attribute` or `method`.

Exemple :
```php
<?
/**
 * This is just a normal commentary
 * @annotation This is an annotation
 */
public function doSomething(){/*...*/}
```

It's very easy to use, and very powerfull if you master it !
