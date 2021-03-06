<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\queries\UserMessageQuery;
use core\db\ActiveRecord;

/**
 * This is the model class for table "admin_message".
 *
 * @property string   $id
 * @property int      $user_id
 * @property int      $sender_id
 * @property string   $sender_name 发送者名字
 * @property string   $subject 摘要
 * @property string   $message
 * @property int      $watched
 * @property int      $created_at
 * @property int      $watched_at
 * @property int      $level 消息级别
 * @property int      $require_receipt 是否需要回执
 *
 */
class UserMessage extends ActiveRecord
{

    const LEVEL_ERROR = 4;

    const LEVEL_WARNING = 2;

    const LEVEL_INFO = 1;

    public static function tableName()
    {
    	return '{{%admin_message}}';
    }


    /**
     * @inheritdoc
     * 
     * @return ActiveQuery
     */
    public static function find()
    {
        return Yii::createObject(UserMessageQuery::class, [ get_called_class()]);
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
              'timestamp' => [
                  'class' => TimestampBehavior::className(),
                  'updatedAtAttribute' => false,
              ],
    	]);
    }

    public function rules()
    {
          return [
          	[['sender_name'], 'required'],
              [['sender_name'], 'string', 'length' => [0, 255]],
              [['message'], 'string'],
              [['watched'], 'default', 'value' => 0],
              [['watched'], 'boolean'],
              [['level'], 'in', 'range' => [
                  static::LEVEL_INFO,
                  static::LEVEL_WARNING,
                  static::LEVEL_ERROR,
              ]],
              [['require_receipt'], 'default', 'value' => 0],
              [['require_receipt'], 'boolean'],
              [['subject'], 'default', 'value' => function() {
                  if(mb_strlen($this->message) > 29) {
                      return mb_substr($this->message, 0, 29) . '...';
                  } else {
                  	return $this->message;
                  }
              }],
          ];
    }




	public function attributeLabels()
	{
        return [
           'id'              => 'ID',
           'user_id'         => Yii::t('all', 'Administrator'),
           'sender_id'       => Yii::t('all', 'Sender id'),
           'sender_name'     => Yii::t('all', 'Sender name'),
           'subject'         => Yii::t('all', 'Subject'),
           'message'         => Yii::t('all', 'Message'),
           'watched'         => Yii::t('all', 'Watched'),
           'watched_at'      => Yii::t('all', 'Watched time'),
           'created_at'      => Yii::t('all', 'Created time'),
           'level'           => Yii::t('all', 'Message level'),
           'require_receipt' => Yii::t('all', 'Require receipt'),
        ];
	}

	public function watchMessage()
	{
		static::getDb()->transaction(function() {
			$this->watched     = 1;
			$this->watched_at  = time();
			if($this->require_receipt && $this->sender instanceof User) {
	            $message = $this->createReceiptMessage($this->sender);
	            $message -> save(false);
			}
			$this->save(false);
     	});
	}

	public function createReceiptMessage($user)
	{
        $message = new static();
        $message->user_id   = $user->id;
        $message->sender_id = $this->user->id;
        $message->sender_name = $this->user->username;

        $message->subject = Yii::t('all', 'Receipt') . $this->subject;
        $message->message = Yii::t('all', 'Receipt message') . $this->message;
        $message->created_at = time();
        $message->level = $this->level;
        return $message;

	}

	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	public function getSender()
	{
		return $this->hasOne(User::className(), ['id' => 'sender_id']);
	}

  public function getSendtime()
  {
     return Yii::$app->formatter->asDatetime($this->created_at);
  }
}