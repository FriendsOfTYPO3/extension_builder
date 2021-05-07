<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace EBT\ExtensionBuilder\Domain\Model;

class NamespaceObject extends Container
{
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param array $preIncludes
     */
    public function setPreIncludes($preIncludes): void
    {
        $this->preIncludes = $preIncludes;
    }

    /**
     * @param array $postIncludes
     */
    public function setPostIncludes($postIncludes): void
    {
        $this->postIncludes = $postIncludes;
    }
}
