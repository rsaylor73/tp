<?php 
class QRGenerator { 
     
    protected $size; 
    protected $data; 
    protected $encoding; 
    protected $errorCorrectionLevel; 
    protected $marginInRows; 
    protected $debug; 
     
    public function __construct($data='http://www.phpgang.com',$size='300',$encoding='UTF-8',$errorCorrectionLevel='L',$marginInRows=4,$debug=false) { 
         
        $this->data=urlencode($data); 
        $this->size=($size>100 && $size<800)? $size : 300; 
        $this->encoding=($encoding == 'Shift_JIS' || $encoding == 'ISO-8859-1' || $encoding == 'UTF-8') ? $encoding : 'UTF-8'; 
        $this->errorCorrectionLevel=($errorCorrectionLevel == 'L' || $errorCorrectionLevel == 'M' || $errorCorrectionLevel == 'Q' || $errorCorrectionLevel == 'H') ?  $errorCorrectionLevel : 'L';
        $this->marginInRows=($marginInRows>0 && $marginInRows<10) ? $marginInRows:4; 
        $this->debug = ($debug==true)? true:false;     
    } 
    public function generate(){ 
         
        $QRLink = "https://chart.googleapis.com/chart?cht=qr&chs=".$this->size."x".$this->size.                 
                   "&chl=" . $this->data .  
                   "&choe=" . $this->encoding . 
                   "&chld=" . $this->errorCorrectionLevel . "|" . $this->marginInRows; 
        if ($this->debug) echo   $QRLink;          
        return $QRLink; 
    } 
}
?>
