<?php 

namespace Admin\Model;
use       Think\Model;

class DataModel extends Model{
	public function __construct() {
        $this->redis = \Think\Cache::getInstance('Redis');
    }

	public function getWeightUnit() {
		$ret = C('WEIGHTUNIT');
		return $ret;
		// $data = $this->redis->get('string:unit');
		// if (empty($data)) {
		// 	$data = [
		// 		'1' => ['id' => '1', 'name' => 'ton', 'packWeight' => 1000, 'status' => 1],
		// 		'2' => ['id' => '2', 'name' => 'Kg', 'packWeight' => 1, 'status' => 1],
		// 		'3' => ['id' => '3', 'name' => 'g', 'packWeight' => 0.001, 'status' => 1],
		// 		'4' => ['id' => '4', 'name' => 'Bag', 'packWeight' => 0, 'status' => 1],
		// 		'5' => ['id' => '5', 'name' => 'Drum', 'packWeight' => 0, 'status' => 1],
		// 	];
		// 	$this->redis->set('string:unit', json_encode($data));
		// } else {
		// 	$data = json_decode($data, true);
		// }
		// $result = [];
		// foreach ($data as $key => $value) {
		// 	if ($value['status'] == 1) {
		// 		unset($value['status']);
		// 		$result[$key] = $value;
		// 	}
		// }
		// return $result;
	}

	public function getPayway() {
		$ret = C('PAYWAP');
		return $ret;
		// $data = $this->redis->get('string:payway');
		// if (empty($data)) {
		// 	$data = [
		// 		'1' => ['id' => '1', 'name' => 'L/C', 'status' => 1],
		// 		'2' => ['id' => '2', 'name' => 'T/T', 'status' => 1],
		// 	];
		// 	$this->redis->set('string:payway', json_encode($data));
		// } else {
		// 	$data = json_decode($data, true);
		// }
		// $result = [];
		// foreach ($data as $key => $value) {
		// 	if ($value['status'] == 1) {
		// 		unset($value['status']);
		// 		$result[$key] = $value;
		// 	}
		// }
		// return $result;
	}
}