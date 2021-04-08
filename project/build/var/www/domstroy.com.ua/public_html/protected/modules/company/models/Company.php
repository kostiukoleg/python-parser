<?php
Yii::import('zii.behaviors.CTimestampBehavior');
Yii::import('application.modules.comment.components.ICommentable');
Yii::import('application.modules.yupe.components.behaviors.ImageUploadBehavior');


/**
 * This is the model class for table "{{company}}".
 *
 * The followings are the available columns in table '{{company}}':
 * @property integer $id
 * @property integer $owner_id
 * @property integer $type
 * @property integer $experience
 * @property integer $product_comments
 * @property string $name
 * @property string $activities
 * @property string $delivery
 * @property string $terms
 * @property string $additional
 * @property string image
 * @property integer $is_active
 * @property integer $sup_type
 * @property integer $business_line1
 * @property integer $business_line2
 * @property bool $is_top
 * @property integer $top_level
 * @property string $top_from
 * @property string $top_to
 * @property integer $locale_r
 * @property integer $locale_c
 * @property integer $position
 * @property integer $created
 * @property string $busyness_line
 * @property string $main_line
 * @property string $updated
 * @property integer $plan
 *
 * The followings are the available model relations:
 * @property User $owner
 * @property CompanyContacts $companyContacts
 * @property CompanySchedule $companySchedules
 * @property Product[] $products
 *
 *
 * @property array $products2
 * @property array $services
 * @property string $countProduct
 *
 * @method getImageUrl($width = 0, $height = 0, $crop = true, $defaultImage = null)

 */

class Company extends yupe\models\YModel
{
    public $items;
    /**
     * @var array
     */
    public $products2 = array();
    /**
     * @var array
     */
    public $services = array();

    /**
     * @var string
     */
    public $countProduct;
    public $countProduct2;

    /*public function init(){
        parent::init();
    }*/
    /**
     * @return string the associated database table name
     */

    public function tableName()
    {
        return '{{company}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules =  array(
            array('owner_id', 'required','on'=>'create, update'),
            array('name', 'required','on'=>'update'),
            array('owner_id, busyness_line1, busyness_line2, is_active, type, experience, product_comments, sup_type, top_level', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>50),
            array('terms', 'length', 'max'=>4000),
            array('activities', 'length', 'max'=>4000),
            ['id,manager_id', 'filter', 'filter' => 'trim'],
            /*array('activities, additional, terms',
                'filter',
                'filter' => [$obj = new CHtmlPurifier(), 'purify']
            ),*/
            array('terms, additional, image, id, is_top, delivery, created, updated, busyness_line, main_line,locale_r,locale_c', 'safe'),
            array('id, type, experience, product_comments, name, activities, delivery, terms, additional, is_active, sup_type, is_top, top_level, products, created, owner_id, items', 'safe', 'on'=>'search'),
        );
        if ($this->sup_type != 3) {
            $rules[] = array('main_line, locale_r', 'required', 'on'=>'update');
        } else {
            $rules[] = array('locale_r', 'required', 'on'=>'update');
        }

