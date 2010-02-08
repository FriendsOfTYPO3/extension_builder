<?php

/***************************************************************
*  Copyright notice
*
*  (c) <f:format.date format="Y">now</f:format.date> <f:for each="{extension.persons}" as="person">{person.name} <f:if condition="{person.email}"><{person.email}></f:if><f:if condition="{person.company}">, {person.company}</f:if>
*  			</f:for>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Controller for the {domainObject.name} object
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}
// TODO: As your extension matures, you should use Tx_Extbase_MVC_Controller_ActionController as base class, instead of the ScaffoldingController used below.
class {domainObject.controllerName} extends Tx_ExtbaseKickstarter_Scaffolding_AbstractScaffoldingController {
	
	<f:for each="{domainObject.actions}" as="action">
	/**
	 * {action.name} action
	 *
	 * @return string The rendered {action.name} action
	 */
	public function {action.name}Action() <![CDATA[{]]>
	<![CDATA[}]]>
	</f:for>
}
?>
