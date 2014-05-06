<?php 
/*
#########################
ระบบคำนวนเงินเดือน เวลาทำงาน และค่าคอมมิสชั่น
ผู้พัฒนา: นาย บรรหาร เนรวงค์
พัฒนาเมื่อ: 2014-05-02 13:10 AM

ภายในประกอบด้วย

- index()
- findTimeWork() หาเวลาทำงาน
- findNewSalary() หาเงินเดือนใหม่
- findCommission() หาค่าคอมมิสชั่น
- findAverage() หาค่าเฉลี่ยของเงินเดือน คอมมิสชั่น ยอดขาย เงินเดือนใหม่
- findMin() หาค่าน้อยสุดของเงินเดือน คอมมิสชั่น ยอดขาย เงินเดือนใหม่
- findMax() หาค่ามากที่สุดของเงินเดือน คอมมิสชั่น ยอดขาย เงินเดือนใหม่
#########################
*/
class PspAss3 extends CI_Controller {//a

	function __construct()//a
	{		
		parent::__construct();//a
		$this->load->helper(array('form', 'url'));//a
		$this->load->helper('date');//a
		
	}
	public function index()
	{
		$this->load->model('Program3Model'); //โหลดโมเดล Program3Model//a
		
		$listEmp = $this->Program3Model->findByAll();//a
		
		foreach($listEmp->result() as $row){//a
			$this->Program3Model->setEmpId($row->empid);//เซ็ททไอดีี่จะค้นหา //a

			$salaryOld = $row->salary;//a
			$yearsDiff = $this->findTimeWork();//หาเวลาทำงานแต่ล่ะID//a
			$newSalary = $this->findNewSalary($yearsDiff,$salaryOld);//เรียกใช้findNewSalary() หาเงินเดือนใหม่//a
			$saleAmount= $row->saleamount;
			$commission = $this->findCommission($saleAmount);//สั่งหาค่าคอมมิสชั่น//a
		}
		$data['listEmp'] = $this->Program3Model->findByAll();//a
		
		$this->findAverage();//สั่งหาค่าเฉลี่ย//a
		$this->findMin();//a
		$this->findMax();//a
		
		$data['averageSalary'] = $this->Program3Model->getAverageSalary();//ดึงค่าเฉลี่ยเงินเดือนที่เก็บไว้ไปให้วิว//a
		$data['averageSaleAmount'] = $this->Program3Model->getAverageSaleAmount();//a
		$data['averageCommission'] = $this->Program3Model->getAverageCommission();//a
		$data['averageNewSalary'] = $this->Program3Model->getAverageNewSalary();//a
		
		$data['minSalary'] = $this->Program3Model->getMinSalary();//ดึงค่าน้อยสุดไปให้วิว//a
		$data['minSaleAmount'] = $this->Program3Model->getMinSaleAmount();//a
		$data['minCommission'] = $this->Program3Model->getMinCommission();//a
		$data['minNewSalary'] = $this->Program3Model->getMinNewSalary();//a
		
		$data['maxSalary'] = $this->Program3Model->getMaxSalary();//ดึงค่ามากที่สุดไปให้วิว//a
		$data['maxSaleAmount'] = $this->Program3Model->getMaxSaleAmount();//a
		$data['maxCommission'] = $this->Program3Model->getMaxCommission();//a
		$data['maxNewSalary'] = $this->Program3Model->getMaxNewSalary();//a
		
		
		$this->load->view('psp_ass3',$data);//a
		//$this->load->view('testController',$data);//a
	}
	
	function findTimeWork(){//a
	
		$this->load->model('Program3Model'); //โหลดโมเดล Program3Model//a
		//$this->Program3Model->setEmpId("57001");//เซ็ททไอดีี่จะค้นหา //a
		$date = $this->Program3Model->findByPK();//a
		
		foreach($date->result() as $row){//a
			$dateOld  = $row->datein;//a
		}
		$dataNow = unix_to_human(now());//ดึงค่าเวลาปัจจุบันมาใช้ date("Y-m-d H:i:s");//a
		$dataNow = date("Y-m-d");//ปรับฟอแมตให้เหลือแค่ ปี เดือน วัน//a
		
		$timeDiff = abs(strtotime($dataNow) - strtotime($dateOld));//a
		$yearsDiff = floor($timeDiff / (365*60*60*24));//a

		//$newSalary = $this->findNewSalary($yearsDiff,$salaryOld);//เรียกใช้findNewSalary() หาเงินเดือนใหม่
		//$commission = $this->findCommission($saleAmount);//สั่งหาค่าคอมมิสชั่น
		
		//$data['salaryOld'] = $salaryOld;
		//$data['commission'] = $commission;
		//$this->load->view('testController',$data);
		return $yearsDiff;//a
	}
	
