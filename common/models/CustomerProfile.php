<?php

namespace common\models;

use Yii;
use yii\helpers\Url;
use common\helpers\Form;
use core\behaviors\UploadedBehavior;
use core\helpers\App;


class CustomerProfile extends ActiveRecord
{

    const SEX_MALE   = 0;
    const SEX_FEMALE = 1;
   
   public $avatorFile;

   public $avatorDelete;

   /**
    * {@inheritdoc}
    */
    public static function tableName()
    {
        return '{{%customer_profile}}'; 
    }

    public function formName()
    {
        return 'profile';
    }

    public function behaviors()
    {
         return array_merge(parent::behaviors(), [
             'avator' => [
                 'class' => UploadedBehavior::className(),
                 'attribute' => 'avator',
                 'path' => '@media/customer',
             ],
         ]);
    }

    public static function sexHashOptions()
    {
        return [
           self::SEX_MALE => Yii::t('app', 'Male'),
           self::SEX_FEMALE => Yii::t('app', 'Female'),
        ];
    }


    public function rules()
    {
        return [
            [['url'], 'string', 'length'=>[0,255]],
            [['url'], 'url'],
            [['wechat'], 'string', 'length' => [5,32]],
            [['qq'], 'integer'],
            [['sex'], 'boolean'],
            [['dob'], 'date',
               'format' => 'php:Y-m-d',
               'timestampAttributeFormat' => 'php:Y-m-d',
               'timestampAttributeTimeZone' => Yii::$app->timeZone,
            ],
            [['dob'], function() {}, 'clientValidate' => function($attribute, $params, $validator) {
                return 'yii.validation.regularExpression(value, messages, {
                    skipOnEmpty: true,
                    not : false,
                    pattern : /^(19[4-9][0-9]|20[0-9]{2})[-/](0[1-9]|1[0-2])[-/](0[1-9]|1[0-2])$/,
                    message : \'不是有效的日期格式\'
                })';
            }],
            [['avatorFile'], 'image', 'extensions' => ['jpg', 'png', 'gif', 'jpeg']],
            [['avatorDelete'], 'default', 'value' => 0],
            [['avatorDelete'], 'boolean'],
            [['url', 'wechat', 'qq', 'sex','dob', 'avator', 'city', 'note', 'sex', 'bio', 'username'], 
               'default',
               'value' => null,
           ], 
        ];
    }

    public function scenarios()
    {
        $default = [
           'username',
           'bio',
           'url',
           'wechat',
           'qq',
           'sex',
           'dob',
           'avatorFile',
           'avatorDelete',
           'city',
           'note',
        ];
        return [
            static::SCENARIO_DEFAULT => $default,
            static::SCENARIO_CREATE => $default,
            static::SCENARIO_UPDATE => $default,
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('admin', 'Name'),
            'bio'      => Yii::t('admin', 'Bio'),
            'url'      => Yii::t('admin', 'User home page'),
            'wechat'   => Yii::t('admin', 'Wechat'),
            'qq'       => Yii::t('admin', 'QQ'),
            'sex'      => Yii::t('admin', 'Sex'),
            'dob'      => Yii::t('admin', 'Date of birth'),
            'city'     => Yii::t('admin', 'The city I live'),
            'note'     => Yii::t('admin', 'Note information'),
            'avator'   => Yii::t('admin', 'Avator'),
            'avatorFile' => Yii::t('admin', 'Avator'),
            'avatorDelete' => Yii::t('all', 'Delete image'),
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['customer_id'], $fields['id']);
        $fields['avator'] = function($model, $field) {
            return $this->getAvatorUrl(true);
        };
        return $fields;
    }

    public function getAvatorUrl($absolute = true)
    {
        if($this->avator) {
            return App::getMediaUrl('customer/'. $this->avator, $absolute);
        }
        return App::getImageUrl('placeholder.jpg');
    }

    public function attributeHints()
    {
        return [
            'dob' => '有效的日期格式为 yyyy-mm-dd 或者 yyyy/mm/dd',
        ];
    }


    /**
     * 获取 customer 实例
     * 
     * @return common\models\Customer 实例.
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id'])->inverseOf('profile');
    }


    public function setCustomer($customer)
    {
         $this->customer_id = ($customer instanceof Customer) ? $customer->id : $customer;
    }
}