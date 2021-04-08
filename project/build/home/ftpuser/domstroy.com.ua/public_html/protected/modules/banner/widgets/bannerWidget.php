<?php
Yii::import('application.modules.banner.models.*');
/**
 * Created by PhpStorm.
 * User: Viking
 * Date: 23.08.2016
 * Time: 15:18
 */
class BannerWidget extends \yupe\widgets\YWidget {

    public $view = 'view';

    public $position = 1;

    public $priority = 1;

    public $data = [];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('status = 1');
        $criteria->addCondition('position = '.$this->position);
        //$criteria->limit = 1;
        $criteria->order ='RAND(), priority DESC';
        $banner = Banner::model()->find($criteria);
        $date = date("Y-m-d");
        if(null !== $banner){
            $criteria = new CDbCriteria;
            $criteria->compare('banner_id',$banner->id);
            $criteria->compare('date',$date);
            $viewed = BannerViews::model()->find($criteria);
            if (!$viewed) {
              $viewed = new BannerViews;
              $viewed->banner_id = $banner->id;
              $viewed->date = $date;
            }
            $viewed->showed++;
            $viewed->save(false);
            //$views = $model->showed;
            //$model->showed = $views+1;
            //$model->save(false,'showed');
        }
        
        $this->render($this->view,['model'=>$banner]);
    }

}