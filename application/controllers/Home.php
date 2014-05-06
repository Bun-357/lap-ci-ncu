<?php
/*
#########################
�к���ҹ˹ѧ���
���Ѳ��: �.���ķ��� ͹ص���������
�Ѳ�������: 2014-02-11 11:11 AM
���㹻�Сͺ����

- index() ����Ѻ�ʴ����˹���á
- search() ����Ѻ�ʴ��š�ä���˹ѧ���
- add() ����˹ѧ���
- delete($bookId) ź˹ѧ���
#########################
*/
class Home extends CI_Controller 
{
	function __construct()
	{		
		parent::__construct();
		$this->load->model('Book'); //�� $this->Book ������
	}
	
	## �ʴ��Ť����á ##
	function index()
	{		
		$data['listbooks'] = $this->Book->findByAll();
		
		$this->load->view('home', $data);		
	}
	
	## ����˹ѧ��͵������ ##
	function search()
	{
		$bookTitle = $this->input->post('bookTitle');
		
		$data['listbooks'] = $this->Book->findByKeyword('bookTitle', $bookTitle);
		$this->load->view('home', $data);		
	}

	## ����˹ѧ��� ##
	function add()
	{
		/*
		- bookTitle
		- bookAuthor
		- bookISDN
		- bookDate
		*/
		$bookTitle = $this->input->post('bookTitle');
		$bookAuthor = $this->input->post('bookAuthor');
		$bookISDN = $this->input->post('bookISDN');
		$bookDate = $this->input->post('bookDate');
		
		$this->Book->setBookTitle($bookTitle);
		$this->Book->setBookAuthor($bookAuthor);
		$this->Book->setBookISDN($bookISDN);
		$this->Book->setBookDate($bookDate);
		$this->Book->add();
		
		$this->index();
	}
	
	## ź˹ѧ��ͨҡ ���� PK ##
	function delete($bookId)
	{
		$this->Book->setBookId($bookId);
		$this->Book->delete();
		
		$this->index();
	}
}

?>