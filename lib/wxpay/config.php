<?php
class Config{
    private $cfg = array(
        'url'=>'https://pay.swiftpass.cn/pay/gateway',
        'mchId'=>'102560554468',
		//'mchId'=>'7551000001',
        'key'=>'41a1c334e34ca9a399b06b2e152d4e5c',
        //'key'=>'9d101c97133837e13dde2d32a5054abb',
        'version'=>'1.0'
       );
    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }
}
?>