<?php

App::uses('Component', 'Controller');

class S3Component extends Component {
    
    const bucket = 'necampus';
    
    private function connect(){
        try{
            $credentials = new Aws\Credentials\Credentials(Configure::read('S3_api_key'), Configure::read('S3_secret_key'));
            $s3 = new Aws\S3\S3Client([
                'version'     => 'latest',
                'region' => 'ap-east-1',
                'credentials' => $credentials
            ]);
            return $s3;
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            return false;
        }
    }
    
    
    public function upload($file = array(), $options = array()){
        try{
            if ($file['error'] != 0) {
                return [false, false];
            }
            if(!isset($options['type'])){
                $options['type'] = 'media';
            }
            $key = 'ne-' . $options['type'] . '-' . md5($file['name'] . microtime(TRUE));
            $s3 = $this->connect();
            $result = $s3->putObject([
                'Bucket' => self::bucket,
                'Key'    => $key,
                'Body'   => fopen($file['tmp_name'], 'r+'),
                'ACL'    => 'public-read',
                'Metadata' => $options
            ]);

            if(isset($result['ObjectURL'])){
                return [$key, $result['ObjectURL']];
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            return [false, false];
        }
    }
    
    public function delete($object_key){
        try{
            $s3 = $this->connect();
            $promise = $s3->deleteObject([
                'Bucket' => self::bucket, // REQUIRED
                'Key' => $object_key, // REQUIRED
            ]);
            return true;
        }catch(Exception $ex){
            error_log($ex->getMessage());
            return false;
        }
    }

}
