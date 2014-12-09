<?php
namespace Home\Helper;

class ApiHelper{

    public static function post($url, $data) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$remote_server);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    /**
     * 通过curl获取信息
     * 
     * @param $url
     * @return return_type
     * @author zhoupeng
     * @date 2014-4-27 下午06:12:12
     */
    public static function curlGetInfo($url){
        
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_HEADER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出  
        $result=curl_exec($ch);
        
        if(curl_errno($ch)){
           \Think\Log::write("请求url出错，原因如下:". curl_error($ch),'ERRO');  
        }  
        
        curl_close($ch);
        
        return $result;
    }

    /**
     *  批量获取curl结果函数
     *  
     *  urlarr = array(
     *      'module' => url
     *  );
     *  @author zhouhongchang
     *  @param  array $urlarr  url array
     *  @return array
     **/
    public static function curl_multi_fetch( $urlarr = array() ){
        $result = $res = $ch = array();
        $nch = 0;
        $mh = curl_multi_init();
        foreach ($urlarr as $nk => $url) {
            $timeout  = 2;
            $ch[$nch] = curl_init();
            curl_setopt_array($ch[$nch], array(
                CURLOPT_URL => $url,
                CURLOPT_HEADER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeout,
            ));
            curl_multi_add_handle($mh, $ch[$nch]);
            ++$nch;
        }

        /* wait for performing request */
        do {
            $mrc = curl_multi_exec($mh, $running);
        } while (CURLM_CALL_MULTI_PERFORM == $mrc);
        while ($running && $mrc == CURLM_OK) {
             while (curl_multi_exec($mh, $running) === CURLM_CALL_MULTI_PERFORM);
            if (curl_multi_select($mh) != -1) {
                // pull in new data;
                do {
                    $mrc = curl_multi_exec($mh, $running);
                } while (CURLM_CALL_MULTI_PERFORM == $mrc);
            }
        }
     
        if ($mrc != CURLM_OK) {
            error_log("CURL Data Error");
        }
     
        /* get data */
        $nch = 0;
        foreach ($urlarr as $moudle=>$node) {
            if (($err = curl_error($ch[$nch])) == '') {
                $res[$nch]=curl_multi_getcontent($ch[$nch]);
                $result[$moudle]=$res[$nch];
            }
            curl_multi_remove_handle($mh,$ch[$nch]);
            curl_close($ch[$nch]);
            ++$nch;
        }
        curl_multi_close($mh);
        return  $result;
    }
    
    public static function batchDecodeJson($arr) 
    {
        if (is_array($arr) && count($arr)){
            $result = array();
            foreach($arr as $k => $v) {
                $result[$k] = json_decode($v, 1);
            }
            return $result;
        }
    }
}
