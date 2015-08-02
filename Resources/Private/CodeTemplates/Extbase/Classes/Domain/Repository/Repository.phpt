<?php
namespace VENDOR\Package\Domain\Repository;

/**
 *  DomainObject Repository
 */
class DomainObjectRepository {

	/**
	 * @var array
	 */
	protected $defaultOrderings = array(
		'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
	);

}