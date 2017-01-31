<?php

namespace Gcdtech\Aws\Settings;

use Aws\Credentials\CredentialProvider;
use Rhubarb\Crown\Exceptions\SettingMissingException;
use Rhubarb\Crown\Settings;

class AwsSettings extends Settings
{
    public $profile = null;
    public $region = "";

    public $iniCredentialsFile = "";

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
        }

        return array_merge($settings, $additionalSettings);
    }
}