<?php
/**
 * Behavior que implementa exclusão lógica de registros
 *
 * Uso: deve existir uma coluna na tabela do modelo utilizado que indique se o registro
 * está ativo ou não (valor binário do tipo boolean, integer, tinyint, etc). Por default
 * o nome dessa coluna é 'active'. Se outro nome for utilizado, deve ser informado ao behavior
 * através do atributo 'field' das configurações do behavior.
 * A exclusão de registros é feita através do método softDelete().
 *
 * Qualquer busca feita pelos registros que utilizem o behavior SoftDelete somente incluem os
 * registros cujo campo active seja true (1)
 *
 * PHP version > 5.3.1
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2011-2012, Radig - Soluções em TI, www.radig.com.br
 * @link http://www.radig.com.br
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * @package radig
 * @subpackage SoftDelete.Models.Behaviors
 */
 class SoftDeleteBehavior extends ModelBehavior
{
	public $defaultSettings = array(
		'field' => 'deleted'
	);

	public function setup(Model $Model, $config = array())
	{
		$this->settings[$Model->alias] = Set::merge($this->defaultSettings, $config);
	}

	/**
	 * Adiciona condição de registros ativos nas buscas do modelo
	 * @param Model $Model
	 * @param array $queryData
	 * @see ModelBehavior::beforeFind()
	 */
	public function beforeFind(Model $Model, $queryData)
	{
		parent::beforeFind($Model, $queryData);
		$this->_prepareFind($queryData, $Model);

		return $queryData;
	}

	/**
	 * Informa se o registro existe e esta ativo (não deletado)
	 * @param  Model $Model
	 * @param  int   $id    ID do registro
	 * @return bool
	 */
	public function active($Model, $id)
	{
		return (bool)$Model->find('count', array(
			'conditions' => array(
				$Model->alias . '.' . $Model->primaryKey => $id,
				$Model->alias . '.' . $this->settings[$Model->alias]['field'] => false
			),
			'recursive' => -1,
			'callbacks' => false
		));
	}

	/**
	 * Implementa soft-delete para modelos da aplicação
	 *
	 * @param Model $Model
	 * @param int $id
	 */
	public function softDelete(&$Model, $id)
	{
		$Model->id = $id;

		return ($Model->saveField($this->settings[$Model->alias]['field'], true) !== false);
	}

	/**
	 * Método para "desdeletar" uma entrada do modelo.
	 *
	 * @param Model $Model
	 * @param int $id
	 */
	public function unDelete(&$Model, $id)
	{
		$Model->id = $id;

		return ($Model->saveField($this->settings[$Model->alias]['field'], false) !== false);
	}

	private function _prepareFind(&$query, &$Model)
	{
		$fieldName = $this->settings[$Model->alias]['field'];
		$field =  $Model->alias . '.' . $fieldName;
		$schema = $Model->schema();
		$associateds = $Model->getAssociated();

		if (isset($schema[$fieldName])) {
			$query['conditions'][$field] = false;
		}

		if (empty($associateds)) {
			return;
		}

		foreach ($associateds as $associated => $type) {
			if (!$Model->{$associated}->Behaviors->attached('SoftDelete')) {
				continue;
			}

			$config = $Model->{$type}[$associated];

			$afield = $this->settings[$associated]['field'];
			$aschema = $Model->{$associated}->schema();

			if (isset($aschema[$afield])) {
				$config['conditions'][$associated . '.' . $afield] = false;
			}

			$Model->unbindModel(array($type => array($associated)));

			$Model->bindModel(array(
				$type => array(
					$associated => $config
				)
			));
		}
	}
}
