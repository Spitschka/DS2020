<?php


class FileUpload {
		
	private static $mimeTypesMSOffice = array(
			'application/msword',
			'application/msword',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'application/vnd.ms-word.document.macroEnabled.12',
			'application/vnd.ms-word.template.macroEnabled.12',
			'application/vnd.ms-excel',
			'application/vnd.ms-excel',
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'application/vnd.ms-excel.sheet.macroEnabled.12',
			'application/vnd.ms-excel.template.macroEnabled.12',
			'application/vnd.ms-excel.addin.macroEnabled.12',
			'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'application/vnd.ms-powerpoint',
			'application/vnd.ms-powerpoint',
			'application/vnd.ms-powerpoint',
			'application/vnd.ms-powerpoint',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'application/vnd.openxmlformats-officedocument.presentationml.template',
			'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'application/vnd.ms-powerpoint.template.macroEnabled.12',
			'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
			'application/vnd.ms-access',
			'application/pdf',
			'application/zip'
	);
	
	private static $mimesPicture = [
			'image/png',
			'image/tiff',
			'image/jpeg',
			'image/jpg',
			'image/gif'
	];
	
	private $data = [];
	
	
	public function __construct($data) {
		$this->data = $data;
	}
	
	public function getFileName() {
		return $this->data['uploadFileName'];
	}
	
	public function getExtension() {
		return $this->data['uploadFileExtension'];
	}
	
	public function getMimeType() {
		return $this->data['uploadFileMimeType'];
	}
	
	public function getUploadTime() {
		return $this->data['uploadTime'];
	}
	
	public function getAccessCode() {
		return $this->data['fileAccessCode'];
	}
	
	public function getURLToFile($forceDownload=false) {
		
		if($this->getAccessCode() == '') {
			$this->data['fileAccessCode'] = strtoupper(md5(rand()) . md5(rand()));
			DB::getDB()->query("UPDATE uploads SET fileAccessCode='" . $this->data['fileAccessCode'] . "' WHERE uploadID='" . $this->getID() . "'");
		}
		
		return DB::getGlobalSettings()->urlToIndexPHP . "?page=FileDownload&uploadID=" . $this->getID() . "&accessCode=" . $this->getAccessCode() . (($forceDownload) ? ("&fd=1") : (""));
	}
	
	/**
	 * Direkter Pfad zur Datei
	 * @return string
	 */
	public function getFilePath() {
	    return "uploads/" . $this->getID() . ".dat";
	}
	
	/**
	 * 
	 * @return user|NULL
	 */
	public function getUploader() {
		return user::getUserByID($this->data['uploaderUserID']);
	}
	
	public function getID() {
		return $this->data['uploadID'];
	}
	
	public function delete() {
		@unlink('uploads/' . $this->getID() . ".dat");
		DB::getDB()->query("DELETE FROM uploads WHERE uploadID='" . $this->getID() . "'");
	}
	
	public function getFileTypeIcon() {
		switch(strtolower($this->getExtension())) {
			case 'pdf':
				return 'fa fa-file-pdf-o';
			
			default:
				return 'fa fa-file-o';
		}
	}
	
	public function getFileSize() {
		if(!file_exists("uploads/" . $this->getID() . ".dat")) {
			return 'n/a';
		}
		
		return str_replace(".",",",round(filesize("uploads/" . $this->getID() . ".dat") / 1024 / 1024,2)) . " MB";
	}
	
	
	public function sendFile() {
		if(!file_exists("uploads/" . $this->getID() . ".dat")) {
			new errorPage("Upload existiert nicht!");
		}
		
		header('Content-Description: Dateidownload');
		header('Content-Type: ' . $this->getMimeType());
		header('Content-Disposition: attachment; filename="'. $this->getFileName() . "." . $this->getExtension() . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize("uploads/" . $this->getID() . ".dat"));
		readfile("uploads/" . $this->getID() . ".dat");
		exit(0);
	}
	
	
	public function sendImageWidthMaxWidth($maxWidth) {
		
		if(!file_exists("uploads/" . $this->getID() . ".dat")) {
			new errorPage("Upload existiert nicht!");
		}
		
		$oldSize = getImageSize ( "uploads/" . $this->getID() . ".dat" );
		
		$scale = $maxWidth / $oldSize [0];
		
		$newWidth = round ( $oldSize [0] * $scale );
		$newHeight = round ( $oldSize [1] * $scale );
		
		$altesBild = ImageCreateFromJPEG ( "uploads/" . $this->getID() . ".dat" );
		$neuesBild = imagecreatetruecolor ( $newWidth, $newHeight );
		
		ImageCopyResized ( $neuesBild, $altesBild, 0, 0, 0, 0, $newWidth, $newHeight, $oldSize [0], $oldSize [1] );
		
		header ( "Content-type: image/jpeg" );
		
		ImageJPEG ( $neuesBild );
		
		exit ( 0 ); // Script zur Sicherheit beenden
	}
	
