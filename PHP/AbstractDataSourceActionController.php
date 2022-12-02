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

namespace Skyline\API;

use Skyline\API\Page\PageSetupInterface;
use Skyline\API\Persistent\NativeSessionPersistentManager;
use Skyline\API\Persistent\PersistentManagerInterface;
use Skyline\API\Predicate\PredicateInterface;
use Skyline\API\Sorting\SortDescriptorInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractDataSourceActionController extends Controller\AbstractAPIActionController
{
	/** @var PageSetupInterface|null */
	private $pageSetup;

	/** @var SortDescriptorInterface[] */
	private $sortDescriptors = [];

	/** @var PredicateInterface[] */
	private $filterPredicates = [];

	/** @var PersistentManagerInterface|null */
	private $persistentManager;

	/**
	 * You must override the preflight action method and make it public
	 * You also need to route preflight requests from the skyline-table-view-component to this method.
	 */
	protected function preflightAction() {
		/** @var Request $request */
		$request = $this->get("request");

		if(strcasecmp('post', $request->getMethod()) == 0) {
			// Change environment
			$this->setupEnvironment($request);
		} else {
			$this->setupEnvironment();
		}
	}

	/**
	 * You must override the fetch data action method and make it public
	 * You also need to route fetch requests from the skyline-table-view-component to this method.
	 */
	protected function fetchDataAction() {
		$this->setupEnvironment();

	}

	/**
	 * @return PersistentManagerInterface|null
	 */
	protected function makePersistentManager(): ?PersistentManagerInterface {
		return new NativeSessionPersistentManager(
			$this->getSelectedTableViewPrefix()
		);
	}

	/**
	 * This method is designed to distinguish between different tables that call the api.
	 *
	 * @return string
	 */
	protected function getSelectedTableViewPrefix(): string {
		return "tbl";
	}

	/**
	 * ========================== INTERN MANAGEMENT =====================
	 * @param Request|null $request
	 * @return void
	 */

	private function setupEnvironment(Request $request = NULL) {
		if($request) {

		} else {

		}
	}

	/**
	 * @return PageSetupInterface|null
	 */
	public function getPageSetup(): ?PageSetupInterface
	{
		return $this->pageSetup;
	}

	/**
	 * @return SortDescriptorInterface[]
	 */
	public function getSortDescriptors(): array
	{
		return $this->sortDescriptors;
	}

	/**
	 * @return PredicateInterface[]
	 */
	public function getFilterPredicates(): array
	{
		return $this->filterPredicates;
	}

	/**
	 * @return PersistentManagerInterface|null
	 */
	public function getPersistentManager(): ?PersistentManagerInterface
	{
		if($this->persistentManager === NULL)
			$this->persistentManager = $this->makePersistentManager() ?: false;
		return $this->persistentManager ?: NULL;
	}
}