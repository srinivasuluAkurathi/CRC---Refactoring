<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function test(){
    echo '1234';exit;
}
function create_growsumo_customer($grsm_cust_data){
            if(isset($grsm_cust_data['sent_from']) && $grsm_cust_data['sent_from'] == 'masterclass'){
                if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
                    $grsm_cust_data['ip_address'] = trim(end(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));
                } else {
                    $grsm_cust_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
                }
            }
            $ch = curl_init('https://api.growsumo.com/v1/customers');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type:  application/json'));
            $auth = GROWSUMO_PK.':'.GROWSUMO_SK;

            $data = json_encode(array('key' => $grsm_cust_data['cust_key'],'partner_key' => $grsm_cust_data['cust_ref'],'email' => $grsm_cust_data['cust_email'],'name' => $grsm_cust_data['cust_name'],'ip_address' => $grsm_cust_data['ip_address']));

            curl_setopt($ch, CURLOPT_USERPWD, $auth);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            $response = curl_exec($ch);

            curl_close($ch);

            if($response === false || $response['http_code'] != 200) {
                if (curl_error($ch)) {
                $response .= "\n  ". curl_error($ch); 
                @mail('debuglog@creditrepaircloud.com','Failed creating growsumo customer',$response."<br> Data:".print_r($grsm_cust_data));
                }
            }
        }

        /*
        # function to get a customer detail from growsumo
        # Para: $cust_id
        # process: Calling grousumo endpoint to get custome data
        */
        function get_growsumo_customer($cust_id){
            $ch = curl_init('https://api.growsumo.com/v1/customers/'.$cust_id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $auth = GROWSUMO_PK.':'.GROWSUMO_SK;
            curl_setopt($ch, CURLOPT_USERPWD, $auth);
            $response = curl_exec($ch);
            curl_close($ch);
            if($response === false || $response['http_code'] != 200) {
                if (curl_error($ch)) {
                $response .= "\n  ". curl_error($ch);
                @mail('debuglog@creditrepaircloud.com','Failed creating growsumo customer',$response."<br> Data:".print_r($grsm_cust_data));
                }
            }
            return json_decode($response);
        }
        /*
        # function to get a customer detail from growsumo
        # Para: $cust_id
        # process: Calling grousumo endpoint to get custome data
        */
        function update_growsumo_customer($update_grsm_cust_arr){
            if(isset($update_grsm_cust_arr['sent_from']) && $update_grsm_cust_arr['sent_from'] == 'masterclass'){
                if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
                    $update_grsm_cust_arr['ip_address'] = trim(end(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));
                } else {
                    $update_grsm_cust_arr['ip_address'] = $_SERVER['REMOTE_ADDR'];
                }
            }
            
            $ch = curl_init('https://api.growsumo.com/v1/customers/'.$update_grsm_cust_arr['old_customer_key']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type:  application/json'));
            $auth = GROWSUMO_PK.':'.GROWSUMO_SK;
            
            $data = json_encode(array('name' => $update_grsm_cust_arr['full_name'],'ip_address'=>$update_grsm_cust_arr['ip_address'],'key'=>$update_grsm_cust_arr['new_customer_key']));
            curl_setopt($ch, CURLOPT_USERPWD, $auth);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $response = curl_exec($ch);
            curl_close($ch);
            if($response === false || $response['http_code'] != 200) {
                if (curl_error($ch)) {
                $response .= "\n  ". curl_error($ch);
                @mail('debuglog@creditrepaircloud.com','Failed creating growsumo customer',$response."<br> Data:".print_r($grsm_cust_data));
                }
            }
    }
    /*
        # Objective: To create a transaction in the growsumo
        # Para: $grsm_cust_tran_data(Array)
        # Functionality : Calling grousumo endpoint to create customer with PK and and SK
        */
        function create_growsumo_transaction($grsm_cust_tran_data){
            $CI =& get_instance();
            $ch = curl_init('https://api.growsumo.com/v1/customers/'.$grsm_cust_tran_data['cust_key'].'/transactions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type:  application/json'));
            $auth = GROWSUMO_PK.':'.GROWSUMO_SK;
            $data = json_encode(array('key' => $grsm_cust_tran_data['tran_key'],'product_key'=>'crc_masterclass','amount' => $grsm_cust_tran_data['amount'],'currency' => 'USD'));
            curl_setopt($ch, CURLOPT_USERPWD, $auth);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $response = curl_exec($ch);
            curl_close($ch);       
            
            if($response === false || $response['http_code'] != 200) {
                if (curl_error($ch)) {
                $response .= "\n  ". curl_error($ch); 
                @mail('debuglog@creditrepaircloud.com','Failed creating growsumo transaction',$response."<br> Data:".print_r($grsm_cust_tran_data));
                }
             }
             $transaction_log_array = array(
                'customer_email'=>$grsm_cust_tran_data['cust_email'],
                'amount'=>$grsm_cust_tran_data['amount'],
                'transaction_key'=>$grsm_cust_tran_data['tran_key'],
                'cust_key'=>$grsm_cust_tran_data['cust_key'],
                'partner_key'=>$grsm_cust_tran_data['cust_ref'],
                'status'=>$response
             );
             $CI->common_repository->insertGenData(DB_DEFAULT, 'cro_growsumo_transaction_logs',$transaction_log_array);

        }


?>