<?php

namespace app\modules\SocialAuth\models;

use Yii;

/**
 * User class.
 */
class User extends \app\models\User
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuths()
    {
        return $this->hasMany(Auth::className(), ['user_id' => 'id']);
    }
    
    /**
     * @return User object
     */
    public static function fromClient($client) {
        $attributes = $client->getUserAttributes();

        $user = new User([
            'username' => (string)$attributes['id'] . '@' . $client->getId() . '.' . Yii::$app->security->generateRandomString(4),
            'email' => (string)$attributes['id'] . '@' . $client->getId() . '.dev',
            'password' => Yii::$app->security->generateRandomString(6),
        ]);
        $user->generateAuthKey();
        $user->generatePasswordResetToken();

        return $user;
    }
}
