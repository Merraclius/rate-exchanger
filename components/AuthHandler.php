<?php

namespace app\components;

use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;

use app\models\Auth;
use app\models\User;

/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class AuthHandler
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * AuthHandler constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Handle current $client. If not recognize it, we sign up user in other case we just log in him
     */
    public function handle()
    {
        list($id) = $this->extractValues('id');

        /* @var Auth $auth */
        $auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'sourceId' => $id,
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) {
                $this->login($auth);
            } else {
                $this->signup();
            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $auth = new Auth([
                    'userId' => Yii::$app->user->id,
                    'source' => $this->client->getId(),
                    'sourceId' => (string)$id,
                ]);
                if (!$auth->save()) {
                    Yii::$app->getSession()->setFlash('error',
                        'Unable to link ' .
                        $this->client->getTitle() .
                        ' account: ' .
                        json_encode($auth->getErrors())
                    );
                }
            } else { // there's existing auth
                Yii::$app->getSession()->setFlash('error',
                    'Unable to link ' .
                    $this->client->getTitle() .
                    ' account. There is another user using it.'
                );
            }
        }
    }

    /**
     * @param Auth|null $auth
     */
    private function login(Auth $auth = null)
    {
        /* @var User $user */
        $user = $auth->user;
        Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
    }

    /**
     * Sign up and log in client
     */
    private function signup()
    {
        list($emails, $id, $username) = $this->extractValues('emails', 'id', 'displayName');

        $email = ArrayHelper::getValue(array_pop($emails), "value");
        $user = User::find()->email($email)->one();

        if ($email !== null && $user) {
            Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
        } else {
            $user = new User([
                'username' => $username,
                'email' => $email,
            ]);
            $user->generateAuthKey();

            if ($user->save()) {
                $auth = new Auth([
                    'userId' => $user->id,
                    'source' => $this->client->getId(),
                    'sourceId' => (string)$id,
                ]);
                if ($auth->save()) {
                    Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
                } else {
                    Yii::$app->getSession()->setFlash('error',
                        'Unable to save ' .
                        $this->client->getTitle() .
                        " account: " .
                        json_encode($auth->getErrors())
                    );
                }
            } else {
                Yii::$app->getSession()->setFlash('error',
                    'Unable to save user: ' .
                    json_encode($user->getErrors())
                );
            }
        }

    }

    /**
     * Get list of required fields from function attributes and
     * Extract values from user attributes from client identity
     *
     * @return array
     */
    private function extractValues()
    {
        $values = array_map(function ($field) {
            $attributes = $this->client->getUserAttributes();
            return ArrayHelper::getValue($attributes, $field);
        }, func_get_args());

        return $values;
    }
}