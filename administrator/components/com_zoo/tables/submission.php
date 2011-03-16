    <?php
/**
* @package   ZOO Component
* @file      submission.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: SubmissionTable
		The table class for submissions.
*/
class SubmissionTable extends YTable {

	protected function __construct() {
		parent::__construct('Submission', ZOO_TABLE_SUBMISSION, 'id');
	}

/*
		Function: save
			Override. Save object to database table.

		Returns:
			Boolean.
	*/
	public function save($object) {

		if ($object->name == '') {
			throw new SubmissionTableException('Invalid name');
		}

		if ($object->alias == '' || preg_match('/[^\x{00C0}-\x{00D6}x{00D8}-\x{00F6}x{00F8}-\x{00FF}x{0370}-\x{1FFF}a-z0-9\-]/u', $object->alias)) {
			throw new SubmissionTableException('Invalid slug');
		}

		if (SubmissionHelper::checkAliasExists($object->alias, $object->id)) {
			throw new SubmissionTableException('Slug already exists, please choose a unique slug');
		}

		return parent::save($object);
	}

}

/*
	Class: SubmissionTableException
*/
class SubmissionTableException extends YException {}