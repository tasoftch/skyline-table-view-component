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

namespace Skyline\API\Sorting;


class SortDescriptor implements SortDescriptorInterface
{
	/** @var string */
	private $key;
	/** @var bool */
	private $ascending;
	/** @var bool */
	private $caseSensitive;

	/**
	 * SortDescriptor constructor.
	 * @param string $key
	 * @param bool $ascending
	 * @param bool $caseSensitive
	 */
	public function __construct(string $key, bool $ascending = true, bool $caseSensitive = false)
	{
		$this->key = $key;
		$this->ascending = $ascending;
		$this->caseSensitive = $caseSensitive;
	}


	/**
	 * @inheritDoc
	 */
	public function getKey(): string
	{
		return $this->key;
	}

	/**
	 * @inheritDoc
	 */
	public function isAscending(): bool
	{
		return $this->ascending;
	}

	/**
	 * @inheritDoc
	 */
	public function isCaseSensitive(): bool
	{
		return $this->caseSensitive;
	}
}