<?php
declare(strict_types=1);

namespace cheetah;

use Exception;
use function file_exists;
use function finfo_file;
use function finfo_open;
use function in_array;
use function mkdir;
use function move_uploaded_file;

/**
 * File handling
 * @author Jakub Janek
 */
class File {
	private $max_size = 0;
	private $formats = [];
	private $directory = "";
	private $dir;
	private $db;

	/**
	 * File constructor.
	 * @param array formats that can be uploaded
	 * @param string directory to upload and check files in
	 * @param int maximal size of a file
	 * @throws Exception
	 */
	public function __construct(array $formats, string $directory = 'cdn/', int $max_size = 5000000) {
		$this->max_size = $max_size;
		$this->formats = $formats;
		$this->directory = $directory;

		$this->db = new Database();
	}

	/**
	 * Upload list of files given by form
	 * @param array var $_FILES
	 * @return array
	 */
	public function upload(array $files): array {
		$uploaded_files = [];

		foreach ($files as $key => $file) {
			do {
				$name = uniqid('');
			} while (file_exists($this->directory . $name));

			$info = finfo_open(FILEINFO_MIME_TYPE);
			$type = finfo_file($info, $file['tmp_name']);

			//Corresponding to files database table
			$uploaded_files[$key] = (object)[
				'name' => $name,
				'original_name' => $file['name'],
				'directory' => $this->directory,
				'type' => $type,
				'size' => $file['size']
			];

			if (!in_array($type, $this->formats)) $uploaded_files[$key]->error = true;

			//Upload, insert into database and return id into the list
			if (move_uploaded_file($file['tmp_name'], $this->directory . $name)
			&& !$uploaded_files[$key]->error) {
				$uploaded_files[$key]->fid = $this->db->insert('file')
					->values((array)$uploaded_files[$key])
					->execute();

			} else $uploaded_files[$key]->error = true;
		}

		return $uploaded_files;
	}

	/**
	 * Deletes file and removes it from database (Needs check)
	 * @param int fid of a file in database
	 * @return bool
	 */
	public function delete(int $fid): bool {
		$file = $this->db->select('file')
			->items(['fid', 'directory', 'name'])
			->condition('fid', $fid)
			->execute()
			->fetch_all(MYSQLI_ASSOC);

		if (!unlink($file['directory'] . $file['name'])) return false;

		$this->db->delete('file')
			->condition('fid', $fid)
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
}