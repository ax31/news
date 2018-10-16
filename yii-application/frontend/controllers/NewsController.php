<?php

namespace frontend\controllers;

use common\models\Like;
use common\models\News;
use yii\helpers\Json;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use Yii;

class NewsController extends Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => News::find()->with(['like']),
            'sort' => [
                'attributes' => [
                    'title',
                    'created_at'
                ]
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionLike()
    {
        $id = Yii::$app->request->post('id');
        $ip = Yii::$app->request->getUserIP();
        $existLike = Like::findOne([
            'id_news' => $id,
            'ip' => $ip
        ]);
        $result = '';

        if (empty($existLike)) {
            $like = new Like();
            $like->id_news = $id;
            $like->ip = $ip;

            if ($like->insert()) {
                $result = 'insert';
            }
        } else {
            if ($existLike->delete()) {
                $result = 'delete';
            }
        }

        echo Json::encode($result);
    }
}
