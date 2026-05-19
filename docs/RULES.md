# Rules Reference

Detailed documentation for every Rector rule shipped by this package.

All current rules are parameterless. If a rule becomes configurable in the future, its parameters should be documented in its section.

## Table of Contents

- [Rules Reference](#rules-reference)
  - [Table of Contents](#table-of-contents)
  - [AddTypedClassConstantRector](#addtypedclassconstantrector)
  - [CollapseSequentialStrReplaceRector](#collapsesequentialstrreplacerector)
  - [ExtractAssignmentFromIfConditionRector](#extractassignmentfromifconditionrector)
  - [NullableBoolReturnToFalseRector](#nullableboolreturntofalserector)
  - [ReplaceMultipleEqualWithInArrayRector](#replacemultipleequalwithinarrayrector)
  - [Yii2](#yii2)
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

Parameters: none.

## Yii2

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