	function findNewSalary($yearsDiff,$salaryOld){//a
		$newSalary = 0;//a
		
		if($yearsDiff >= 10){//กรณีอำยุงำนตั้งแต่ 10 ปีขึ้นไป ให้ขึ้นเงินเดือน 10%//a
			$newSalary = (float)$salaryOld * 0.1;//a
		}else{//กรณีอำยุงำนไม่ถึง 10 ปี ขึ้นเงินเดือน 5%//a
			$newSalary = (float)$salaryOld * 0.05;//a
		}
		
		$this->load->model('Program3Model'); //โหลดโมเดล Program3Model//a
		$this->Program3Model->setNewSalary($newSalary);//เซ็ทค่าเงือนเดือนใหม่ตามที่หาได้//a
		$this->Program3Model->updateNewSalary();//อัพเดทลงในฐานข้อมูล//a
		
		
		return $newSalary;//a
	}
	
	function findCommission($saleAmount){//a
		$commission = 0;//a
		
		if($saleAmount >= 500000){//กรณีมียอดขำยตั้งแต่ 500,000 ขึ้นไป คิดค่ำนำยหน้ำเป็น 0.2% จำกยอดขำย//a
			$commission = $saleAmount*0.002;//a
		}
		
		if($saleAmount < 500000){//กรณียอดขำยไม่ถึง 500,000 บำท คิดค่ำนำยหน้ำเป็น 0.1% จำกยอดขำย//a
			$commission = $saleAmount*0.001;//a
		}
		
		$this->load->model('Program3Model'); //โหลดโมเดล Program3Model//a
		$this->Program3Model->setCommission($commission);//เซ็ทค่าคอมมิสชั่นตามที่หาได้//a
		$this->Program3Model->updateCommission();//อัพเดทลงในฐานข้อมูล//a
		
		return $commission;//a
	}
	
	function findAverage(){//a
		$this->load->model('Program3Model'); //โหลดโมเดล Program3Model//a
		$dataAll = $this->Program3Model->findByAll();//เพื่อดึงข้อมูลมาทั้งหมด//a
		
		$zimaSalary = 0;//เก็บค่าเงือนเดือนทั้งหมดรวมกัน//a
		$countArray = 0;//นับจำนวนอาเรยไว้เพื่อนำมาเป้นตัวหารหาค่าเฉลี่ย//a
		
		foreach($dataAll->result() as $row){//วนลูปเพื่อคำนวณค่าเฉลี่ยของSalary//a
			$zimaSalary  = (double)$zimaSalary + (double)$row->salary;//a
			$countArray = $countArray + 1;//a
		}
		
		$averageSalary = (double)$zimaSalary / (double)$countArray;//คำนวณค่าเฉลี่ย//a
		$this->Program3Model->setAverageSalary($averageSalary);//เก็บค่าไว้รอแสดงผล//a
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		$zimaSaleAmount = 0;//เก็บค่ายอดขายทั้งหมดรวมกัน//a
		$countArray = 0;//นับจำนวนอาเรยไว้เพื่อนำมาเป้นตัวหารหาค่าเฉลี่ย//a
		
		foreach($dataAll->result() as $row){//วนลูปเพื่อคำนวณค่าเฉลี่ยของSaleAmount//a
			$zimaSaleAmount  = (double)$zimaSaleAmount + (double)$row->saleamount;//a
			$countArray = $countArray + 1;//a
		}
		
		$averageSaleAmount = (double)$zimaSaleAmount / (double)$countArray;//คำนวณค่าเฉลี่ย//a
		$this->Program3Model->setAverageSaleAmount($averageSaleAmount);//เก็บค่าไว้รอแสดงผล//a
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		$zimaCommission = 0;//เก็บค่าคอมมิสชั่นทั้งหมดรวมกัน//a
		$countArray = 0;//นับจำนวนอาเรยไว้เพื่อนำมาเป้นตัวหารหาค่าเฉลี่ย//a
		
		foreach($dataAll->result() as $row){//วนลูปเพื่อคำนวณค่าเฉลี่ยของCommission//a
			$zimaCommission  = (double)$zimaCommission + (double)$row->commission;//a
			$countArray = $countArray + 1;//a
		}
		
		$averageCommission = (double)$zimaCommission / (double)$countArray;//คำนวณค่าเฉลี่ย//a
		$this->Program3Model->setAverageCommission($averageCommission);//เก็บค่าไว้รอแสดงผล//a
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		
		$zimaNewSalary = 0;//เก็บค่าเงินเดือนใหม่ั้งหมดรวมกัน//a
		$countArray = 0;//นับจำนวนอาเรยไว้เพื่อนำมาเป้นตัวหารหาค่าเฉลี่ย//a
		
		foreach($dataAll->result() as $row){//วนลูปเพื่อคำนวณค่าเฉลี่ยของCommission//a
			$zimaNewSalary   = (double)$zimaNewSalary  + (double)$row->newsalary;//a
			$countArray = $countArray + 1;//a
		}
		
		$averageNewSalary = (double)$zimaNewSalary / (double)$countArray;//คำนวณค่าเฉลี่ย//a
		$this->Program3Model->setAverageNewSalary($averageNewSalary);//เก็บค่าไว้รอแสดงผล//a
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		
	}
	
