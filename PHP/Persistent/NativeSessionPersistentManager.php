<?php
/*
 * BSD 3-Clause License
 *
 * Copyright (c) 2022, TASoft Applications
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 *  Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 *  Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace Skyline\API\Persistent;


use Skyline\API\Page\PageSetup;
use Skyline\API\Page\PageSetupInterface;
use Skyline\API\Predicate\Predicate;
use Skyline\API\Predicate\PredicateInterface;
use Skyline\API\Sorting\SortDescriptor;
use Skyline\API\Sorting\SortDescriptorInterface;

class NativeSessionPersistentManager implements PersistentManagerInterface
{
	/** @var string */
	private $prefix;

	/**
	 * NativeSessionPersistentManager constructor.
	 * @param string $prefix
	 */
	public function __construct(string $prefix)
	{
		$this->prefix = $prefix;
		if(!session_id())
			session_start();
	}


	/**
	 * @inheritDoc
	 */
	public function loadPageSetup(): ?PageSetupInterface
	{
		if(isset( $_SESSION[$this->prefix . "_pg_cnt"] )) {
			return new PageSetup(
				$_SESSION[$this->prefix . "_pg_sel"],
				$_SESSION[$this->prefix . "_pg_cnt"]
			);
		}
		return NULL;
	}

	/**
	 * @inheritDoc
	 */
	public function loadSortDescriptors(): ?array
	{
		if(isset($_SESSION[$this->prefix . "_sort"])) {
			$data = unserialize($_SESSION[$this->prefix . "_sort"]);
			$descriptors = [];
			foreach($data as $d) {
				$descriptors[] = new SortDescriptor(
					...$d
				);
			}
			return $descriptors;
		}
		return NULL;
	}

	/**
	 * @inheritDoc
	 */
	public function loadPredicates(): ?array
	{
		if(isset($_SESSION[$this->prefix . "_pred"])) {
			$data = unserialize($_SESSION[$this->prefix . "_pred"]);
			$predicates = [];
			foreach($data as $d) {
				$predicates[] = new Predicate(
					...$d
				);
			}
			return $predicates;
		}
		return NULL;
	}

	/**
	 * @inheritDoc
	 */
	public function savePageSetup(PageSetupInterface $pageSetup)
	{
		$_SESSION[$this->prefix . "_pg_cnt"] = $pageSetup->getPageSize();
		$_SESSION[$this->prefix . "_pg_sel"] = $pageSetup->getSelectedPage();
	}

	/**
	 * @inheritDoc
	 */
	public function saveSortDescriptors(array $sortDescriptors)
	{
		$data = [];
		foreach($sortDescriptors as $descriptor) {
			$data[] = [
				$descriptor->getKey(),
				$descriptor->isAscending(),
				$descriptor->isCaseSensitive()
			];
		}
		$_SESSION[$this->prefix . "_sort"] = serialize( $data );
	}

	/**
	 * @inheritDoc
	 */
	public function savePredicates(array $predicates)
	{
		$data = [];
		foreach($predicates as $predicate) {
			$data[] = [
				$predicate->getKey(),
				$predicate->getCompareString(),
				$predicate->isRegex()
			];
		}
		$_SESSION[$this->prefix . "_pred"] = serialize( $data );
	}

	/**
	 * @return string
	 */
	public function getPrefix(): string
	{
		return $this->prefix;
	}
}