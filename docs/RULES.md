# Rules Reference

Detailed documentation for every Rector rule shipped by this package.

Each configurable rule documents its parameters in its section.

## Table of Contents

- [Rules Reference](#rules-reference)
  - [Table of Contents](#table-of-contents)
  - [AddTypedClassConstantRector](#addtypedclassconstantrector)
  - [CollapseSequentialStrReplaceRector](#collapsesequentialstrreplacerector)
  - [ExtractAssignmentFromIfConditionRector](#extractassignmentfromifconditionrector)
  - [Legacy Rector](#legacy-rector)
    - [CountArrayToEmptyArrayComparisonRector](#countarraytoemptyarraycomparisonrector)
    - [NestedTernaryToMatchRector](#nestedternarytomatchrector)
    - [ReplaceTestFunctionPrefixWithAttributeRector](#replacetestfunctionprefixwithattributerector)
  - [NullableBoolReturnToFalseRector](#nullableboolreturntofalserector)
  - [ReplaceMultipleEqualWithInArrayRector](#replacemultipleequalwithinarrayrector)
  - [Yii2](#yii2)
    - [Yii2AddRelationQueryGenericRector](#yii2addrelationquerygenericrector)
    - [Yii2AddPropertyTagsRector](#yii2addpropertytagsrector)
    - [Yii2FindAllIdShortcutRector](#yii2findallidshortcutrector)
    - [Yii2FindOneFindAllShortcutRector](#yii2findonefindallshortcutrector)
    - [Yii2FindOneIdShortcutRector](#yii2findoneidshortcutrector)
    - [Yii2PropertyAccessRector](#yii2propertyaccessrector)
    - [Yii2RedundantActiveRecordSelfLookupRector](#yii2redundantactiverecordselflookuprector)
    - [Yii2UseExistsInsteadOfCountRector](#yii2useexistsinsteadofcountrector)
    - [Yii2UseExistsInsteadOfOneNotNullRector](#yii2useexistsinsteadofonenotnullrector)
    - [Yii2UserFindOneToIdentityRector](#yii2userfindonetoidentityrector)

## AddTypedClassConstantRector

Adds an explicit constant type when it can be safely inferred from scalar or array literals. It skips constants that are already typed, use `null`, use expressions, or mix incompatible types in the same declaration.

**Before**

```php
final class Foo
{
    public const MAX = 10;
    protected const NAME = 'demo';
    private const ENABLED = true;
}
```

**After**

```php
final class Foo
{
    public const int MAX = 10;
    protected const string NAME = 'demo';
    private const bool ENABLED = true;
}
```

Parameters: none.

## CollapseSequentialStrReplaceRector

Collapses consecutive `str_replace()` calls that reuse the same replacement value into one call with an array of search values. This reduces temporary variables and keeps the original replacement semantics.

**Before**

```php
final class PhoneNormalizer
{
    public function normalize(string $phone): string
    {
        $value = str_replace('+', '', $phone);
        $value = str_replace(' ', '', $value);
        $value = str_replace('-', '', $value);

        return str_replace('(', '', $value);
    }
}
```

**After**

```php
final class PhoneNormalizer
{
    public function normalize(string $phone): string
    {
        return str_replace(['+', ' ', '-', '('], '', $phone);
    }
}
```

Parameters: none.

## ExtractAssignmentFromIfConditionRector

Moves assignments out of `if` conditions into standalone statements. It also supports selected comparisons, negations, and some function wrappers so the resulting condition stays readable and behavior stays the same.

**Before**

```php
if (($model = User::findOne($id)) !== null) {
    return $model;
}
```

**After**

```php
$model = User::findOne($id);
if ($model !== null) {
    return $model;
}
```

Parameters: none.

## Legacy Rector

### CountArrayToEmptyArrayComparisonRector

Replaces supported `count()` checks on expressions with a native `array` type by comparison with `[]`. It supports zero comparisons and `if`/`elseif` truthiness checks, while leaving `Countable` objects and `while` conditions unchanged.

**Before**

```php
function hasItems(array $items): bool
{
    return count($items) > 0;
}
```

**After**

```php
function hasItems(array $items): bool
{
    return $items !== [];
}
```

Parameters: none.

### NestedTernaryToMatchRector

Converts a nested long ternary assigned to a variable into `match`. When every condition is a strict comparison of the same variable, the variable becomes the match subject; otherwise the rule emits `match (true)` only for native boolean conditions. Short ternaries and non-boolean truthiness checks are skipped.

**Before**

```php
$result = $status === 'active' ? 'enabled' : ($status === 'disabled' ? 'off' : 'unknown');
```

**After**

```php
$result = match ($status) {
    'active' => 'enabled',
    'disabled' => 'off',
    default => 'unknown',
};
```

Parameters: none.

### ReplaceTestFunctionPrefixWithAttributeRector

Adds PHPUnit's `#[Test]` attribute to public test-prefixed methods in PHPUnit test classes and removes the `test` or `test_` prefix. Methods named exactly `test` or `test_`, non-public helpers, and methods that already have the attribute keep their names unchanged.

**Before**

```php
final class CalculatorTest extends \PHPUnit\Framework\TestCase
{
    public function testOnePlusOneShouldBeTwo(): void
    {
        $this->assertSame(2, 1 + 1);
    }
}
```

**After**

```php
final class CalculatorTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function onePlusOneShouldBeTwo(): void
    {
        $this->assertSame(2, 1 + 1);
    }
}
```

Parameters: none.

## NullableBoolReturnToFalseRector

Turns `?bool` return types into `bool` and rewrites direct `return null;` statements to `return false;`. Nested closures keep their own return types and returns unchanged.

**Before**

```php
function isReady(): ?bool
{
    if (rand(0, 1)) {
        return null;
    }

    return true;
}
```

**After**

```php
function isReady(): bool
{
    if (rand(0, 1)) {
        return false;
    }

    return true;
}
```

Parameters: none.

## ReplaceMultipleEqualWithInArrayRector

Replaces repeated equality or inequality checks against the same variable with `in_array()`. Strict comparisons produce `in_array(..., true)`, while negative chains become `!in_array(...)`.

**Before**

```php
if ($status === 'new' || $status === 'active' || $status === 'done') {
    return true;
}
```

**After**

```php
if (in_array($status, ['new', 'active', 'done'], true)) {
    return true;
}
```

Parameters:

- `threshold` (int, default: `3`) — minimum number of repeated comparisons before the rule replaces them with `in_array()`. Set it to `2` to also convert two-value chains.

## Yii2

### Yii2AddRelationQueryGenericRector

Adds the related model type to an `ActiveQuery` return annotation for direct Yii2 `hasOne()` and `hasMany()` relations. The method must return `yii\db\ActiveQuery`, have an exact `@return ActiveQuery` annotation, and contain a single direct relation return using `Model::class`. Existing generics, dynamic model classes, and other query types are unchanged.

**Before**

```php
use yii\db\ActiveQuery;

/**
 * @return ActiveQuery Query for the book author.
 */
public function getAuthor(): ActiveQuery
{
    return $this->hasOne(Author::class, ['id' => 'author_id']);
}

/**
 * @return ActiveQuery
 */
public function getBooks(): ActiveQuery
{
    return $this->hasMany(Book::class, ['author_id' => 'id']);
}
```

**After**

```php
use yii\db\ActiveQuery;

/**
 * @return ActiveQuery<Author> Query for the book author.
 */
public function getAuthor(): ActiveQuery
{
    return $this->hasOne(Author::class, ['id' => 'author_id']);
}

/**
 * @return ActiveQuery<Book>
 */
public function getBooks(): ActiveQuery
{
    return $this->hasMany(Book::class, ['author_id' => 'id']);
}
```

Parameters: none.

### Yii2AddPropertyTagsRector

Adds missing `@property`, `@property-read`, and `@property-write` tags for public Yii2 magic accessors declared by a `yii\base\BaseObject` subclass. Types come from method PHPDoc or native signatures. For `yii\db\BaseActiveRecord`, direct `hasOne()` getters become nullable related-model properties and `hasMany()` getters become related-model arrays. Classes with custom `__get()` or `__set()` implementations are skipped.

**Before**

```php
final class Settings extends \yii\base\BaseObject
{
    public function getName(): string
    {
        return '';
    }

    public function setName(string $name): void
    {
    }

    public function getCount(): int
    {
        return 0;
    }
}
```

**After**

```php
/**
 * @property string $name
 * @property-read int $count
 */
final class Settings extends \yii\base\BaseObject
{
    public function getName(): string
    {
        return '';
    }

    public function setName(string $name): void
    {
    }

    public function getCount(): int
    {
        return 0;
    }
}
```

Parameters:

- `refine_property_tag_kinds` (bool, default: `false`) — replaces existing `@property` tags with `@property-read` or `@property-write` when only one accessor exists. Tags with both accessors remain `@property`.
- `remove_unresolved_property_tags` (bool, default: `false`) — removes property tags for names with no matching accessor or supported ActiveRecord relation getter.

```php
use Rector\Config\RectorConfig;
use Vix\RectorRules\Yii2\Yii2AddPropertyTagsRector;

return RectorConfig::configure()
    ->withConfiguredRule(Yii2AddPropertyTagsRector::class, [
        Yii2AddPropertyTagsRector::REFINE_PROPERTY_TAG_KINDS => true,
        Yii2AddPropertyTagsRector::REMOVE_UNRESOLVED_PROPERTY_TAGS => true,
    ]);
```

### Yii2FindAllIdShortcutRector

Simplifies Yii2 `findAll()` calls that wrap an ID condition in a one-element array. It only rewrites the exact `['id' => ...]` shortcut form.

**Before**

```php
$models = User::findAll(['id' => $ids]);
```

**After**

```php
$models = User::findAll($ids);
```

Parameters: none.

### Yii2FindOneFindAllShortcutRector

Converts `Model::find()->where(...)->one()` and `->all()` chains into `findOne()` and `findAll()` shortcuts. It preserves array conditions when needed and skips cases where extra chaining such as `limit()` could change behavior.

**Before**

```php
$model = User::find()->where(['id' => $id])->one();
```

**After**

```php
$model = User::findOne($id);
```

Parameters: none.

### Yii2FindOneIdShortcutRector

Simplifies Yii2 `findOne()` calls that use the array form for a single `id` lookup. Composite conditions and other keys are left unchanged.

**Before**

```php
$model = User::findOne(['id' => $id]);
```

**After**

```php
$model = User::findOne($id);
```

Parameters: none.

### Yii2PropertyAccessRector

Replaces Yii2 user getter calls with direct property access for the built-in `user` component. This currently targets `getId()` and `getIdentity()`.

**Before**

```php
$id = Yii::$app->user->getId();
$identity = Yii::$app->user->getIdentity();
```

**After**

```php
$id = Yii::$app->user->id;
$identity = Yii::$app->user->identity;
```

Parameters: none.

### Yii2RedundantActiveRecordSelfLookupRector

Replaces redundant lookup of the current Yii2 Active Record model by its own `id` with `$this`. It supports `self`, `static`, and the current class name, plus the direct `findOne()` form and `find()->where(...)->one()` form. `limit(1)` between `where()` and `one()` is also supported.

**Before**

```php
final class User extends ActiveRecord
{
    public function getCurrentModel(): self
    {
        return self::findOne($this->id);
    }
}
```

**After**

```php
final class User extends ActiveRecord
{
    public function getCurrentModel(): self
    {
        return $this;
    }
}
```

**Before**

```php
final class User extends ActiveRecord
{
    public function getCurrentModel(): self
    {
        return self::find()->where(['id' => $this->id])->limit(1)->one();
    }
}
```

**After**

```php
final class User extends ActiveRecord
{
    public function getCurrentModel(): self
    {
        return $this;
    }
}
```

Parameters: none.

### Yii2UseExistsInsteadOfCountRector

Replaces supported Yii2 `count()` comparisons with `exists()` or `!exists()` when the comparison only checks whether at least one row matches. This avoids unnecessary counting.

**Before**

```php
$hasUsers = User::find()->where(['active' => 1])->count() > 0;
$hasNoUsers = User::find()->where(['active' => 1])->count() === 0;
```

**After**

```php
$hasUsers = User::find()->where(['active' => 1])->exists();
$hasNoUsers = !User::find()->where(['active' => 1])->exists();
```

Parameters: none.

### Yii2UseExistsInsteadOfOneNotNullRector

Replaces strict `one() === null` and `one() !== null` checks with `exists()` or `!exists()`. Both direct and mirrored `null` comparisons are supported.

**Before**

```php
$hasUser = User::find()->where(['id' => $id])->one() !== null;
$missingUser = User::find()->where(['id' => $id])->one() === null;
```

**After**

```php
$hasUser = User::find()->where(['id' => $id])->exists();
$missingUser = !User::find()->where(['id' => $id])->exists();
```

Parameters: none.

### Yii2UserFindOneToIdentityRector

Replaces lookups for the currently authenticated Yii2 user with direct access to `Yii::$app->user->identity`. It supports both scalar and simple array `findOne()` forms on the `User` model.

**Before**

```php
$user = User::findOne(Yii::$app->user->id);
```

**After**

```php
$user = Yii::$app->user->identity;
```

Parameters: none.
