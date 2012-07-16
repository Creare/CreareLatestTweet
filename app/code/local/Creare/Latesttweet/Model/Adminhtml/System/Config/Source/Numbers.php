<?php
class Creare_Latesttweet_Model_Adminhtml_System_Config_Source_Numbers
{
    public function toOptionArray()
    {
		$array = array();
		for($i = 1; $i<=10; $i++)
		{
			$array[] = array('value'=>$i, 'label'=>ucfirst($i));
		}        
        return $array;
    }
}
