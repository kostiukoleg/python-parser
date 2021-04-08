<?php
Yii::import('application.models.CompanyReit');
Yii::import('application.modules.currency.models.*');
Yii::import('application.modules.measurements.models.*');
class MasterController extends \yupe\components\controllers\FrontController
{
	/**
	 * Отображает компанию по указанному идентификатору
	 *
	 * @param integer $id Идинтификатор компанию для отображения
	 *
	 * @return void
	 */
	public function actionView($slug)
	{
	    Yii::import('application.modules.gallery.models.Gallery');

	    $id = explode('-', $slug)[0];
	    $model = $this->loadCompany($id);

	    $correct_slug = Company::getSlug($model);
	    if (Yii::app()->request->requestUri!==$correct_slug) {
			$this->redirect($correct_slug, true, 301);
		}
	    $gallery = null;
	    if(null !==$model){
	        $gallery = Gallery::model()->find(['condition'=>'owner='.$model->owner_id]);
        }
		$this->render('view', ['model' => $model,'gallery'=>$gallery]);
	}

	/**
	 * Управление компаниями.
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$this->layout = '//layouts/company';
		$model = new Company();

		$model->unsetAttributes(); // clear any default values
		if (Yii::app()->getRequest()->getParam('Company') !== null)
			$model->setAttributes(Yii::app()->getRequest()->getParam('Company'));
		$this->render(
			'index',
			[
				'dataProvider' => $model->search(['page_size' => 10, 'pagination' => true,'sup_type'=>'2']),
				//'dataProviderTop' => $model->search(['limit' => 5]),
				'model' => $model,
			]
		);
	}

	/**
	 * Возвращает модель по указанному идентификатору
	 * Если модель не будет найдена - возникнет HTTP-исключение.
	 *
	 * @param integer идентификатор нужной модели
	 *
	 * @return void
	 */
	public function loadModel($id)
	{
		$model = Company::model()->findByPk($id);

		if ($model === null)
			throw new CHttpException(404, Yii::t('CompanyModule.company', 'Запрошенная страница не найдена.'));

		return $model;
	}
	public function loadCompany($id)
	{

		$model = Company::model()->with('owner')->findByPk($id);
		/*$criteria2 = new CDbCriteria();
		$criteria2->addCondition(array(
			'company_id ='.$model->id,
			'sup_type = 1'
		));
		$criteria2->limit = 20;
		$model->countProduct = Product::model()->count($criteria2);
		$criteria2->with =['producer', 'manufacturer'];
		$model->products2 = Product::model()->findAll($criteria2);

		$criteria3 = new CDbCriteria();
		$criteria3->addCondition(array(
			'company_id ='.$model->id,
			'sup_type = 2'
		));
		$criteria3->limit = 20;
		$model->countProduct2 = Product::model()->count($criteria3);
		$criteria3->with =['producer', 'manufacturer'];
		$model->services = Product::model()->findAll($criteria3);*/

		if ($model === null || $model->is_active == 0)
            $this->redirect(Yii::app()->createUrl('/master'));
			//throw new CHttpException(404, Yii::t('CompanyModule.company', 'Запрошенная страница не найдена.'));

		return $model;
	}
}
