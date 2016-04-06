<?php

namespace app\modules\SocialAuth\controllers;

use Yii;
use yii\web\Controller;
use app\modules\SocialAuth\models\User;
use app\modules\SocialAuth\models\Auth;

/**
 * Default controller for the `social-auth` module
 */
class DefaultController extends Controller
{
    public $defaultAction = 'auth';
    
    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => 'beta\handle_social_authorization', //[$this, 'onAuthSuccess'],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function onAuthSuccess($client)
    {
        $attributes = $client->getUserAttributes();

        /* @var $auth Auth */
        $auth = Auth::find()->where([
            'source' => $client->getId(),
            'source_id' => $attributes['id'],
        ])->one();
        
        if (Yii::$app->user->isGuest) {
            if ($auth) { // авторизация
                $user = $auth->user;
                Yii::$app->user->login($user);
            } else { // регистрация
              
              $user = User::fromClient($client);
              
              $transaction = $user->getDb()->beginTransaction();
              if ($user->save()) {
                  
                  $auth = Auth::fromClient($client);
                  $auth['user_id'] = $user->id;

                  if ($auth->save()) {
                      $transaction->commit();
                      Yii::$app->user->login($user);
                  } else {
                      print_r($auth->getErrors());
                  }
              } else {
                  print_r($user->getErrors());
              }

            }
        } else { // Пользователь уже залогинен
            $user = Yii::$app->user->identity;

      			if ($oldAuth = $user->getAuths()->where(['user_id' => $user->id, 'source' => $client->getId()])->andWhere(['!=', 'source_id', $attributes['id']])->one()) {
      				// но к пользователю привязана старая авторизация
      				$oldAuth->setAttribute('user_id', '0');
      				$oldAuth->save();
      			}
      
      			if (!$auth) {
      				// если авторизации нет в базе
      
              $auth = Auth::fromClient($client);
              $auth['user_id'] = $user->id;
      			} else {
      				// если авторизация есть в базе
      
      				if ($user->id != $auth->user_id) {
      					$auth->link('user', $user);
      				}
      			}
        }

		}
}
