<?php 
if(!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * CodeIgniter - Rackspace Cloudfiles API
 *
 * Description:
 * This library helps you use the Rackspace Cloudfiles API more efficiently.
 *
 * For installation and usage: https://bitbucket.org/modomg/codeigniter-rackspace-cloudfiles/
 *
 * @copyright	Copyright (c) 2011 Modo Media Group
 * @version 	1.2
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 **/
#require_once("cloudfiles.php");
class Cfiles{
    
    private $CI;                                   // CodeIgniter instance
	private $auth;                                 // Store authentication
	private $conn;                                 // Store connection

	// Cloud API parameters
	private $cf_username;                          // Cloud Username
	private $cf_api;                               // Cloud API Key
	
	// Main Variables
	public $cf_container;                          // Container to use
	public $cf_folder;                             // Folder to use
    
    // Error Handling
    public $stop_on_error = FALSE;                 // Stop script on error?
    private $errors = array();                     // Hold all errors
   
	function __construct($params = array())
	{
		$this->CI =& get_instance();
		
		log_message('debug', 'RS Cloudfiles Class Initialized');

		$this->initialize($params);
	}
	
	// Initializes the library parameters
	public function initialize($params = array())
	{
		// Set API preferences from the config file if they are not passed in the $params array
		foreach (array('cf_username', 'cf_api') as $key)
		{
			$this->$key = (isset($params[$key])) ? $params[$key] : $this->CI->config->item($key);
		}
        
        #require_once(APPPATH.'libraries/cf/cloudfiles.php');
        require_once(APPPATH.'libraries/cf/CF_Connection.php'); # S @@@@@ 18/April/2013        
        
        //authenticate connection
		$this->auth = new CF_Authentication($this->cf_username, $this->cf_api);
        try
        {
            $this->auth->authenticate();
            
            try
            {
                //create the connection
                $this->conn = new CF_Connection($this->auth);
            }
            catch(Exception $e)
            {
                $this->_show_error($e);
            }
        }
        catch(Exception $e)
        {
            $this->_show_error($e);
        }
	}
        
        public function copy_obj($obj_name,$container_name_source,$container_name_target)
        {
            #$auth = new CF_Authentication();
            $conn = new CF_Authentication($this->cf_username, $this->cf_api);
            $images = $this->conn->get_container($container_name_source);
            
            
            $status = $images->cfs_http->copy_object($obj_name,$container_name_source,$container_name_target,$metadata);
		
        /*if ($status == 404) {
            $m = "Specified object '".$this->name."/".$obj_name;
            $m.= "' did not exist as source to copy from or '".$container_name_target."' did not exist as target to copy to.";
            throw new NoSuchObjectException($m);
        }
        if ($status < 200 || $status > 299) {
            throw new InvalidResponseException(
                "Invalid response (".$status."): ".$this->cfs_http->get_error());
        }*/
            
        }
	
	public function do_container($action='a')
	{
		if($action == 'a') //add
		{
			try
            {
                //Create a remote Container
                $new_container = $this->conn->create_container($this->cf_container);
                
                try
                {
                    //enable logs
                    $new_container->log_retention(TRUE);
                    
                    try
                    {
                        //publish and return URI
                        return $new_container->make_public();
                    }
                    catch(Exception $e)
                    {
                        $this->_handle_error($e);
                        return FALSE;
                    }
                }
                catch(Exception $e)
                {
                    $this->_handle_error($e);
                    return FALSE;
                }
            }
            catch(Exception $e)
            {
                $this->_handle_error($e);
                return FALSE;
            }
		}
		elseif($action == 'd') //delete
		{
            if($my_container = $this->container_info())
            {
                //get all objects
                if($objects = $this->get_objects())
                {
                    foreach($objects as $object)
                    {
                        $object = str_replace($this->cf_container.'/', '', $object->name);

                        //delete object
                        $this->do_object('d', $object);
                    }
                }

                //delete container
                $this->conn->delete_container($this->cf_container);
            }
		}
    }
	
	public function do_object($action='a', $file_name='', $file_location='', $original_file='')
	{
		$my_container = $this->container_info();
		
		if($action == 'a') //add
		{
            try
            {
                //move local file to server
                $my_object = $my_container->create_object($this->cf_folder.$file_name);
                
                try
                {
                    $my_object->load_from_filename($file_location.$file_name);

                    if($original_file != '')
                    {
                        try
                        {
                            $my_object->metadata = array("original" => $original_file);
                            $my_object->sync_metadata();
                        }
                        catch(Exception $e)
                        {
                            $this->_handle_error($e);
                            return FALSE;
                        }
                    }
                }
                catch(Exception $e)
                {
                    $this->_handle_error($e);
                    return FALSE;
                }
                return TRUE;
            }
            catch(Exception $e)
            {
                $this->_handle_error($e);
                return FALSE;
            }
		}
		elseif($action == 'd') //delete
		{
			//delete file
            try
            {
                #echo "<pre>"; print_r($my_container); exit();
                $my_container->delete_object($this->cf_folder.$file_name);
                return TRUE;
            }
            catch(Exception $e)
            {
                $this->_handle_error($e);
                return FALSE;
            }           
            
		}
    }

    # made by S @@@@@ 17/April/2013 for the add file directly to the Cloud file rackspace
    public function do_object_custom($action='a', $file_name='', $file_location='', $original_file='', $tmp_path='')
    {
        $my_container = $this->container_info();
        if($action == 'a') //add
        {
            try
            {
                //move local file to server
                $my_object = $my_container->create_object($file_name);
                try
                {
                    $my_object->load_from_filename($tmp_path);

                    if($original_file != '')
                    {
                        try
                        {
                            $my_object->metadata = array("original" => $original_file);
                            $my_object->sync_metadata();
                        }
                        catch(Exception $e)
                        {
                            $this->_handle_error($e);
                            return FALSE;
                        }
                    }
                }
                catch(Exception $e)
                {
                    $this->_handle_error($e);
                    return FALSE;
                }
            }
            catch(Exception $e)
            {
                $this->_handle_error($e);
                return FALSE;
            }
            return TRUE;
        }
    }
	
	public function container_info()
	{
		try
        {
            $my_container = $this->conn->get_container($this->cf_container);
            
            if(is_object($my_container))
            {
                return $my_container;
            }
            else
            {
                throw new Exception('Container name is not valid.');
                return FALSE;
            }
        }
        catch(Exception $e)
        {
            $this->_handle_error($e);
            return FALSE;
        }
	}
	
	public function get_objects()
	{
		$my_container = $this->container_info();
        
        try
        {
            return $my_container->get_objects(0, NULL, NULL, $this->cf_folder);
        }
        catch(Exception $e)
        {
            $this->_handle_error($e);
            return FALSE;
        }
	}
	
	public function get_object($name)
	{
		$my_container = $this->container_info();
        try
        {
            return $my_container->get_object($name);
        }
        catch(Exception $e)
        {
            $this->_handle_error($e);
            return FALSE;
        }
	}
    
    public function download_object($current_name, $new_name, $location)
    {
        $my_container = $this->container_info();
        try
        {
            $my_file = $my_container->get_object($current_name);
            
            try
            {
                return $my_file->save_to_filename($location.$new_name);
            }
            catch(Exception $e)
            {
                $this->_handle_error($e);
                return FALSE;
            }
        }
        catch(Exception $e)
        {
            $this->_handle_error($e);
            return FALSE;
        }
    }
    
    private function _show_error($e)
    {
        show_error($e->getMessage());
        log_message('error', $e->getMessage());
    }
    
    private function _handle_error($e)
    {
        if($this->stop_on_error == TRUE) 
            $this->_show_error($e);
        else 
            $this->_set_error($e);
    }
    
    private function _set_error($e)
    {
        $this->errors[] = $e->getMessage();
    }
    
    public function has_errors()
    {
        return count($this->errors) > 0 ? TRUE : FALSE;
    }
    
    public function get_errors()
    {
        return $this->errors;
    }

    public function get_container_list()
    {
        return $this->conn->list_containers();
    }
}

/* End of file cfiles.php */
/* Location: ./application/libraries/cfiles.php */