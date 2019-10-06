<?php
namespace piliugin\recaptcha3;

use piliugin\recaptcha3\exception\InvalidConfigException;
use piliugin\recaptcha3\exception\TransportException;
use yii\di\Instance;
use yii\validators\Validator;

class RecaptchaValidator extends Validator
{
    /**
     * @var bool
     */
    public $skipOnEmpty = false;

    /**
     * Recaptcha component
     * @var string
     */
    public $componentName = 'recaptcha';

    /**
     * the minimum score for this request (0.0 - 1.0)
     * @var null|int
     */
    public $acceptanceScore = 0.5;

    /**
     * @var string - this value will be compared with google`s response
     */
    public $action;

    /**
     * @var Recaptcha
     */
    protected $component;

    /**
     * @throws InvalidConfigException
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $component = Instance::ensure($this->componentName, Recaptcha::class);

        if ($component == null) {
            throw new InvalidConfigException(\Yii::t('recaptcha3', 'component required.'));
        }

        $this->component = $component;

        if ($this->message === null) {
            $this->message = \Yii::t('recaptcha3', 'The verification code is incorrect.');
        }
    }

    /**
     * @param string $value - client-side response token
     *
     * @return array|null
     */
    protected function validateValue($value)
    {
        try {
            $info = $this->component->getInfoByToken($value);
        } catch (TransportException $e) {
            return null;
        }

        if ($info['success'] === false) {
            return null;
        }

        if ($info['score'] > $this->acceptanceScore && $info['action'] === $this->action) {
            return null;
        }

        return [$this->message, []];
    }
}
