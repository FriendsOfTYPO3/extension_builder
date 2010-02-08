<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Tx_ExtbaseKickstarter_ViewHelpers_OpeningTagViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {
	public function render() {
		return '<' . $this->renderChildren() . '>';
	}
}
?>