        return $rules;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'owner' => array(self::BELONGS_TO, 'User', 'owner_id'),
            'companyContacts' => array(self::HAS_ONE, 'CompanyContacts', 'company_id'),
            'companySchedules' => array(self::HAS_ONE, 'CompanySchedule', 'company_id'),
            'products' => array(self::HAS_MANY, 'Product','company_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        if($this->sup_type == 1)
            return array(
                'id' => Yii::t('CompanyModule.company','company inner id'),
                'owner_id' => Yii::t('CompanyModule.company','company owner'),
                'manager_id' => 'Персональный менеджер',
                'type' => Yii::t('CompanyModule.company','company type'),
                'experience' => Yii::t('CompanyModule.company','company experience'),
                'product_comments' => Yii::t('CompanyModule.company','is company product comments allowed'),
                'name' => Yii::t('CompanyModule.company','company name'),
                'activities' => Yii::t('CompanyModule.company','description of activities'),
                'activities_site' => Yii::t('CompanyModule.company','description of activities site'),
                'delivery' => Yii::t('CompanyModule.company','product delivery cities list'),
                'terms' => Yii::t('CompanyModule.company','payment and delivery terms'),
                'additional' => Yii::t('CompanyModule.company','additional information'),
                'image' => Yii::t('CompanyModule.company','company logo'),
                'is_active' => Yii::t('CompanyModule.company','company active?'),
                'sup_type' => Yii::t('CompanyModule.company','super type'),
                'is_top' => Yii::t('CompanyModule.company','is Top'),
                'top_level' => Yii::t('CompanyModule.company','Top level'),
                'top_from' => Yii::t('CompanyModule.company','Top from'),
                'top_to' => Yii::t('CompanyModule.company','Top to'),
                'busyness_line' => Yii::t('CompanyModule.company','Busyness line'),
                'busyness_line1' => Yii::t('CompanyModule.company','Busyness line'),
                'busyness_line2' => Yii::t('CompanyModule.company','Busyness line'),
                'main_line' => Yii::t('CompanyModule.company','Main line'),
                'locale_r' => Yii::t('CompanyModule.company','LocaleR'),
                'locale_c' => Yii::t('CompanyModule.company','LocaleC'),
            );
        if($this->sup_type == 2)
            return array(
                'id' => Yii::t('CompanyModule.company','company inner id'),
                'owner_id' => Yii::t('CompanyModule.company','company owner'),
                'manager_id' => 'Персональный менеджер',
                'type' => Yii::t('CompanyModule.company','master type'),
                'experience' => Yii::t('CompanyModule.company','company experience'),
                'product_comments' => Yii::t('CompanyModule.company','is master comments allowed'),
                'name' => Yii::t('CompanyModule.company','master name'),
                'activities' => Yii::t('CompanyModule.company','description of activities master'),
                'activities_site' => Yii::t('CompanyModule.company','description of activities master site'),
                'delivery' => Yii::t('CompanyModule.company','services delivery cities list'),
                'terms' => Yii::t('CompanyModule.company','payment and delivery terms master'),
                'additional' => Yii::t('CompanyModule.company','Additional Information'),
                'image' => Yii::t('CompanyModule.company','master logo'),
                'is_active' => Yii::t('CompanyModule.company','master active?'),
                'sup_type' => Yii::t('CompanyModule.company','super type'),
                'is_top' => Yii::t('CompanyModule.company','is Top'),
                'top_level' => Yii::t('CompanyModule.company','Top level'),
                'top_from' => Yii::t('CompanyModule.company','Top from'),
                'top_to' => Yii::t('CompanyModule.company','Top to'),
                'busyness_line' => Yii::t('CompanyModule.company','Busyness line'),
                'busyness_line1' => Yii::t('CompanyModule.company','Busyness line'),
                'busyness_line2' => Yii::t('CompanyModule.company','Busyness line'),
                'main_line' => Yii::t('CompanyModule.company','Main line'),
                'locale_r' => Yii::t('CompanyModule.company','LocaleR'),
                'locale_c' => Yii::t('CompanyModule.company','LocaleC'),
            );

        return array(
            'items' => 'Обьявления',
            'id' => Yii::t('CompanyModule.company','company inner id'),
            'owner_id' => Yii::t('CompanyModule.company','company owner'),
            'manager_id' => 'Персональный менеджер',
            'type' => Yii::t('CompanyModule.company','company type'),
            'experience' => Yii::t('CompanyModule.company','company experience'),
            'product_comments' => Yii::t('CompanyModule.company','is company product comments allowed'),
            'name' => Yii::t('CompanyModule.company','company name'),
            'activities' => Yii::t('CompanyModule.company','description of activities'),
            'activities_site' => Yii::t('CompanyModule.company','description of activities site'),
            'delivery' => Yii::t('CompanyModule.company','product delivery cities list'),
            'terms' => Yii::t('CompanyModule.company','payment and delivery terms'),
            'additional' => Yii::t('CompanyModule.company','additional information'),
            'image' => Yii::t('CompanyModule.company','company logo'),
            'is_active' => Yii::t('CompanyModule.company','company active?'),
            'sup_type' => Yii::t('CompanyModule.company','super type'),
            'is_top' => Yii::t('CompanyModule.company','is Top'),
            'top_level' => Yii::t('CompanyModule.company','Top level'),
            'top_from' => Yii::t('CompanyModule.company','Top from'),
            'top_to' => Yii::t('CompanyModule.company','Top to'),
            'busyness_line' => Yii::t('CompanyModule.company','Busyness line'),
            'main_line' => Yii::t('CompanyModule.company','Main line Service'),
            'locale_r' => Yii::t('CompanyModule.company','LocaleR'),
            'locale_c' => Yii::t('CompanyModule.company','LocaleC'),
        );
    }

