<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.26
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

Loader::loadClass('Base.Controller.ControllerError');

/**
 * class ControllerModel
 * 
 * main class for all controllers
 * containing basic methods and properties
 * for model
 * 
 * @package     Base
 * @category    Controllers
 * @author      amostovoy
 * @abstract
 */
abstract class ControllerModel extends ControllerError
{
    /**
     * Return controller asociated model
     * @return Model
     */
    public final function getCModel()
    {
        return $this->model;
    }
	/**
	 * load model class and create object var
	 * if model already loaded - skip it and go next given model
	 *
	 * @param mixed $models array or string ',' separated model names
     * @param bool  $site_part (optional d:true) if it set to true file will be loaded from site part directory
     * @return bool return true if all was loaded
	 */
	protected final function loadModel($models, $site_part=true)
	{
		if ( is_string( $models ) )
		{
			$models = str_replace(' ', '', $models);
			$models = explode(',', $models);
		}

		foreach ($models as $model_file)
		{
            $model = explode('/', $model_file);
            $model_name = array_pop( $model );
			$varName = 'mod_'.strtolower($model_name);
			if ( !isset($this->overloaded_data[$varName]) )
			{
//                Loader::loadModel($model_file, $site_part);
                
                $this->overloaded_data[$varName] = App::model($model_file, $site_part);

//				$modelClass = Request::MODEL_PREFIX.ucfirst($model_name);
//				$this->overloaded_data[$varName] = new $modelClass($this->paging());
			}
            $models_vars[] = $varName;
		}
        if (count($models_vars) > 1)
        {
            return $models_vars;
        }
        else 
        {
            return $models_vars[0];
        }
	}
    /**
     * Create main model for current controller
     * Store model object into model property
     * @param string $model model name.
     * @param bool $site_part using site part or not during include model file
     */
    protected function createControllerModel($model, $site_part=true)
    {
        $this->loadModel($model, $site_part);
        $this->model = $this->{'mod_' . strtolower($model)};
    }
}
/* End of file ControllerModel.php */
/* Location: ./class/Base/Controller/ControllerModel.php */
?>