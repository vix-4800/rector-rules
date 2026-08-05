<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\Yii2\Yii2AddRelationQueryGenericRector;

/**
 * @internal
 */
#[CoversClass(Yii2AddRelationQueryGenericRector::class)]
final class Yii2AddRelationQueryGenericRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideRelationCases')]
    #[Test]
    public function addsRelatedModelToActiveQueryReturnType(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideRelationCases(): iterable
    {
        yield 'has one relation' => [
            <<<'PHP'
                <?php

                use yii\db\ActiveQuery;

                final class Book
                {
                    /**
                     * @return ActiveQuery Query for the book author.
                     */
                    public function getAuthor(): ActiveQuery
                    {
                        return $this->hasOne(Author::class, ['id' => 'author_id']);
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                use yii\db\ActiveQuery;

                final class Book
                {
                    /**
                     * @return ActiveQuery<Author> Query for the book author.
                     */
                    public function getAuthor(): ActiveQuery
                    {
                        return $this->hasOne(Author::class, ['id' => 'author_id']);
                    }
                }
                PHP,
        ];

        yield 'has many relation' => [
            <<<'PHP'
                <?php

                use yii\db\ActiveQuery;

                final class Author
                {
                    /**
                     * @return ActiveQuery
                     */
                    public function getBooks(): ActiveQuery
                    {
                        return $this->hasMany(Book::class, ['author_id' => 'id']);
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                use yii\db\ActiveQuery;

                final class Author
                {
                    /**
                     * @return ActiveQuery<Book>
                     */
                    public function getBooks(): ActiveQuery
                    {
                        return $this->hasMany(Book::class, ['author_id' => 'id']);
                    }
                }
                PHP,
        ];

        yield 'relation to model in the same namespace' => [
            <<<'PHP'
                <?php

                namespace App\Models;

                use yii\db\ActiveQuery;

                final class Book
                {
                    /**
                     * @return ActiveQuery
                     */
                    public function getAuthor(): ActiveQuery
                    {
                        return $this->hasOne(Author::class, ['id' => 'author_id']);
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                namespace App\Models;

                use yii\db\ActiveQuery;

                final class Book
                {
                    /**
                     * @return ActiveQuery<App\Models\Author>
                     */
                    public function getAuthor(): ActiveQuery
                    {
                        return $this->hasOne(Author::class, ['id' => 'author_id']);
                    }
                }
                PHP,
        ];
    }

    #[DataProvider('provideSkippedCases')]
    #[Test]
    public function skipsUnsupportedRelations(string $input): void
    {
        $this->doTestCode($input);
    }

    public static function provideSkippedCases(): iterable
    {
        yield 'already generic return type' => [
            <<<'PHP'
                <?php

                use yii\db\ActiveQuery;

                final class Book
                {
                    /**
                     * @return ActiveQuery<Author>
                     */
                    public function getAuthor(): ActiveQuery
                    {
                        return $this->hasOne(Author::class, ['id' => 'author_id']);
                    }
                }
                PHP,
        ];

        yield 'dynamic related model class' => [
            <<<'PHP'
                <?php

                use yii\db\ActiveQuery;

                final class Book
                {
                    /**
                     * @return ActiveQuery
                     */
                    public function getAuthor(): ActiveQuery
                    {
                        return $this->hasOne($this->authorClass, ['id' => 'author_id']);
                    }
                }
                PHP,
        ];

        yield 'different return type' => [
            <<<'PHP'
                <?php

                final class Book
                {
                    /**
                     * @return \yii\db\Query
                     */
                    public function getAuthor(): \yii\db\Query
                    {
                        return $this->hasOne(Author::class, ['id' => 'author_id']);
                    }
                }
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return Yii2AddRelationQueryGenericRector::class;
    }
}
