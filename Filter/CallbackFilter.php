<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\PropelAdminBundle\Filter;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;

class CallbackFilter extends AbstractFilter
{
	/**
	 * {@inheritdoc}
	 */
	protected function association(ProxyQueryInterface $queryBuilder, $data)
	{
		return array($this->getOption('alias', $queryBuilder->getRootAlias()), false);
	}

	/**
	 * {@inheritdoc}
	 */
	public function filter(ProxyQueryInterface $query, $alias, $field, $data)
	{
		if (!is_callable($this->getOption('callback'))) {
			throw new \RuntimeException(sprintf('Please provide a valid callback option "filter" for field "%s"', $this->getName()));
		}

		$this->active = call_user_func($this->getOption('callback'), $query, $alias, $field, $data);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultOptions()
	{
		return array(
			'callback'    => null,
			'field_type'  => 'text',
			'operator_type' => 'hidden',
			'operator_options' => array()
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRenderSettings()
	{
		return array('sonata_type_filter_default', array(
			'field_type'    => $this->getFieldType(),
			'field_options' => $this->getFieldOptions(),
			'operator_type' => $this->getOption('operator_type'),
			'operator_options' => $this->getOption('operator_options'),
			'label'         => $this->getLabel()
		));
	}

	/**
	 * Return the mapping between the selected filter type and the criteria comparison.
	 *
	 * @return array
	 */
	protected function getCriteriaMap()
	{
		return array(
			ChoiceType::TYPE_EQUAL          => ModelCriteria::EQUAL,
		);
	}
}
