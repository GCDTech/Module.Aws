<?php

namespace Gcdtech\Aws\Settings;

use Aws\Credentials\CredentialProvider;
use Rhubarb\Crown\Settings;

class AwsSettings extends Settings
{
    public $profile = null;
    public $region = "";

    public $iniCredentialsFile = "";

    public function getClientSettings($additionalSettings)
    {
        $settings = [
            'version' => 'latest',
            'region' => $this->region,
            'profile' => $this->profile
        ];

        if ($this->iniCredentialsFile){
            $provider = CredentialProvider::ini($this->profile, $this->iniCredentialsFile);
            $settings['credentials'] = $provider;
        }

        return array_merge($settings, $additionalSettings);
    }
}