    public function attributeDescriptions()
    {
        if($this->sup_type == 1)
            return array(
                'id' => Yii::t('CompanyModule.company','company inner id'),
                'owner_id' => Yii::t('CompanyModule.company','company owner'),
                'type' => Yii::t('CompanyModule.company','company type'),
                'experience' => Yii::t('CompanyModule.company','company experience'),
                'product_comments' => Yii::t('CompanyModule.company','is company product comments allowed'),
                'name' => Yii::t('CompanyModule.company','company name'),
                'activities' => Yii::t('CompanyModule.company','description of activities'),
                'activities_site' => Yii::t('CompanyModule.company','description of activities master site'),
                'delivery' => Yii::t('CompanyModule.company','<b>Регионы доставки товара</b></br>Можно выбрать один или несколько, для выбора нескольких удерживайте нажатой клавишу Ctrl'),
                'terms' => Yii::t('CompanyModule.company','payment and delivery terms'),
                'additional' => Yii::t('CompanyModule.company','additional information'),
                'image' => Yii::t('CompanyModule.company','company logo'),
                'is_active' => Yii::t('CompanyModule.company','company active?'),
                'sup_type' => Yii::t('CompanyModule.company','super type'),
                'is_top' => Yii::t('CompanyModule.company','is Top'),
                'top_level' => Yii::t('CompanyModule.company','Top level'),
                'top_from' => Yii::t('CompanyModule.company','Top from'),
                'top_to' => Yii::t('CompanyModule.company','Top to'),
                'busyness_line' => Yii::t('CompanyModule.company','<b>Направление деятельности</b></br>Можно выбрать одно или несколько направлений'),
                'main_line' => Yii::t('CompanyModule.company','<b>Ваше основное направление деятельности</b><br/>Будет отображаться на вашей карточке компании.'),
            );
        if($this->sup_type == 2)
            return array(
                'id' => Yii::t('CompanyModule.company','company inner id'),
                'owner_id' => Yii::t('CompanyModule.company','company owner'),
                'type' => Yii::t('CompanyModule.company','master type'),
                'experience' => Yii::t('CompanyModule.company','company experience'),
                'product_comments' => Yii::t('CompanyModule.company','is master comments allowed'),
                'name' => Yii::t('CompanyModule.company','master name'),
                'activities' => Yii::t('CompanyModule.company','description of activities'),
                'delivery' => Yii::t('CompanyModule.company','<b>Регионы доставки товара</b></br>Можно выбрать один или несколько, для выбора нескольких удерживайте нажатой клавишу Ctrl'),
                'terms' => Yii::t('CompanyModule.company','payment terms'),
                'additional' => Yii::t('CompanyModule.company','Additional Information'),
                'image' => Yii::t('CompanyModule.company','master logo'),
                'is_active' => Yii::t('CompanyModule.company','master active?'),
                'sup_type' => Yii::t('CompanyModule.company','super type'),
                'is_top' => Yii::t('CompanyModule.company','is Top'),
                'top_level' => Yii::t('CompanyModule.company','Top level'),
                'top_from' => Yii::t('CompanyModule.company','Top from'),
                'top_to' => Yii::t('CompanyModule.company','Top to'),
                'busyness_line' => Yii::t('CompanyModule.company','<b>Направление деятельности</b></br>Можно выбрать одно или несколько направлений'),
                'main_line' => Yii::t('CompanyModule.company','<b>Ваше основное направление деятельности</b><br/>Будет отображаться на вашей карточке мастера.'),
            );

        return array(
            'id' => Yii::t('CompanyModule.company','company inner id'),
            'owner_id' => Yii::t('CompanyModule.company','company owner'),
            'type' => Yii::t('CompanyModule.company','company type'),
            'experience' => Yii::t('CompanyModule.company','company experience'),
            'product_comments' => Yii::t('CompanyModule.company','is company product comments allowed'),
            'name' => Yii::t('CompanyModule.company','company name'),
            'activities' => Yii::t('CompanyModule.company','description of activities'),
            'delivery' => Yii::t('CompanyModule.company','<b>Регионы доставки товара</b></br>Можно выбрать один или несколько'),
            'terms' => Yii::t('CompanyModule.company','payment and delivery terms'),
            'additional' => Yii::t('CompanyModule.company','additional information'),
            'image' => Yii::t('CompanyModule.company','company logo'),
            'is_active' => Yii::t('CompanyModule.company','company active?'),
            'sup_type' => Yii::t('CompanyModule.company','super type'),
            'is_top' => Yii::t('CompanyModule.company','is Top'),
            'top_level' => Yii::t('CompanyModule.company','Top level'),
            'top_from' => Yii::t('CompanyModule.company','Top from'),
            'top_to' => Yii::t('CompanyModule.company','Top to'),
            'busyness_line' => Yii::t('CompanyModule.company','<b>Направление деятельности</b></br>Можно выбрать одно или несколько направлений, для выбора нескольких удерживайте нажатой клавишу Ctrl'),
            'main_line' => Yii::t('CompanyModule.company','<b>Ваше основное направление деятельности</b><br/>Будет отображаться на вашей карточке компании.'),
        );
    }

