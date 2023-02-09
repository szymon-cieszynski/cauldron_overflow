<?php

namespace App\Factory;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Question>
 *
 * @method        Question|Proxy create(array|callable $attributes = [])
 * @method static Question|Proxy createOne(array $attributes = [])
 * @method static Question|Proxy find(object|array|mixed $criteria)
 * @method static Question|Proxy findOrCreate(array $attributes)
 * @method static Question|Proxy first(string $sortedField = 'id')
 * @method static Question|Proxy last(string $sortedField = 'id')
 * @method static Question|Proxy random(array $attributes = [])
 * @method static Question|Proxy randomOrCreate(array $attributes = [])
 * @method static QuestionRepository|RepositoryProxy repository()
 * @method static Question[]|Proxy[] all()
 * @method static Question[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Question[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Question[]|Proxy[] findBy(array $attributes)
 * @method static Question[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Question[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class QuestionFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function unpublished(): self
    {
        return $this->addState(['askedAt'=>null]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->realText(50),
            //'slug' => self::faker()->slug(),
            'question' => self::faker()->paragraphs(
                self::faker()->numberBetween(1,4),
                true
            ),
            'askedAt' => self::faker()->dateTimeBetween('-100 days', '-1 minute'),
            'votes' => rand(-20, 50),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        //funkcja wykonujaca sie za kazdym zapisaniem danych z funkcji wyzej, tutaj jesli damy wlasnego sluga to zapisze sluga powiazanego z tytulem pytania..
        // a nie jak wczesniej randowmowy ciag tekstu
        return $this
//            ->afterInstantiate(function(Question $question) {
//                if (!$question->getSlug()) {
//                    $slugger = new AsciiSlugger();
//                    $question->setSlug($slugger->slug($question->getName()));
//                }
//            })
            ;
    }

    protected static function getClass(): string
    {
        return Question::class;
    }
}
