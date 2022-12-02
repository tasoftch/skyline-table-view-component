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

import {Context} from "../core/context";
import {TableViewEvents} from "./events";

import $ from "../utils/jquery";

const def_opts = {
    show_all: false,
    did_reload: false
}

export class TableView extends Context {
    get dataSource() {
        return this.plugins.get("data_source");
    }

    constructor(id, el, options) {
        super(id, new TableViewEvents());
        this.el = el;
        this.options = Object.assign({}, def_opts, options);
        this.tbody = $(el).find("tbody");
        this.row_cache = [];
    }

    noteRowsDidChange(rows) {
        if(typeof rows === 'object') {
            const handle = (idx, mode, animated) => {


                let $tr = this.row_cache[ idx ];

                if(mode === 'del') {
                    if(animated) {
                        $tr.fadeOut();
                        setTimeout(()=>{
                            $tr.remove()
                            this.row_cache.splice(idx, 1);
                        }, 500);
                    } else {
                        $tr.remove()
                        this.row_cache.splice(idx, 1);
                    }
                    return;
                }

                if(mode === 'add' || mode === 'ins')
                    $tr = $("<tr>");

                if(mode === 'cha')
                    $tr.html("");

                const data = {};
                const meta = {};
                this.trigger('fetchrow', {index: idx,data, meta});
                this.trigger('renderrow', {index:idx, row:$tr, data, meta});

                if(mode === 'add') {
                    this.row_cache[idx] = $tr;

                    if(animated) {
                        $tr.hide();
                        this.tbody.append($tr);
                        $tr.fadeIn();
                    } else {
                        this.tbody.append($tr);
                    }
                }
                if(mode === 'ins') {
                    const $ref = this.row_cache[idx];
                    this.row_cache.splice(idx, 0, $tr);

                    if(animated) {
                        $tr.hide();
                        $ref.before($tr);
                        $tr.fadeIn();
                    } else {
                        $ref.before($tr);
                    }
                }
            }

            if(typeof rows.from !== 'undefined' && typeof rows.to !== "undefined") {
                for(let i = rows.from;i<rows.to;i++)
                    handle(i, 'add');
            } else {
                if(!this.options.did_reload) {
                    this.reloadData();
                    return;
                }

                if(typeof rows.inserted !== 'undefined') {
                    rows.inserted.forEach(i=>handle(i, 'ins', rows.animated));
                } else if( typeof rows.changed !== 'undefined') {
                    rows.changed.forEach(i=>handle(i, 'cha'));
                } else if(typeof rows.append !== 'undefined') {
                    rows.append.forEach(i=>handle(i, 'add', rows.animated));
                } else if(typeof rows.deleted !== 'undefined') {
                    rows.deleted.forEach(i=>handle(i, 'del', rows.animated));
                } else {
                    rows.forEach(i=>handle(i, 'cha'));
                }
            }
        }
    }

    reloadData() {
        if(!this.dataSource)
            throw new Error("No datasource specified");
        this.row_cache.forEach(r=>r.remove());
        this.row_cache = [];

        this.trigger('reload');
        this.options.did_reload = true;
    }
}