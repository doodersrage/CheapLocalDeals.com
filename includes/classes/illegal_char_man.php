<?PHP

class man_ill_char {
	private $cln_chars,$ill_chars;
	public $text_to_cln;
	
	function __construct() {
		$this->assign_ill_chars();
	}
	
	// builds list of illegal characters
	function assign_ill_chars() {
		global $dbh;
		
		$sql_query = "SELECT
						illegal_char,
						legal_char
					 FROM
						illegal_chars
					 ;";
	
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute();

		$this->ill_chars = array();
		$this->cln_chars = array();
		while ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$this->ill_chars[] = $row['illegal_char'];
			$this->cln_chars[] = $row['legal_char'];
		}

	}
	
	// cleans illegal chars from text
	function clean_text() {
		
		$this->text_to_cln = str_replace($this->ill_chars,$this->cln_chars,$this->text_to_cln);
		
	}
	
}

?>