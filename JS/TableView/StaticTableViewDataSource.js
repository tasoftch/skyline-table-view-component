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

import {Plugin} from "../core/plugin";

export class StaticTableViewDataSource extends Plugin {
    get name() {
        return "data_source";
    }

    addRow(cells, animated) {
        this.rows.push(cells);
        this.tableview.noteRowsDidChange({animated, append: [this.rows.length-1]});
        return this;
    }

    insertRow(cells, index, animated) {
        this.rows.splice(index, 0, cells);
        this.tableview.noteRowsDidChange({animated, inserted:[index]});
        return this;
    }

    replaceRow(cells, index) {
        if(index < this.rows.length) {
            this.rows[index] = cells;
            this.tableview.noteRowsDidChange({changed:[index]});
        }
        return this;
    }

    deleteRow(index, animated) {
        if(index < this.rows.length) {
            this.rows.splice(index, 1);
            this.tableview.noteRowsDidChange({animated, deleted:[index]});
        }
        return this;
    }

    install(context, options) {
        this.tableview = context;
        this.rows = [];

        context.on('reload', ()=>{
            this.tableview.noteRowsDidChange({from:0, to:this.rows.length});
        })
        context.on("fetchrow", ({index, data, meta}) => {
            meta.template = 'main';
            Object.assign(data, this.rows[index]);
        });
    }
}