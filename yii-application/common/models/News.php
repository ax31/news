<?php

namespace common\models;

use yii\db\ActiveRecord;
use Yii;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $image
 * @property string $created_at
 * @property string $updated_at
 */
class News extends ActiveRecord
{
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['title', 'content', 'image'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['imageFile'], 'file', 'extensions' => 'jpg, png', 'maxSize' => 2097152]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'imageFile' => 'Image',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getLike()
    {
        $ip = Yii::$app->request->getUserIP();

        return $this->hasOne(Like::className(), ['id_news' => 'id'])->where(['=', 'ip', $ip]);
    }

    public function getTruncateContent()
    {
        $countWords = StringHelper::countWords($this->content);

        if ($countWords > 30) {
            return StringHelper::truncateWords($this->content, 30);
        }

        return null;
    }

    public function getLikeClass()
    {
        $likeClass = 'default';

        if ($this->like) {
            $likeClass = 'primary';
        }

        return $likeClass;
    }
}
