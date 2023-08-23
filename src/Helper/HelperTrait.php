<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Helper;

/**
 * The PDF library must use this trait if it needs to access helper classes.
 */
trait HelperTrait
{
    /**
     * @var Generator The generator object
     */
    public $generator;
}
