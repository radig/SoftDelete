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
 * @copyright 2011, Radig - Soluções em TI, www.radig.com.br
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
	
	public function setup($model, $config = array())
	{
		$this->settings[$model->alias] = Set::merge($this->defaultSettings, $config);
	}
	
	/**
	 * Adiciona condição de registros ativos nas buscas do modelo
	 * @param Model $model
	 * @param array $queryData
	 * @see ModelBehavior::beforeFind()
	 */
	public function beforeFind($model, $queryData)
	{
		parent::beforeFind($model, $queryData);
		
		$f = $this->settings[$model->alias]['field'];
		$c = $model->alias . '.' . $f;

		$schema = $model->schema();
	
		// Verifica o modelo corrente se possui o campo de busca
		if(isset($schema[$f]) && !isset($queryData['conditions'][$c]))
		{
			$queryData['conditions'][$c] = false;
		}
		return $queryData;
	}
	
	/**
	 * Implementa soft-delete para modelos da aplicação
	 * 
	 * @param Model 
	 * @param int $id
	 */
	public function softDelete(&$model, $id)
	{
		$data = array('id' => $id, 'deleted' => true);
		$model->id = $id;
			
		return $model->save($data);
	}
}