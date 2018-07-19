<?php

namespace Gcdtech\Aws\Settings;

use Aws\Credentials\CredentialProvider;
use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Crown\Exceptions\SettingMissingException;
use Rhubarb\Crown\Settings;

class AwsSettings extends Settings
{
    public $profile = null;
    public $region = "";

    public $iniCredentialsFile = "";

    public $credentialsAccessKeyId = "";
    public $credentialsSecretAccessKey = "";

    /**
     * Provide a path to a custom CA bundle if required
     *
     * http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html#verify
     *
     * @var string
     */
    public $sslCaBundlePemPath = "";

    /**
     * Set to false to disable SSL verification
     * @var bool
     */
    public $sslVerify = true;

    public function getClientSettings($additionalSettings = [])
    {
        if (!$this->region){
            throw new SettingMissingException("AwsSettings", "region");
        }

        if (!empty($this->profile) && (!empty($this->iniCredentialsFile) || !empty($this->credentialsAccessKeyId))) {
            $exception = new RhubarbException("The settings profile and either iniCredentialsFile or credentialsAccessKeyId cannot both be set. Due to the Amazon SDK presuming
             the credentials file will be in the profile's home directory and will not look in any other location");
            throw $exception;
        }

        $settings = [
            'version' => 'latest',
            'region' => $this->region,
            'profile' => $this->profile,
            'http'    => [
                'verify' => ($this->sslVerify ? ($this->sslCaBundlePemPath ? $this->sslCaBundlePemPath : true) : false)
            ]
        ];

        if ($this->iniCredentialsFile){
            $provider = CredentialProvider::ini($this->profile, $this->iniCredentialsFile);
            $settings['credentials'] = $provider;
        } else if($this->credentialsAccessKeyId && $this->credentialsSecretAccessKey) {
            $settings['credentials'] = [
                'key'    => $this->credentialsAccessKeyId,
                'secret' => $this->credentialsSecretAccessKey
            ];
        }

        return array_merge($settings, $additionalSettings);
    }
}
