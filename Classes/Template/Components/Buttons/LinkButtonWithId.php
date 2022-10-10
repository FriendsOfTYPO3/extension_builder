<?php

declare(strict_types=1);

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

namespace EBT\ExtensionBuilder\Template\Components\Buttons;

use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;

class LinkButtonWithId extends LinkButton
{
    /**
     * id attribute of the link
     */
    protected string $id = '';

    /**
     * Get type
     * Pretend that we are a link button to make the button valid
     */
    public function getType(): string
    {
        return LinkButton::class;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Renders the markup for the button
     */
    public function render(): string
    {
        $attributes = [
            'href' => $this->getHref(),
            'class' => 'btn btn-default btn-sm ' . $this->getClasses(),
            'id' => $this->getId(),
            'title' => $this->getTitle()
        ];
        $labelText = '';
        if ($this->showLabelText) {
            //  $labelText = ' <span class="simpleMode">Show</span><span class="advancedMode">Hide</span> ' . $this->title . '.';
            $labelText = ' ' . $this->title;
        }
        foreach ($this->dataAttributes as $attributeName => $attributeValue) {
            $attributes['data-' . $attributeName] = $attributeValue;
        }
        if ($this->isDisabled()) {
            $attributes['disabled'] = 'disabled';
            $attributes['class'] .= ' disabled';
        }
        $attributesString = '';
        foreach ($attributes as $key => $value) {
            $attributesString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value ?? '') . '"';
        }

        return '<a ' . $attributesString . '>'
            . $this->getIcon()->render() . htmlspecialchars($labelText)
        . '</a>';
    }
}
