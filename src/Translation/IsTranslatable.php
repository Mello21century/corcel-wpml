<?php

namespace Wpml\Translation;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Wpml\PostBuilder;



/**
 * @method Builder inLanguage(string $lang)
 * @property Translation $wpml
 */
trait IsTranslatable
{

    public array $extraAppends = ['language'];

    /**
     * @return string[]
     */
    public function getWith(): array
    {
        dd($this->with);
        return array_merge($this->with, ['wpml', 'meta']);
    }

    protected function getArrayableAppends(): array
    {
        return array_merge(parent::getArrayableAppends(), $this->extraAppends);
    }

    public function getAppends(): array
    {
        return array_merge($this->appends, ['language']);
    }

    /**
     * Translation data relationship.
     *
     * @return HasOne
     */
    public function wpml(): HasOne
    {
        return $this->hasOne(Translation::class, 'element_id');
    }

    /**
     * Gets the value.
     * Tries to un serialize the object and returns the value if that doesn't work.
     *
     * @return string
     */
    public function getLanguageAttribute(): ?string
    {
        return $this->wpml?->language_code;
    }


    /**
     * Scope a query for translated posts.
     *
     * @return TranslationCollection
     */
    public function scopeTranslation(): TranslationCollection
    {
        // Find Translation Group ID

        $element = Translation::where('element_id', $this->ID)->first();

        // Find Translation collection
        return Translation::where('trid', $element->trid)->get();
    }

    public function scopeInLanguage(Builder $query, $lang = 'en'): Builder
    {
        return $query
            ->whereHas('wpml',fn(Builder $q) => $q->where('language_code',$lang));
    }
    /**
     * Overriding newQuery() to the custom PostBuilder with some interesting methods.
     *
     * @param bool $excludeDeleted
     *
     * @return PostBuilder
     */
    public function newQuery(bool $excludeDeleted = true): PostBuilder
    {
        $builder = new PostBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with('wpml')->with($this->with);
        // disabled the default orderBy because else Post::all()->orderBy(..)
        // is not working properly anymore.
        // $builder->orderBy('post_date', 'desc');
        if (isset($this->postType) and $this->postType) {
            $builder->type($this->postType);
        }
        if ($excludeDeleted and $this->softDelete) {
            $builder->whereNull($this->getQualifiedDeletedAtColumn());
        }
        // dump(['newQuery ' => $this]);
        return $builder;
    }
}
