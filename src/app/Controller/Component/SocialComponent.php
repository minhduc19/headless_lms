<?php


class SocialComponent extends Component
{

    public function getFacebookUserInfo($accessToken)
    {
        $fields = "id,first_name,last_name,email";
        $url = "https://graph.facebook.com/v8.0/me";
        $params = [
            'fields' => $fields,
            'access_token' => $accessToken
        ];
        return $this->doCurl($url, 'get', $params);
    }
    
    public function getFacebookAppInfo($accessToken)
    {
        $url = 'https://graph.facebook.com/v8.0/app';
        $params = [
            'access_token' => $accessToken
        ];
        return $this->doCurl($url, 'get', $params);
    }
    
    public function getGoogleUserInfo($accessToken)
    {
        $url = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $params = [
            'access_token' => $accessToken
        ];
        return $this->doCurl($url, 'get', $params);
    }
    
    public function getAppleUserInfo($apple_code)
    {
        $clientInfo = [
            'client_id' => 'com.necampus',
            'client_secret' => 'eyJraWQiOiI0UDlUNlRTTUZKIiwiYWxnIjoiRVMyNTYifQ.eyJpc3MiOiJUUUQ5MjRQSlM1IiwiaWF0IjoxNjE5NjE5NDE5LCJleHAiOjE2MzUxNzE0MTksImF1ZCI6Imh0dHBzOi8vYXBwbGVpZC5hcHBsZS5jb20iLCJzdWIiOiJjb20ubmVjYW1wdXMifQ.-Gv1jbsxmFS-JyBN9qvcJaVTduxsu9_sEUu4o2Mv_i5BmeyJA9nLoImLapG30Opbc0KNVa5v5mJX-mDx4pXqTw',
            //'client_secret' => 'eyJraWQiOiI0UDlUNlRTTUZKIiwiYWxnIjoiRVMyNTYifQ.eyJpc3MiOiJUUUQ5MjRQSlM1IiwiaWF0IjoxNjAwNTAyMjc2LCJleHAiOjE2MTYwNTQyNzYsImF1ZCI6Imh0dHBzOi8vYXBwbGVpZC5hcHBsZS5jb20iLCJzdWIiOiJjb20ubmVjYW1wdXMuc2VydmljZXMifQ.6S-918TO-Y_gh6rMrkrbtwlBP6apNGUvuv0L2lBLlq1C0eQplPDx_7w9258G0Qt44D1UMgDmWHNGzB2vqyO8TA',
        ];
        $params = [
                'grant_type' => 'authorization_code',
                'code' => $apple_code,
                'client_id' => $clientInfo['client_id'],
                'client_secret' => $clientInfo['client_secret'],
        ];
        $headers = [
            'Accept: application/json',
            'User-Agent: curl', # Apple requires a user agent header at the token endpoint
        ];
        $url = 'https://appleid.apple.com/auth/token';
        return $this->doCurl($url, 'post', http_build_query($params), $headers);
    }

    private function doCurl($url, $method = 'get', $data = array(), $headers = []) {
        $method = strtolower($method);
        $ch = curl_init();

        if ($method == 'get' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if (!isset($info['http_code'])) {
            error_log(json_encode($info));
            return false;
        }

        if($info['http_code'] != 200){
            error_log(json_encode($info));
            return false;
        }
        return json_decode($result, true);
    }
    
    
}