    public function behaviors()
    {
        return [
            'imageUpload' => [
                'class' => 'yupe\components\behaviors\ImageUploadBehavior',
                'attributeName' => 'image',
                'minSize' => 0,
                'maxSize' => 5242880,
                'types' => 'jpg,jpeg,png,gif',
                'uploadPath' => 'company',
            ],
            'sortable' => [
                'class' => 'yupe\components\behaviors\SortableBehavior',
            ],
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search($settings = array())
    {
        $criteria=new CDbCriteria;

        $criteria->compare('t.id',$this->id);
        $criteria->compare('t.type',$this->type);
        $criteria->compare('t.experience',$this->experience);
        $criteria->compare('t.name',$this->name,true);
        $criteria->compare('t.activities',$this->activities,true);
        $criteria->compare('t.delivery',$this->delivery,true);
        $criteria->compare('t.is_active',true);

        //$criteria->compare('t.main_line',$this->busyness_line);
        $criteria->compare('t.locale_r',$this->locale_r,true);
        $criteria->compare('t.locale_c',$this->locale_c,true);
        //Searching only for TOP
        if($this->is_top){
            $criteria->compare('t.is_top',(int)$this->is_top,false);
        }
        //If we have Limit
        if(isset($settings['limit'])){
            $criteria->limit = $settings['limit'];
        }
        if(isset($settings['sup_type'])){
            $criteria->compare('t.sup_type',$settings['sup_type'],false);
        }
        if($this->busyness_line){
            $criteria->addColumnCondition(['busyness_line1' => $this->busyness_line, 'busyness_line2' => $this->busyness_line, 'main_line' => $this->busyness_line], 'OR');
        }
        $criteria->addCondition(['t.is_active' => 1]);
        $criteria->join .= ' JOIN {{user_user}} user ON t.owner_id = user.id';
        //$criteria->addCondition('user.avatar IS NOT NULL');
        //Ordering by TOP as default
        $criteria->order = 't.plan DESC, RAND()';

        //$criteria->with = 'owner';
        $provider =  new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>isset($settings['pagination']) && isset($settings['page_size'])?array('pageSize'=>$settings['page_size']):false,
        ));
        return $provider;
    }