	function findMin(){//a
		$this->load->model('Program3Model'); //โหลดโมเดล Program3Model//a
		$dataAll = $this->Program3Model->findByAll();//เพื่อดึงข้อมูลมาทั้งหมด//a
		
		$minSalary = array();//a
		$count = 0;//a
		
		foreach ($dataAll->result() as $row) {//a
			$minSalary[$count] = $row->salary;//a
			$count = $count+1;//a
		}
		
		$this->Program3Model->setMinSalary(min($minSalary));//เก็บค่าไว้รอแสดงผล//a
		
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		
		$minSaleAmount = array();//a
		$count = 0;//a
		
		foreach ($dataAll->result() as $row) {//a
			$minSaleAmount[$count] = $row->saleamount;//a
			$count = $count+1;//a
		}
		
		$this->Program3Model->setMinSaleAmount(min($minSaleAmount));//เก็บค่าไว้รอแสดงผล//a
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		
		$minCommission = array();//a
		$count = 0;//a
		
		foreach ($dataAll->result() as $row) {//a
			$minCommission[$count] = $row->commission;//a
			$count = $count+1;//a
		}
		
		$this->Program3Model->setMinCommission(min($minCommission));//เก็บค่าไว้รอแสดงผล//a
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		
		$minNewSalary = array();//a
		$count = 0;//a
		
		foreach ($dataAll->result() as $row) {//a
			$minNewSalary[$count] = $row->newsalary;//a
			$count = $count+1;//a
		}
		
		$this->Program3Model->setMinNewSalary(min($minNewSalary));//เก็บค่าไว้รอแสดงผล//a
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		
	}
	
	function findMax(){//a
		$this->load->model('Program3Model'); //โหลดโมเดล Program3Model//a
		$dataAll = $this->Program3Model->findByAll();//เพื่อดึงข้อมูลมาทั้งหมด//a
		
		$maxSalary = array();//a
		$count = 0;//a
		
		foreach ($dataAll->result() as $row) {//a
			$maxSalary[$count] = $row->salary;//a
			$count = $count+1;//a
		}
		
		$this->Program3Model->setMaxSalary(max($maxSalary));//เก็บค่าไว้รอแสดงผล//a
		
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		
		$maxSaleAmount = array();//a
		$count = 0;//a
		
		foreach ($dataAll->result() as $row) {//a
			$maxSaleAmount[$count] = $row->saleamount;//a
			$count = $count+1;//a
		}
		
		$this->Program3Model->setMaxSaleAmount(max($maxSaleAmount));//เก็บค่าไว้รอแสดงผล//a
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		
		$maxCommission = array();//a
		$count = 0;//a
		
		foreach ($dataAll->result() as $row) {//a
			$maxCommission[$count] = $row->commission;//a
			$count = $count+1;//a
		}
		
		$this->Program3Model->setMaxCommission(max($maxCommission));//เก็บค่าไว้รอแสดงผล//a
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		
		$maxNewSalary = array();//a
		$count = 0;//a
		
		foreach ($dataAll->result() as $row) {//a
			$maxNewSalary[$count] = $row->newsalary;//a
			$count = $count+1;//a
		}
		
		$this->Program3Model->setMaxNewSalary(max($maxNewSalary));//เก็บค่าไว้รอแสดงผล//a
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		
	}
}

/* End of file pspAss3.php */
/* Location: ./application/controllers/pspAss3.php */