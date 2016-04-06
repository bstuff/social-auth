<?php

namespace app\modules\SocialAuth\controllers;

use Yii;
use app\modules\SocialAuth\models\User;
use app\modules\SocialAuth\models\UserSearch;
use yii\web\Controller;


/**
 * AdminController implements the CRUD actions for User model.
 */
class AdminController extends Controller
{
    public function behaviors()
    {
        return [
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionUsers()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('/user/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
