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
    - [AddParamArrayDocblockBasedOnArrayMapRector](#addparamarraydocblockbasedonarraymaprector)
    - [AddReturnArrayDocblockBasedOnArrayMapRector](#addreturnarraydocblockbasedonarraymaprector)
    - [AddReturnDocblockForDimFetchArrayFromAssignsRector](#addreturndocblockfordimfetcharrayfromassignsrector)
    - [AddSensitiveParameterAttributeRector](#addsensitiveparameterattributerector)
    - [ChangeNestedForeachIfsToEarlyContinueRector](#changenestedforeachifstoearlycontinuerector)
    - [ChangeNestedIfsToEarlyReturnRector](#changenestedifstoearlyreturnrector)
    - [ChangeOrIfContinueToMultiContinueRector](#changeorifcontinuetomulticontinuerector)
    - [CombineIfRector](#combineifrector)
    - [ConstAndTraitDeprecatedAttributeRector](#constandtraitdeprecatedattributerector)
    - [CountArrayToEmptyArrayComparisonRector](#countarraytoemptyarraycomparisonrector)
    - [DeprecatedAnnotationToDeprecatedAttributeRector](#deprecatedannotationtodeprecatedattributerector)
    - [DisallowedEmptyRuleFixerRector](#disallowedemptyrulefixerrector)
    - [ExplicitBoolCompareRector](#explicitboolcomparerector)
    - [FluentSettersToStandaloneCallMethodRector](#fluentsetterstostandalonecallmethodrector)
    - [JsonThrowOnErrorRector](#jsonthrowonerrorrector)
    - [NestedFuncCallsToPipeOperatorRector](#nestedfunccallstopipeoperatorrector)
    - [NestedTernaryToMatchRector](#nestedternarytomatchrector)
    - [NewInInitializerRector](#newininitializerrector)
    - [RemoveNamedArgsInDataProviderRector](#removenamedargsindataproviderrector)
    - [RenameDeprecatedMethodCallRector](#renamedeprecatedmethodcallrector)
    - [ReplaceTestFunctionPrefixWithAttributeRector](#replacetestfunctionprefixwithattributerector)
    - [ReturnBinaryOrToEarlyReturnRector](#returnbinaryortoearlyreturnrector)
    - [SequentialAssignmentsToPipeOperatorRector](#sequentialassignmentstopipeoperatorrector)
    - [ShortenElseIfRector](#shortenelseifrector)
    - [SimplifyIfElseToTernaryRector](#simplifyifelsetoternaryrector)
    - [SwitchNegatedTernaryRector](#switchnegatedternaryrector)
  - [NullableBoolReturnToFalseRector](#nullableboolreturntofalserector)
  - [ReplaceMultipleEqualWithInArrayRector](#replacemultipleequalwithinarrayrector)
  - [Yii2](#yii2)
    - [Yii2AddRelationQueryGenericRector](#yii2addrelationquerygenericrector)
    - [Yii2AddPropertyTagsRector](#yii2addpropertytagsrector)
    - [Yii2FindAllIdShortcutRector](#yii2findallidshortcutrector)
    - [Yii2FindOneFindAllShortcutRector](#yii2findonefindallshortcutrector)
    - [Yii2FindOneIdShortcutRector](#yii2findoneidshortcutrector)
    - [Yii2MergeModelRulesRector](#yii2mergemodelrulesrector)
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

All rules below use the `Vix\RectorRules\LegacyRector` namespace. Register the restored rules individually in your Rector configuration.

### AddParamArrayDocblockBasedOnArrayMapRector

Adds or refines a class-method array parameter annotation using the declared parameter type of an `array_map()` callback. Existing useful element types are preserved.

**Before**

```php
final class SomeClass
{
    public function run(array $items): void
    {
        array_map(fn (string $item) => trim($item), $items);
    }
}
```

**After**

```php
final class SomeClass
{
    /**
     * @param string[] $items
     */
    public function run(array $items): void
    {
        array_map(fn (string $item) => trim($item), $items);
    }
}
```

Parameters: none.

### AddReturnArrayDocblockBasedOnArrayMapRector

Adds or refines an array return annotation using the declared return types of closures or arrow functions passed to returned `array_map()` calls. It supports functions and class methods, and preserves existing useful return annotations.

**Before**

```php
final class ImproveSimpleArray
{
    /**
     * @return array
     */
    public function process(array $items)
    {
        return array_map(function ($item): int {
            return $item;
        }, $items);
    }
}
```

**After**

```php
final class ImproveSimpleArray
{
    /**
     * @return int[]
     */
    public function process(array $items)
    {
        return array_map(function ($item): int {
            return $item;
        }, $items);
    }
}
```

Parameters: none.

### AddReturnDocblockForDimFetchArrayFromAssignsRector

Adds a generic `@return` annotation for a class method that builds an array using indexed assignments and returns that variable. It infers key and value types, including conditional assignments, while preserving existing useful annotations and the native return type.

**Before**

```php
final class ConditionalAssign
{
    public function toArray(): array
    {
        $items = [];

        if (mt_rand(0, 1)) {
            $items['key'] = 100;
        }

        return $items;
    }
}
```

**After**

```php
final class ConditionalAssign
{
    /**
     * @return array<string, int>
     */
    public function toArray(): array
    {
        $items = [];

        if (mt_rand(0, 1)) {
            $items['key'] = 100;
        }

        return $items;
    }
}
```

Parameters: none.

### AddSensitiveParameterAttributeRector

Adds `#[SensitiveParameter]` to function and method parameters whose names appear in the configuration. Existing attributes are preserved without duplication. Requires PHP 8.2 or newer.

**Before**

```php
function login(string $username, string $password): void
{
}
```

**After**

```php
function login(string $username, #[\SensitiveParameter] string $password): void
{
}
```

Parameters:

- `sensitive_parameters` (`list<string>`, default: `[]`) — parameter names to mark, such as `password` or `token`.

```php
use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\AddSensitiveParameterAttributeRector;

return RectorConfig::configure()
    ->withConfiguredRule(AddSensitiveParameterAttributeRector::class, [
        AddSensitiveParameterAttributeRector::SENSITIVE_PARAMETERS => ['password'],
    ]);
```

### ChangeNestedForeachIfsToEarlyContinueRector

Flattens supported nested `if` statements inside a `foreach` by inverting the conditions and inserting early `continue` statements. The original loop body follows the guards; unsupported nesting and branches are left unchanged.

**Before**

```php
class Fixture
{
    public function run()
    {
        $items = [];

        foreach ($values as $value) {
            if ($value === 5) {
                if ($value2 === 10) {
                    $items[] = 'maybe';
                }
            }
        }
    }
}
```

**After**

```php
class Fixture
{
    public function run()
    {
        $items = [];

        foreach ($values as $value) {
            if ($value !== 5) {
                continue;
            }
            if ($value2 !== 10) {
                continue;
            }
            $items[] = 'maybe';
        }
    }
}
```

Parameters: none.

### ChangeNestedIfsToEarlyReturnRector

Flattens nested return-only conditions by inverting the outer condition and using the following return as an early fallback. A single `if` and unsupported branches are left unchanged.

**Before**

```php
class Fixture
{
    public function run()
    {
        if ($value === 5) {
            if ($value2 === 10) {
                return 'yes';
            }
        }

        return 'no';
    }
}
```

**After**

```php
class Fixture
{
    public function run()
    {
        if ($value !== 5) {
            return 'no';
        }
        if ($value2 === 10) {
            return 'yes';
        }

        return 'no';
    }
}
```

Parameters: none.

### ChangeOrIfContinueToMultiContinueRector

Splits a supported `if` condition joined with `||` and containing only `continue` into separate guards. It preserves the order of checks and the original continue level.

**Before**

```php
class Fixture
{
    public function canDrive(Car $newCar)
    {
        foreach ($cars as $car) {
            if ($car->hasWheels() || $car->hasFuel()) {
                continue;
            }
            $car->setWheel($newCar->wheel);
            $car->setFuel($newCar->fuel);
        }
    }
}
```

**After**

```php
class Fixture
{
    public function canDrive(Car $newCar)
    {
        foreach ($cars as $car) {
            if ($car->hasWheels()) {
                continue;
            }
            if ($car->hasFuel()) {
                continue;
            }
            $car->setWheel($newCar->wheel);
            $car->setFuel($newCar->fuel);
        }
    }
}
```

Parameters: none.

### CombineIfRector

Combines supported nested `if` statements into a single condition joined with `&&`. The outer block must contain only the nested condition; branches with `else` or `elseif` are skipped.

**Before**

```php
class Fixture
{
    public function run()
    {
        if ($cond1) {
            if ($cond2) {
                return 'foo';
            }
        }
    }
}
```

**After**

```php
class Fixture
{
    public function run()
    {
        if ($cond1 && $cond2) {
            return 'foo';
        }
    }
}
```

Parameters: none.

### ConstAndTraitDeprecatedAttributeRector

Replaces `@deprecated` annotations on global constants and traits with `#[Deprecated]`. A leading version becomes `since`, and the remaining description becomes `message`. Other PHPDoc tags are preserved. Requires PHP 8.5 or newer.

**Before**

```php
/**
 * @deprecated use new constant
 */
const CONSTANT = 'some reason';

/**
 * @deprecated 2.0.0 do not use
 */
const UNUSED = 'ignored';
```

**After**

```php
#[\Deprecated(message: 'use new constant')]
const CONSTANT = 'some reason';

#[\Deprecated(message: 'do not use', since: '2.0.0')]
const UNUSED = 'ignored';
```

Parameters: none.

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

### DeprecatedAnnotationToDeprecatedAttributeRector

Replaces `@deprecated` annotations on functions, methods, and class constants with `#[Deprecated]`, carrying over the message and an optional version. Other PHPDoc tags are preserved. Uses the PHP 8.4 attribute, which can emit runtime deprecation notices rather than only informing static analysis.

**Before**

```php
final class Fixture
{
    /**
     * @deprecated use new constant.
     */
    public const CONSTANT = 'some reason.';

    /**
     * @deprecated 1.0.1 use new method.
     */
    public function run()
    {
    }
}
```

**After**

```php
final class Fixture
{
    #[\Deprecated(message: 'use new constant.')]
    public const CONSTANT = 'some reason.';

    #[\Deprecated(message: 'use new method.', since: '1.0.1')]
    public function run()
    {
    }
}
```

Parameters: none.

### DisallowedEmptyRuleFixerRector

Replaces supported `empty()` and `!empty()` checks with strict comparisons derived from the native value type. It adds `isset()` guards where needed for uninitialized properties or supported negated array-offset checks. Unknown types and unsupported expressions are left unchanged.

**Before**

```php
final class SomeEmptyArray
{
    public function run(array $items)
    {
        return empty($items);
    }
}
```

**After**

```php
final class SomeEmptyArray
{
    public function run(array $items)
    {
        return $items === [];
    }
}
```

Parameters:

- `treat_as_non_empty` (bool, default: `false`) — when `true`, treats the string `'0'` as non-empty, so a non-nullable string check uses only `=== ''` or `!== ''`. Nullable scalar checks can also simplify to a null comparison.

```php
use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\DisallowedEmptyRuleFixerRector;

return RectorConfig::configure()
    ->withConfiguredRule(DisallowedEmptyRuleFixerRector::class, [
        DisallowedEmptyRuleFixerRector::TREAT_AS_NON_EMPTY => true,
    ]);
```

### ExplicitBoolCompareRector

Replaces scalar truthiness checks in `if`, `elseif`, and long ternaries with explicit comparisons. Strings account for both an empty string and `'0'`; integers and floats are compared with zero. Native booleans, mixed types, arrays, and supported object conditions are skipped.

**Before**

```php
final class ExplicitString
{
    public function run(string $item)
    {
        if (!$item) {
            return 'empty';
        }

        if ($item) {
            return 'not empty';
        }
    }
}
```

**After**

```php
final class ExplicitString
{
    public function run(string $item)
    {
        if ($item === '' || $item === '0') {
            return 'empty';
        }

        if ($item !== '' && $item !== '0') {
            return 'not empty';
        }
    }
}
```

Parameters: none.

### FluentSettersToStandaloneCallMethodRector

Splits supported fluent setter chains into standalone calls on the same object. When a newly created object is returned, it introduces a variable and returns it after the setters. Chains involving getters or unsupported object types are skipped.

**Before**

```php
use App\SomeSetterClass;

final class SomeClass
{
    public function setup()
    {
        return (new SomeSetterClass())
            ->setName('John')
            ->setSurname('Doe');
    }
}
```

**After**

```php
use App\SomeSetterClass;

final class SomeClass
{
    public function setup()
    {
        $someSetterClass = new SomeSetterClass();
        $someSetterClass->setName('John');
        $someSetterClass->setSurname('Doe');
        return $someSetterClass;
    }
}
```

Parameters: none.

### JsonThrowOnErrorRector

Adds `JSON_THROW_ON_ERROR` to supported `json_encode()` and `json_decode()` calls, combining it with existing constant flags and supplying missing decode defaults. Calls using named arguments, first-class callables, or statically resolved string/array inputs are skipped, as are enclosing statements containing `json_last_error()` or `json_last_error_msg()`. Errors then throw `JsonException` instead of returning `false` or `null`.

**Before**

```php
json_encode($content);
json_decode($json);
```

**After**

```php
json_encode($content, JSON_THROW_ON_ERROR);
json_decode($json, null, 512, JSON_THROW_ON_ERROR);
```

Parameters: none.

### NestedFuncCallsToPipeOperatorRector

Converts supported nested function calls in assignments or returns into a PHP 8.5 pipe chain. The inner value becomes the initial operand, and the functions run from the innermost call outward. Unsupported argument layouts and chains below the configured depth are skipped.

**Before**

```php
final class NestedFunctions
{
    public function run()
    {
        $result = trim(strtolower(htmlspecialchars('  Hello World!  ')));
    }
}
```

**After**

```php
final class NestedFunctions
{
    public function run()
    {
        $result = '  Hello World!  '
            |> htmlspecialchars(...)
            |> strtolower(...)
            |> trim(...);
    }
}
```

Parameters:

- `minimum_depth` (int, default: `2`, minimum: `2`) — minimum number of nested calls required for a pipe chain.

```php
use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;
use Vix\RectorRules\LegacyRector\NestedFuncCallsToPipeOperatorRector;

return RectorConfig::configure()
    ->withPhpVersion(PhpVersion::PHP_85)
    ->withConfiguredRule(NestedFuncCallsToPipeOperatorRector::class, [
        NestedFuncCallsToPipeOperatorRector::MINIMUM_DEPTH => 3,
    ]);
```

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

### NewInInitializerRector

Moves a constructor assignment such as `$this->logger = $logger ?? new NullLogger` into a `new` parameter default and promotes the matching property. It preserves property visibility and attributes, and skips unsupported constructor contracts, complex initializers, or parameters used later. Requires PHP 8.1 or newer. Explicit `null` no longer triggers the original fallback assignment.

**Before**

```php
class SomeClass
{
    private Logger $logger;

    public function __construct(
        ?Logger $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger;
    }
}
```

**After**

```php
class SomeClass
{
    public function __construct(private ?Logger $logger = new NullLogger)
    {
    }
}
```

Parameters: none.

### RemoveNamedArgsInDataProviderRector

Removes argument-array keys from recognized PHPUnit data providers so values are supplied positionally. Providers referenced through PHPDoc or `#[DataProvider]` are supported. Named yielded datasets retain their labels; unreferenced providers and non-test classes are skipped.

**Before**

```php
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AttributeDataProvider extends TestCase
{
    #[DataProvider('provideValues')]
    public function testValue(int $value, string $label): void
    {
        self::assertGreaterThan(0, $value);
    }

    public static function provideValues(): iterable
    {
        yield 'first case' => ['value' => 100, 'label' => 'first'];
        yield 'second case' => ['value' => 200, 'label' => 'second'];
    }
}
```

**After**

```php
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AttributeDataProvider extends TestCase
{
    #[DataProvider('provideValues')]
    public function testValue(int $value, string $label): void
    {
        self::assertGreaterThan(0, $value);
    }

    public static function provideValues(): iterable
    {
        yield 'first case' => [100, 'first'];
        yield 'second case' => [200, 'second'];
    }
}
```

Parameters: none.

### RenameDeprecatedMethodCallRector

Renames instance and static method calls when the target method is deprecated and its description suggests an existing, non-deprecated method on the same class. Supported suggestions include `Use newMethod()`, `replaced by newMethod()`, and `{@see newMethod()}`. Cross-class suggestions, magic methods, dynamic names, and first-class callables are skipped. In this example, `getData()` is annotated with `@deprecated Use fetchData() instead` and `fetchData()` exists on the same class.

**Before**

```php
$data = $apiClient->getData();
```

**After**

```php
$data = $apiClient->fetchData();
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

### ReturnBinaryOrToEarlyReturnRector

Splits supported `return` expressions joined with `||` into early `return true` guards and a final return. The remaining expression is cast to `bool` when its type does not already ensure a boolean result. Unsupported expressions without a matching object call are skipped.

**Before**

```php
class Fixture
{
    public function accept()
    {
        return $this->something() || $this->somethingElse();
    }
}
```

**After**

```php
class Fixture
{
    public function accept()
    {
        if ($this->something()) {
            return true;
        }
        return (bool) $this->somethingElse();
    }
}
```

Parameters: none.

### SequentialAssignmentsToPipeOperatorRector

Collapses supported consecutive single-argument function-call assignments into a PHP 8.5 pipe chain. Each call must consume the previous assigned variable. The final variable receives the chain result, and intermediate assignments disappear; single calls and unsupported assignment shapes are skipped.

**Before**

```php
$value = "hello world";
$result1 = function3($value);
$result2 = function2($result1);
$result = function1($result2);
```

**After**

```php
$value = "hello world";
$result = $value
    |> function3(...)
    |> function2(...)
    |> function1(...);
```

Parameters: none.

### ShortenElseIfRector

Rewrites an `else` block containing only one `if` into `elseif`, preserving supported comments and nested branches. Blocks with additional statements, alternative syntax, or embedded HTML are left unchanged.

**Before**

```php
if ($first) {
    process();
} else {
    if ($second) {
        process();
    }
}
```

**After**

```php
if ($first) {
    process();
} elseif ($second) {
    process();
}
```

Parameters: none.

### SimplifyIfElseToTernaryRector

Replaces an `if`/`else` whose branches each assign to the same target with one ternary assignment. It skips `elseif` branches, nested ternaries, overly long output, and branches whose comments cannot be preserved.

**Before**

```php
class Fixture
{
    public function run()
    {
        if (empty($value)) {
            $this->arrayBuilt[][$key] = true;
        } else {
            $this->arrayBuilt[][$key] = $value;
        }
    }
}
```

**After**

```php
class Fixture
{
    public function run()
    {
        $this->arrayBuilt[][$key] = empty($value) ? true : $value;
    }
}
```

Parameters: none.

### SwitchNegatedTernaryRector

Removes a leading negation from a long ternary condition and swaps the two result branches. Short ternaries and conditions without the supported negation are left unchanged.

**Before**

```php
class Fixture
{
    public function run(bool $upper, string $name)
    {
        return ! $upper
            ? $name
            : strtoupper($name);
    }
}
```

**After**

```php
class Fixture
{
    public function run(bool $upper, string $name)
    {
        return $upper
            ? strtoupper($name)
            : $name;
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

Adds the related model type to an `ActiveQuery` return annotation for Yii2 `hasOne()` and `hasMany()` relations, including relation query chains such as `viaTable()`. The method must return `yii\db\ActiveQuery`, have an exact `@return ActiveQuery` annotation, and contain a single relation return using `Model::class`. Existing generics, dynamic model classes, and other query types are unchanged.

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

Adds missing `@property`, `@property-read`, and `@property-write` tags for public Yii2 magic accessors declared by a `yii\base\BaseObject` subclass. Types come from method PHPDoc or native signatures. For `yii\db\BaseActiveRecord`, `hasOne()` getters become nullable related-model properties and `hasMany()` getters become related-model arrays, including relations with query chains such as `viaTable()`. Classes with custom `__get()` or `__set()` implementations are skipped.

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

### Yii2MergeModelRulesRector

Merges `yii\base\Model::rules()` entries that use the same validator and identical options into one entry. Attributes from string and literal attribute-array forms are combined without duplicates. It only changes a `rules()` method whose body is a single `return [...]` containing literal rule arrays; dynamic entries, spreads, and other method bodies are left unchanged.

**Before**

```php
final class LoginForm extends \yii\base\Model
{
    public function rules(): array
    {
        return [
            ['login', 'required'],
            ['password', 'required'],
            ['email', 'string', 'max' => 255],
        ];
    }
}
```

**After**

```php
final class LoginForm extends \yii\base\Model
{
    public function rules(): array
    {
        return [
            [['login', 'password'], 'required'],
            ['email', 'string', 'max' => 255],
        ];
    }
}
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
