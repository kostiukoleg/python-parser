<?php

/**
 * This is the model class for table "{{company_viewed}}".
 *
 * The followings are the available columns in table '{{company_viewed}}':
 * @property integer $id
 * @property integer $company_id
 * @property string $date
 * @property integer $views
 *
 * @property-read Company $company
 */
class CompanyViewed extends yupe\models\YModel
{
    public $company_name;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{company_viewed}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['company_id, date', 'required'],
            ['date, views, callback', 'default']
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'company'  => [self::BELONGS_TO, 'Company', 'company_id'],
        ];
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->with = ['company'];
        $criteria->compare('date', $this->date);
        $criteria->compare('company_id', $this->company_id);
        if ($this->company_name) {
            $criteria->addSearchCondition('company.name', $this->company_name);
        }

        return new CActiveDataProvider(
            get_class($this), [
                'criteria' => $criteria,
                'sort' => ['defaultOrder' => 't.`date` DESC'],
            ]
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company',
            'date' => 'Date',
            'views' => 'Views',
            'callback' => 'Callback'
        ];
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CompanyViewed the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function countDayHits($company_id, $date = null)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'sum(hits) as hits';
        $criteria->addCondition('company_id = '.$company_id);
        if (is_array($date)) {
            if ($date[0]) {
                $criteria->addCondition('date >= "' . date('Y-m-d', strtotime($date[0])) . '"');
            }
            if ($date[1]) {
                $criteria->addCondition('date <= "' . date('Y-m-d', strtotime($date[1])) . '"');
            }
        } elseif ($date) {
            $criteria->addCondition('date = "' . date('Y-m-d', strtotime($date)) . '"');
        } else {
            $criteria->addCondition('date = "' . date('Y-m-d') . '"');
        }
        $hits = CompanyHits::model()->find($criteria);
        return $hits ? (int) $hits->hits : 0;
    }

    public function getCountDayHits()
    {
        return static::countDayHits($this->company_id, $this->date);
    }

    public static function countDayViber($company_id, $date = null)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'sum(viber_click) as viber_click';
        $criteria->addCondition('company_id = '.$company_id);
        if (is_array($date)) {
            if ($date[0]) {
                $criteria->addCondition('date >= "' . date('Y-m-d', strtotime($date[0])) . '"');
            }
            if ($date[1]) {
                $criteria->addCondition('date <= "' . date('Y-m-d', strtotime($date[1])) . '"');
            }
        } elseif ($date) {
            $criteria->addCondition('date = "' . date('Y-m-d', strtotime($date)) . '"');
        } else {
            $criteria->addCondition('date = "' . date('Y-m-d') . '"');
        }
        $viber = CompanyViewed::model()->find($criteria);
        return $viber ? (int) $viber->viber_click : 0;
    }

    public function getCountDayViber()
    {
        return static::countDayViber($this->company_id, $this->date);
    }	

    public static function countDayCallback($company_id, $date = null)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'sum(callback) as callback';
        $criteria->addCondition('company_id = '.$company_id);
        if (is_array($date)) {
            if ($date[0]) {
                $criteria->addCondition('date >= "' . date('Y-m-d', strtotime($date[0])) . '"');
            }
            if ($date[1]) {
                $criteria->addCondition('date <= "' . date('Y-m-d', strtotime($date[1])) . '"');
            }
        } elseif ($date) {
            $criteria->addCondition('date = "' . date('Y-m-d', strtotime($date)) . '"');
        } else {
            $criteria->addCondition('date = "' . date('Y-m-d') . '"');
        }
        $callback = CompanyViewed::model()->find($criteria);
        return $callback ? (int) $callback->callback : 0;
    }

    public function getCountDayCallback()
    {
        return static::countDayCallback($this->company_id, $this->date);
    }	
}
