{namespace k=Tx_ExtensionBuilder_ViewHelpers}
/**
 *<f:if condition="{classSchema}">
 * {classSchema.description}</f:if>
 *
 * @package {extension.extensionKey}
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *<f:for each="{classSchema.annotations}" as="annotation">
 * @{annotation}</f:for>
 */