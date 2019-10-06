<?php
namespace piliugin\recaptcha3;

use piliugin\recaptcha3\exception\InvalidConfigException;
use piliugin\recaptcha3\exception\TransportException;
use yii\base\Component;
use yii\httpclient\Client;
use yii\web\View;

/**
 * @property string $validationEndpoint
 * @property string $scriptEndpoint
 */
class Recaptcha extends Component
{
    public $publicKey;

    public $privateKey;

    protected $endpoint = 'https://www.google.com/recaptcha';

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!$this->publicKey) {
            throw new InvalidConfigException('publicKey required');
        }

        if (!$this->privateKey) {
            throw new InvalidConfigException('privateKey required');
        }
    }

    /**
     * @return string
     */
    protected function getValidationEndpoint()
    {
        return "{$this->endpoint}/api/siteverify";
    }

    public function getScriptEndpoint()
    {
        return "{$this->endpoint}/api.js";
    }

    /**
     * @param View $view
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function registerScript($view)
    {
        $url = "{$this->scriptEndpoint}?render={$this->publicKey}";
        $view->registerJsFile($url, [
            'position' => $view::POS_HEAD,
        ], 'recaptcha-v3-script');

        $view->registerJs(
            "window.recaptchaKey = '{$this->publicKey}'",
            $view::POS_HEAD,
            'recaptcha-v3-key'
        );
    }

    /**
     * @param string $clientSideToken
     *
     * @return array
     * @throws TransportException
     */
    public function getInfoByToken($clientSideToken)
    {
        $data = [
            'secret' => $this->privateKey,
            'response' => $clientSideToken,
            'remoteip' => \Yii::$app->has('request') ? \Yii::$app->request->remoteIP : null,
        ];
        return $this->request($data);
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws TransportException
     */
    public function request($data)
    {
        $client = new Client();

        try {
            $response = $client->post($this->validationEndpoint, $data)->send();
        } catch (\yii\httpclient\Exception $e) {
            throw new TransportException($e->getMessage(), 0, $e);
        }

        if (!$response->isOk) {
            throw new TransportException($response->statusCode);
        }

        return $response->data;
    }
}
