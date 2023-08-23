<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Helper;

/**
 * Provides the hooks for helpers
 *
 * Concrete Helper classes must extend this class
 *
 * @author Chris Yates
 */
abstract class PDF
{
    /** @property-write \BeastBytes\Pdf\Pdf $generator The PDF generator object */

    /**
     * @var \BeastBytes\Pdf\Pdf The PDF generator object
     */
    private $generator;

    /**
     * Sets the generator
     *
     * @param Generator $generator The generator
     */
    public function setGenerator(Generator $generator): void
    {
        $this->generator = $generator;
    }

    /**
     * Called if an unrecognised method is called
     *
     * Look for the method in the PDF library class and then the generator; if the method is not found pass to the
     * parent to resolve.
     *
     * Do not call this method directly as it is a PHP magic method that will be implicitly called when an unknown
     * method is being invoked.
     *
     * @param string $name Method name
     * @param array $params Method parameters
     * @return mixed Result of the method call
     */
    public function __call($name, $params)
    {
        if (method_exists($this->generator->pdf, $name)) {
            return call_user_func_array([$this->generator->pdf, $name], $params);
        }

        if (method_exists($this->generator, $name)) {
            return call_user_func_array([$this->generator, $name], $params);
        }

        return parent::__call($name, $params);
    }
}
