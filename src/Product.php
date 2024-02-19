<?php

/**
 *  Post Model
 *
 * @author Socheat <https://github.com/socheatsok78>
 */

namespace Wpml;

use Wpml\Translation\IsTranslatable;
use Illuminate\Database\Eloquent\Builder;

class Product extends \Corcel\WooCommerce\Model\Product
{
    use IsTranslatable;

}
