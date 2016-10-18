Symfony Validator Extras
==============================

In order to simplify request validation and reduce amount of boilerplate, this package
provides you set of additional constraints.

## Json and FixedJson constraints

If you are dealing with json requests, you probably hate `Collection` validator. As a replacement this library provides you two additional constraints: `JsonValidator` and `FixedJsonValidator`.

The difference between `JsonValidator` and `FixedJsonValidator`, which is both extends from `CollectionValidator`, is that
they use different value for option `allowExtraFields`. `FixedJson` doesn't allow extra fields, but `Json` - just ignores that.

### Shortcuts

This validator provides you some shortcuts for common validators:

```php
$rules = new FixedJson([
    'foo' => 'string',
    'bar' => 'email',
    'buz' => 'datetime',
]);
```

this is equivalent of

```php
$rules = new Collection([
    'foo' => [new NotNull(), new Type('string')],
    'bar' => [new NotNull(), new Email()],
    'buz' => [new NotNull(), new DateTime()]
]);
```

List of available shortcuts:

 ShortCut | Constraint Used
----------|-----------------
`date    `| `new Date()`
`datetime`| `new DateTime()`
`time    `| `new Time()`
`email   `| `new Email()`
`url     `| `new Url()`
`file    `| `new File()`
`image   `| `new Image()`
`null    `| `new IsNull()`
`*any strhing*` | `new Type('*any strhing*')`

### Null Safety

By default all constraint (except `null`) shortcuts expands with `NotNull` constraint. If `null` is acceptable value
for you, you can just add question mark at the beginning of shortcut. So this rules will be equivalent:

```
$rules = new Json([
    'foo' => '?string',
    'bar' => 'string'
]);

$equivalent = new Collection([
    'allowExtraFields' => true,
    'fields' => [
        'foo' => [new Type('string')],
        'bar' => [new NotNull(), new Type('string')]
    ]
]);
```

The syntax is taken from PHP 7.1

### Optional fields

If your json request has optional keys, then you probably will write something like this:

```php
$rules = new Collection([
    'foo' => new Optional([new NotNull(), new Type('string')])
]);
```

With `JsonValidator` the same rule can be written as:

```php
$rules = new Json([
    'foo?' => 'string'
]);
```

Please note that question mark (`?`) will be skipped if you manually provide info about is this field is required or not.
So using this rule:

```php
$fules = new Json([
    'foo?' = new Required([new NotNull()]),
    'bar??' => 'string',
])
```

validator will expect `foo?` containing not null value. And property `bar?` considered as optional since it has question
mark in it's end (`?`).


