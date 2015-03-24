<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Images;

class CompatGetOperation extends \Icybee\Modules\Files\GetOperation
{
	protected function lazy_get_record()
	{
		$nid = $this->request['nid'];

		return $nid ? $this->module->model[$nid] : null;
	}
}