     public function searchCompanyById($id)
    {
        $name = Yii::app()->getDb()->createCommand()
            ->select('name')
            ->from('{{company}}')
            ->where('id= :id', [':id' => $id])
            ->queryAll();
        return $name;
    }

    public function searchAdmin($settings = array())
    {
        $criteria=new CDbCriteria;

        $criteria->compare('t.id',$this->id);
        $criteria->compare('t.type',$this->type);
        $criteria->compare('t.experience',$this->experience);
        $criteria->compare('t.name',$this->name,true);
        $criteria->compare('t.activities',$this->activities,true);
        $criteria->compare('t.delivery',$this->delivery,true);
        $criteria->compare('t.is_active',$this->is_active);
        $criteria->compare('t.owner_id',$this->owner_id);
        $criteria->compare('t.manager_id',$this->manager_id);
        $criteria->compare('t.busyness_line',$this->busyness_line,true);
        $criteria->compare('t.locale_r',$this->locale_r);
        $criteria->compare('t.locale_c',$this->locale_c);
        $criteria->compare('t.sup_type',$this->sup_type);
        //Searching only for TOP
        /*if($this->is_top){
            $criteria->compare('t.is_top',(int)$this->is_top,false);
        }*/
        //If we have Limit
        if(isset($settings['limit'])){
            $criteria->limit = $settings['limit'];
        }
        /*if(isset($settings['sup_type'])){
            $criteria->compare('t.sup_type',$settings['sup_type'],false);
        }*/
        //$criteria->addCondition(['t.is_active' => 1]);
        //$criteria->join .= ' JOIN {{user_user}} user ON t.owner_id = user.id';
        //$criteria->addCondition('user.avatar IS NOT NULL');
        $provider =  new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>isset($settings['pagination']) && isset($settings['page_size'])?array('pageSize'=>$settings['page_size']):false,
            'sort' => ['defaultOrder' => $this->getTableAlias().'.id DESC'],
        ));
        return $provider;
    }

    public function searchInCompanyList()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.
        //var_dump(1);exit;
        $criteria=new CDbCriteria;
        //$criteria->with = array('owner','companyContacts');
        //$criteria->together = true;
        //$criteria->with = array('owner','companySchedules');

        //$criteria->compare('id',$this->id);
        $criteria->compare('type',$this->type);
        $criteria->compare('experience',$this->experience);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('activities',$this->activities,true);
        $criteria->compare('delivery',$this->delivery,true);
        $criteria->compare('is_top',(int)$this->is_top,false);
        if(!Yii::app()->getUser()->isSuperUser()){
            $criteria->compare('is_active',1,true);
        }
        $provider =  new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
        return $provider->getData();
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Company the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getCompanyByOwner($owner = 1)
    {

        $criteria=new CDbCriteria;
        $criteria->compare('owner_id',$owner);
        if(!Yii::app()->getUser()->isSuperUser()){
            $criteria->compare('is_active',1,true);
        }

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    public function getFormattedList($criteria = null)
    {
        $companies = Company::model()->findAllByAttributes(['is_active' => 1], $criteria);
        $list = [];
        foreach ($companies as $company) {
            $list[$company->name] = $company->name;
        }
        return $list;
    }

    public function getFormattedList2($criteria = null)
    {
        $companies = Company::model()->findAllByAttributes(['is_active' => 1], $criteria);
        $list = [];
        foreach ($companies as $company) {
            $list[$company->id] = $company->name;
        }
        return $list;
    }

    public function getFormattedList3($criteria = null)
    {
        $companies = Company::model()->findAllByAttributes(array(),$criteria);
        $list = [];
        foreach ($companies as $company) {
            $list[$company->name] = $company->name;
        }

        return $list;
    }

    public function getFormattedById($criteria = null)
    {
        $companies = Company::model()->findAll($criteria);
        $list = [];
        foreach ($companies as $company) {
            $list[$company->id] = $company->name;
        }
        return $list;
    }

    public function getName()
    {
        if ($this->sup_type === '1') {
            return $this->name;
        } elseif ($this->sup_type === '2') {
            $owner = User::model()->findByPk($this->owner_id);
            if (null != $owner->getFullName()) {
                return $owner->getFullName();
            }
        }
    }

    public function getImage($w,$h,$c,$d)
    {
        if($this->sup_type == 1){
            return $this->getImageUrl($w,$h,$c,$d);
        }else if($this->sup_type == 2){
            $owner = User::model()->findByPk($this->owner_id);
            if(null != $owner->getFullName()){
                return $owner->getAvatar($w,$h);
            }
        }
    }

//	public function getImageUrl(){
////		TODO Methods to crop and resize
//		if(sizeof($this->image) < 1)
//			$this->image = 'shop.png';
//		return ("/uploads/company/".$this->image);
//	}


//	public function getProductsFromCompany(){  неможу сформулювати правильний запит
//		$criteria = new CDbCriteria();
//		$criteria->condition = array('owner_id' => owner_id);
//		$products = Product::model()->findAll($criteria);
////				$products = Product::model()->with('company')->findAll();
//
//		var_dump($products);
//		return $products;
//	}
    public function getSlug($company) {
        $slug = '';
        if ($company->sup_type==='1') {
            $slug .= '/company/';
        } elseif ($company->sup_type==='2') {
            $slug .= '/master/';
        } else {
            return 'ErrorCompanySupType!!!!!!';
        }
        $slug .= $company->id . '-' . \yupe\helpers\YText::translit($company->name);
        return Yii::app()->createUrl($slug);
    }
    public function setPublishedStart($company, $date_t, $amount){
        $connection = Yii::app()->db;
        $command=$connection->createCommand('INSERT INTO `main_published` SET `id_company` ="'.$company.'", `date` ="'.$date_t.'", `amount` = "'.$amount.'"');
        $command->execute();
    }
    public function setPublishedMinus($company, $new_published){
        $connection = Yii::app()->db;
        $command=$connection->createCommand('UPDATE `main_published` SET `amount` = "'.$new_published.'" WHERE `id_company` ="'.$company.'"');
        $command->execute();
    }
    public function setProductsPublished($id_product, $id_company, $date_published){
        $connection = Yii::app()->db;
        $command=$connection->createCommand('INSERT INTO `main_published_products` SET `id_company` ="'.$id_company.'", `id_products` ="'.$id_product.'", `date_published` = "'.$date_published.'"');
        $command->execute();
    }
    public function setUpdateProductsPublished($id_product, $date_published){
        $connection = Yii::app()->db;
        $command=$connection->createCommand('UPDATE `main_published_products` SET `date_published` = "'.$date_published.'" WHERE `id_products` ="'.$id_product.'"');
        $command->execute();
    }
    public function setDeletePublished($id_company){
        $connection = Yii::app()->db;
        $command=$connection->createCommand('DELETE FROM `main_published` WHERE `id_company` ="'.$id_company.'"');
        $command->execute();
    }
    public function setDeleteProductsPublished($id_company){
        $connection = Yii::app()->db;
        $command=$connection->createCommand('DELETE FROM `main_published_products` WHERE `id_company` ="'.$id_company.'"');
        $command->execute();
    }
    public function getProductPublished($id){
        $id = Yii::app()->getDb()->createCommand()
            ->select('id')
            ->from('{{published_products}}')
            ->where('id_products= :id', [':id' => $id])
            ->queryAll();
        return $id;
    }
}