	/**
	 * 
	 * @param int $id
	 * @return FileUpload|null
	 */
	public static function getByID($id) {
		$upload = DB::getDB()->query_first("SELECT * FROM uploads WHERE uploadID='" . DB::getDB()->escapeString($id) . "'");
		if($upload['uploadID'] > 0) {
			return new FileUpload($upload);
		}
		
		return null;
	}
	
	/**
	 * 
	 * @param String $fieldName
	 * @param String $fileName
	 * @return String[]
	 */
	public static function uploadPicture($fieldName, $fileName) {
		
		
		return self::uploadFileImpl($fieldName, self::$mimesPicture, $fileName);	
	}
	
	public static function uploadPDF($fieldName, $fileName) {
		$mimePDF = [
				'application/pdf'
		];
		
		return self::uploadFileImpl($fieldName, $mimePDF, $fileName);	
	}
	
	public static function uploadOfficeDocumentsAndPDF($fieldName, $fileName) {
		$mimes = self::$mimeTypesMSOffice;
		$mimes[] = 'application/pdf';
		return self::uploadFileImpl($fieldName, $mimes, $fileName);	
	}
	
	public static function uploadOfficeDocument($fieldName, $fileName) {
		return self::uploadFileImpl($fieldName, self::$mimeTypesMSOffice, $fileName);
	}
	
	public static function uploadOfficePdfOrPicture($fieldName, $fileName) {
		$mimes = self::$mimeTypesMSOffice;
		$mimes[] = 'application/pdf';
		for($i = 0; $i < sizeof(self::$mimesPicture); $i++) $mimes[] = self::$mimesPicture[$i];
		
		return self::uploadFileImpl($fieldName, $mimes, $fileName);
	}
	
	/**
	 * 
	 * @param String $filename
	 * @param TCPDF $tcpdf
	 */
	public static function uploadFromTCPdf($filename, $tcpdf) {
		$mime = 'application/pdf';
		
		DB::getDB()->query("INSERT INTO uploads
				(
					uploadFileName,
					uploadFileExtension,
					uploadFileMimeType,
					uploadTime,
					uploaderUserID,
                    fileAccessCode
				) values(
					'" . DB::getDB()->escapeString($filename) . "',
					'pdf',
					'" . $mime . "',
					UNIX_TIMESTAMP(),
					" . DB::getSession()->getUser()->getUserID() . ",
					'" . strtoupper(md5(rand()) . md5(rand())) . "'
				)
			");
		
		
		$newID = DB::getDB()->insert_id();
		
		$saveDir = getcwd() . "/uploads/" . $newID . ".dat";
		
		$tcpdf->Output($saveDir, 'F');
				
		$data = DB::getDB()->query_first("SELECT * FROM uploads WHERE uploadID='" . $newID. "'");
		
		return [
				'result' => true,
				'uploadobject' => new FileUpload($data),
				'mimeerror' => false,
				'text' => "Save from TCPDF OK"
		];
		
		
		
	}
	
	private static function uploadFileImpl($fieldName, $mimes, $fileName='') {
		if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
			return [
				'result' => false,
				'uploadobject' => null,
				'mimeerror' => false,
				'text' => "Es gab einen Fehler beim Upload: " . $_FILES['file']['error']
			];			
		}
		
		$mime = null;
		
		if($fileName == '') $fileName = $_FILES[$fieldName]['name'];
		
		$ext = strtolower(array_pop(explode('.',$_FILES[$fieldName]['name'])));
		
		if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $_FILES[$fieldName]['tmp_name']);
			finfo_close($finfo);
			$mimetype = str_replace("; charset=binary", "", $mimetype);
			if(!in_array($mimetype, $mimes)) {
				$mime = null;
			}
			else $mime = $mimetype;
		}
		else new errorPage("MIME Type kann nicht bestimmt werden!");
		
		if($mime != null) {
			
			DB::getDB()->query("INSERT INTO uploads
				(
					uploadFileName,
					uploadFileExtension,
					uploadFileMimeType,
					uploadTime,
					uploaderUserID
				) values(
					'" . DB::getDB()->escapeString($fileName) . "',
					'" . $ext . "',
					'" . $mime . "',
					UNIX_TIMESTAMP(),
					" . DB::getSession()->getUser()->getUserID() . "				
				)
			");
			
			$newID = DB::getDB()->insert_id();
			
			@move_uploaded_file($_FILES[$fieldName]["tmp_name"], "uploads/" . $newID . ".dat");
			
			$data = DB::getDB()->query_first("SELECT * FROM uploads WHERE uploadID='" . $newID. "'");
			
			return [
					'result' => true,
					'uploadobject' => new FileUpload($data),
					'mimeerror' => false,
					'text' => "Upload OK"
			];
		}
		else {
			return [
					'result' => false,
					'uploadobject' => null,
					'mimeerror' => true,
					'text' => "wrong mime type"
			];
		}
	}
}

