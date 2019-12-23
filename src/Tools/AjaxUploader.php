<?php

namespace Just\Tools;

use Illuminate\Support\Facades\Storage;

/**
 * Class receives data from js-script and upload files
 *
 * @author Viacheslav Dymarchuk
 */
class AjaxUploader {
    
    private $file_name = '';
    private $file_size = 0;
    private $deny_ext = ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'pl', 'cgi', 'html', 'htm', 'js', 'asp', 'aspx', 'bat', 'sh', 'cmd'];
    private $upload_errors = array(
        UPLOAD_ERR_OK => "No errors.",
        UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
        UPLOAD_ERR_FORM_SIZE => "Larger than form MAX_FILE_SIZE.",
        UPLOAD_ERR_PARTIAL => "Partial upload.",
        UPLOAD_ERR_NO_FILE => "No file.",
        UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
        UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
        UPLOAD_ERR_EXTENSION => "File upload stopped by extension."
    );
    private $finish_function = '';
    private $cross_origin = false;
    
    /**
     * Server request data
     * 
     * @var array $request
     */
    private $request = [
        'ax-max-file-size' => '8M',
        'ax-file-path' => 'storage/photos',
        'ax-allow-ext' => [],
        'ax-override' => false
    ];
      
    private $tmp_name = "/tmp";
    
    /**
     * Block id
     * 
     * @var int $block_id 
     */
    private $block_id = null;
    
    /**
     * Item id in the block
     * 
     * @var int $item_id
     */
    private $item_id = null;
    
    /**
     * If image is uploaded should it be cropped
     * 
     * @var boolean $shouldBeCropped
     */
    private $shouldBeCropped = false;
    
    /**
     * Current csrf token
     * 
     * @var string $token 
     */
    private $token = '';


    /**
     * Full file path after upload and before resizing
     * 
     * @var string $fullFilePath
     */
    private $imageCode = null;
    
    private $width = 0;
            
    private $height = 0;
            
    function __construct($deny_ext = array()) {
        $this->request = array_replace($this->request, \Request::all());
        $this->tmp_name = \Request::file()['ax_file_input']->getPath()."/".\Request::file()['ax_file_input']->getBasename();
        
        //set data from JAVASCRIPT
        $this->setMaxFileSize($this->request['ax-max-file-size']);
        $this->setUploadPath($this->request['ax-file-path']);
        $this->setAllowExt(!empty($this->request['ax-allow-ext']) ? explode('|', $this->request['ax-allow-ext']) : array() );
        $this->setOverride($this->request['ax-override']);
        
        //set deny
        $this->deny_ext += $deny_ext;
        
        //active parameters neccessary for upload
        $this->file_name = isset($this->request['ax-file-name']) ? $this->request['ax-file-name'] : \Request::file()['ax_file_input']->getClientOriginalName();
        $this->file_size = isset($this->request['ax-file-size']) ? $this->request['ax-file-size'] : \Request::file()['ax_file_input']->getSize();
        
        //create a temp folder for uploading the chunks
        $ini_val = @ini_get('upload_tmp_dir');
        $this->temp_path = $ini_val ? $ini_val : sys_get_temp_dir();
        $this->temp_path = $this->temp_path . DIRECTORY_SEPARATOR;
        
    }

    /**
     * Set the maximum file size, expected string with byte notation
     * @param string $max_file_size
     */
    public function setMaxFileSize($max_file_size = '10M') {
        $this->max_file_size = $max_file_size;
    }

    /**
     * Set the allow extension file to upload
     * @param array $allow_ext
     */
    public function setAllowExt($allow_ext = array()) {
        $this->allow_ext = $allow_ext;
    }

    /**
     * Set the upload path as string
     * @param string $upload_path
     */
    public function setUploadPath($upload_path) {
        $upload_path = rtrim($upload_path, '\\/');
        $this->upload_path = $upload_path . DIRECTORY_SEPARATOR;
        // Create thumb path if do not exits
        $this->makeDir($this->upload_path);
    }

    public function setOverride($bool) {
        $this->override = $bool;
    }

    private function makeDir($dir) {
        if (!file_exists(public_path($dir)) && !empty($dir)) {
            $done = @mkdir(public_path($dir), 0775, true);
            if (!$done) {
                return $this->message(-1, 'Cannot create upload folder');
            }
        }
    }

    //Check if file size is allowed
    private function checkSize() {
        //------------------max file size check from js
        $max_file_size = $this->max_file_size;
        $size = $this->file_size;
        $rang = substr($max_file_size, -1);
        $max_size = !is_numeric($rang) && !is_numeric($max_file_size) ? str_replace($rang, '', $max_file_size) : $max_file_size;
        if ($rang && $max_size) {
            switch (strtoupper($rang)) {//1024 or 1000??
                case 'Y': $max_size = $max_size * 1024; //Yotta byte, will arrive such day???
                case 'Z': $max_size = $max_size * 1024;
                case 'E': $max_size = $max_size * 1024;
                case 'P': $max_size = $max_size * 1024;
                case 'T': $max_size = $max_size * 1024;
                case 'G': $max_size = $max_size * 1024;
                case 'M': $max_size = $max_size * 1024;
                case 'K': $max_size = $max_size * 1024;
            }
        }

        if (!empty($max_file_size) && $size > $max_size) {
            return false;
        }
        //-----------------End max file size check

        return true;
    }

    //Check if file name is allowed and remove illegal windows chars
    private function checkName() {
        //comment if not using windows web server
        $windowsReserved = array('CON', 'PRN', 'AUX', 'NUL', 'COM1', 'COM2', 'COM3', 'COM4', 'COM5', 'COM6', 'COM7', 'COM8', 'COM9',
            'LPT1', 'LPT2', 'LPT3', 'LPT4', 'LPT5', 'LPT6', 'LPT7', 'LPT8', 'LPT9');
        $badWinChars = array_merge(array_map('chr', range(0, 31)), array("<", ">", ":", '"', "/", "\\", "|", "?", "*"));

        $this->file_name = str_replace($badWinChars, '', $this->file_name);

        //check if legal windows file name
        if (in_array($this->file_name, $windowsReserved)) {
            return false;
        }
        return true;
    }

    /**
     * Check if a file exits or not and calculates a new name for not oovverring other files
     * @param string $upload_path
     */
    private function checkFileExits($upload_path = '') {
        if ($upload_path == ''){
            $upload_path = $this->upload_path;
        }
        if (!$this->override) {
            usleep(rand(100, 900));

            $filename = $this->file_name;
            //$upload_path 	= $this->upload_path;

            $file_data = pathinfo($filename);
            $file_base = $file_data['filename'];
            $file_ext = $file_data['extension']; //PHP 5.2>
            //Disable this lines of code to allow file override
            $c = 0;
            while (file_exists($upload_path . $filename)) {
                $find = preg_match('/\((.*?)\)/', $filename, $match);
                if (!$find){
                    $match[1] = 0;
                }
                else{
                    $file_base = str_replace("(" . $match[1] . ")", "", $file_base);
                }

                $match[1] ++;

                $filename = $file_base . "(" . $match[1] . ")." . $file_ext;
            }
            // end
            $this->file_name = $filename;
        }
    }

    public function _checkFileExists() {
        $filename = $this->file_name;
        $upload_path = $this->upload_path;
        return file_exists($upload_path . $filename);
    }

    public function deleteFile() {
        $del = @unlink($this->upload_path . $this->file_name);
        return $del;
    }

    //Check if file type is allowed for upload
    private function checkExt() {
        $file_ext = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));

        //extensions not allowed for security reason and check if is allowed extension
        if (in_array($file_ext, $this->deny_ext) || (!in_array($file_ext, $this->allow_ext) && count($this->allow_ext))) {
            return false;
        }
        return true;
    }

    private function uploadAjax() {
        $currByte = isset($this->request['ax-start-byte']) ? $this->request['ax-start-byte'] : 0;
        $isLast = isset($this->request['ax-last-chunk']) ? $this->request['ax-last-chunk'] : 'true';
        
        $flag = FILE_APPEND;
        if ($currByte == 0) {
            $this->checkFileExits($this->temp_path); //check if file exits in temp path, not so neccessary
            $flag = 0;
        }

        //we get the path only for the first chunk
        $full_path = $this->temp_path . $this->file_name;

        //formData post files just normal upload in \Request::file, older ajax upload post it in input
        $post_bytes = file_get_contents(isset(\Request::file()['ax_file_input']) ? $this->tmp_name : 'php://input');

        //some rare times (on very very fast connection), file_put_contents will be unable to write on the file, so we try until it writes
        $try = 20;
        while (@file_put_contents($full_path, $post_bytes, $flag) === false && $try > 0) {
            usleep(50);
            $try--;
        }

        if (!$try) {
            return $this->message(-1, 'Cannot write on file.');
        }

        //delete the temporany chunk
        if (isset(\Request::file()['ax_file_input'])) {
            @unlink($this->tmp_name);
        }
        
        //if it is not the last chunk just return success chunk upload
        if ($isLast != 'true') {
            return $this->message(1, 'Chunk uploaded');
        } else {
            $this->checkFileExits($this->upload_path);
            $ret = rename($full_path, $this->upload_path . $this->file_name); //move file from temp dir to upload dir TODO this can be slow on big files and diffrent drivers
            if ($ret) {
                $extra_info = $this->finish();
                return $this->message(1, 'File uploaded', $extra_info);
            } else {
                return $this->message(1, 'File move error', $extra_info);
            }
        }
    }

    private function uploadStandard() {
        $this->checkFileExits($this->upload_path);
        $dir = str_replace("../storage/app/public/", "", \Request::all()['ax-file-path']);
        $result = Storage::disk('public')->putFileAs($dir, \Request::file()['ax_file_input'], $this->file_name);
        
        if (!$result) { //if any error return the error
            return $this->message(-1, 'File move error');
        } else {
            $extra_info = $this->finish();
            return $this->message(1, 'File uploaded', $extra_info);
        }
    }

    public function uploadFile() {
        if ($this->checkFile()) {//this checks every chunk FIXME is right?
            $is_ajax = isset($this->request['ax-last-chunk']) && isset($this->request['ax-start-byte']);
            if ($is_ajax) {//Ajax Upload, FormData Upload and FF3.6 php://input upload
                return $this->uploadAjax();
            } else { //Normal html and flash upload
                return $this->uploadStandard();
            }
        }
    }

    private function finish() {
        ob_start();
        $request = request();
        $request->request->set('currentFile', $this->file_name);
        $model = app('Just\Controllers\AdminController')->handleForm($request);

        $this->imageCode = $model->image;
        $this->shouldBeCropped = $model->shouldBeCropped;
        $this->block_id = $model->block_id;
        $this->item_id = $model->id;
        $this->width = $model->layout()->width;
        $this->height = $model->layout()->width;
        $this->token = csrf_token();
        if($model->shouldBeCropped and !empty($model->parameters->cropDimentions)){
            $cropDimentions = explode(":", $model->parameters->cropDimentions);
            if(count($cropDimentions) == 2){
                $wh = $cropDimentions[0]/$cropDimentions[1];
                $this->width = $model->layout()->width;
                $this->height = round($this->width / $wh);
            }
        }
        
        //run the external user success function
        if ($this->finish_function && function_exists($this->finish_function)) {
            try {
                call_user_func($this->finish_function, $this->upload_path . $this->file_name);
            } catch (Exception $e) {
                echo $e->getTraceAsString();
            }
        }
        $value = ob_get_contents();
        ob_end_clean();
        return $value;
    }

    private function checkFile() {
        //check uploads error
        if (isset(\Request::file()['ax_file_input'])) {
            if (\Request::file()['ax_file_input']->getError() !== UPLOAD_ERR_OK) {
                return $this->message(-1, $this->upload_errors[\Request::file()['ax_file_input']->getError()]);
            }
        }

        //check ext
        $allow_ext = $this->checkExt();
        if (!$allow_ext) {
            return $this->message(-1, 'File extension is not allowed');
        }

        //check name
        $fn_ok = $this->checkName();
        if (!$fn_ok) {
            return $this->message(-1, 'File name is not allowed. System reserved.');
        }

        //check size
        if (!$this->checkSize()) {
            return $this->message(-1, 'File size exceeded maximum allowed: ' . $this->max_file_size);
        }
        return true;
    }

    public function header(\Illuminate\Http\Response &$response) {
        $response->header("Cache-Control", "no-cache, must-revalidate") // HTTP/1.1
                ->header("Expires", "Sat, 26 Jul 1997 05:00:00 GMT") // Date in the past
                ->header("X-Content-Type-Options", "nosniff");
        
        if ($this->cross_origin) {
            $response->header("Access-Control-Allow-Origin", "*")
                    ->header("Access-Control-Allow-Credentials", "false")
                    ->header("Access-Control-Allow-Methods", "OPTIONS, HEAD, GET, POST, PUT, PATCH, DELETE")
                    ->header("Access-Control-Allow-Headers", "Content-Type, Content-Range, Content-Disposition");
        }
    }

    private function message($status, $msg, $extra_info = '') {
        $content = json_encode(array(
            'name' => $this->file_name,
            'size' => $this->file_size,
            'status' => $status,
            'info' => $msg,
            'more' => $extra_info,
            'image' => $this->imageCode,
            'block_id' => $this->block_id,
            'item_id' => $this->item_id,
            'token' => $this->token,
            'crop' => $this->shouldBeCropped,
            'width' => $this->width,
            'height' => $this->height
        ));
        
        $response = response($content);
        
        $this->header($response);
        
        return $response;
    }

    public function onFinish($fun) {
        $this->finish_function = $fun;
    }

}
