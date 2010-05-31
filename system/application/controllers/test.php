<?php
class Test extends Controller {

	function test() {
		parent::Controller();
	}

	function index($offset = 0)
	{
		//ob_start();
		$initial_content = $this->ajax_page( 0, 1 );
		//$initial_content = ob_get_contents();
		//ob_end_clean();

		$data['table'] = "<div id='content'>" . $initial_content . "</div>" ;

	    $this->load->view('page',$data);

	}

	function ajax_page($offset = 0, $_return=0)
	{
	  	$this->load->model('model_table');
		$this->load->library('Jquery_pagination');

		$config['base_url'] = site_url('test/ajax_page/');
		/* Here i am indicating to the url from where the pagination links and the table section will be fetched */

		$config['div'] = '#content';
		/* CSS selector  for the AJAX content */

		$limit = 1;
		$config['total_rows'] = $this->model_table->num_rows();
	    $config['per_page'] = $limit;

	    $this->jquery_pagination->initialize($config);

	    $this->load->library('table');

	    $html =  $this->jquery_pagination->create_links() . '<br />'
				.  'lhf';

		if( $_return == 1 )
			return $html;
		else
			echo $html;
	}
}
?>