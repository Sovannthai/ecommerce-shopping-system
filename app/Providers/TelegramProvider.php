<?php
namespace App\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;
use Illuminate\Http\Request;

class TelegramProvider extends AbstractProvider
{
    protected $scopes = [];

    protected function getAuthUrl($state)
    {
        return 'https://telegram.org/auth?' . http_build_query($this->getCodeFields($state), '', '&', $this->encodingType);
    }
    protected function getTokenUrl()
    {
        return 'https://telegram.org/auth/token';
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://telegram.org/user', [
            'query' => [
                'access_token' => $token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => $user['username'],
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'email' => null,
            'avatar' => $user['photo_url'],
        ]);
    }

    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);
    }
}
