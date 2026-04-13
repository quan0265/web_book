<?php 

class Pagination{
	private $total_row;// total row
	private $row_one_page; // num row one page
	private $num_page_show ; // num page show
	private $total_page;
	private $current_page;
	private $page_url; // type/?page=

	public function __construct($total_row, $current_page=1, $row_one_page=5, $page_url=null, $num_page_show=5){
		$this->total_row 	= $total_row;
		$this->row_one_page	= $row_one_page;
		if ($num_page_show%2==0) {
			$num_page_show 		= $num_page_show + 1;
		}
		$this->num_page_show 	= $num_page_show;
		$this->current_page = $current_page;
		$this->total_page  	= ceil($total_row/$row_one_page);
		$this->page_url = $page_url;
	}
	public function html(){
		
		$paginationHTML 	= '';
		if($this->total_page > 1){
			// $page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			// if(isset($_GET['page'])){
			// 	if((int)($_GET['page'])>=10){
			// 		$page_url = substr($page_url,0,-8);
			// 	}
			// 	else{
			// 		$page_url = substr($page_url,0,-7);
			// 	}
			// }

			$start 	= '';
			$prev 	= '';
			if($this->current_page > 1){
				$start 	= "<li class='page-item'><a class='page-link' data-page='1' href='{$this->page_url}1'><<</a></li>";
				$prev 	= "<li class='page-item'><a class='page-link' data-page='".($this->current_page-1)."' href='{$this->page_url}".($this->current_page-1)."'><</a></li>";
			}
			$next 	= '';
			$end 	= '';
			if($this->current_page < $this->total_page){
				$next 	= "<li class='page-item'><a class='page-link' data-page='".($this->current_page+1)."' href='{$this->page_url}".($this->current_page+1)."'>></a></li>";
				$end 	= "<li class='page-item'><a class='page-link' data-page='".$this->total_page."' href='{$this->page_url}".$this->total_page."'>>></a></li>";
			}

			if($this->num_page_show < $this->total_page){
				if($this->current_page == 1){
					$startPage 	= 1;
					$endPage 	= $this->num_page_show;
				}else if($this->current_page == $this->total_page){
					$startPage		= $this->total_page - $this->num_page_show + 1;
					$endPage		= $this->total_page;
				}else{
					$startPage		= $this->current_page - ($this->num_page_show-1)/2;
					$endPage		= $this->current_page + ($this->num_page_show-1)/2;
					if($startPage < 1){
						$endPage	= $endPage + 1;
						$startPage 	= 1;
					}
					if($endPage > $this->total_page){
						$endPage	= $this->total_page;
						$startPage 	= $endPage - $this->num_page_show + 1;
					}
				}

			}else{
				$startPage		= 1;
				$endPage		= $this->total_page;
			}
			/**************/
			$listPages = '';
			for($i = $startPage; $i <= $endPage; $i++){
				if($i == $this->current_page) {
					$listPages .= "<li class='page-item active'><a class='page-link' data-page='".$i."' href='#'>".$i.'</a>';
				}else{
					$listPages .= "<li class='page-item'><a class='page-link' data-page='".$i."' href='{$this->page_url}".$i."'>".$i.'</a>';
				}
			}
			$paginationHTML = '<ul class="pagination pagination-sm pagination-bordered mb-0">'.$start.$prev.$listPages.$next.$end.'</ul>';
		}
		return $paginationHTML;
	}
}



 ?>