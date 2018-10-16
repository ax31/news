<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "likes".
 *
 * @property int $id
 * @property int $id_news
 * @property string $ip
 *
 * @property News $news
 */
class Like extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'likes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_news', 'ip'], 'required'],
            [['id_news'], 'integer'],
            [['ip'], 'string'],
            [
                ['id_news'],
                'exist',
                'skipOnError' => true,
                'targetClass' => News::className(),
                'targetAttribute' => ['id_news' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_news' => 'Id News',
            'ip' => 'Ip',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(News::className(), ['id' => 'id_news']);
    }
}
