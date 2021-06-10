<?php

require_once APP . 'vendor' . DS . "autoload.php";

class JWTComponent extends Component
{

    const CODE_INVALID_ACCESS_TOKEN = 3;
    const CODE_TOKEN_EXPIRED = 10;

    public function generateAccessToken($user)
    {
        $currentTime = time();
        $tokenBuilder = new Lcobucci\JWT\Builder();
        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        $tokenBuilder->setId($user['id']);
        $tokenBuilder->setIssuedAt($currentTime);
        $tokenBuilder->set('first_name', $user['first_name']);
        $tokenBuilder->set('last_name', $user['last_name']);
        $tokenBuilder->set('email', $user['email']);
        $tokenBuilder->set('login_type', $user['login_type']);
        $tokenBuilder->set('social_id', isset($user['social_id']) ? $user['social_id'] : '');
        $tokenBuilder->setExpiration($currentTime + Configure::read('token_expire'));
        $tokenBuilder->sign($signer, Configure::read('TOKEN_SIGN_SECRET_KEY'));
        $token = $tokenBuilder->getToken();
        return (string) $token;
    }

    public function getUserFromAccessToken($jwt)
    {
        if (!$jwt) {
            return false;
        }
        $tokenParser = new \Lcobucci\JWT\Parser;
        try {
            $token = $tokenParser->parse($jwt);
        } catch (\Exception $e) {
            return false;
        }

        if (!$token->verify(new \Lcobucci\JWT\Signer\Hmac\Sha256(), Configure::read('TOKEN_SIGN_SECRET_KEY'))) {
            return false;
        }

        if (!$token->validate(new Lcobucci\JWT\ValidationData())) {
            # expired token
            return false;
        }
        try {
            
            $tokenData = [
                'id' => $token->getClaim('jti'),
                'first_name' => $token->getClaim('first_name'),
                'last_name' => $token->getClaim('last_name'),
                'social_id' => $token->getClaim('social_id'),
                'login_type' => $token->getClaim('login_type'),
            ];
//            
//            $user = ClassRegistry::init('User');
//            
//            $u = $user->findById($token->getClaim('jti'));
//            
//            if(empty($u) || $u['User']['status'] == 'lock'){
//                return false;
//            }
//            $tokenData = [
//                'user_id' => $u['User']['id'],
//                'phone' => $u['User']['phone'],
//                'user_title' => $u['User']['title'],
//                'brand_name' => $u['User']['brand_name'],
//                'user_type' => $u['User']['user_type'],
//                'avatar' => $u['User']['avatar'],
//                'status' => $u['User']['status'],
//                'following' => $u['User']['following'],
//                'login_type' => $u['User']['login_type'],
//                'last_contact_sync' => $u['User']['last_contact_sync']
//            ];
        } catch (Exception $e) {
            return false;
        }
        return $tokenData;
    }
}
