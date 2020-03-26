<?php
class MyRedis
{
    private $all_conn = array();
    public $redis = NULL;
    private $ttl = 3600;
    public  $debug = true;

    function __construct( $debug=true ){
        $this->debug = $debug;
    }
    function __destruct(){ // 
        if( $this->redis ){
            $this->redis->close();
        }
        if( $this->all_conn ){
            foreach( $this->all_conn as $_redis ){
                $_redis->close();
            }
        }
    }

    /*
     * redis的pconnect是基于host+port的 我们的配置文件是基于host+port+dbN的 所以改为connect
     */
    function use_redis($conn_name){
        if( isset( $this->all_conn[$conn_name] ) ){
            $this->redis = $this->all_conn[$conn_name];
        }else{
            global $REDIS_CONFIG;
            if(!isset($REDIS_CONFIG[$conn_name])){
                if($this->debug){
                    die("redis {$conn_name} config name not exist !");
                }else{
                    exit;
                }
            }
            $config = $REDIS_CONFIG[$conn_name];
            $config_count = count($config);
            if($config_count > 1){ //一般是多个从库的情况，这里简单地采用随机连一个数据库
                $config = $config[rand(0, $config_count - 1)];
            }else{
                $config = $config[0];
            }
            $this->redis = new Redis();
            $conn_result = $this->redis->connect( $config['host'], $config['port'] );
            if( $conn_result===false ){
                @file_put_contents( '/data/logs/redis_conn_error.log', date('Y-m-d H:i:s') ." conn_name:{$conn_name} ". var_export(debug_backtrace(),true)."\n", FILE_APPEND );
            }
            if( $config['password'] ){
                $auth_result = $this->redis->auth( $config['password'] );
                if( ! $auth_result ){
                    die("{$conn_name} redis auth fail !");
                }
            }
            if( isset($config['dbN']) ){
                $this->redis->select( $config['dbN'] );
            }
            else{
                $this->redis->select( 0 );
            }
            $this->all_conn[$conn_name] = $this->redis;
            //return $this;
        }
    }
}
