<?php
declare(strict_types=1);

namespace cheetah;

/**
 * File handling, doesn't work or was not tested. WIP
 * @param string directory to upload and check files in
 * @param int maximal size of a file
 * @param array formats that can be uploaded
 * @author Jakub Janek
 */
class File {
	public function __construct(array $formats, string $directory = '/cdn', int $max_size = 5000000) {
		$this->max_size = $max_size;
		$this->formats = $formats;
		$this->directory = $directory;

		$this->db = new Database();
	}

	/**
	 * Upload list of files given by form
	 * @param array var $_FILES
	 * @return object
	 */
	public function upload(array $files): object {
		foreach ($files as $key => $file) {
			do {
				$name = $this->_generateFileName();
			} while (\file_exists($this->directory . $name));
 
			$info = \finfo_open(FILEINFO_MIME_TYPE);
			$type = \finfo_file($info, $file['tmp_name']);

			//Corresponding to files database table
			$uploaded_files[$key] = (object) [
				'name' => $name,
				'original_name' => $file['name'],
				'directory' => $this->directory,
				'type' => $type,
				'size' => $file['size'] 
			];

			if (!\in_array($type, $this->formats)) $uploaded_files[$key]->error = true;

			//Upload, insert into database and return id into the list
			if (\move_uploaded_file($file['tmp_name'], $this->directory . $name)
				&& !$uploaded_files->error) {

				$this->db->insert('files')
					->values((array) $uploaded_files[$key])
					->execute(); //get inserted id is not possible with current db engine

				$uploaded_files[$key]->id = $this->db->select('files')
					->item('id')
					->condition('name', $uploaded_files['name'])
					->execute()
					->result[0]['id'];


			} else $uploaded_files->error = true;
		}

		return $uploaded_files;
	}

	/**
	 * Deletes file and removes it from database (Needs check)
	 * @param int id of a file in database
	 * @return bool
	 */
	public function delete(int $id): bool {
		//$file = $this->db->fetch('files', ['id', 'directory', 'name'], "id = {$id}");
		$file = $this->db->select('files')
			->items(['id', 'directory', 'name'])
			->condition('id', $id)
			->execute()
			->result[0];

		if (!unlink($file['directory'] . $file['name'])) return false;

		$this->db->delete('files')
			->condition('id', $id)
			->execute();

		return true;
	}

	/**
	 * Create if doesn't exist and set current directory
	 * @param string directory name
	 * @return boolean
	 */
	public function setDirectory(string $dir): bool {
		if (!file_exists($dir)) {
			if (!mkdir($dir)) return false;
		}

		$this->dir = $dir;

		return true;
	}

	/**
	 * Generates 10 character random string of characters
	 * @return string
	 */
	private function _generateFileName(): string {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		
		for ($i = 0; $i < 10; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}
}